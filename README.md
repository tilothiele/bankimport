# Import of Bank Statement File

* The import file is a csv File. The first line ist skipped as headers.
* Field separator is a semicolon ';'.
* Record separator is newline '\n'.
* String values may be quoted by "
* Supported encoding s: ISO-8859-1, UTF-8

Not all fields are getting imported. 
The static mapping to Dolibarr fields:

TBW

# 📑 Record Description – Haspa CSV (camt.052 v8 Export)

This document describes the structure of the CSV export file (Haspa, format camt.052 v8).

---

## Table of Fields

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
| **Counterparty BIC** (`BIC (SWIFT-Code)`) | BIC of the partner’s bank. | `HASPDEHHXXX` |
| **Amount** (`Betrag`) | Transaction amount. Positive = credit (inflow), Negative = debit (outflow). | `5.00` |
| **Currency** (`Waehrung`) | Currency of the transaction. | `EUR` |
| **Info** | Status information, usually `"Umsatz gebucht"` (“transaction booked”). | `Umsatz gebucht` |

---

## Notes

- **Positive amounts** = incoming funds (credits).  
- **Negative amounts** = outgoing payments, charges, fees.  
- Some fields are **only filled for SEPA direct debits** (e.g. *Creditor ID*, *Mandate Reference*, *Collector Reference*).  
- **Booking Text + Payment Purpose** often need to be combined to fully identify the transaction.  
- **Info** field is usually static (`Umsatz gebucht` = booked transaction).  

---
