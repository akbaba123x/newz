<?php

######################################################################
# phpRS Preview 1.4.8
######################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi ke kompletnimu zobrazeni preview clanku, ktery je identifikovan pomoci promenne $cisloclanku.
*/

// vyuzivane tabulky:

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("autor.php"); // autorizace pristupu (soubor preview.php je volan z administrace)
include_once("myweb.php");

// test na pritomnost promenne $cisloclanku
if (!isset($GLOBALS["cisloclanku"])):
  echo "<html><body><p align=\"center\" class=\"z\">".RS_VW_ERR1."<p></body></html>\n";
  exit();
else:
  $GLOBALS["cisloclanku"]=phprs_sql_escape_string($GLOBALS["cisloclanku"]);
endif;

include("trclanek.php");

$clanek = new CClanek();
$clanek->HlidatAktDatum(0); // vypnuti hlidani "data vydani clanku"
$clanek->HlidatVydani(0); // vypnuti hlidani "vydani clanku"
$vysledek_dotazu=$clanek->NactiClanek($GLOBALS["cisloclanku"]);

if ($vysledek_dotazu==1):
  if ($clanek->Ukaz("sablona")==""):
    // chybova hlaska: Chyba pri zobrazovani clanku cislo XXXX! System nemuze nalezt odpovidajici sablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["cisloclanku"]."! ".RS_IN_ERR1_2."<p>\n";
  else:
    // vlozeni systemovych promennych do layoutu
    $vzhledwebu->UlozPro("title",$clanek->Ukaz("titulek"));
    $vzhledwebu->UlozPro("keywords",$clanek->Ukaz("slovni_popis"));
    // tvorba stranky
    $vzhledwebu->Generuj();
    // urceni pozadovane varianty clankove sablony
    $rs_typ_clanku='cely';
    // nacteni sablony
    include_once($clanek->Ukaz("sablona"));
    $vzhledwebu->Generuj();
  endif;
else:
  // chybova hlaska: Chyba! Clanek cislo XXXX neexistuje!
  echo "<p align=\"center\" class=\"z\">".RS_VW_ERR2_1." ".$GLOBALS["cisloclanku"]." ".RS_VW_ERR2_2."<p>\n";
endif;
?>