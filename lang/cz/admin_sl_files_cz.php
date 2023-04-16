<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "files" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_FIL_ROZ_SHOW_FILES','Download souborů');
define('RS_FIL_ROZ_ADD_FILES','Přidání souboru');
define('RS_FIL_ROZ_DEL_FILES','Vymazání souboru');
define('RS_FIL_ROZ_EDIT_FILES','Editace souboru');
define('RS_FIL_ROZ_SEKCE_FILES','Správa download sekcí');
define('RS_FIL_ROZ_POSTA_FILES','Poštovní centrum');
// pomocne fce
define('RS_FIL_POM_ERR_ZADNA_SEKCE','Prozatím nebyla nadefinována žádná sekce!'); // N
define('RS_FIL_POM_ERR_ZADNY_LEVEL','Prozatím nebyl definován žádný level!'); // N
define('RS_FIL_POM_VSECHNY_SEKCE','-- všechny sekce --'); // N
// hlavni fce - sprava souboru
define('RS_FIL_SS_ZPET','Zpět na hlavní stránku sekce');
define('RS_FIL_SS_PRIDAT_FILES','Přidat nový soubor');
define('RS_FIL_SS_SPRAVA_SEKCI','Správa download sekcí');
define('RS_FIL_SS_ZOBRAZ_SOUBORY','Zobraz soubory'); // N
define('RS_FLI_SS_OMEZIT_SEKCE','omezit výpis na sekci'); // N
define('RS_FLI_SS_CELK_POCET','Celkem souborů:'); // N
define('RS_FIL_SS_NAZEV','Název');
define('RS_FIL_SS_SOUBOR','Soubor');
define('RS_FIL_SS_VELIKOST','Velikost');
define('RS_FIL_SS_DATUM','Datum');
define('RS_FIL_SS_VERZE','Verze');
define('RS_FIL_SS_POCET_STAZ','Počet<br />stažení');
define('RS_FIL_SS_SEKCE','Sekce');
define('RS_FIL_SS_LEVEL_SB','Přístupový<br />level'); // N
define('RS_FIL_SS_AKCE','Akce');
define('RS_FIL_SS_SMAZ','Smaž<br />soubor');
define('RS_FIL_SS_ZADNY_FILES','Databáze neobsahuje žádný odpovídající záznam!');
define('RS_FIL_SS_UPRAVIT','Edituj');
define('RS_FIL_SS_SMAZ_OZNAC','Vymaž všechny označené soubory');
define('RS_FIL_SS_FORM_NAZEV','Název');
define('RS_FIL_SS_FORM_SEKCE','Sekce');
define('RS_FIL_SS_FORM_OBSAH','Komentář k souboru');
define('RS_FIL_SS_FORM_OBSAH_INFO','Sem vložte text, který bude co možná nejvíce vystihovat druh a vlastnosti nabízeného souboru.');
define('RS_FIL_SS_FORM_VLOZENI_SB_INFO','Zadejte ručně URL adresu pro přístup k souboru<br />nebo použijte dialog pro přímý upload souboru.'); // N
define('RS_FIL_SS_FORM_URL_SB','URL adresa souboru');
define('RS_FIL_SS_FORM_UPLOAD_SB','upload souboru'); // N
define('RS_FIL_SS_FORM_JMENO_SB','Jméno souboru');
define('RS_FIL_SS_FORM_VELIKOST','Velikost souboru');
define('RS_FIL_SS_FORM_JMENO_ZDROJ','Jméno zdroje');
define('RS_FIL_SS_FORM_ADR_ZDROJ','Adresa zdroje');
define('RS_FIL_SS_FORM_DATUM','Datum vložení');
define('RS_FIL_SS_FORM_VERZE','Verze');
define('RS_FIL_SS_FORM_SLOVNI_KAT','Slovní kategorie');
define('RS_FIL_SS_FORM_POCET','Počítadlo');
define('RS_FIL_SS_FORM_LEVEL_SB','Level souboru'); // N
define('RS_FIL_SS_FORM_ZOBRAZIT','Zobrazit soubor');
define('RS_FIL_SS_ZADNY_ZDROJ_INFO','V případě, že nechcete specifikovat zdroj, tak do obou políček vložte znak pomlčku ("-").');
define('RS_FIL_SS_OK_ADD_FILES','Akce proběhla úspěšně! Soubor byl přidán.');
define('RS_FIL_SS_OK_ADD_FILES_UPLOAD','Do systému byl úspěšně uploadován nový soubor.'); // N
define('RS_FIL_SS_OK_DEL_FILES','Akce proběhla úspěšně! Soubor byl vymazán.');
define('RS_FIL_SS_OK_DEL_FILES_VICE','Akce proběhla úspěšně! Soubory byly vymazány.');
define('RS_FIL_SS_OK_EDIT_FILES','Akce proběhla úspěšně! Informace o souboru byly aktualizovány.');
define('RS_FIL_SS_ODESLAT_MAIL','Odeslat informační e-mail.');
define('RS_FIL_SS_DEL_POCET_NULA','Neoznačili jste ani jeden soubor!');
// hlavni fce - sprava download sekci
define('RS_FIL_DS_ZPET_SEKCE','Zpět na přehled sekcí');
define('RS_FIL_DS_NAZEV','Název');
define('RS_FIL_DS_HL_SEKCE','Hlavní sekce');
define('RS_FIL_DS_AKCE','Akce');
define('RS_FIL_DS_ZADNA_SEKCE','Databáze neobsahuje žádný odpovídající záznam!');
define('RS_FIL_DS_UPRAVIT','Edituj');
define('RS_FIL_DS_SMAZ','Smaž');
define('RS_FIL_DS_NASTAV_HL_SEKCI','Nastav jako hlavní sekci');
define('RS_FIL_DS_NASTAV_INFO','<b>Upozornění:</b> Vždy musí být jedna ze sekcí označena jako hlavní!');
define('RS_FIL_DS_NADPIS_ADD','Přidání nové download sekce');
define('RS_FIL_DS_FORM_NAZEV','Název sekce');
define('RS_FIL_DS_FORM_HL_SEKCE','Nastavit jako hlavní sekci');
define('RS_FIL_DS_OK_ADD_SEKCE','Akce proběhla úspěšně! Byla vytvořena nová sekce.');
define('RS_FIL_DS_OK_NASTAV_SEKCE','Akce proběhla úspěšně! Nastavení sekcí bylo aktualizováno.');
define('RS_FIL_DS_OK_DEL_SEKCE','Akce proběhla úspěšně! Sekce byla vymazána.');
define('RS_FIL_DS_OK_EDIT_SEKCE','Akce proběhla úspěšně! Sekce byla aktualizována.');
define('RS_FIL_DS_ERR_PLNA_SEKCE','Akci nelze provést, jelikož vámi označená sekce není prázdná!');
// pomocna fce - postovni centrum
define('RS_FIL_PC_FROM_PREDMET','Předmět e-mailu');
define('RS_FIL_PC_FORM_OBSAH','Obsah e-mailu');
define('RS_FIL_PC_TL_ODELAT_MAIL','Odeslat e-mail');
define('RS_FIL_DS_ERR_NEEXISTUJE_SB','Neočekávaná chyba! Ověřte si prosím platnost souboru.');
// casti informacniho mailu
define('RS_FIL_PC_PREDMET_MAIL','download ze dne');
define('RS_FIL_PC_OBS_MAIL_1','Nový soubor');
define('RS_FIL_PC_OBS_MAIL_2','S pozdravem');
define('RS_FIL_PC_OBS_MAIL_3','redakce');
?>
