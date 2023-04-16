<?php

######################################################################
# phpRS Download 1.6.1
######################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
Vstupni promenna "sekce" umoznuje omezit vypis na specifickou sekci.
Vstupni promenna "soubor" inicializuje presmerovani na odpovidajici soubor.
id_detail - dopopsat, chybelo
akce      - dopopsat, chybelo
*/

// vyuzivane tabulky: rs_download, rs_download_sekce, rs_levely

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");

if (
	(isset($GLOBALS["sekce"]) && !ctype_digit($GLOBALS["sekce"]))
	||
	(isset($GLOBALS["id_detail"]) && !ctype_digit($GLOBALS["id_detail"]))
) {
	// chybny vstup
	echo "Nepovoleny pristup! / Hacking attempt!";
	die;
}

// odchyceni pozadavku na download konkretniho souboru
if (isset($GLOBALS["soubor"])):
  $GLOBALS["soubor"]=phprs_sql_escape_string($GLOBALS["soubor"]); // bezpecnostni korekce
  // prime vlozeni potrebnych knihoven, fci a objektu - bez pouziti souboru "myweb.php"
  include_once("specfce.php");
  include_once("trmyreader.php");
  // inic.
  $dotaz_where='';
  // test na povinnost hlidani levelu
  if (NactiConfigProm('hlidat_level',0)==1):
    $dotaz_where.=" and l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."'"; // level ctenare musi byt vyssi nebo roven levelu souboru
  endif;
  // dotaz na soubor
  $dotazsoubor=phprs_sql_query("select d.furl,d.fjmeno from ".$GLOBALS["rspredpona"]."download as d, ".$GLOBALS["rspredpona"]."levely as l where d.idd='".$GLOBALS["soubor"]."' and d.zobraz=1 and d.level_souboru=l.idl".$dotaz_where,$GLOBALS["dbspojeni"]);
  // test na uspesnost dotazu a existenci souboru
  if ($dotazsoubor!==false):
    if (phprs_sql_num_rows($dotazsoubor)>0):
      @phprs_sql_query("update ".$GLOBALS["rspredpona"]."download set pocitadlo=(pocitadlo+1) where idd='".$GLOBALS["soubor"]."'",$GLOBALS["dbspojeni"]); // zapocitani stazeni
      $pole_soubor=phprs_sql_fetch_assoc($dotazsoubor);
      header("Content-Description: File Transfer");
      header("Content-Type: application/force-download");
      header("Content-Disposition: attachment; filename=\"".$pole_soubor["fjmeno"]."\"");
      header("Location: ".$pole_soubor["furl"]); // presmerovani stranky
      exit();
    endif;
  endif;
endif;

include_once("myweb.php");

// test na exist. sekce
if (!isset($GLOBALS["sekce"])): $GLOBALS["sekce"]=0; endif; // 0 = neexistujici sekce
$GLOBALS["sekce"]=(int)$GLOBALS["sekce"];
if (!isset($GLOBALS["id_detail"])): $GLOBALS["id_detail"]=0; endif; // 0 = neexistujici sekce
$GLOBALS["id_detail"]=(int)$GLOBALS["id_detail"];
// test na exist. akce
if (!isset($GLOBALS['akce'])): $GLOBALS['akce']="sekce"; endif;

// vypis sekci
function VypisSekci($akt_sekce = 0)
{
$pocet_sloupcu=3; // pocet sloupcu v nabidce sekci

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);

