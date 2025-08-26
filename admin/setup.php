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

require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Security check
if (!$user->admin) {
    accessforbidden();
}

$langs->load("admin");
$langs->load("bankimport@bankimport");

// Handle form submission
$action = GETPOST('action', 'alpha');
$token = GETPOST('token', 'alpha');

if ($action == 'update' && $token) {
    // Handle configuration updates here
    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
}

llxHeader('', $langs->trans("BANKIMPORT_Setup"));

print load_fiche_titre($langs->trans("BANKIMPORT_Setup"));

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';

print '<table class="noborder centpercent">';

// Module Information
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("BANKIMPORT_Setup_Module_Info").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("BANKIMPORT_Setup_Author").'</td>';
print '<td>Tilo Thiele &lt;tilo.thiele@hamburg.de&gt;</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("BANKIMPORT_Setup_License").'</td>';
print '<td>MIT</td>';
print '</tr>';

// Configuration Options
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("BANKIMPORT_Setup_Configuration").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("BANKIMPORT_Setup_Max_File_Size").'</td>';
print '<td>10 MB</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("BANKIMPORT_Setup_Supported_Encodings").'</td>';
print '<td>UTF-8, ISO-8859-1</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td>'.$langs->trans("BANKIMPORT_Setup_CSV_Separator").'</td>';
print '<td>Semikolon (;)</td>';
print '</tr>';

// Features
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("BANKIMPORT_Setup_Features").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td colspan="2">';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_CSV").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_Encoding").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_Duplicate").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_Validation").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_Multilingual").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Feature_Security");
print '</td>';
print '</tr>';

// Requirements
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("BANKIMPORT_Setup_Requirements").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td colspan="2">';
print '• '.$langs->trans("BANKIMPORT_Setup_Requirement_PHP").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Requirement_Dolibarr").'<br>';
print '• '.$langs->trans("BANKIMPORT_Setup_Requirement_Permissions");
print '</td>';
print '</tr>';

// Save button
print '<tr class="liste_titre">';
print '<td colspan="2" class="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
print '</td>';
print '</tr>';

print '</table>';
print '</form>';

// Help section
print '<br>';
print '<div class="info">';
print '<strong>'.$langs->trans("BANKIMPORT_Setup_Help_Title").'</strong><br>';
print $langs->trans("BANKIMPORT_Setup_Help_Description").'<br><br>';
print '<strong>'.$langs->trans("BANKIMPORT_Setup_Help_Usage").'</strong><br>';
print $langs->trans("BANKIMPORT_Setup_Help_Usage_Details");
print '</div>';

llxFooter();
$db->close();
