<?php

######################################################################
# phpRS RSS 1.1.3 (RSS version 2.00)
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic, rs_clanky

/*
Tento soubor zajistuje generovani RSS souboru pro moznost vzajemne vymeny clanku mezi informacnimi servery.
Generuje RSS verze 2.0.

Promenna $mnozstvi definuje pocet clanku v RSS souboru. Default hodnota = 5
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");

function OdstranNoveRadky($retezec = '')
{
$pole_co=array("\n","\r\n");
$pole_cim=array(' ',' ');
return str_replace($pole_co,$pole_cim,trim($retezec));
}

function SestavRSS($data = '')
{
// GMDate() - GMT
echo '<?xml version="1.0" encoding="'.$GLOBALS['rsconfig']['kodovani'].'"?' . ">
<rss version=\"2.0\">
\t<channel>
\t\t<title>".$GLOBALS["wwwname"]."</title>
\t\t<link>".$GLOBALS["baseadr"]."</link>
\t\t<description>".$GLOBALS["wwwdescription"]."</description>
\t\t<language>cs</language>
\t\t<lastBuildDate>".GMDate("D, d M Y H:i:s")." GMT</lastBuildDate>
\t\t<webMaster>".$GLOBALS["redakceadr"]." (webmaster)</webMaster>
\t\t<managingEditor>".$GLOBALS["redakceadr"]." (redakce)</managingEditor>
\t\t<copyright>".Date("Y")." ".$GLOBALS["wwwname"].". All rights reserved.</copyright>\r\n";
echo $data;
echo "\t</channel>
</rss>";
}

$dnesnidatum=Date("Y-m-d H:i:s"); // dnesni datum ve formatu DateTime
// test na pritomnost promenne mnozstvi
if (!isset($GLOBALS["mnozstvi"])): $GLOBALS["mnozstvi"]=5; else: $GLOBALS["mnozstvi"]=(int)$GLOBALS["mnozstvi"]; endif;

// generovani RSS souboru - obsahuje nejaktualnejsi clanky
$dotaz="select c.link,c.titulek,c.uvod,date_format(datum,'%a, %d %b %Y %H:%i:%S ') as datum,t.nazev from ".$GLOBALS["rspredpona"]."clanky as c,".$GLOBALS["rspredpona"]."topic as t ";
$dotaz.="where c.tema=t.idt and c.visible='1' and datum<'".$dnesnidatum."' order by c.datum desc limit 0,".(int)$GLOBALS["mnozstvi"];

$dotazclanky=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetclanky=phprs_sql_num_rows($dotazclanky);

$prdata='';
if ($pocetclanky>0): // existuji nejake clanky
  // z nazev a uvodu clanku jsou odstraneny vsechy HTML tagy
  while ($pole_data = phprs_sql_fetch_assoc($dotazclanky)):
    $prdata .="\t\t<item>\r\n";
    $prdata .="\t\t\t<title>".htmlspecialchars(strip_tags($pole_data["titulek"]))."</title>\r\n";  // nazev clanku
    $prdata .="\t\t\t<link>".$baseadr."view.php?cisloclanku=".$pole_data["link"]."</link>\r\n"; // link clanku
    $prdata .="\t\t\t<pubDate>".$pole_data["datum"]." GMT</pubDate>\r\n"; // datum vydani
    $prdata .="\t\t\t<description>".OdstranNoveRadky(htmlspecialchars(strip_tags($pole_data["uvod"])))."</description>\r\n"; // uvodni cast
    $prdata .="\t\t\t<category>".$pole_data["nazev"]."</category>\r\n"; // kategorie
    $prdata .="\t\t\t<guid isPermaLink=\"true\">".$baseadr."view.php?cisloclanku=".$pole_data["link"]."</guid>\r\n";
    $prdata .="\t\t</item>\r\n";
  endwhile;
endif;

header("Content-Type: text/xml; charset=".$GLOBALS['rsconfig']['kodovani']);
SestavRSS($prdata);
?>