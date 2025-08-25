# Import von Kontoauszügen

Beispiel Importdatei (iso-8859-1)

```
"Auftragskonto";"Buchungstag";"Valutadatum";"Buchungstext";"Verwendungszweck";"Glaeubiger ID";"Mandatsreferenz";"Kundenreferenz (End-to-End)";"Sammlerreferenz";"Lastschrift Ursprungsbetrag";"Auslagenersatz Ruecklastschrift";"Beguenstigter/Zahlungspflichtiger";"Kontonummer/IBAN";"BIC (SWIFT-Code)";"Betrag";"Waehrung";"Info"
"DE82200505501139214280";"04.08.25";"04.08.25";"GUTSCHRIFT UEBERWEISUNG";"Martina Pilz ";"";"";"";"";"";"";"JOSEF HEINZ PILZ MARTINA PILZ";"DE54200505501335467997";"HASPDEHHXXX";"5,00";"EUR";"Umsatz gebucht"
"DE82200505501139214280";"31.07.25";"01.08.25";"ABSCHLUSS";"Abrechnung 31.07.2025 siehe Anlage Abrechnung 31.07.2025 Information zur Abrechnung Kontostand am 31.07.2025 4.556,49 + Abrechnungszeitraum vom 01.07.2025 bis 31.07.2025 Abrechnung 31.07.2025 0,00+ Sollzinss�tze am 30.07.2025 13,5900 v.H. Soll-Zins 4,0000 v.H. �berz-Zins Es handelt sich hierbei um eine umsatzsteuerfreie Leistung. Kontostand/Rechnungsabschluss am 31.07.2025 4.556,49 + Rechnungsnummer: 20250731-HH001-00157275003 ";"";"";"";"";"";"";"";"1139214280";"20050550";"0,00";"EUR";"Umsatz gebucht"
"DE82200505501139214280";"31.07.25";"01.08.25";"ENTGELTABSCHLUSS";"Entgeltabrechnung siehe Anlage Abrechnung 31.07.2025 Information zur Abrechnung Entgelte vom 01.07.2025 bis 31.07.2025 34,85- Grundpreis (Kontof�hrung) 15,50- Zahlungsverkehr 19,35- Abrechnung 31.07.2025 34,85- Es handelt sich hierbei um eine umsatzsteuerfreie Leistung. Rechnungsnummer: 20250731-HH001-00157275009 ";"";"";"";"";"";"";"";"0000000000";"20050550";"-34,85";"EUR";"Umsatz gebucht"
date;datev;label;amount;oper;ref;categorie;transaction_id;bank_other;iban_other;owner_other
2025-08-01;2025-08-01;Miete August;-850.00;VIR;REF001;Miete;TXN001;Deutsche Bank;DE12345678901234567890;Max Mustermann
2025-08-02;2025-08-01;Gehalt August;2500.00;VIR;REF002;Gehalt;TXN002;Commerzbank;DE09876543210987654321;Musterfirma GmbH
```
