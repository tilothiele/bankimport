# Import of Bank Statement File

**Author**: Tilo Thiele <tilo.thiele@hamburg.de>
**License**: MIT (see License.txt)
**Github**: git@github.com:tilothiele/bankimport.git

## Description

Das BankImport-Modul ermöglicht den Import von Bankauszügen im CSV-Format (camt.052 v8) in Dolibarr. Das Modul unterstützt verschiedene Kodierungen und verhindert Duplikate durch Import-Schlüssel.

### Features

- ✅ Import von CSV-Dateien (camt.052 v8 Format)
- ✅ Unterstützung für UTF-8 und ISO-8859-1 Kodierung
- ✅ Automatische Erkennung und Vermeidung von Duplikaten
- ✅ Validierung der CSV-Daten vor dem Import
- ✅ Mehrsprachige Unterstützung (Deutsch/Englisch)
- ✅ Verbesserte Fehlerbehandlung und Logging
- ✅ Sichere Datei-Upload-Validierung

### System Requirements

- **PHP**: 7.4 oder höher
- **Dolibarr**: 16.0.0 oder höher
- **Aktiviertes Bank-Modul** in Dolibarr

## Installation

Siehe [INSTALL.md](INSTALL.md) für detaillierte Installationsanweisungen.

### Quick Start

1. Kopieren Sie das Modul in `/path/to/dolibarr/htdocs/custom/bankimport/`
2. Aktivieren Sie das Modul in Dolibarr (Setup → Module/Applications)
3. Konfigurieren Sie die Berechtigungen
4. Gehen Sie zu **Bank** → **Kontoauszüge importieren**

## CSV Format

### Supported Format

* Die Import-Datei ist eine CSV-Datei. Die erste Zeile wird als Header übersprungen.
* Feldtrennzeichen ist ein Semikolon ';'.
* Zeilentrennzeichen ist Zeilenumbruch '\n'.
* String-Werte können in Anführungszeichen gesetzt werden.
* Unterstützte Kodierungen: ISO-8859-1, UTF-8

### Field Mapping

Nicht alle Felder werden importiert. Das statische Mapping zu Dolibarr-Feldern:

| CSV Field | Dolibarr Field | Description |
|-----------|----------------|-------------|
| 1 | dateo | Buchungstag |
| 2 | datev | Valutadatum |
| 4 | label | Verwendungszweck |
| 5 | creditor_id | Gläubiger-ID (in notes) |
| 6 | ref | Mandatsreferenz |
| 8 | collector_ref | Sammlerreferenz (in notes) |
| 11 | owner_other | Begünstigter/Zahlungspflichtiger |
| 12 | iban_other | Kontonummer/IBAN (Gegenpartei) |
| 13 | bank_other | BIC (Gegenpartei) |
| 14 | amount | Betrag |
| 15 | currency | Währung |

## 📑 Record Description – Haspa CSV (camt.052 v8 Export)

This document describes the structure of the CSV export file (Haspa, format camt.052 v8).

---

### Table of Fields

| Field name | Description | Example |
|------------|-------------|---------|
| **Account** (`Auftragskonto`) | IBAN of the account for which the statement is created. | `DE82200505501139432180` |
| **Booking Date** (`Buchungstag`) | Date on which the bank posts the transaction. | `04.08.25` |
| **Value Date** (`Valutadatum`) | Value date (date relevant for interest calculation). | `04.08.25` |
| **Booking Text** (`Buchungstext`) | Short text from the bank indicating the transaction type. | `GUTSCHRIFT UEBERWEISUNG` |
| **Payment Purpose** (`Verwendungszweck`) | Purpose of payment or accounting text provided by the originator. | `Tina Pilz` |
| **Creditor ID** (`Glaeubiger ID`) | SEPA Creditor Identifier (for SEPA direct debits). | *(empty in example)* |
| **Mandate Reference** (`Mandatsreferenz`) | Mandate reference of the SEPA direct debit. | *(empty in example)* |
| **Customer Reference (End-to-End)** | End-to-End reference from the originator. | *(empty in example)* |
| **Collector Reference** (`Sammlerreferenz`) | Batch reference of a SEPA direct debit collection. | *(empty in example)* |
| **Original Direct Debit Amount** (`Lastschrift Ursprungsbetrag`) | Original amount of the direct debit before chargeback. | *(empty in example)* |
| **Chargeback Fee** (`Auslagenersatz Ruecklastschrift`) | Bank fee related to chargebacks. | *(empty in example)* |
| **Counterparty Name** (`Beguenstigter/Zahlungspflichtiger`) | Name of the business partner (payer or payee). | `TINA PILZ` |
| **Counterparty IBAN** (`Kontonummer/IBAN`) | IBAN of the business partner. | `DE54200505123435467997` |
| **Counterparty BIC** (`BIC (SWIFT-Code)`) | BIC of the partner's bank. | `HASPDEHHXXX` |
| **Amount** (`Betrag`) | Transaction amount. Positive = credit (inflow), Negative = debit (outflow). | `5.00` |
| **Currency** (`Waehrung`) | Currency of the transaction. | `EUR` |
| **Info** | Status information, usually `"Umsatz gebucht"` ("transaction booked"). | `Umsatz gebucht` |

---

### Notes

- **Positive amounts** = incoming funds (credits).
- **Negative amounts** = outgoing payments, charges, fees.
- Some fields are **only filled for SEPA direct debits** (e.g. *Creditor ID*, *Mandate Reference*, *Collector Reference*).
- **Booking Text + Payment Purpose** often need to be combined to fully identify the transaction.
- **Info** field is usually static (`Umsatz gebucht` = booked transaction).

## Security Features

- ✅ CSRF-Schutz durch Dolibarr-Token-System
- ✅ Sichere Datei-Upload-Validierung
- ✅ SQL-Injection-Schutz durch `$db->escape()`
- ✅ Berechtigungsprüfung vor Zugriff
- ✅ Dateigrößen-Limit (10 MB)
- ✅ Dateityp-Validierung

## Support

Bei Fragen oder Problemen:
- **Autor**: Tilo Thiele
- **E-Mail**: tilo.thiele@hamburg.de
- **Lizenz**: MIT

## Changelog

Siehe [ChangeLog.md](ChangeLog.md) für detaillierte Änderungen.

### Version 0.0.10
- Erste Veröffentlichung
- Unterstützung für camt.052 v8 Format
- UTF-8 und ISO-8859-1 Kodierung
- Duplikat-Erkennung
- Verbesserte Fehlerbehandlung
- Mehrsprachige Unterstützung (Deutsch/Englisch)
- Sichere Implementierung mit Validierung
