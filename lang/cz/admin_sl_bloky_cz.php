<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "bloky" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_BLO_ROZ_SPRAVA_BLOK_SL','Správa sloupců a bloků');
define('RS_BLO_ROZ_ADD_SL','Přidání sloupce');
define('RS_BLO_ROZ_DEL_SL','Smazání sloupce');
define('RS_BLO_ROZ_EDIT_SL','Úprava sloupce');
define('RS_BLO_ROZ_EDIT_BLOK','Úprava bloku');
define('RS_BLO_ROZ_ADD_BLOK','Přidání bloku');
define('RS_BLO_ROZ_DEL_BLOK','Smazání bloku');

define('RS_BLO_ANKETY','Systémový blok: Ankety'); // N
define('RS_BLO_NOVINKY','Systémový blok: Novinky'); // N
define('RS_BLO_RUBRIKY','Systémový blok: Seznam rubrik'); // N
define('RS_BLO_KALENDAR','Systémový blok: Kalendář'); // N
define('RS_BLO_HLAVNI_BLOK','Systémový blok: Hlavní blok'); // N
define('RS_BLO_VYHL_VSE','Vyhledávání ve všech článcích webzinu!'); // N
define('RS_BLO_VYHL_TEXT','Vyhledat text'); // N
define('RS_BLO_HLEDAT',' Hledat ');  // N

// pomocne fce
define('RS_BLO_POM_ERR_ZADNY_LEVEL','Prozatím nebyl definován žádný level!');
// hlavni fce - sloupce
define('RS_BLO_SL_PRIDAT_BLOK','Přidat nový blok');
define('RS_BLO_SL_PRIDAT_SL','Přidat nový sloupec');
define('RS_BLO_SL_ZPET','Zpět na hlavní stránku sekce');
define('RS_BLO_SL_SLOUPEC','sloupec');
define('RS_BLO_SL_ZOBRAZIT_SL','Zobrazit sloupec:');
define('RS_BLO_SL_UPRAVIT_SL','Upravit sloupec');
define('RS_BLO_SL_VYMAZAT_SL','Vymazat sloupec');
define('RS_BLO_SL_NADPIS_BLOK','Nadpis:');
define('RS_BLO_SL_JM_SYS_BLOK','Jméno sys. bloku:');
define('RS_BLO_SL_ZOBRAZIT_BLOK','Zobrazit blok:');
define('RS_BLO_SL_PRIORITA_BLOK','Priorita&nbsp;bloku:');
define('RS_BLO_SL_UPRAVIT_BLOK','Upravit blok');
define('RS_BLO_SL_VYMAZAT_BLOK','Vymazat blok');
define('RS_BLO_SL_FORM_ZOBRAZIT_SL','Zobrazit sloupec');
define('RS_BLO_SL_OK_DEL_SL','Akce proběhla úspěšně! Sloupec byl vymazán');
define('RS_BLO_SL_OK_ADD_SL','Akce proběhla úspěšně! Byl přidán nový sloupec.');
define('RS_BLO_SL_OK_EDIT_SL','Akce proběhla úspěšně! Sloupec byl aktualizován.');
define('RS_BLO_SL_ERR_NENI_PRAZDNY','Požadovanou akci nelze provést, jelikož vybraný sloupec není prázdý!');
// hlavni fce - blok
define('RS_BLO_BL_ZPET','Zpět na hlavní stránku sekce');
define('RS_BLO_BL_NADPIS_BLOK','Nadpis bloku');
define('RS_BLO_BL_OBSAH','Obsah');
define('RS_BLO_BL_OBSAH_INFO','Sem vložte text i s HTML syntaxí nebo si vyberte některou z předdefinovaných šablon. Součástí předdefinových šablon jsou i systémové bloky!');
define('RS_BLO_BL_SLOUPEC','Sloupec');
define('RS_BLO_BL_SABLONA','Šablona');
define('RS_BLO_BL_SAB_ZADNA','Žádná');
define('RS_BLO_BL_SAB_VYHLED_BOX','Vyhledávácí box');
define('RS_BLO_BL_SAB_BEZ_UROVENE_RUBRIKY','Bez úrovňový abecední seznam rubrik');
define('RS_BLO_BL_TYP_BLOK','Typ bloku');
define('RS_BLO_BL_TYP_BLOK_INFO','(definuje vzhled bloku)');
define('RS_BLO_BL_TYP_1','Typ 1');
define('RS_BLO_BL_TYP_2','Typ 2');
define('RS_BLO_BL_TYP_3','Typ 3');
define('RS_BLO_BL_TYP_4','Typ 4');
define('RS_BLO_BL_TYP_5','Typ 5');
define('RS_BLO_BL_PRIOR_BLOK','Priorita bloku');
define('RS_BLO_BL_PRIOR_BLOK_INFO','(1 = minimální, 255 maximální)');
define('RS_BLO_BL_ZOBRAZIT_BLOK','Zobrazit blok');
define('RS_BLO_BL_ZPUSOB_ZOBR','Způsob zobrazení');
define('RS_BLO_BL_ZZ_VSUDE','Všude');
define('RS_BLO_BL_ZZ_JEN_NA_HL','Jen na hlavní stránce');
define('RS_BLO_BL_ZZ_MIMO_HL','Všude mimo hlavní stránku');
define('RS_BLO_BL_LEVEL','Level bloku'); 
define('RS_BLO_BL_UPOZORNENI','<b>Upozornění:</b><br />Systémový blok: Hlavní blok se v celém systému může vyskytovat pouze jednou. Založení dvou bloků tohoto typu není možné.');
define('RS_BLO_BL_OK_EDIT_BLOK','Akce proběhla úspěšně! Blok byl aktualizován.');
define('RS_BLO_BL_OK_ADD_BLOK','Akce proběhla úspěšně! Byl přidán nový blok.');
define('RS_BLO_BL_OK_DEL_BLOK','Akce proběhla úspěšně! Blok byl vymazán.');
define('RS_BLO_BL_ERR_VICE_HL_SYS_BLOKU','Systémový blok: Hlavní blok nelze založit, protože již jeden blok tohoto typu existuje.');

?>