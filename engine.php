<?php

######################################################################
# phpRS Engine 1.6.7
######################################################################

// Copyright (c) 2001-2007 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic, rs_links, rs_links_sekce, rs_user, rs_levely

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

function Verze()
{
include_once("version.php");
echo "<div align=\"center\"><span class=\"nadpis\">".$phprsversion."</span><br><br>".$GLOBALS["layoutversion"]."</div><br />\n";
}

function ShowTopics()
{
$dotaztopic=phprs_sql_query("select idt,nazev,obrazek from ".$GLOBALS["rspredpona"]."topic where zobrazit=1 order by nazev",$GLOBALS["dbspojeni"]);
$pocettopic=phprs_sql_num_rows($dotaztopic);
if ($pocettopic==0):
  echo "<p align=\"center\" class=\"z\">".RS_EN_TOPIC_ERR1."</p>\n";
else:
  $pocet_sl=3;
  $akt_sl=1;
  // start - table
  echo "<table cellspacing=\"5\" border=\"0\" align=\"center\">\n";
  while ($pole_data = phprs_sql_fetch_assoc($dotaztopic)):
    if ($akt_sl==1):
      echo "<tr>\n";
    endif;
    echo '<td align="center" height="120" width="120" class="z">';
    echo '<a href="search.php?rstext=all-phpRS-all&amp;rstema='.$pole_data["idt"].'"><img src="'.$pole_data["obrazek"].'" border="0" alt="'.$pole_data["nazev"].'" /></a><br /><br />';
    echo '<a href="search.php?rstext=all-phpRS-all&amp;rstema='.$pole_data["idt"].'">'.$pole_data["nazev"].'</a>';
    echo "</td>\n";
    if ($akt_sl==$pocet_sl):
      echo "</tr>\n";
      $akt_sl=1;
    else:
      $akt_sl++;
    endif;
  endwhile;
  // dokonceni tabulky
  if ($akt_sl!=1&&$akt_sl<=$pocet_sl):
    for ($pom=$akt_sl;$pom<=$pocet_sl;$pom++):
      echo "<td></td>\n";
    endfor;
    echo "</tr>\n";
  endif;
  // konec - table
  echo "</table>\n";
endif;
echo "<br>\n";
}

function AktSekceLinks($aktid,$aktnazev,$testovaneid)
{
// funkce testujici shodu aktualne vybrane sekce se zaslanou sekci pres promenne $akt...
if ($aktid==$testovaneid):
  return '<span class="weblinks-sekce-akt">'.$aktnazev.'</span>';
else:
  return '<a href="index.php?akce=linky&amp;sekce='.$aktid.'">'.$aktnazev.'</a>';
endif;
}

