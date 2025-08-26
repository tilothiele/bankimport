# BankImport Modul - Setup-Symbol aktivieren

## Problem: Kein Zahnrad-Symbol auf der Modulseite

Wenn auf der Modulseite kein Zahnrad-Symbol (⚙️) für die Konfiguration angezeigt wird, müssen Sie das Modul neu aktivieren.

## Lösung: Modul neu aktivieren

### Schritt 1: Modul deaktivieren

1. Gehen Sie zu **Setup** → **Module/Applications**
2. Suchen Sie nach **"BankImport"** in der Liste
3. Klicken Sie auf **"Deaktivieren"** (falls das Modul aktiviert ist)

### Schritt 2: Modul aktivieren

1. Klicken Sie auf **"Aktivieren"** neben dem BankImport-Modul
2. Dolibarr wird das Modul neu laden und die Konfigurationsseite registrieren

### Schritt 3: Zahnrad-Symbol prüfen

Nach der Aktivierung sollte das **Zahnrad-Symbol (⚙️)** neben dem Modulnamen erscheinen.

## Was das Zahnrad-Symbol bietet

Das Zahnrad-Symbol führt zur Konfigurationsseite des Moduls, wo Sie finden:

### Modul-Informationen
- **Version**: 0.0.10
- **Autor**: Tilo Thiele
- **Lizenz**: MIT

### Konfiguration
- **Maximale Dateigröße**: 10 MB
- **Unterstützte Kodierungen**: UTF-8, ISO-8859-1
- **CSV-Trennzeichen**: Semikolon (;)

### Features
- Import von CSV-Dateien (camt.052 v8 Format)
- Unterstützung für verschiedene Kodierungen
- Automatische Duplikat-Erkennung
- Validierung der CSV-Daten
- Mehrsprachige Unterstützung
- Sichere Implementierung

### Systemanforderungen
- PHP 7.4 oder höher
- Dolibarr 16.0.0 oder höher
- Aktiviertes Bank-Modul

## Alternative: Direkter Zugriff

Falls das Zahnrad-Symbol immer noch nicht erscheint, können Sie auch direkt zur Konfigurationsseite navigieren:

```
https://ihre-dolibarr-domain.com/custom/bankimport/admin/setup.php
```

## Häufige Probleme

### Problem: Zahnrad-Symbol erscheint nicht nach Aktivierung
**Lösung**:
- Browser-Cache leeren
- Dolibarr-Cache leeren (Setup → Tools → Clear cache)
- Seite neu laden

### Problem: "Zugriff verweigert" auf Konfigurationsseite
**Lösung**:
- Stellen Sie sicher, dass Sie als Administrator angemeldet sind
- Überprüfen Sie die Dateiberechtigungen

### Problem: Modul kann nicht aktiviert werden
**Lösung**:
- Überprüfen Sie die PHP-Version (mindestens 7.4)
- Überprüfen Sie die Dolibarr-Version (mindestens 16.0.0)
- Prüfen Sie die Dolibarr-Logs auf Fehler

## Technische Details

Das Zahnrad-Symbol wird durch die Konfiguration in der Modulklasse aktiviert:

```php
$this->config_page_url = array('setup.php@bankimport');
```

Diese Zeile teilt Dolibarr mit, dass das Modul eine Konfigurationsseite hat, die über `admin/setup.php` erreichbar ist.

## Support

Bei weiterhin bestehenden Problemen:
- Überprüfen Sie die Dolibarr-Logs
- Stellen Sie sicher, dass alle Dateien korrekt kopiert wurden
- Kontaktieren Sie den Systemadministrator
