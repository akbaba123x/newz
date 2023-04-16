<?php

######################################################################
# phpRS Atom 1.0.1 (Atom version 1.0)
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic, rs_clanky

/*
Tento soubor zajistuje generovani XML Atom souboru pro moznost vzajemne vymeny clanku mezi informacnimi servery.
Generuje Atom verze 1.0.

Promenna $mnozstvi definuje pocet clanku v Atom souboru. Default hodnota = 5
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");

function OdstranNoveRadky($retezec = '')
{
$pole_co=array("\n","\r\n");
$pole_cim=array(' ',' ');
return str_replace($pole_co,$pole_cim,trim($retezec));
}

function VratCasRFC3339($cas = '')
{
// uzkazka vysledku: 2006-04-06T20:30:34+01:00

list($rozklad_datum,$rozklad_cas)=explode(' ',$cas);
return $rozklad_datum.'T'.$rozklad_cas.'+'.$GLOBALS['casovy_posun'];
}

function SestavAtom($data = '')
{
// Casy se zapisují podle RFC 3339
echo '<?xml version="1.0" encoding="'.$GLOBALS['rsconfig']['kodovani'].'"?' . ">
<feed xmlns=\"http://www.w3.org/2005/Atom\">
\t<title>".$GLOBALS["wwwname"]."</title>
\t<id>".$GLOBALS["baseadr"]."</id>
\t<updated>".VratCasRFC3339(Date("Y-m-d H:i:s"))."</updated>
\t<author>
\t\t<name>".$GLOBALS["redakceadr"]." (redakce)</name>
\t</author>
\t<link rel=\"alternate\" type=\"text/html\" href=\"".$GLOBALS["baseadr"]."\" />
\t<rights>".Date("Y")." ".$GLOBALS["wwwname"].". All rights reserved.</rights>\r\n";
echo $data;
echo "</feed>";
}

$dnesnidatum=Date("Y-m-d H:i:s"); // dnesni datum ve formatu DateTime
// test na pritomnost promenne mnozstvi
if (!isset($GLOBALS["mnozstvi"])): $GLOBALS["mnozstvi"]=5; else: $GLOBALS["mnozstvi"]=(int)$GLOBALS["mnozstvi"]; endif;
// nutno vypocitat posun casoveho pasma
$GLOBALS['casovy_posun']=date("H:i",date("Z"));

// generovani Atom souboru - obsahuje nejaktualnejsi clanky
$dotaz="select c.link,c.titulek,c.uvod,c.datum,t.nazev from ".$GLOBALS["rspredpona"]."clanky as c,".$GLOBALS["rspredpona"]."topic as t ";
$dotaz.="where c.tema=t.idt and c.visible='1' and datum<'".$dnesnidatum."' order by c.datum desc limit 0,".(int)$GLOBALS["mnozstvi"];

$dotazclanky=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetclanky=phprs_sql_num_rows($dotazclanky);

$prdata='';
if ($pocetclanky>0): // existuji nejake clanky
  // z nazev a uvodu clanku jsou odstraneny vsechy HTML tagy
  while ($pole_data = phprs_sql_fetch_assoc($dotazclanky)):
    $prdata .="\t<entry>\r\n";
    $prdata .="\t\t<title>".htmlspecialchars(strip_tags($pole_data["titulek"]))."</title>\r\n";  // nazev clanku
    $prdata .="\t\t<id>".$baseadr."view.php?cisloclanku=".$pole_data["link"]."</id>\r\n"; // link clanku
    $prdata .="\t\t<updated>".VratCasRFC3339($pole_data["datum"])."</updated>\r\n"; // datum vydani
    $prdata .="\t\t<summary>".OdstranNoveRadky(htmlspecialchars(strip_tags($pole_data["uvod"])))."</summary>\r\n"; // uvodni cast
    $prdata .="\t\t<category term=\"".$pole_data["nazev"]."\" />\r\n"; // kategorie
    $prdata .="\t\t<link rel=\"alternate\" type=\"text/html\" href=\"".$baseadr."view.php?cisloclanku=".$pole_data["link"]."\" />\r\n";
    $prdata .="\t</entry>\r\n";
  endwhile;
endif;

header("Content-Type: text/xml; charset=".$GLOBALS['rsconfig']['kodovani']);
SestavAtom($prdata);
?>