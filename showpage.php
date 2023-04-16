<?php

######################################################################
# phpRS ShowPage 1.2.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_alias

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("myweb.php");

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

// preklad aliasu
if (!empty($GLOBALS["name"])):
  // bezpecnostni korekce
  $GLOBALS["name"]=phprs_sql_escape_string($GLOBALS["name"]);
  // dotaz na alias
  $dotazpage=phprs_sql_query("select hodnota from ".$GLOBALS["rspredpona"]."alias where alias='".$GLOBALS["name"]."' and typ='sablona'",$GLOBALS["dbspojeni"]);
  if ($dotazpage!==false&&phprs_sql_num_rows($dotazpage)>0):
    // nacteni dat
    $pole_data=phprs_sql_fetch_assoc($dotazpage);
    // zobrazeni textoveho souboru
    $prchyba=ReadFile($pole_data["hodnota"]);
    if ($prchyba==0):
      // CHYBA: Pozadovana stranka nenalezena!
      echo "<div align=\"center\">".RS_SW_ERR2."</div>\n";
    endif;
  else:
    // CHYBA: System nemuze identifikovat pozadovanou stranku!
    echo "<div align=\"center\">".RS_SW_ERR1."</div>\n";
  endif;
else:
  // CHYBA: System nemuze identifikovat pozadovanou stranku!
  echo "<div align=\"center\">".RS_SW_ERR1."</div>\n";
endif;

// dokonceni tvorby stranky
KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>