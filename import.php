<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

function limit_string(?string $text, int $length = 255, bool $fixed = false): string {
    if ($text === null) {
        return $fixed ? str_repeat(' ', $length) : '';
    }
    $limited = substr($text, 0, $length);
    return $fixed ? str_pad($limited, $length) : $limited;
}

function ddmmyy2dol_date(string $s) {
    $dd = substr($s, 0, 2);
    $mm = substr($s, 3, 2);
    $yyyy = substr($s, 6, 2);
    if(!empty($yyyy)) {
        $yyyy = '20' . $yyyy;
    }
    return dol_mktime(0, 0, 0, $mm, $dd, $yyyy);
}

$langs->load("bankimport@bankimport");

llxHeader('', $langs->trans("BANKIMPORT_Title"));

print load_fiche_titre($langs->trans("BANKIMPORT_Title"));

// Bankkonto auswählen
$accountid = GETPOST('accountid', 'int');
$encoding  = GETPOST('encoding', 'alpha'); // UTF-8 oder ISO-8859-1

if (empty($accountid)) {
    print "<p style=\"color:red\">" . $langs->trans("BANKIMPORT_Choose_account") . "</p>";
}

$form = new Form($db);
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print $langs->trans("BANKIMPORT_Bank_account") . ': ';
print $form->select_comptes($accountid, 'accountid', 0, '', 1, 0, 'all');

// Upload
print '<br><br>' . $langs->trans("BANKIMPORT_File_label") . ': <input type="file" name="statement">';

// Encoding Dropdown
print '<br><br>Encoding: ';
print '<select name="encoding">';
$encodings = array('UTF-8' => 'UTF-8', 'ISO-8859-1' => 'ISO-8859-1');
foreach ($encodings as $key => $label) {
    $selected = ($encoding == $key) ? 'selected' : '';
    print '<option value="'.$key.'" '.$selected.'>'.$label.'</option>';
}
print '</select>';

print '<br><br><input type="submit" class="button" value="' . $langs->trans("BANKIMPORT_Importieren_label") . '">';
print '</form><br>';

// --- Upload behandeln ---
if (!empty($_FILES['statement']['tmp_name']) && $accountid > 0) {
    $filename = $_FILES['statement']['tmp_name'];
    $handle = fopen($filename, "r");
    if ($handle) {
        $row = 0;
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Kopfzeile überspringen
            
            // Encoding konvertieren
            foreach ($data as &$field) {
                if ($encoding && strtoupper($encoding) !== 'UTF-8') {
                    $field = iconv($encoding, "UTF-8//TRANSLIT", $field);
                }
            }
            
            $dateo  = ddmmyy2dol_date($data[1]);
            $datev  = ddmmyy2dol_date($data[2]);
            $label  = limit_string($data[4]);
            $amount = price2num($data[14]);
            $oper   = 'VIR';
            $ref       = trim($data[6]);
            $categorie = null; #(int) $data[3];
            $transaction_id = null; #$data[7];
            $bank_other = $data[13];
            $iban_other = $data[12];
            $owner_other = $data[11];
            
            $import_key = trim($transaction_id);
            if(empty($import_key)) {
                $import_key = implode('|', array(
                    trim($iban_other),
                    trim($owner_other),
                    number_format($amount, 2, '.', ''),
                    trim($label),
                    trim($ref)
                ));
                $import_key = substr(sha1($import_key), 0, 14);
            }
            
            $account = new Account($db);
            $account->fetch($accountid);
            $num_releve = null;
            $amount_main_currency = null; #$data[15];
            $note = '';
            $sep = '';
            if(!empty($data[8])) {
                $note = $sep . 'Sammlerreferenz=' . $data[8];
                $sep = ' ';
            }
            if(!empty($data[5])) {
                $note = $sep . 'GlaeubigerId=' . $data[5];
                $sep = ' ';
            }
            
            $db->begin();
            
            $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."bank WHERE import_key = '".$db->escape($import_key)."'";
            $resql = $db->query($sql);
            
            if ($resql->num_rows > 0) {
                print '<p style="color:red">' . $langs->trans("BANKIMPORT_Msg_already_imported", $row) . '.</p>';
                $db->rollback();
            } else {
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
                    $num_releve,
                    $amount_main_currency,
                    $note
                    );
                if ($bankline_id > 0) {
                    $sql = "UPDATE ".MAIN_DB_PREFIX."bank SET import_key = '".$db->escape($import_key)."' WHERE rowid = ".((int) $bankline_id);
                    $db->query($sql);
                    $desc = $iban_other.", ".$owner_other.", ".$bank_other.", ".$amount;
                    print '<p style="color:green">' . $langs->trans("BANKIMPORT_Msg_successfully_imported", $desc) . '</p>';
                    $db->commit();
                } else {
                    print '<p style="color:red">' . $langs->trans("BANKIMPORT_Msg_error_in_line", $row, $db->error) . '</p>';
                    $db->rollback();
                }
            }
        }
        fclose($handle);
    } else {
        print '<p style="color:red">' . $langs->trans("BANKIMPORT_Msg_could_not_open_file") . '</p>';
    }
}

llxFooter();
$db->close();