// vypis se provadi pouze, kdyz jsou k dispozici dve a vice polozek
if ($pocetsek>1):
   // vypis tabulky sekci
   echo "<table border=\"0\" align=\"center\">\n";
   for ($pom=0;$pom<$pocetsek;$pom++):
     $pole_data=phprs_sql_fetch_assoc($dotazsek); // nacteni dat
     if (($pom % $pocet_sloupcu) == 0):
       echo "<tr>\n";
     endif;
     echo "<td class=\"download-sekce\">";
     if ($pole_data["ids"]==$akt_sekce):
       echo "<span class=\"download-sekce-akt\">".$pole_data["nazev"]."</span>";
     else:
       echo "<a href=\"download.php?sekce=".$pole_data["ids"]."\">".$pole_data["nazev"]."</a>";
     endif;
     echo "</td>\n";
     if (($pom % $pocet_sloupcu) == ($pocet_sloupcu-1)):
       echo "</tr>\n";
     endif;
   endfor;
   // dokonceni tabulky sekci
   $chybi=($pom % $pocet_sloupcu);
   if ($chybi > 0):
     for ($pom=0; $pom < ($pocet_sloupcu - $chybi); $pom++):
       echo "<td>&nbsp;</td>\n";
     endfor;
     echo "</tr>\n";
   endif;
   echo "</table>\n";
endif;
}

