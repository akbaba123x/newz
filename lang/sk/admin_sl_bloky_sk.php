<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "bloky" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_BLO_ROZ_SPRAVA_BLOK_SL','Správa stĺpcov a blokov');
define('RS_BLO_ROZ_ADD_SL','Pridanie stĺpca');
define('RS_BLO_ROZ_DEL_SL','Zmazanie stĺpca');
define('RS_BLO_ROZ_EDIT_SL','Úprava stĺpca');
define('RS_BLO_ROZ_EDIT_BLOK','Úprava bloku');
define('RS_BLO_ROZ_ADD_BLOK','Pridanie bloku');
define('RS_BLO_ROZ_DEL_BLOK','Zmazanie bloku');

define('RS_BLO_ANKETY','Systémový blok: Ankety'); // N
define('RS_BLO_NOVINKY','Systémový blok: Novinky'); // N
define('RS_BLO_RUBRIKY','Systémový blok: Zoznam rubrík'); // N
define('RS_BLO_KALENDAR','Systémový blok: Kalendár'); // N
define('RS_BLO_HLAVNI_BLOK','Systémový blok: Hlavný blok'); // N
define('RS_BLO_VYHL_VSE','Vyhľadávanie vo všetkých článkoch serveru'); // N
define('RS_BLO_VYHL_TEXT','Vyhľadať text'); // N
define('RS_BLO_HLEDAT',' Hľadať '); // N

// pomocne fce
define('RS_BLO_POM_ERR_ZADNY_LEVEL','Zatiaľ nebol definovaný žiadny level!'); 
// hlavni fce - sloupce
define('RS_BLO_SL_PRIDAT_BLOK','Pridať nový blok');
define('RS_BLO_SL_PRIDAT_SL','Pridať nový stĺpec');
define('RS_BLO_SL_ZPET','Späť na hlavnú stránku sekcie');
define('RS_BLO_SL_SLOUPEC','stĺpec');
define('RS_BLO_SL_ZOBRAZIT_SL','Zobraziť stĺpec:');
define('RS_BLO_SL_UPRAVIT_SL','Upraviť stĺpec');
define('RS_BLO_SL_VYMAZAT_SL','Vymazať stĺpec');
define('RS_BLO_SL_NADPIS_BLOK','Nadpis:');
define('RS_BLO_SL_JM_SYS_BLOK','Meno sys. bloku:');
define('RS_BLO_SL_ZOBRAZIT_BLOK','Zobraziť blok:');
define('RS_BLO_SL_PRIORITA_BLOK','Priorita&nbsp;bloku:');
define('RS_BLO_SL_UPRAVIT_BLOK','Upraviť blok');
define('RS_BLO_SL_VYMAZAT_BLOK','Vymazať blok');
define('RS_BLO_SL_FORM_ZOBRAZIT_SL','Zobraziť stĺpec');
define('RS_BLO_SL_OK_DEL_SL','Akcia prebehla úspešne! Stĺpec bol vymazaný');
define('RS_BLO_SL_OK_ADD_SL','Akcia prebehla úspešne! Bol pridaný nový stĺpec.');
define('RS_BLO_SL_OK_EDIT_SL','Akcia prebehla úspešne! Stĺpec bol aktualizovaný.');
define('RS_BLO_SL_ERR_NENI_PRAZDNY','Požadovanú akciu nemožno vykonať, pretože vybraný stĺpec nie je prázdy!');
// hlavni fce - blok
define('RS_BLO_BL_ZPET','Späť na hlavnú stránku sekcie');
define('RS_BLO_BL_NADPIS_BLOK','Nadpis bloku');
define('RS_BLO_BL_OBSAH','Obsah');
define('RS_BLO_BL_OBSAH_INFO','Sem vložte text aj s HTML syntaxou alebo si vyberte niektorú z preddefinovaných šablón. Súčasťou preddefinových šablón sú aj systémové bloky!');
define('RS_BLO_BL_SLOUPEC','Stĺpec');
define('RS_BLO_BL_SABLONA','Šablóna');
define('RS_BLO_BL_SAB_ZADNA','Žiadna');
define('RS_BLO_BL_SAB_VYHLED_BOX','Vyhľadávací box');
define('RS_BLO_BL_SAB_BEZ_UROVENE_RUBRIKY','Bezúrovňový abecedný zoznam rubrík');
define('RS_BLO_BL_TYP_BLOK','Typ bloku');
define('RS_BLO_BL_TYP_BLOK_INFO','(definuje vzhľad bloku)');
define('RS_BLO_BL_TYP_1','Typ 1');
define('RS_BLO_BL_TYP_2','Typ 2');
define('RS_BLO_BL_TYP_3','Typ 3');
define('RS_BLO_BL_TYP_4','Typ 4');
define('RS_BLO_BL_TYP_5','Typ 5');
define('RS_BLO_BL_PRIOR_BLOK','Priorita bloku');
define('RS_BLO_BL_PRIOR_BLOK_INFO','(1 = minimálna, 255 maximálna)');
define('RS_BLO_BL_ZOBRAZIT_BLOK','Zobraziť blok');
define('RS_BLO_BL_ZPUSOB_ZOBR','Spôsob zobrazenia');
define('RS_BLO_BL_ZZ_VSUDE','Všade');
define('RS_BLO_BL_ZZ_JEN_NA_HL','Len na hlavnej stránke');
define('RS_BLO_BL_ZZ_MIMO_HL','Všade mimo hlavnej stránky');
define('RS_BLO_BL_LEVEL','Level bloku'); 
define('RS_BLO_BL_UPOZORNENI','<b>Upozornenie:</b><br />Systémový blok: Hlavný blok sa v celom systému môže vyskytovaľ len raz. Založenie dvoch blokov tohoto typu nie je možné.');
define('RS_BLO_BL_OK_EDIT_BLOK','Akcia prebehla úspešne! Blok bol aktualizovaný.');
define('RS_BLO_BL_OK_ADD_BLOK','Akcia prebehla úspešne! Bol pridaný nový blok.');
define('RS_BLO_BL_OK_DEL_BLOK','Akcia prebehla úspešne! Blok bol vymazaný.');
define('RS_BLO_BL_ERR_VICE_HL_SYS_BLOKU','Systémový blok: Hlavný blok nemožno založiť, pretože už jeden blok tohoto typu existuje.');

?>