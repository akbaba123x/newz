<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "files" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_FIL_ROZ_SHOW_FILES','Download súborov');
define('RS_FIL_ROZ_ADD_FILES','Pridanie súboru');
define('RS_FIL_ROZ_DEL_FILES','Vymazanie súboru');
define('RS_FIL_ROZ_EDIT_FILES','Editácia súboru');
define('RS_FIL_ROZ_SEKCE_FILES','Správa download sekcií');
define('RS_FIL_ROZ_POSTA_FILES','Poštové centrum');
// pomocne fce
define('RS_FIL_POM_ERR_ZADNA_SEKCE','Zatiaľ nebola nadefinovaná žiadna sekcia!'); // N
define('RS_FIL_POM_ERR_ZADNY_LEVEL','Zatiaľ nebol definovaný žiadny level!'); // N
define('RS_FIL_POM_VSECHNY_SEKCE','-- všetky sekcie --'); // N
// hlavni fce - sprava souboru
define('RS_FIL_SS_ZPET','Späť na hlavnú stránku sekcie');
define('RS_FIL_SS_PRIDAT_FILES','Pridať nový súbor');
define('RS_FIL_SS_SPRAVA_SEKCI','Správa download sekcií');
define('RS_FIL_SS_ZOBRAZ_SOUBORY','Zobraz súbory'); // N
define('RS_FLI_SS_OMEZIT_SEKCE','obmedziť výpis na sekciu'); // N
define('RS_FLI_SS_CELK_POCET','Celkom súborov:'); // N
define('RS_FIL_SS_NAZEV','Názov');
define('RS_FIL_SS_SOUBOR','Súbor');
define('RS_FIL_SS_VELIKOST','Veľkosť');
define('RS_FIL_SS_DATUM','Dátum');
define('RS_FIL_SS_VERZE','Verzia');
define('RS_FIL_SS_POCET_STAZ','Počet<br />stiahnutí');
define('RS_FIL_SS_SEKCE','Sekcia');
define('RS_FIL_SS_LEVEL_SB','Přístupový<br />level'); // N
define('RS_FIL_SS_AKCE','Akcia');
define('RS_FIL_SS_SMAZ','Zmaž<br />súbor');
define('RS_FIL_SS_ZADNY_FILES','Databáza neobsahuje žiadny odpovedajúci záznam!');
define('RS_FIL_SS_UPRAVIT','Edituj');
define('RS_FIL_SS_SMAZ_OZNAC','Vymaž všetky označené súbory');
define('RS_FIL_SS_FORM_NAZEV','Názov');
define('RS_FIL_SS_FORM_SEKCE','Sekcia');
define('RS_FIL_SS_FORM_OBSAH','Komentár k súboru');
define('RS_FIL_SS_FORM_OBSAH_INFO','Sem vložte text, ktorý bude pokiaľ možno nejviac vystihovať druh a vlastnosti ponúkaného súboru.');
define('RS_FIL_SS_FORM_VLOZENI_SB_INFO','Zadajte ručne URL adresu pre prístup k súboru<br />alebo použite dialóg pre priamy upload súboru.'); // N
define('RS_FIL_SS_FORM_URL_SB','URL adresa súboru');
define('RS_FIL_SS_FORM_UPLOAD_SB','upload súboru'); // N
define('RS_FIL_SS_FORM_JMENO_SB','Meno súboru');
define('RS_FIL_SS_FORM_VELIKOST','Veľkosť súboru');
define('RS_FIL_SS_FORM_JMENO_ZDROJ','Meno zdroja');
define('RS_FIL_SS_FORM_ADR_ZDROJ','Adresa zdroja');
define('RS_FIL_SS_FORM_DATUM','Dátum vloženia');
define('RS_FIL_SS_FORM_VERZE','Verzia');
define('RS_FIL_SS_FORM_SLOVNI_KAT','Slovná kategória');
define('RS_FIL_SS_FORM_POCET','Počítadlo');
define('RS_FIL_SS_FORM_LEVEL_SB','Level súboru'); // N
define('RS_FIL_SS_FORM_ZOBRAZIT','Zobraziť súbor');
define('RS_FIL_SS_ZADNY_ZDROJ_INFO','V prípade, že nechcete špecifikovať zdroj, tak do oboch políčok vložte znak pomlčky ("-").');
define('RS_FIL_SS_OK_ADD_FILES','Akcia prebehla úspešne! Súbor bol pridaný.');
define('RS_FIL_SS_OK_ADD_FILES_UPLOAD','Do systému bol úspešne uploadovaný nový súbor.'); // N
define('RS_FIL_SS_OK_DEL_FILES','Akcia prebehla úspešne! Súbor bol vymazaný.');
define('RS_FIL_SS_OK_DEL_FILES_VICE','Akcia prebehla úspešne! Súbory boli vymazané.');
define('RS_FIL_SS_OK_EDIT_FILES','Akcia prebehla úspešne! Informácie o súbore boli aktualizované.');
define('RS_FIL_SS_ODESLAT_MAIL','Odoslať informačný e-mail.');
define('RS_FIL_SS_DEL_POCET_NULA','Neoznačili ste ani jeden súbor!');
// hlavni fce - sprava download sekci
define('RS_FIL_DS_ZPET_SEKCE','Späť na prehľad sekcií');
define('RS_FIL_DS_NAZEV','Názov');
define('RS_FIL_DS_HL_SEKCE','Hlavná sekcia');
define('RS_FIL_DS_AKCE','Akcia');
define('RS_FIL_DS_ZADNA_SEKCE','Databáza neobsahuje žiadny odpovedajúci záznam!');
define('RS_FIL_DS_UPRAVIT','Edituj');
define('RS_FIL_DS_SMAZ','Zmaž');
define('RS_FIL_DS_NASTAV_HL_SEKCI','Nastav ako hlavnú sekciu');
define('RS_FIL_DS_NASTAV_INFO','<b>Upozornenie:</b> Vždy musí byť jedna zo sekcií označená ako hlavná!');
define('RS_FIL_DS_NADPIS_ADD','Pridanie novej download sekcie');
define('RS_FIL_DS_FORM_NAZEV','Názov sekcie');
define('RS_FIL_DS_FORM_HL_SEKCE','Nastaviť ako hlavnú sekciu');
define('RS_FIL_DS_OK_ADD_SEKCE','Akcia prebehla úspešne! Bola vytvorená nová sekcia.');
define('RS_FIL_DS_OK_NASTAV_SEKCE','Akcia prebehla úspešne! Nastavenie sekcií bolo aktualizované.');
define('RS_FIL_DS_OK_DEL_SEKCE','Akcia prebehla úspešne! Sekcia bola vymazaná.');
define('RS_FIL_DS_OK_EDIT_SEKCE','Akcia prebehla úspešne! Sekcia bola aktualizovaná.');
define('RS_FIL_DS_ERR_PLNA_SEKCE','Akciu nemožno vykonať, pretože vami označená sekcia nie je prázdna!');
// pomocna fce - postovni centrum
define('RS_FIL_PC_FROM_PREDMET','Predmet e-mailu');
define('RS_FIL_PC_FORM_OBSAH','Obsah e-mailu');
define('RS_FIL_PC_TL_ODELAT_MAIL','Odoslať e-mail');
define('RS_FIL_DS_ERR_NEEXISTUJE_SB','Neočakávaná chyba! Overte si prosím platnosť súboru.');
// casti informacniho mailu
define('RS_FIL_PC_PREDMET_MAIL','download zo dňa');
define('RS_FIL_PC_OBS_MAIL_1','Nový súbor');
define('RS_FIL_PC_OBS_MAIL_2','S pozdravom');
define('RS_FIL_PC_OBS_MAIL_3','redakcia');
?>