// zobrazeni downloadu
function ShowDownload()
{
// inic.
$zobrazvypis=0; // zobrazit vypis - default ne
$dotaz_where='';

// test omezeni na sekci
if ($GLOBALS["sekce"]==0):
  // neexistuje upresneni sekce - nacteni hl. sekce
  $dotazhl=phprs_sql_query("select ids from ".$GLOBALS["rspredpona"]."download_sekce where hlavnisekce='1'",$GLOBALS["dbspojeni"]);
  if ($dotazhl!==false&&phprs_sql_num_rows($dotazhl)>0):
    list($GLOBALS["sekce"])=phprs_sql_fetch_row($dotazhl); // id hlavni sekce
    $dotaz_where.="d.idsekce='".$GLOBALS["sekce"]."' and ";
    $zobrazvypis=1; // vypis - ano
  endif;
else:
  // existuje prom. sekce
  $dotaz_where.="d.idsekce='".$GLOBALS["sekce"]."' and ";
  $zobrazvypis=1; // vypis - ano
endif;

// test na povinnost hlidani levelu
if (NactiConfigProm('hlidat_level',0)==1):
  $dotaz_where.="l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."' and "; // level ctenare musi byt vyssi nebo roven levelu souboru
endif;

// zobrazeni sekci
VypisSekci($GLOBALS["sekce"]);
echo "<p></p>\n";

// dotaz na soubory
if ($zobrazvypis==1): // vypis = ano
  $dotaz="select d.idd,d.nazev,d.komentar,d.fjmeno,d.fsize,d.zdroj_jm,d.zdroj_adr,d.datum,d.verze,d.kat,d.pocitadlo ";
  $dotaz.="from ".$GLOBALS["rspredpona"]."download as d, ".$GLOBALS["rspredpona"]."levely as l ";
  $dotaz.="where ".$dotaz_where."d.zobraz=1 and d.level_souboru=l.idl order by d.datum desc";

  $dotazvyp=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  $pocetvyp=phprs_sql_num_rows($dotazvyp);
else:
  $pocetvyp=0; // nula znemozni zobrazeni
endif;

// prehlad souboru
if ($pocetvyp>0): // je zobrazen jen, kdyz existuje jedna a vice pol.
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"download-z\"><td align=\"center\"><b>&nbsp;</b></td>
<td align=\"center\"><b>".RS_DW_JMENO_SB."</b></td>
<td align=\"center\"><b>".RS_DW_VEL_SB."</b></td>
<td align=\"center\"><b>".RS_DW_DATUM_SB."</b></td>
<td align=\"center\"><b>".RS_DW_VER_SB."</b></td>
<td align=\"center\"><b>".RS_DW_KAT."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetvyp;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazvyp);
    echo "<tr class=\"download-z\"><td align=\"center\">";
    echo "<a href=\"download.php?akce=detail&amp;id_detail=".$pole_data["idd"]."&amp;sekce=".$GLOBALS["sekce"]."\"><img src=\"image/info.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_KLIKNI."\" title=\"".RS_DW_KLIKNI."\" /></a>&nbsp;&nbsp;";
    echo "<a href=\"download.php?soubor=".$pole_data["idd"]."\"><img src=\"image/download.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_STAHNI."\" title=\"".RS_DW_STAHNI."\" /></a>";
    echo "</td>\n";
    echo "<td align=\"center\">".$pole_data["nazev"]."<br /><a href=\"download.php?soubor=".$pole_data["idd"]."\">".$pole_data["fjmeno"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["fsize"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["verze"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["kat"]."</td></tr>\n";
  endfor;
  echo "</table>\n";
endif;

echo "<p></p>\n";
}

function ShowDetail()
{
echo "<p align=\"center\" class=\"download-z\"><a href=\"download.php?akce=sekce&amp;sekce=".$GLOBALS["sekce"]."\">".RS_DW_ZPET."</a></p>\n";

// inic.
$dotaz_where='';

// test na povinnost hlidani levelu
if (NactiConfigProm('hlidat_level',0)==1):
  $dotaz_where.=" and l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."'"; // level ctenare musi byt vyssi nebo roven levelu souboru
endif;

$dotaz="select d.* from ".$GLOBALS["rspredpona"]."download as d, ".$GLOBALS["rspredpona"]."levely as l where d.idd='".$GLOBALS["id_detail"]."' and d.zobraz=1 and d.level_souboru=l.idl".$dotaz_where;

$dotazvyp=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetvyp=phprs_sql_num_rows($dotazvyp);

if ($pocetvyp==1):
  // nacteni dat
  $pole_data=phprs_sql_fetch_assoc($dotazvyp);
  // zpracovani dat
  $format_zdroj=0;
  if ($pole_data["zdroj_jm"]!=''&&$pole_data["zdroj_jm"]!='-'):
    $format_zdroj+=1;
  endif;
  if ($pole_data["zdroj_adr"]!=''&&$pole_data["zdroj_adr"]!='-'):
    $format_zdroj+=2;
  endif;
  // vypis dat
  echo "<div class=\"download-z\">\n";
  echo "<a href=\"download.php?soubor=".$pole_data["idd"]."\"><img src=\"image/download.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".RS_DW_STAHNI."\" title=\"".RS_DW_STAHNI."\" /></a> ";
  echo "<strong><a href=\"download.php?soubor=".$pole_data["idd"]."\">".$pole_data["nazev"]."</a></strong><br />\n";
  echo $pole_data["komentar"];
  echo "</div><br />\n";
  echo "<div class=\"download-z\">\n";
  echo RS_DW_VER_SB.": ".$pole_data["verze"]."<br />\n";
  echo RS_DW_DATUM_SB.": ".MyDatetimeToDate($pole_data["datum"])."<br />\n";
  echo RS_DW_KAT.": ".$pole_data["kat"]."<br />\n";
  switch ($format_zdroj):
    case 1: echo RS_DW_ZDROJ_SB.": ".$pole_data["zdroj_jm"]."<br />\n"; break;
    case 2: echo RS_DW_ZDROJ_SB.": <a href=\"".$pole_data["zdroj_adr"]."\">".$pole_data["zdroj_adr"]."</a><br />\n"; break;
    case 3: echo RS_DW_ZDROJ_SB.": <a href=\"".$pole_data["zdroj_adr"]."\">".$pole_data["zdroj_jm"]."</a><br />\n"; break;
  endswitch;
  echo RS_DW_VEL_SB.": ".$pole_data["fsize"]."<br />\n";
  echo RS_DW_POCET_STAZ.": ".$pole_data["pocitadlo"]."x<br />\n";
  echo "</div>\n";
endif;

echo "<p></p>\n";
}

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

echo "<p class=\"nadpis\">".RS_DW_NADPIS."</p>\n"; // nadpis

switch ($GLOBALS['akce']):
  case 'sekce': ShowDownload(); break; // vypsani download sekci
  case 'detail': ShowDetail(); break; // detail souboru
endswitch;

KonecObrTabulka();  // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>