# BankImport Modul - Installationsanleitung

## Übersicht

Das BankImport-Modul ermöglicht den Import von Bankauszügen im CSV-Format (camt.052 v8) in Dolibarr. Das Modul unterstützt verschiedene Kodierungen und verhindert Duplikate durch Import-Schlüssel.

## Systemanforderungen

- **PHP**: 7.4 oder höher
- **Dolibarr**: 21.0.0 oder höher
- **Aktiviertes Bank-Modul** in Dolibarr

## Installation

### 1. Modul installieren

1. Kopieren Sie das `bankimport` Verzeichnis in den `custom` Ordner Ihrer Dolibarr-Installation:
   ```
   /path/to/dolibarr/htdocs/custom/bankimport/
   ```

2. Stellen Sie sicher, dass die Dateiberechtigungen korrekt sind:
   ```bash
   chmod -R 755 /path/to/dolibarr/htdocs/custom/bankimport/
   chown -R www-data:www-data /path/to/dolibarr/htdocs/custom/bankimport/
   ```

oder:

1. Melden Sie sich als Administrator in Dolibarr an
2. Gehen Sie zu **Start** → **Setup** → **Module/Applications**
3. Schalten Sie in den Tab **Externes Modul hinzufügen**
4. Wählen Sie die Datei `module_bankimport-<version>.zip` und klicken Sie auf **Upload**

### 2. Modul aktivieren

1. Melden Sie sich als Administrator in Dolibarr an
2. Gehen Sie zu **Start** → **Setup** → **Module/Applications**
3. Suchen Sie nach "BankImport" in der Liste
4. Klicken Sie auf **Aktivieren**

### 3. Berechtigungen konfigurieren

1. Gehen Sie zu **Start** → **Setup** → **Users & Groups** → **Permissions**
2. Wählen Sie den gewünschten Benutzer oder die gewünschte Benutzergruppe aus
3. Klicken Sie auf den Tab **Berechtigungen**
4. Aktivieren Sie die Berechtigung *Transaktionen erstellen/bearbeiten/löschen und vergleichen* unter dem Bank-Modul

## Verwendung

### CSV-Datei vorbereiten

Die CSV-Datei muss folgendes Format haben:
- **Trennzeichen**: Semikolon (;)
- **Kodierung**: UTF-8 oder ISO-8859-1
- **Erste Zeile**: Header (wird übersprungen)
- **Format**: camt.052 v8

### Import durchführen

1. Gehen Sie zu **Bank** → **Kontoauszüge importieren**
2. Wählen Sie das Bankkonto aus
3. Wählen Sie die CSV-Datei aus
4. Wählen Sie die Kodierung (UTF-8 oder ISO-8859-1)
5. Klicken Sie auf **Importieren**

### Import-Ergebnisse

Das Modul zeigt folgende Informationen an:
- **Erfolgreich importiert**: Anzahl der neuen Transaktionen
- **Übersprungen**: Anzahl der bereits importierten Transaktionen
- **Fehler**: Detaillierte Fehlermeldungen für problematische Zeilen

## Konfiguration

### Erweiterte Einstellungen

Das Modul unterstützt folgende Konfigurationen:
- **Maximale Dateigröße**: 10 MB (standardmäßig)
- **Unterstützte Kodierungen**: UTF-8, ISO-8859-1
- **Duplikat-Erkennung**: Automatisch durch Import-Schlüssel

### CSV-Feld-Mapping

Das Modul verwendet folgendes Feld-Mapping für camt.052 v8:
- Feld 0: Kontonummer (IBAN)
- Feld 1: Buchungstag
- Feld 2: Valutadatum
- Feld 3: Buchungstext
- Feld 4: Verwendungszweck
- Feld 5: Gläubiger-ID
- Feld 6: Mandatsreferenz
- Feld 8: Sammlerreferenz
- Feld 11: Begünstigter/Zahlungspflichtiger
- Feld 12: Kontonummer/IBAN (Gegenpartei)
- Feld 13: BIC (Gegenpartei)
- Feld 14: Betrag
- Feld 15: Währung
- Feld 16: Info

## Fehlerbehebung

### Häufige Probleme

1. **"Datei konnte nicht geöffnet werden"**
   - Überprüfen Sie die Dateiberechtigungen
   - Stellen Sie sicher, dass die Datei nicht beschädigt ist

2. **"Insufficient columns in CSV"**
   - Überprüfen Sie das CSV-Format
   - Stellen Sie sicher, dass Semikolon als Trennzeichen verwendet wird

3. **"Invalid file type"**
   - Verwenden Sie nur CSV-Dateien
   - Überprüfen Sie die Dateiendung

4. **Kodierungsprobleme**
   - Versuchen Sie eine andere Kodierung (UTF-8 oder ISO-8859-1)
   - Überprüfen Sie die Datei in einem Texteditor

### Logs überprüfen

Bei Problemen überprüfen Sie die Dolibarr-Logs:
- **Apache/Nginx Logs**: `/var/log/apache2/error.log` oder `/var/log/nginx/error.log`
- **PHP Logs**: `/var/log/php/error.log`
- **Dolibarr Logs**: Im Dolibarr-Admin-Bereich unter **Tools** → **Logs**

## Support

Bei Fragen oder Problemen:
- **Autor**: Tilo Thiele
- **E-Mail**: tilo.thiele@hamburg.de
- **Lizenz**: MIT

## Changelog

### Version 0.0.10
- Erste Veröffentlichung
- Unterstützung für camt.052 v8 Format
- UTF-8 und ISO-8859-1 Kodierung
- Duplikat-Erkennung
- Verbesserte Fehlerbehandlung
- Mehrsprachige Unterstützung (Deutsch/Englisch/Französisch)
