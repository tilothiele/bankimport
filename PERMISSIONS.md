# BankImport Modul - Berechtigungen konfigurieren

## Problem: "Zugriff verweigert" beim Klick auf "Kontoauszüge importieren"

Wenn Sie die Meldung "Zugriff verweigert" erhalten, müssen die Berechtigungen für das BankImport-Modul konfiguriert werden.

## Lösung: Berechtigungen einrichten

### Schritt 1: Modul neu aktivieren

1. Gehen Sie zu **Setup** → **Module/Applications**
2. Suchen Sie nach "BankImport"
3. **Deaktivieren** Sie das Modul (falls aktiviert)
4. **Aktivieren** Sie das Modul erneut

### Schritt 2: Berechtigungen konfigurieren

1. Gehen Sie zu **Setup** → **Users & Groups** → **Permissions**
2. Wählen Sie die gewünschte Benutzergruppe aus (z.B. "Users" oder "Administrators")
3. Scrollen Sie zum Abschnitt **BankImport**
4. Aktivieren Sie die Berechtigung **"Bankauszüge importieren"**
5. Klicken Sie auf **Speichern**

### Schritt 3: Benutzer-Berechtigungen prüfen

1. Gehen Sie zu **Setup** → **Users & Groups** → **Users**
2. Wählen Sie den gewünschten Benutzer aus
3. Gehen Sie zum Tab **Permissions**
4. Stellen Sie sicher, dass die BankImport-Berechtigungen aktiviert sind

## Alternative: Temporäre Lösung

Falls die Berechtigungen nicht funktionieren, können Sie temporär die Berechtigungsprüfung deaktivieren:

### In der Datei `custom/bankimport/import.php`:

Ändern Sie Zeile 23-25 von:
```php
// Security check - check for bankimport rights
if (!$user->rights->bankimport->import) {
    accessforbidden();
}
```

zu:
```php
// Security check - temporarily disabled for testing
// if (!$user->rights->bankimport->import) {
//     accessforbidden();
// }
```

**⚠️ Wichtig**: Diese Änderung sollte nur für Tests verwendet werden und muss vor dem produktiven Einsatz wieder rückgängig gemacht werden!

## Überprüfung der Installation

### 1. Modul-Status prüfen

Gehen Sie zu **Setup** → **Module/Applications** und stellen Sie sicher, dass:
- BankImport als **aktiviert** angezeigt wird
- Keine Fehlermeldungen vorhanden sind

### 2. Menü-Eintrag prüfen

Gehen Sie zu **Bank** und prüfen Sie, ob der Menüpunkt **"Kontoauszüge importieren"** angezeigt wird.

### 3. Berechtigungen testen

1. Melden Sie sich mit einem Benutzer an, der die Berechtigungen hat
2. Gehen Sie zu **Bank** → **Kontoauszüge importieren**
3. Die Seite sollte ohne "Zugriff verweigert" laden

## Häufige Probleme

### Problem: Berechtigungen werden nicht gespeichert
**Lösung**:
- Cache leeren (Setup → Tools → Clear cache)
- Browser-Cache leeren
- Dolibarr neu starten

### Problem: Modul wird nicht angezeigt
**Lösung**:
- Überprüfen Sie die Dateiberechtigungen
- Stellen Sie sicher, dass alle Dateien korrekt kopiert wurden
- Prüfen Sie die Dolibarr-Logs auf Fehler

### Problem: Menüpunkt fehlt
**Lösung**:
- Modul deaktivieren und wieder aktivieren
- Überprüfen Sie die Menü-Konfiguration in der Modulklasse

## Support

Bei weiterhin bestehenden Problemen:
- Überprüfen Sie die Dolibarr-Logs
- Kontaktieren Sie den Systemadministrator
- Erstellen Sie ein Issue im GitHub-Repository
