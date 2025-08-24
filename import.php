<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

//require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/relevebank.class.php';

function limit_string(?string $text, int $length = 255, bool $fixed = false): string {
    if ($text === null) {
        return $fixed ? str_repeat(' ', $length) : '';
    }
    $limited = substr($text, 0, $length);
    return $fixed ? str_pad($limited, $length) : $limited;
}

$langs->load("bankimport@bankimport");

llxHeader('', 'Bankauszüge importieren');

print load_fiche_titre("Bankauszüge importieren");

// Bankkonto auswählen (z. B. aus Setup oder Dropdown)
$accountid = GETPOST('accountid', 'int');
if (empty($accountid)) {
    print '<p style="color:red">Bitte zuerst ein Bankkonto auswählen!</p>';
}
$form = new Form($db);
print '<form action="'.$_SERVER["PHP_SELF"].'" method="post" enctype="multipart/form-data">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print 'Bankkonto: ';
print $form->select_comptes($accountid, 'accountid', 0, '', 1, 0, 'all');
// Upload
print '<br><br>Datei: <input type="file" name="statement">';
print '<input type="submit" class="button" value="Importieren">';
print '</form><br>';


// --- Upload behandeln ---
if (!empty($_FILES['statement']['tmp_name']) && $accountid > 0) {
    $filename = $_FILES['statement']['tmp_name'];
    
    $handle = fopen($filename, "r");
    if ($handle) {
        
        // date;datev;label;amount;oper;ref;categorie;transaction_id;bank_other;iban_other;owner_other
        $row = 0;
        while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Kopfzeile überspringen
            
            $dateo  = dol_mktime(0, 0, 0,
                substr($data[0], 5, 2),
                substr($data[0], 8, 2),
                substr($data[0], 0, 4));
            $datev  = dol_mktime(0, 0, 0,
                substr($data[1], 5, 2),
                substr($data[1], 8, 2),
                substr($data[0], 0, 4));
            $label  = limit_string($data[2]);
            $amount = price2num($data[3]);
            $oper   = trim($data[4]) ?: 'VIR'; // Fallback
            $ref       = trim($data[5]); // Zahlungsreferenz
            $categorie = (int) $data[6]; // Kategorie-ID (oder 0 wenn leer)
            $transaction_id = $data[7];
            $bank_other = $data[8];
            $iban_other = $data[9];
            $owner_other = $data[10];
            $import_key = trim($transaction_id);
            if(empty($import_key)) {
                $import_key = implode('|', array(
                    trim($iban_other),        // IBAN Aussteller
                    trim($owner_other),       // Kontoinhaber Aussteller
                    number_format($amount, 2, '.', ''), // Betrag normiert
                    trim($label),              // Verwendungszweck / Label
                    trim($ref)           // Referenz
                ));
                $import_key = substr(sha1($import_key), 0, 14);
            }
            $amount_main_currency = null;
            $num_releve = '';
            
            // Bankeintrag erzeugen
            $account = new Account($db);
            $account->fetch($accountid);
            
            $db->begin();
            
            $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."bank WHERE import_key = '".$import_key."'";
            $resql = $db->query($sql);
            
            if ($resql->num_rows > 0) {
                print '<p style="color:red">Zeile '.$row.': bereits importiert.</p>';
                $db->rollback();
            } else {            
                $bankline_id = $account->addline(
                    $dateo,            // Datum der Buchung (Timestamp)
                    $oper,               // Oper (Operationscode, optional)
                    $label,           // Label / Verwendungszweck
                    $amount,          // Betrag (positiv/negativ)
                    $ref,               // Numéro (Schecknummer / Zahlungsreferenz)
                    $categorie,               // Kategorie (string oder ID)
                    $user,             // User-Objekt (aktuell eingeloggter User)
                    $owner_other,
                    $bank_other,
                    $iban_other,
                    );
                if ($bankline_id > 0) {
                    $sql = "UPDATE ".MAIN_DB_PREFIX."bank SET import_key = '".$db->escape($import_key)."' WHERE rowid = ".((int) $bankline_id);
                    $db->query($sql);
                    $desc = $iban_other.", ".$owner_other.", ".$bank_other.", ".$amount;
                    print '<p style="color:green">Zeile erfolgreich importiert: '.$desc.'</p>';
                    $db->commit();
                } else {
                    print '<p style="color:red">Fehler bei Zeile '.$row.': '.$account->error.'</p>';
                    $db->rollback();
                }
            }
        }
        fclose($handle);
    } else {
        print '<p style="color:red">Datei konnte nicht geöffnet werden.</p>';
    }
}

llxFooter();
$db->close();