function ShowLinks()
{
// test na existenci promenne "sekce"
if (!isset($GLOBALS["sekce"])): $GLOBALS["sekce"]=0; endif; // 0 = neexistujici sekce
// bezpecnostni korekce
$GLOBALS["sekce"]=phprs_sql_escape_string($GLOBALS["sekce"]);

$zobrazvypis=0; // inic. zobr. - ne
$mnozstvilinku=50; // nastaveni mnozstvi zobrazenych linku

echo "<p class=\"nadpis\">".RS_EN_LINKS_NADPIS."</p>\n"; // nadpis

// urceni omezeni
if ($GLOBALS["sekce"]==0):
  // neexistuje upresneni sekce - nacteni hl. sekce
  $dotazhl=phprs_sql_query("select ids from ".$GLOBALS["rspredpona"]."links_sekce where hlavnisekce='1'",$GLOBALS["dbspojeni"]);
  if ($dotazhl!==false&&phprs_sql_num_rows($dotazhl)>0):
    list($GLOBALS["sekce"])=phprs_sql_fetch_row($dotazhl); // id hlavni sekce
    $promezeni="where idsekce='".$GLOBALS["sekce"]."' ";
    $zobrazvypis=1; // vypis - ano
  endif;
else:
  // existuje prom. sekce
  $promezeni="where idsekce='".$GLOBALS["sekce"]."' ";
  $zobrazvypis=1; // vypis - ano
endif;

// zobrazeni prehledu sekci
$prepinac=0;
$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."links_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);
if ($pocetsek>1):
  echo "<table border=\"0\" align=\"center\">\n";
  while($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    if ($prepinac==0):
      echo "<tr><td class=\"weblinks-sekce\">".AktSekceLinks($pole_data["ids"],$pole_data["nazev"],$GLOBALS["sekce"])."</td>\n";
      $prepinac=1;
    else:
      echo "<td class=\"weblinks-sekce\">".AktSekceLinks($pole_data["ids"],$pole_data["nazev"],$GLOBALS["sekce"])."</td></tr>\n";
      $prepinac=0;
    endif;
  endwhile;
  if (($pocetsek>0)&&($prepinac==1)): echo "<td>&nbsp;</td></tr>\n"; endif;
  echo "</table>\n";
  echo "<br>\n";
endif;

// vypis linku
if ($zobrazvypis==1):
  $dotazlink=phprs_sql_query("select titulek,adresa,komentar,zdroj_jm,zdroj_url,zobraz_zdroj from ".$GLOBALS["rspredpona"]."links ".$promezeni."order by datum desc limit 0,".$mnozstvilinku,$GLOBALS["dbspojeni"]);
  $pocetlink=phprs_sql_num_rows($dotazlink);
  if ($pocetlink==0):
    echo "<p align=\"center\" class=\"z\">".RS_EN_LINKS_ERR1."</p>\n";
  else:
    echo "<div class=\"weblinks-z\">\n";
    while ($pole_data = phprs_sql_fetch_assoc($dotazlink)):
      echo '<a href="'.$pole_data["adresa"].'" target="_blank">'.$pole_data["titulek"].'</a>';
      if ($pole_data["komentar"]!=''): // test na existenci komentare
        echo ' - '.$pole_data["komentar"];
      endif;
      if ($pole_data["zobraz_zdroj"]==1): // test na zobrazeni zdroje
        echo '&nbsp;&nbsp;<i>'.RS_EN_LINKS_ZDROJ.': <a href="'.$pole_data["zdroj_url"].'" target="_blank">'.$pole_data["zdroj_jm"].'</a></i>';
      endif;
      echo "<br /><br />\n";
    endwhile;
    echo "</div>\n";
  endif;
endif;
echo "<br>\n";
}

function ShowStatistics()
{
// inic.
$mnozstvipolozek=15; // nastaveni mnozstvi zobrazenych clanku
$akt_cas=Date("Y-m-d H:i:s");
$dotaz_where='';

echo "<p class=\"nadpis\">".RS_EN_STAT_NADPIS."</p>\n"; // nadpis

// test na povinnost hlidani levelu
if (NactiConfigProm('hlidat_level',0)==1):
  // testuje se s ciselnou hodnotou ulozenou v tabulce "rs_levely"
  $dotaz_where.=" and l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."'"; // level ctenare musi byt vyssi nebo roven levelu clanku
endif;

// vypis n-nejctenejsich clanku (nejsou zapocitavany kratka clanky => typ_clanku!=2)
$dotaz="select c.link,c.titulek,c.datum,c.autor,c.visit,u.jmeno,u.email ";
$dotaz.="from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."user as u, ".$GLOBALS["rspredpona"]."levely as l ";
$dotaz.="where c.visible=1 and c.datum<='".$akt_cas."' and c.visit>0 and c.typ_clanku!=2 and c.level_clanku=l.idl".$dotaz_where." and c.autor=u.idu ";
$dotaz.="order by c.visit desc limit 0,".$mnozstvipolozek;

$dotaznej=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetnej=phprs_sql_num_rows($dotaznej);

// test na existenci clanku
if ($pocetnej==0):
  echo "<p align=\"center\" class=\"z\">".RS_EN_STAT_ERR1."</p>\n";
else:
  echo "<div class=\"z\">\n";
  while ($pole_data = phprs_sql_fetch_assoc($dotaznej)):
    echo "<strong><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></strong> ";
    echo "(<i>".$pole_data["visit"]."x, ".MyDatetimeToDate($pole_data["datum"]).", ".$pole_data["jmeno"]."</i>)<br /><br />\n";
  endwhile;
  echo "</div>\n";
endif;
echo "<br>\n";
}

?>