<?php

######################################################################
# phpRS HomePage 1.6.6
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: *

define('IN_CODE',true); // inic. ochranne konstanty

if (!include_once("config.php")) {
	if (is_file("instalace_phprs.php")) {
		header("Location: instalace_phprs.php"); // neexistuje config - presmerovanie na instalacny script ak existuje
		die();
	}
	die("<!-- CONFIG LOAD ERROR -->");
}
include_once("myweb.php");

// zobrazeni hlavniho bloku
function HlavniBlok()
{
// pocet clanku zobrazenych na hlavni strane
$pocetclanku=NactiConfigProm('pocet_clanku',0);
// povoluje/zakazuje moznost strankovani hl. stranky
$strankovani=NactiConfigProm('povolit_str',0);
// povoluje/zakazuje hlidani platnosti doby zobrazovani clanku na hlavni strane
$hlidatplatnost=NactiConfigProm('hlidat_platnost',0);
// povoluje/zakazuje hlidani levelu
$hlidatlevel=NactiConfigProm('hlidat_level',0);
// povoluje/zakazuje pouziti zakakove clankove sablony
$zakazsab=NactiConfigProm('zobrazit_zakaz',0);

// zpracovani strankovani
$odclanku=0;
if ($strankovani==1):
  // vypocet startovni pozice
  if (isset($GLOBALS["strana"])):
    $odclanku=($GLOBALS["strana"]-1)*$pocetclanku;
  else:
    $GLOBALS["strana"]=1;
  endif;
endif;

// nacteni tridy clanky
include_once("trclanek.php");

$GLOBALS["clanek"] = new CClanek();
$GLOBALS["clanek"]->HlidatPlatnost($hlidatplatnost);
$GLOBALS["clanek"]->HlidatLevel($hlidatlevel);
$GLOBALS["clanek"]->NastavZakazovouSab($zakazsab);
$GLOBALS["clanek"]->NastavLevelCtenare($GLOBALS["prmyctenar"]->UkazLevel());
$GLOBALS["clanek"]->NastavHlStr(1);
$GLOBALS["clanek"]->NactiClanky($pocetclanku,$odclanku);

for ($pom=0;$pom<$GLOBALS["clanek"]->Ukaz("pocetclanku");$pom++):
  // volani sablony
  if ($GLOBALS["clanek"]->Ukaz("sablona")==''):
    // chybova hlaska: Chyba pri zobrazovani clanku cislo xxxx! System nemuze nalezt odpovidajici sablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["clanek"]->Ukaz("link")."! ".RS_IN_ERR1_2."<p>\n";
  else:
    // urceni pozadovane varianty sablony
    if ($GLOBALS["clanek"]->Ukaz("zakazova_sab")==1): // test na aplikaci zakazove varianty
      $rs_typ_clanku='zakazany';
    else:
      if ($GLOBALS["clanek"]->Ukaz("typ_clanku")==2): // 1 - standardni, 2 - kratky
        $rs_typ_clanku='kratky';
      else:
        $rs_typ_clanku='nahled';
      endif;
    endif;
    // nacteni sablony; pozor, musi byt povoleno vice-nasobne vlozeni sablony
    include($GLOBALS["clanek"]->Ukaz("sablona"));
  endif;
  $GLOBALS["clanek"]->DalsiRadek(); // prechod na dalsi radek
endfor;

// navigacniho menu
if ($strankovani==1):
  // vypocet mnozstvi rotaci
  $celkem_cla=$GLOBALS["clanek"]->CelkemClanku();
  if ($pocetclanku>0):
    $pocet_str=ceil($celkem_cla/$pocetclanku);
  else:
    $pocet_str=ceil($celkem_cla/10); // defaultni mnozstvi clanku na str. 10
  endif;
  // sestaveni listy
  if ($pocet_str>1):
    echo "<div align=\"right\" class=\"strankovani\">\n";
    // index
    echo "<a href=\"?strana=1\">".RS_IN_IDX."</a>";
    // akt. rozmezi
    echo " | ".($odclanku+1)."-".($odclanku+$pocetclanku);
    // predchozi
    if ($GLOBALS["strana"]>1):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]-1)."\">".RS_IN_PRED."</a>";
    endif;
    // nasledujici
    if ($GLOBALS["strana"]<$pocet_str):
      echo " | <a href=\"?strana=".($GLOBALS["strana"]+1)."\">".RS_IN_NASL."</a>";
    endif;
    // celkovy pocet
    echo " | ".RS_IN_CELKEM_1." ".$celkem_cla." ".RS_IN_CELKEM_2;
    echo "</div>\n";
    echo "<br />\n";
  endif;
endif;

}

if (!isset($GLOBALS["akce"])):

  $vzhledwebu->hlavnistranka=1; // aktivuje nastaveni hlavni stranka

  // standardni zobrazeni
  $vzhledwebu->Generuj();
  HlavniBlok();
  $vzhledwebu->Generuj();

else:

  include_once("engine.php"); // nacteni vykonnych funkci

  $vzhledwebu->Generuj();
  ObrTabulka();  // Vlozeni layout prvku
    // volba akce
    switch($GLOBALS["akce"]):
      case "verze": Verze(); break;
      case "temata": ShowTopics(); break;
      case "linky": ShowLinks(); break;
      case "statistika": ShowStatistics(); break;
    endswitch;
  KonecObrTabulka();   // Vlozeni layout prvku
  $vzhledwebu->Generuj();

endif;
?>