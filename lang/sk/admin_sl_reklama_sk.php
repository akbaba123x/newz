<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "reklama" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2012 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

define('RS_REK_SUBSYSTEM','Reklamní systém');
// submenu
define('RS_REK_SUB_HORNI_POZICE','Horná pozícia');
define('RS_REK_SUB_DOLNI_POZICE','Dolná pozícia');
define('RS_REK_SUB_KAMPAN','Reklamné kampane');
define('RS_REK_SUB_REKLAMA','Reklamné texty a bannery'); // P
define('RS_REK_SUB_ZPET','Späť do hlavného menu');
// rozcestnik
define('RS_REK_ROZ_HORNI_POZICE','Reklamný systém - Horná pozícia');
define('RS_REK_ROZ_DOLNI_POZICE','Reklamný systém - Dolná pozícia');
define('RS_REK_ROZ_SHOW_KAMPAN','Reklamné kampane');
define('RS_REK_ROZ_ADD_KAMPAN','Pridanie reklamnej kampane');
define('RS_REK_ROZ_DEL_KAMPAN','Vymazanie reklamnej kampane');
define('RS_REK_ROZ_SHOW_REKL','Reklamné texty a bannery');
define('RS_REK_ROZ_ADD_REKL','Pridanie reklamného textu alebo banneru');
define('RS_REK_ROZ_EDIT_REKL','Úprava reklamného textu alebo banneru');
define('RS_REK_ROZ_USE_REKL','Použitie reklamného textu alebo banneru');
define('RS_REK_ROZ_DEL_REKL','Vymazanie reklamného textu alebo banneru');
// pomocne fce
define('RS_REK_POM_ERR_BEZ_KAMPANE','Nie je definovaná žiadna kampaň!'); // N
// hlavni fce - hodni/dolni pozice
define('RS_REK_HD_ZPUSOB_REKL','Aktuálny spôsob fungovania reklamnej pozície:'); // N
define('RS_REK_HD_ZPUSOB_KOD','reklamný kód'); // N
define('RS_REK_HD_ZPUSOB_KAMPAN','reklamná kampaň'); // N
define('RS_REK_HD_AKTIVUJ_KOD','Prepnúť na reklamný kód'); // N
define('RS_REK_HD_AKTIVUJ_KAMPAN','Prepnúť na reklamnú kampaň'); // N
define('RS_REK_HD_UPRAVIT_HORNI','Upraviť Hornú pozíciu');
define('RS_REK_HD_UPRAVIT_DOLNI','Upraviť Dolnú pozíciu');
define('RS_REK_HD_OK_EDIT_REKL_KOD','Akcia prebehla úspešne! Reklamný kód bol aktualizovaný.');
// hlavni fce - kampan
define('RS_REK_KM_ZPET','Späť na hlavnú stránku sekcie');
define('RS_REK_KM_PRIDAT','Pridať novú kampaň');
define('RS_REK_KM_KAMPAN','Kampaň');
define('RS_REK_KM_EMAIL','E-mail');
define('RS_REK_KM_POZNAMKA','Poznámka');
define('RS_REK_KM_AKCE','Akcia');
define('RS_REK_KM_ZADNA_KAMPAN','Nebola nájdená žiadna kampaň!');
define('RS_REK_KM_SMAZ','Zmaž');
define('RS_REK_KM_NADPIS_ADD_KAMPAN','Pridanie novej reklamnej kampane');
define('RS_REK_KM_FORM_KAMPAN','Názov kampane');
define('RS_REK_KM_FORM_POZNAMKA','Poznámka');
define('RS_REK_KM_FORM_EMAIL','E-mail');
define('RS_REK_KM_OK_ADD_KAMPAN','Akcia prebehla úspešne! Bola pridaná nová reklamná kampaň.');
define('RS_REK_KM_OK_DEL_KAMPAN','Akcia prebehla úspešne! Reklamná kampaň bola vymazaná.');
define('RS_REK_KM_ERR_AKTIVNI_KAMPAN','Pozor chyba! Táto kampaň obsahuje aktívne reklamné texty alebo bannery!');
// hlavni fce - reklamni prvky
define('RS_REK_RP_ZPET','Späť na hlavnú stránku sekcie');
define('RS_REK_RP_PRIDAT','Pridanie nového reklamného textu alebo banneru');
define('RS_REK_RP_ID','ID'); // N
define('RS_REK_RP_ID_KAMPAN','ID kampaň:'); // N
define('RS_REK_RP_BANNER','Banner / text');
define('RS_REK_RP_DATUM','Dátum');
define('RS_REK_RP_POC_ZOBR','Počet<br />zobrazení'); // N
define('RS_REK_RP_POC_KLIKU','Počet<br />kliknutí'); // N
define('RS_REK_RP_USPESNOST','Percentuálna<br />úspešnosť'); // N
define('RS_REK_RP_AKCE','Akcia');
define('RS_REK_RP_SMAZ','Zmaž'); // N
define('RS_REK_RP_ZADNA_POLOZKA','Nebola nájdená žiadna položka!');
define('RS_REK_RP_POUZIJ','Použi');
define('RS_REK_RP_UPRAVIT','Edituj');
define('RS_REK_RP_SMAZ_OZNAC','Vymaž všetky označené položky'); // N
define('RS_REK_RP_FORM_DATUM','Dátum vloženia');
define('RS_REK_RP_FORM_CELK_POC_ZOBR','Celkový počet zobrazení'); // N
define('RS_REK_RP_FORM_CELK_POC_KLIKU','Celkový počet kliknutí'); // N
define('RS_REK_RP_FORM_CELK_USPESNOST','Celková percentuálna úspešnosť'); // N
define('RS_REK_RP_FORM_DATUM_RESET','Dátum posledného resetu počítadla'); // N
define('RS_REK_RP_FORM_AKT_POC_ZOBR','Aktuálny počet zobrazení'); // N
define('RS_REK_RP_FORM_AKT_POC_KLIKU','Aktuálny počet kliknutí'); // N
define('RS_REK_RP_FORM_AKT_USPESNOST','Aktuálna percentuálna úspešnosť'); // N
define('RS_REK_RP_FORM_RESET_POCITADLA','Reset počítadla'); // N
define('RS_REK_RP_FORM_KAMPAN','Kampaň');
define('RS_REK_RP_FORM_NAZEV_REKL','Názov reklamy');
define('RS_REK_RP_FORM_FORMA_REKL','Forma reklamy');
define('RS_REK_RP_FORM_BANNER','Banner');
define('RS_REK_RP_FORM_TEXT','Text');
define('RS_REK_RP_FORM_REKL_KOD','Reklamný kód');
define('RS_REK_RP_FORM_URL_ADR','URL adresa banneru');
define('RS_REK_RP_FORM_CIL_ADR','Cieľová URL adresa');
define('RS_REK_RP_FORM_PRIDAV_TEXT','Prídavný text');
define('RS_REK_RP_FORM_SIRKA','Šírka banneru');
define('RS_REK_RP_FORM_VYSKA','Výška banneru');
define('RS_REK_RP_FORM_HLA_TEXT','Hlavný text');
define('RS_REK_RP_FORM_BUBL_TEXT','Bublinkový text');
define('RS_REK_RP_TYP_REKL','Reklama');
define('RS_REK_RP_TYP_KAMPAN','Kampaň');
define('RS_REK_RP_REKL_KOD','Reklamný kód:');
define('RS_REK_RP_TL_APL_HORNI_POZICE','Aplikuj na hornú reklamnú pozíciu');
define('RS_REK_RP_TL_APL_DOLNI_POZICE','Aplikuj na dolnú reklamnú pozíciu');
define('RS_REK_RP_TL_GENERUJ_KOD','Vygeneruj reklamný kód');
define('RS_REK_RP_OK_ADD_REKL_C1','Akcia prebehla úspešne!');
define('RS_REK_RP_OK_ADD_REKL_C2A','Bol pridaný nový reklamný banner.');
define('RS_REK_RP_OK_ADD_REKL_C2B','Bol pridaný nový reklamný text.');
define('RS_REK_RO_OK_ADD_REKL_C2C','Bol pridaný nový reklamný kód.');
define('RS_REK_RP_OK_EDIT_REKL_C1','Akcia prebehla úspešne!');
define('RS_REK_RP_OK_EDIT_REKL_C2A','Reklamný banner bol aktualizovaný.');
define('RS_REK_RP_OK_EDIT_REKL_C2B','Reklamný text bol aktualizovaný.');
define('RS_REK_RO_OK_EDIT_REKL_C2C','Reklamný kód bol aktualizovaný.');
define('RS_REK_RP_OK_USE_HORNI_POZICE','Akcia prebehla úspešne! Horná pozícia bola aktualizovaná.');
define('RS_REK_RP_OK_USE_DOLNI_POZICE','Akcia prebehla úspešne! Dolná pozícia bola aktualizovaná.');
define('RS_REK_RP_OK_DEL_REKL','Akcia prebehla úspešne! Všetky označené položky boli úspešne vymazané.'); // P
define('RS_REK_RP_OK_DEL_REKL_NIC','Neoznačili ste ani jednu položku!'); // N
define('RS_REK_RP_INFO_PHPRS_ZNACKY','<b>phpRS systém umožňuje vložiť do akéhokoľvek článku akýkoľvek vyššie uvedený(é) reklamný prvok(ky) prostredníctvom tzv. "phpRS značky".<br />
Reklamná "phpRS značka" má nasledujúcu syntax:</b><br /><br />
<tt>&lt;reklama id="CISLO" typ="TYP_PRVKU"&gt;</tt><br /><br />
<tt>CISLO</tt> ... na miesto tejto premennej je nutné vložiť príslušné ID požadovaného prvku<br />
<tt>TYP_PRVKU</tt> ... možné varianty sú: "banner" alebo "kampan"'); // N

?>