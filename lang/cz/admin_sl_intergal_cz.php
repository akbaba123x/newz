<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "intergal" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_IGA_ROZ_SHOW_GAL','Interní galerie obrázků');
define('RS_IGA_ROZ_ADD_GAL','Přidání nové galerie');
define('RS_IGA_ROZ_EDIT_GAL','Úprava nastavení galerie');
define('RS_IGA_ROZ_DEL_GAL','Vymazání galerie');
define('RS_IGA_ROZ_OPEN_GAL','Prohlížení galerie');
define('RS_IGA_ROZ_ADD_OBR','Přidání nového obrázku');
define('RS_IGA_ROZ_DEL_OBR','Vymazání obrázku');
define('RS_IGA_ROZ_EDIT_OBR','Úprava obrázku');
define('RS_IGA_ROZ_','Přidání nového obrázku');
// hlavni fce - sprava galerii
define('RS_IGA_SG_ZPET','Zpět na hlavní stránku sekce');
define('RS_IGA_SG_PRIDAT_GAL','Přidat novou galerii');
define('RS_IGA_SG_NAZEV_GAL','Název galerie');
define('RS_IGA_SG_VLASTNIK','Vlastník');
define('RS_IGA_SG_AKCE','Akce');
define('RS_IGA_SG_SMAZ','Smaž<br />galerii');
define('RS_IGA_SG_ZADNA_GAL','Nebyla nalezena žádná galerie!');
define('RS_IGA_SG_UPRAVIT','Edituj');
define('RS_IGA_SG_OTEVRIT','Otevři galerii');
define('RS_IGA_SG_SMAZ_OZNAC','Vymaž všechny označené galerie');
define('RS_IGA_SG_UPOZORNENI','<b>Upozornění:</b> Galerii může vymazat pouze majitel nebo administrátor systému, a to za předpokladu, že je prázdná!');
define('RS_IGA_SG_FORM_NAZEV_GAL','Název galerie');
define('RS_IGA_SG_FORM_POPIS','Popis');
define('RS_IGA_SG_FORM_PRAVA','Přístupová práva jiných uživatelů:');
define('RS_IGA_SG_FORM_PRAVA_PROHLIZENI','Prohlížení galerie');
define('RS_IGA_SG_FORM_PRAVA_ZAPIS','Zápis do galerie');
define('RS_IGA_SG_FORM_PRAVA_MAZANI','Mazání v galerii');
define('RS_IGA_SG_OK_ADD_GAL','Akce proběhla úspěšně! Galerie byla přidána.');
define('RS_IGA_SG_OK_EDIT_GAL','Akce proběhla úspěšně! Galerie byla aktualizována.');
define('RS_IGA_SG_OK_DEL_GAL_C1','Galerie');
define('RS_IGA_SG_OK_DEL_GAL_C2','byla vymazána!');
define('RS_IGA_SG_ERR_DEL_POCET_NULA','Neoznačili jste ani jedenu galerii!');
define('RS_IGA_SG_ERR_NENI_PRAZDNA_C1','Galerie');
define('RS_IGA_SG_ERR_NENI_PRAZDNA_C2','nelze vymazat, protože není prázdná!');
// hlavni fce - prace s obrazky
define('RS_IGA_PO_ZPET_PRED','Zpět na předchozí stránku');
define('RS_IGA_PO_ZPET_OBR','Zpět na přehled obrázků v galerii');
define('RS_IGA_PO_PRIDAT_OBR','Přidat nový obrázek');
define('RS_IGA_PO_ZADNY_OBR','Nebyl nalezen žádný obrázek!');
define('RS_IGA_PO_ID','ID:');
define('RS_IGA_PO_NENI_NAHLED','náhled neexistuje');
define('RS_IGA_PO_ORIGINAL','orig.');
define('RS_IGA_PO_SIRKA_VYSKA','ŠxV:');
define('RS_IGA_PO_VELIKOST','Velikost:');
define('RS_IGA_PO_SMAZ','Smazat');
define('RS_IGA_PO_UPRAVIT','Upravit');
define('RS_IGA_PO_SMAZ_OZNAC','Vymaž všechny označené obrázky');
define('RS_IGA_PO_FORM_NAZEV_OBR','Název obrázku');
define('RS_IGA_PO_FORM_POPIS','Popis');
define('RS_IGA_PO_FORM_OBRAZEK','Obrázek');
define('RS_IGA_PO_TL_UPLOAD','Upload nového obrázku');
define('RS_IGA_PO_ADR_ORIG_OBR','Adresa originál:');
define('RS_IGA_PO_ADR_NAHLED_OBR','Adresa náhled:');
define('RS_IGA_PO_OK_UPLOAD_OBR','Upload obrázku byl úspěšně dokončen.');
define('RS_IGA_PO_OK_ADD_OBR','Akce proběhla úspěšně! Obrázek byl uložen.');
define('RS_IGA_PO_OK_EDIT_OBR','Akce proběhla úspěšně! Obrázek byl aktualizován.');
define('RS_IGA_PO_OK_DEL_OBR_C1','Obrázek číslo');
define('RS_IGA_PO_OK_DEL_OBR_C2','byl vymazán!');
define('RS_IGA_PO_ERR_NELZE_UPLOADOVAT','Soubor nelze uploadovat!');
define('RS_IGA_PO_ERR_NULOVA_DELKA','Soubor má nulovou velikost!');
define('RS_IGA_PO_ERR_SECURE_ERR_NEJSOU_DATA','Secure error: Systém nemůže najít uploadovaný soubor nebo nemůže identifikovat formátu souboru!');
define('RS_IGA_PO_ERR_SECURE_ERR_FORMAT','Secure error: Systém nepodporuje upload tohoto formátu souboru!');
define('RS_IGA_PO_ERR_CHYBA_PRI_ULOZENI','V průběhu ukládání souboru na server došlo k neočekávané chybě. Prosím opakujte akci.');
define('RS_IGA_PO_ERR_SECURE_ERR_BEZPECNOST','Secure error: Akce upload souboru neprošela bezpečnostní kontrolou!');
define('RS_IGA_PO_ERR_NELZE_GENER_NAHLED','phpRS systém je schopen automaticky generovat náhledy pouze pro JPEG, PNG a GIF obrázky bez animace a průhlednosti!');
define('RS_IGA_PO_ERR_CHYBA_DEL_ORIG_OBR','V průběhu mazání orig. obrázku došlo k neočekávané chybě!');
define('RS_IGA_PO_ERR_CHYBA_DEL_NAHLED_OBR','V průběhu mazání náhledu došlo k neočekávané chybě!');
define('RS_IGA_PO_ERR_NEMOHU_NAJIT','Systém nemůže identifikovat požadovaný obrázek!');
define('RS_IGA_PO_INFO_PHPRS_ZNACKY','<b>phpRS systém umožňuje vložit do jakéhokoliv článku jakýkoliv výše uvedený(é) obrázek(y) prostřednictvím tzv. "phpRS značky".<br />
Obrázková "phpRS značka" má následující syntaxi:</b><br /><br />
<tt>&lt;obrazek id="CISLO" zarovnani="ZPUSOB_ZAROVNANI" nahled="ZOBRAZIT"&gt;</tt><br /><br />
<tt>CISLO</tt> ... na místo této proměnné je nutné vložit příslušné ID požadovaného obrázku<br />
<tt>ZPUSOB_ZAROVNANI</tt> ... možné varianty jsou: "nastred", "vlevo" nebo "vpravo"<br />
<tt>ZOBRAZIT</tt> ... možné varianty jsou: "ano", "ne" ... případně lze atribut "nahled" úplně vynechat = nezobrazit');
define('RS_IGA_PO_INFO_GENER_NAHLEDU','<b>Upozornění:</b> phpRS systém je schopen automaticky generovat náhledy jen pro JPEG, PNG obrázky a pro GIF obrázky bez animace a průhlednosti.');
define('RS_IGA_PRESUN_DO_JINE_GAL','Přesun obrázku do galerie'); // N
define('RS_IGA_PRESUN_ERR','Obrázek nebyl přesunut do cílové galerie.'); // N
define('RS_IGA_PRESUN_OK1','Obrázek byl úspěšně presunut do '); // N
define('RS_IGA_PRESUN_OK2','cílové galerie'); // N
define('RS_IGA_PRESUN_ERR_PRAVA','Chyba: Přesun obrázku není možný, protože nemáte oprávnění na zápis do cílové galerie.'); // N
// multiupload
define('RS_IGA_MULTIUPL_1','Můžete odeslat maximálně'); // N
define('RS_IGA_MULTIUPL_2','obrázků současně. jejich společná velikost nesmí přesáhnout:'); // N
define('RS_IGA_MULTIUPL_3','(Mega Bajtů)'); // N
define('RS_IGA_MULTIUPL_4','Doporučujem nahrávat fotografie s rozměrmi 800 až 1200 pixelů ve formátu JPG, pro obrázky s nižším počtem barev pak PNG nebo GIF.'); // N

?>