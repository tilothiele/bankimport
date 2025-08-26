<?php
/* Copyright (C) 2024 Tilo Thiele <tilo.thiele@hamburg.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

/**
 * BankImport class
 */
class BankImport extends CommonObject
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;

    /**
     * @var string Error code (or message)
     */
    public $error = '';

    /**
     * @var string[] Error codes (or messages)
     */
    public $errors = array();

    /**
     * @var int Bank account ID
     */
    public $accountid;

    /**
     * @var string File encoding
     */
    public $encoding;

    /**
     * @var array CSV field mapping
     */
    public $fieldMapping = array(
        'account' => 0,
        'booking_date' => 1,
        'value_date' => 2,
        'booking_text' => 3,
        'payment_purpose' => 4,
        'creditor_id' => 5,
        'mandate_reference' => 6,
        'collector_reference' => 8,
        'counterparty_name' => 11,
        'counterparty_iban' => 12,
        'counterparty_bic' => 13,
        'amount' => 14,
        'currency' => 15,
        'info' => 16
    );

    /**
     * Constructor
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Set account ID
     *
     * @param int $accountid Bank account ID
     * @return void
     */
    public function setAccountId($accountid)
    {
        $this->accountid = (int) $accountid;
    }

    /**
     * Set encoding
     *
     * @param string $encoding File encoding
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Validate uploaded file
     *
     * @param array $file $_FILES array element
     * @return bool True if valid, false otherwise
     */
    public function validateFile($file)
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->error = 'No file uploaded';
            return false;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            $this->error = 'Invalid file upload';
            return false;
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            $this->error = 'File too large (max 10MB)';
            return false;
        }

        $allowedTypes = array('text/csv', 'text/plain', 'application/csv');
        if (!in_array($file['type'], $allowedTypes) && !preg_match('/\.csv$/i', $file['name'])) {
            $this->error = 'Invalid file type (CSV required)';
            return false;
        }

        return true;
    }

    /**
     * Process CSV file
     *
     * @param string $filename File path
     * @return array Array with success count and errors
     */
    public function processFile($filename)
    {
        $result = array(
            'success' => 0,
            'errors' => array(),
            'skipped' => 0
        );

        // Validate account ID is set
        if (empty($this->accountid) || $this->accountid <= 0) {
            $this->error = 'No valid bank account selected';
            $result['errors'][] = 'No valid bank account selected';
            return $result;
        }

        $handle = fopen($filename, 'r');
        if (!$handle) {
            $this->error = 'Could not open file';
            return $result;
        }

        $row = 0;
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Skip header

            // Convert encoding if needed
            $data = $this->convertEncoding($data);

            // Validate data
            if (!$this->validateRow($data, $row)) {
                $result['errors'][] = "Row $row: " . $this->error;
                continue;
            }

            // Process row
            $importResult = $this->processRow($data, $row);
            if ($importResult === true) {
                $result['success']++;
            } elseif ($importResult === 'skipped') {
                $result['skipped']++;
            } else {
                $result['errors'][] = "Row $row: " . $importResult;
            }
        }

        fclose($handle);
        return $result;
    }

    /**
     * Convert encoding of data array
     *
     * @param array $data Data array
     * @return array Converted data array
     */
    private function convertEncoding($data)
    {
        if ($this->encoding && strtoupper($this->encoding) !== 'UTF-8') {
            foreach ($data as &$field) {
                $field = iconv($this->encoding, "UTF-8//TRANSLIT", $field);
            }
        }
        return $data;
    }

    /**
     * Validate CSV row data
     *
     * @param array $data Row data
     * @param int $row Row number
     * @return bool True if valid, false otherwise
     */
    private function validateRow($data, $row)
    {
        if (count($data) < 15) {
            $this->error = 'Insufficient columns in CSV';
            return false;
        }

        // Validate required fields
        if (empty($data[$this->fieldMapping['booking_date']])) {
            $this->error = 'Missing booking date';
            return false;
        }

        if (empty($data[$this->fieldMapping['amount']])) {
            $this->error = 'Missing amount';
            return false;
        }

        return true;
    }

    /**
     * Process single CSV row
     *
     * @param array $data Row data
     * @param int $row Row number
     * @return bool|string True on success, 'skipped' if already imported, error message on failure
     */
    private function processRow($data, $row)
    {
        global $user;

        // Extract data
        $dateo = $this->parseDate($data[$this->fieldMapping['booking_date']]);
        $datev = $this->parseDate($data[$this->fieldMapping['value_date']]);
        $label = $this->limitString($data[$this->fieldMapping['payment_purpose']]);
        $amount = price2num($data[$this->fieldMapping['amount']]);
        $oper = 'VIR';
        $ref = trim($data[$this->fieldMapping['mandate_reference']]);
        $categorie = null;
        $transaction_id = null;
        $bank_other = $data[$this->fieldMapping['counterparty_bic']];
        $iban_other = $data[$this->fieldMapping['counterparty_iban']];
        $owner_other = $data[$this->fieldMapping['counterparty_name']];

        // Generate import key
        $import_key = $this->generateImportKey($transaction_id, $iban_other, $owner_other, $amount, $label, $ref);

        // Check if already imported
        if ($this->isAlreadyImported($import_key)) {
            return 'skipped';
        }

        // Prepare notes
        $note = $this->buildNote($data);

        // Begin transaction
        $this->db->begin();

        try {
            $account = new Account($this->db);
            $account->fetch($this->accountid);

            $bankline_id = $account->addline(
                $dateo,
                $oper,
                $label,
                $amount,
                $ref,
                $categorie,
                $user,
                $owner_other,
                $bank_other,
                $iban_other,
                $datev,
                null, // num_releve
                null, // amount_main_currency
                $note
            );

            if ($bankline_id > 0) {
                // Update import key
                $this->updateImportKey($bankline_id, $import_key);
                $this->db->commit();
                return true;
            } else {
                $this->db->rollback();
                return $account->error;
            }
        } catch (Exception $e) {
            $this->db->rollback();
            return $e->getMessage();
        }
    }

    /**
     * Parse date from DD.MM.YY format
     *
     * @param string $dateString Date string
     * @return int Timestamp
     */
    private function parseDate($dateString)
    {
        $dd = substr($dateString, 0, 2);
        $mm = substr($dateString, 3, 2);
        $yyyy = substr($dateString, 6, 2);
        if (!empty($yyyy)) {
            $yyyy = '20' . $yyyy;
        }
        return dol_mktime(0, 0, 0, $mm, $dd, $yyyy);
    }

    /**
     * Limit string length
     *
     * @param string|null $text Text to limit
     * @param int $length Maximum length
     * @param bool $fixed Fixed length
     * @return string Limited string
     */
    private function limitString($text, $length = 255, $fixed = false)
    {
        if ($text === null) {
            return $fixed ? str_repeat(' ', $length) : '';
        }
        $limited = substr($text, 0, $length);
        return $fixed ? str_pad($limited, $length) : $limited;
    }

    /**
     * Generate import key
     *
     * @param string|null $transaction_id Transaction ID
     * @param string $iban_other Counterparty IBAN
     * @param string $owner_other Counterparty name
     * @param float $amount Amount
     * @param string $label Label
     * @param string $ref Reference
     * @return string Import key
     */
    private function generateImportKey($transaction_id, $iban_other, $owner_other, $amount, $label, $ref)
    {
        if (!empty($transaction_id)) {
            return trim($transaction_id);
        }

        $key = implode('|', array(
            trim($iban_other),
            trim($owner_other),
            number_format($amount, 2, '.', ''),
            trim($label),
            trim($ref)
        ));
        return substr(sha1($key), 0, 14);
    }

    /**
     * Check if transaction is already imported
     *
     * @param string $import_key Import key
     * @return bool True if already imported
     */
    private function isAlreadyImported($import_key)
    {
        $sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "bank WHERE import_key = '" . $this->db->escape($import_key) . "'";
        $resql = $this->db->query($sql);
        if ($resql) {
            return $this->db->num_rows($resql) > 0;
        }
        return false;
    }

    /**
     * Update import key for bank line
     *
     * @param int $bankline_id Bank line ID
     * @param string $import_key Import key
     * @return bool Success
     */
    private function updateImportKey($bankline_id, $import_key)
    {
        $sql = "UPDATE " . MAIN_DB_PREFIX . "bank SET import_key = '" . $this->db->escape($import_key) . "' WHERE rowid = " . ((int) $bankline_id);
        return $this->db->query($sql);
    }

    /**
     * Build note from CSV data
     *
     * @param array $data CSV data
     * @return string Note
     */
    private function buildNote($data)
    {
        $note = '';
        $sep = '';

        if (!empty($data[$this->fieldMapping['collector_reference']])) {
            $note .= $sep . 'Sammlerreferenz=' . $data[$this->fieldMapping['collector_reference']];
            $sep = ' ';
        }

        if (!empty($data[$this->fieldMapping['creditor_id']])) {
            $note .= $sep . 'GlaeubigerId=' . $data[$this->fieldMapping['creditor_id']];
            $sep = ' ';
        }

        return $note;
    }
} 
