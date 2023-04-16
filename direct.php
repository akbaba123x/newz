<?php

######################################################################
# phpRS Direct 1.1.5
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_ban

/*
Tento soubor je soucasti interniho reklamniho systemu a zarucuje presmerovani odkazu na cilovou adresu.
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once('config.php');

// test na existenci aliasu
if (!empty($GLOBALS["kam"])):
  // bezpecnostni korekce
  $GLOBALS["kam"]=(int)$GLOBALS["kam"];
  // dotaz na cil
  $dotazdirect=phprs_sql_query("select cil from ".$GLOBALS["rspredpona"]."klik_ban where idb=".$GLOBALS["kam"],$GLOBALS["dbspojeni"]);
  if ($dotazdirect!==false&&phprs_sql_num_rows($dotazdirect)==1): // test na existenci dat
    // nacteni vysledku
    $pole_data=phprs_sql_fetch_assoc($dotazdirect);
    // navyseni pocitadla
    phprs_sql_query("update ".$GLOBALS["rspredpona"]."klik_ban set pocitadlo=(pocitadlo+1) where idb=".$GLOBALS["kam"],$GLOBALS["dbspojeni"]);
    // presmerovani na cilovou stranku
    header('Location: '.$pole_data["cil"]);
    exit();
  else:
    // CHYBA: Pozor chyba! Volany odkaz neexistuje!
    $chyba = 1;
  endif;
else:
  // CHYBA: Pozor chyba! System nemuze identifikovat cilovou oblast!
  $chyba = 2;
endif;

// prime vlozeni potrebnych knihoven, fci a objektu - bez pouziti souboru "myweb.php"
// jen pri chybe
include_once("trmyreader.php");
include_once('sl.php');
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset='.$GLOBALS['rsconfig']['kodovani'].'">
  <title>'.$GLOBALS['wwwname'].'</title>
  <meta name="author" content="Jiří Lukáš">
  <meta name="generator" content="phpRS">
</head>
<body>
<div align="center">'.(($chyba == 1)?RS_DI_ERR1:RS_DI_ERR2).'</div>
</body>
</html>';

?>