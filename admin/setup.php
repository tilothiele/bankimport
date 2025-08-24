<?php
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->load("admin");

llxHeader('', 'BankImport Setup');

print load_fiche_titre("BankImport Setup");
print "Hier kannst du später Parameter einstellen (z. B. Bankkonto, Importformat, …).";

llxFooter();
$db->close();
