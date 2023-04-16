<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "reklama" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2012 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

define('RS_REK_SUBSYSTEM','Reklamní systém');
// submenu
define('RS_REK_SUB_HORNI_POZICE','Horní pozice');
define('RS_REK_SUB_DOLNI_POZICE','Dolní pozice');
define('RS_REK_SUB_KAMPAN','Reklamní kampaně');
define('RS_REK_SUB_REKLAMA','Reklamní texty a bannery'); // P
define('RS_REK_SUB_ZPET','Zpět do hlavního menu');
// rozcestnik
define('RS_REK_ROZ_HORNI_POZICE','Reklamní systém - Horní pozice');
define('RS_REK_ROZ_DOLNI_POZICE','Reklamní systém - Dolní pozice');
define('RS_REK_ROZ_SHOW_KAMPAN','Reklamní kampaně');
define('RS_REK_ROZ_ADD_KAMPAN','Přidání reklamní kampaně');
define('RS_REK_ROZ_DEL_KAMPAN','Vymazání reklamní kampaně');
define('RS_REK_ROZ_SHOW_REKL','Reklamní texty a bannery');
define('RS_REK_ROZ_ADD_REKL','Přidání reklamního textu nebo banneru');
define('RS_REK_ROZ_EDIT_REKL','Úprava reklamního textu nebo banneru');
define('RS_REK_ROZ_USE_REKL','Použití reklamního textu nebo banneru');
define('RS_REK_ROZ_DEL_REKL','Vymazání reklamního textu nebo banneru');
// pomocne fce
define('RS_REK_POM_ERR_BEZ_KAMPANE','Není definována žádná kampaň!'); // N
// hlavni fce - hodni/dolni pozice
define('RS_REK_HD_ZPUSOB_REKL','Aktuální způsob fungování reklamní pozice:'); // N
define('RS_REK_HD_ZPUSOB_KOD','reklamní kód'); // N
define('RS_REK_HD_ZPUSOB_KAMPAN','reklamní kampaň'); // N
define('RS_REK_HD_AKTIVUJ_KOD','Přepnout na reklamní kód'); // N
define('RS_REK_HD_AKTIVUJ_KAMPAN','Přepnout na reklamní kampaň'); // N
define('RS_REK_HD_UPRAVIT_HORNI','Upravit Horní pozici');
define('RS_REK_HD_UPRAVIT_DOLNI','Upravit Dolní pozici');
define('RS_REK_HD_OK_EDIT_REKL_KOD','Akce proběhla úspěšně! Reklamní kód byl aktualizován.');
// hlavni fce - kampan
define('RS_REK_KM_ZPET','Zpět na hlavní stránku sekce');
define('RS_REK_KM_PRIDAT','Přidat novou kampaň');
define('RS_REK_KM_KAMPAN','Kampaň');
define('RS_REK_KM_EMAIL','E-mail');
define('RS_REK_KM_POZNAMKA','Poznámka');
define('RS_REK_KM_AKCE','Akce');
define('RS_REK_KM_ZADNA_KAMPAN','Nebyla nalezena žádná kampaň!');
define('RS_REK_KM_SMAZ','Smaž');
define('RS_REK_KM_NADPIS_ADD_KAMPAN','Přidání nové reklamní kampaně');
define('RS_REK_KM_FORM_KAMPAN','Název kampaně');
define('RS_REK_KM_FORM_POZNAMKA','Poznámka');
define('RS_REK_KM_FORM_EMAIL','E-mail');
define('RS_REK_KM_OK_ADD_KAMPAN','Akce proběhla úspěšně! Byla přidána nová reklamní kampaň.');
define('RS_REK_KM_OK_DEL_KAMPAN','Akce proběhla úspěšně! Reklamní kampaň byla vymazána.');
define('RS_REK_KM_ERR_AKTIVNI_KAMPAN','Pozor chyba! Tato kampaň obsahuje aktivní reklamní texty nebo bannery!');
// hlavni fce - reklamni prvky
define('RS_REK_RP_ZPET','Zpět na hlavní stránku sekce');
define('RS_REK_RP_PRIDAT','Přidání nového reklamního textu nebo banneru');
define('RS_REK_RP_ID','ID'); // N
define('RS_REK_RP_ID_KAMPAN','ID kampaň:'); // N
define('RS_REK_RP_BANNER','Banner / text');
define('RS_REK_RP_DATUM','Datum');
define('RS_REK_RP_POC_ZOBR','Počet<br />zobrazení'); // N
define('RS_REK_RP_POC_KLIKU','Počet<br />kliků'); // N
define('RS_REK_RP_USPESNOST','Procentuální<br />úspěšnost'); // N
define('RS_REK_RP_AKCE','Akce');
define('RS_REK_RP_SMAZ','Smaž'); // N
define('RS_REK_RP_ZADNA_POLOZKA','Nebyla nalezena žádná položka!');
define('RS_REK_RP_POUZIJ','Použij');
define('RS_REK_RP_UPRAVIT','Edituj');
define('RS_REK_RP_SMAZ_OZNAC','Vymaž všechny označené položky'); // N
define('RS_REK_RP_FORM_DATUM','Datum vložení');
define('RS_REK_RP_FORM_CELK_POC_ZOBR','Celkový počet zobrazení'); // N
define('RS_REK_RP_FORM_CELK_POC_KLIKU','Celkový počet kliků'); // N
define('RS_REK_RP_FORM_CELK_USPESNOST','Celková procentuální úspěšnost'); // N
define('RS_REK_RP_FORM_DATUM_RESET','Datum posledního resetu počítadla'); // N
define('RS_REK_RP_FORM_AKT_POC_ZOBR','Aktuální počet zobrazení'); // N
define('RS_REK_RP_FORM_AKT_POC_KLIKU','Aktuální počet kliků'); // N
define('RS_REK_RP_FORM_AKT_USPESNOST','Aktuální procentuální úspěšnost'); // N
define('RS_REK_RP_FORM_RESET_POCITADLA','Reset počítadla'); // N
define('RS_REK_RP_FORM_KAMPAN','Kampaň');
define('RS_REK_RP_FORM_NAZEV_REKL','Název reklamy');
define('RS_REK_RP_FORM_FORMA_REKL','Forma reklamy');
define('RS_REK_RP_FORM_BANNER','Banner');
define('RS_REK_RP_FORM_TEXT','Text');
define('RS_REK_RP_FORM_REKL_KOD','Reklamní kód');
define('RS_REK_RP_FORM_URL_ADR','URL adresa banneru');
define('RS_REK_RP_FORM_CIL_ADR','Cílová URL adresa');
define('RS_REK_RP_FORM_PRIDAV_TEXT','Přídavný text');
define('RS_REK_RP_FORM_SIRKA','Šířka banneru');
define('RS_REK_RP_FORM_VYSKA','Výška banneru');
define('RS_REK_RP_FORM_HLA_TEXT','Hlavní text');
define('RS_REK_RP_FORM_BUBL_TEXT','Bublinkový text');
define('RS_REK_RP_TYP_REKL','Reklama');
define('RS_REK_RP_TYP_KAMPAN','Kampaň');
define('RS_REK_RP_REKL_KOD','Reklamní kód:');
define('RS_REK_RP_TL_APL_HORNI_POZICE','Aplikuj na horní reklamní pozici');
define('RS_REK_RP_TL_APL_DOLNI_POZICE','Aplikuj na dolní reklamní pozici');
define('RS_REK_RP_TL_GENERUJ_KOD','Vygeneruj reklamní kód');
define('RS_REK_RP_OK_ADD_REKL_C1','Akce proběhla úspěšně!');
define('RS_REK_RP_OK_ADD_REKL_C2A','Byl přidán nový reklamní banner.');
define('RS_REK_RP_OK_ADD_REKL_C2B','Byl přidán nový reklamní text.');
define('RS_REK_RO_OK_ADD_REKL_C2C','Byl přidán nový reklamní kód.');
define('RS_REK_RP_OK_EDIT_REKL_C1','Akce proběhla úspěšně!');
define('RS_REK_RP_OK_EDIT_REKL_C2A','Reklamní banner byl aktualizován.');
define('RS_REK_RP_OK_EDIT_REKL_C2B','Reklamní text byl aktualizován.');
define('RS_REK_RO_OK_EDIT_REKL_C2C','Reklamní kód byl aktualizován.');
define('RS_REK_RP_OK_USE_HORNI_POZICE','Akce proběhla úspěšně! Horní pozice byla aktualizována.');
define('RS_REK_RP_OK_USE_DOLNI_POZICE','Akce proběhla úspěšně! Dolní pozice byla aktualizována.');
define('RS_REK_RP_OK_DEL_REKL','Akce proběhla úspěšně! Všechny označené položky byly úspěšně vymazány.'); // P
define('RS_REK_RP_OK_DEL_REKL_NIC','Neoznačili jste ani jednu položku!'); // N
define('RS_REK_RP_INFO_PHPRS_ZNACKY','<b>phpRS systém umožňuje vložit do jakéhokoliv článku jakýkoliv výše uvedený(é) reklamní prvek(ky) prostřednictvím tzv. "phpRS značky".<br />
Reklamní "phpRS značka" má následující syntaxi:</b><br /><br />
<tt>&lt;reklama id="CISLO" typ="TYP_PRVKU"&gt;</tt><br /><br />
<tt>CISLO</tt> ... na místo této proměnné je nutné vložit příslušné ID požadovaného prvku<br />
<tt>TYP_PRVKU</tt> ... možné varianty jsou: "banner" nebo "kampan"'); // N

?>