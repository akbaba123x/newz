<?php

######################################################################
# phpRS Administration Engine - Statistic section 1.4.10
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_clanky, rs_stat_arch

/*
  Tento soubor zajistuje obsluhu "statistiky".
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // statisktika
     case "ShowStatistic": AdminMenu();
          echo "<h2 align=\"center\">".RS_STA_ROZ_STAT."</h2>";
          ShowStatistic();
          break;
     case "StatClanky": AdminMenu();
          echo "<h2 align=\"center\">".RS_STA_ROZ_STAT."</h2>";
          StatClanky();
          break;
     case "StatNavstev": AdminMenu();
          echo "<h2 align=\"center\">".RS_STA_ROZ_STAT."</h2>";
          StatNavstev();
          break;
     case "StatNavMes": AdminMenu();
          echo "<h2 align=\"center\">".RS_STA_ROZ_STAT."</h2>";
          StatNavMes();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowStatistic()
  StatClanky()
  StatNavstev()
  StatNavMes()
*/

function ShowStatistic()
{
// volba statistiky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=StatClanky&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_CTENOST."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=StatNavstev&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_NAVSTEVY."</a></p>\n";
}

function StatClanky()
{
if (!isset($GLOBALS["prstr"])): $GLOBALS["prstr"]=1; endif; // strankovani
if (!isset($GLOBALS["prjak"])): $GLOBALS["prjak"]='vel1'; endif; // zpusob trizeni
if (!isset($GLOBALS["prmn"])): $GLOBALS["prmn"]=50; else: $GLOBALS["prmn"]=phprs_sql_escape_string($GLOBALS["prmn"]); endif; // pocet polozek

// volba statistiky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=StatClanky&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_CTENOST."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=StatNavstev&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_NAVSTEVY."</a></p>\n";

// navigacni menu
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<p align=\"center\" class=\"txt\">
".RS_STA_ST_NAV_TRIDIT.": <select name=\"prjak\" seze=\"1\">\n";
switch ($GLOBALS["prjak"]):
  case 'vel1': echo "<option value=\"vel1\" selected>".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\">".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\">".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\">".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\">".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\">".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
  case 'vel0': echo "<option value=\"vel1\">".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\" selected>".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\">".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\">".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\">".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\">".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
  case 'dat1': echo "<option value=\"vel1\">".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\">".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\" selected>".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\">".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\">".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\">".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
  case 'dat0': echo "<option value=\"vel1\">".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\">".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\">".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\" selected>".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\">".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\">".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
  case 'hod1': echo "<option value=\"vel1\">".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\">".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\">".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\">".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\" selected>".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\">".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
  case 'hod0': echo "<option value=\"vel1\">".RS_STA_ST_NAV_POCET_DOLU."</option><option value=\"vel0\">".RS_STA_ST_NAV_POCET_NAHORU."</option><option value=\"dat1\">".RS_STA_ST_NAV_DATUM_DOLU."</option><option value=\"dat0\">".RS_STA_ST_NAV_DATUM_NAHORU."</option><option value=\"hod1\">".RS_STA_ST_NAV_ZNAMKA_DOLU."</option><option value=\"hod0\" selected>".RS_STA_ST_NAV_ZNAMKA_NAHORU."</option>"; break;
endswitch;
echo "</select> - ".RS_STA_ST_NAV_ZOBRAZIT.": <select name=\"prmn\" seze=\"1\">\n";
switch ($GLOBALS["prmn"]):
  case 30: echo "<option value=\"30\" selected>30 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"50\">50 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"80\">80 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"100\">100 ".RS_STA_ST_NAV_CLANKU."</option>"; break;
  case 50: echo "<option value=\"30\">30 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"50\" selected>50 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"80\">80 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"100\">100 ".RS_STA_ST_NAV_CLANKU."</option>"; break;
  case 80: echo "<option value=\"30\">30 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"50\">50 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"80\" selected>80 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"100\">100 ".RS_STA_ST_NAV_CLANKU."</option>"; break;
  case 100: echo "<option value=\"30\">30 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"50\">50 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"80\">80 ".RS_STA_ST_NAV_CLANKU."</option><option value=\"100\" selected>100 ".RS_STA_ST_NAV_CLANKU."</option>"; break;
endswitch;
echo "</select>
<input type=\"submit\" value=\" ".RS_STA_ST_TL_ZOBRAZ." \" class=\"tl\">
<input type=\"hidden\" name=\"akce\" value=\"StatClanky\"><input type=\"hidden\" name=\"modul\" value=\"stat\">
<input type=\"hidden\" name=\"prstr\" value=\"1\">
</p>
</form>\n";

// zpracovani strankovani a limitu pro SQL dotaz
$dotazcelk=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky",$GLOBALS["dbspojeni"]);
if ($dotazcelk!==false&&phprs_sql_num_rows($dotazcelk)>0):
  list($prpocetcl)=phprs_sql_fetch_row($dotazcelk);
else:
  $prpocetcl=0;
endif;
$prradky=$GLOBALS["prmn"];
$prkolikrat=ceil($prpocetcl/$prradky);
$prlimit='limit '.($prradky*($GLOBALS["prstr"]-1)).','.$prradky;

// navigacni lista
$naviglista="<p align=\"center\">";
for ($pom=1;$pom<($prkolikrat+1);$pom++):
  $naviglista .="| <a href=\"".RS_VYKONNYSOUBOR."?akce=StatClanky&amp;modul=stat&amp;prjak=".$GLOBALS["prjak"]."&amp;prstr=".$pom."&amp;prmn=".$prradky."\">".($prradky*($pom-1))."-".($prradky*$pom)."</a>";
endfor;
$naviglista .=" |</p>\n";

// zobrazeni navigacni listy
echo $naviglista;
// sestaveni "order by"
switch ($GLOBALS["prjak"]):
  case "vel1": $prco="visit desc"; break;
  case "vel0": $prco="visit"; break;
  case "dat1": $prco="datum desc"; break;
  case "dat0": $prco="datum"; break;
  case "hod1": $prco="znamka desc"; break;
  case "hod0": $prco="znamka"; break;
  default: $prco="visit desc"; break;
endswitch;
// vypis
$dotazstat=phprs_sql_query("select link,titulek,datum,visit,(hodnoceni/mn_hodnoceni) as znamka from ".$GLOBALS["rspredpona"]."clanky order by ".$prco." ".$prlimit,$GLOBALS["dbspojeni"]);
$pocetstat=phprs_sql_num_rows($dotazstat);
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr bgcolor=\"#E6E6E6\" class=\"txt\"><td align=\"center\"><b>".RS_STA_ST_LINK."</b></td>
<td align=\"center\"><b>".RS_STA_ST_NAZEV."</b></td>
<td align=\"center\"><b>".RS_STA_ST_DATUM."</b></td>
<td align=\"center\"><b>".RS_STA_ST_PRECTENO."</b></td>
<td align=\"center\"><b>".RS_STA_ST_ZNAMKA."</b></td></tr>\n";
if ($pocetstat==0):
  // CHYBA: Nebyl nalezen zadny clanek!
  echo "<tr class=\"txt\"><td colspan=\"5\" align=\"center\"><b>".RS_STA_ST_ZADNY_CLANEK."</b></td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazstat)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"center\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\" target=\"_blank\">".$pole_data["link"]."</a></td>\n";
    echo "<td align=\"left\" width=\"450\">".$pole_data["titulek"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["datum"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["visit"]."</td>\n";
    echo "<td align=\"center\">".number_format($pole_data["znamka"],2,',','')."</td></tr>\n";
  endwhile;
endif;
echo "</table>
<br>\n";
// zobrazeni navigacni listy
echo $naviglista;
echo "<br>\n";
}

function StatNavstev()
{
// volba statistiky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=StatClanky&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_CTENOST."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=StatNavstev&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_NAVSTEVY."</a></p>\n";

$dotazmm=phprs_sql_query("select min(datum) as mindat,max(datum) as maxdat from ".$GLOBALS["rspredpona"]."stat_arch",$GLOBALS["dbspojeni"]);
list($mindatum,$maxdatum)=phprs_sql_fetch_row($dotazmm);

if (empty($mindatum)):
  // neexistuji data
  echo "<p align=\"center\" class=\"txt\">".RS_STA_ST_ZADNA_DATA."</p>\n";
else:
  // sestaveni omezovace
  list($prminrok,$prminmes,$prminden)=explode("-",$mindatum);
  list($prmaxrok,$prmaxmes,$prmaxden)=explode("-",$maxdatum);
  // zobrazeni ziskanych dat
  for($p1=$prminrok;$p1<=$prmaxrok;$p1++): // pocitani let
    echo "<p align=\"center\" class=\"txt\"><b>".$p1.":</b><br>\n";
    if ($p1==$prminrok): $p2start=($prminmes*1); else: $p2start=1; endif; // prvni rok, zacina se od min. mes.
    if ($p1==$prmaxrok): $p2konec=($prmaxmes*1); else: $p2konec=12; endif; // posledni rok, konci se max. mes.
    for($p2=$p2start;$p2<=$p2konec;$p2++): // pocitani mesicu
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=StatNavMes&amp;modul=stat&amp;prmesic=".$p2."&amp;prrok=".$p1."\">".$p1."/".$p2."</a><br>";
    endfor;
    echo "</p>\n";
  endfor;
endif;

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_STA_ST_DATA_INFO."</p>\n";
}

function StatNavMes()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=StatNavstev&amp;modul=stat\" class=\"navigace\">".RS_STA_ST_ZPET_PRED."</a></p>\n";
// hlavicka
echo "<p align=\"center\" class=\"txt\"><big><strong>".$GLOBALS["prmesic"]."/".$GLOBALS["prrok"]."</strong></big></p>\n";

// zjisteni omezeni pole
$rotuj=1;
$prvypstart=1; // datumy pouzite pro doplneni tabulky
$prvypkonec=31; // datumy pouzite pro doplneni tabulky
while ($rotuj):
  if (checkdate($GLOBALS["prmesic"],$prvypkonec,$GLOBALS["prrok"])): $rotuj=0; else: $prvypkonec--; endif;
endwhile;

// prednaplneni pole
for ($pom=$prvypstart;$pom<=$prvypkonec;$pom++):
  $poledat[$pom][0]=$pom.".".$GLOBALS["prmesic"].".".$GLOBALS["prrok"];
  $poledat[$pom][1]=0;
  $poledat[$pom][2]=0;
  $poledat[$pom][3]=0;
  $poledat[$pom][4]=0;
  $poledat[$pom][5]=0;
  $poledat[$pom][6]=0;
  $poledat[$pom][7]=0;
endfor;

$poslednidat=0; // pom. prom. obsahujici posl. datum

$startdat=$GLOBALS["prrok"]."-".$GLOBALS["prmesic"]."-01"; // start datum
$konecdat=$GLOBALS["prrok"]."-".$GLOBALS["prmesic"]."-31"; // konec datum

$dotazstat=phprs_sql_query("select datum,hodina,visit,pages,os_win,os_linux,os_unix,os_mac,os_dalsi from ".$GLOBALS["rspredpona"]."stat_arch where datum>='".$startdat."' and datum<='".$konecdat."' order by datum,hodina",$GLOBALS["dbspojeni"]);
$pocetstat=phprs_sql_num_rows($dotazstat);

// zaplneni pole
for ($pom=0;$pom<$pocetstat;$pom++):
  list($prdatum,$prhodina,$prvisit,$prpages,$pros_win,$pros_linux,$pros_unix,$pros_mac,$pros_dalsi)=phprs_sql_fetch_row($dotazstat);
  list($prvyprok,$prvypmes,$prvypden)=explode("-",$prdatum); // rozklad datumu
  $prvypden=$prvypden*1; // odstraneni nuly u jednocifernych cislic
  if ($poslednidat==$prvypden): // shodne datum
    // datum se nemeni
    $poledat[$prvypden][1]=($poledat[$prvypden][1]+$prvisit);
    $poledat[$prvypden][2]=($poledat[$prvypden][2]+$prpages);
    $poledat[$prvypden][3]=($poledat[$prvypden][3]+$pros_win);
    $poledat[$prvypden][4]=($poledat[$prvypden][4]+$pros_linux);
    $poledat[$prvypden][5]=($poledat[$prvypden][5]+$pros_unix);
    $poledat[$prvypden][6]=($poledat[$prvypden][6]+$pros_mac);
    $poledat[$prvypden][7]=($poledat[$prvypden][7]+$pros_dalsi);
  else: // nove datum
    $poledat[$prvypden][1]=$prvisit;
    $poledat[$prvypden][2]=$prpages;
    $poledat[$prvypden][3]=$pros_win;
    $poledat[$prvypden][4]=$pros_linux;
    $poledat[$prvypden][5]=$pros_unix;
    $poledat[$prvypden][6]=$pros_mac;
    $poledat[$prvypden][7]=$pros_dalsi;
    $poslednidat=$prvypden; // ulozeni datumu pro test
  endif;
endfor;

// test na existenci dat
if (count($poledat)>0): // existuji data
  $suma1=0;
  $suma2=0;
  $suma3=0;
  $suma4=0;
  $suma5=0;
  $suma6=0;
  $suma7=0;
  // vypis
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr bgcolor=\"#E6E6E6\" class=\"txt\"><td align=\"center\"><b>".RS_STA_ST_2_DATUM."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_JEDINECNE."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_RELOAD."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_OS_WIN."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_OS_LINUX."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_OS_UNIX."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_OS_MAC."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_OS_JINY."</b></td></tr>\n";
  for ($pom=$prvypstart;$pom<=$prvypkonec;$pom++):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"right\">".$poledat[$pom][0]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][1]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][2]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][3]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][4]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][5]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][6]."</td>\n";
    echo "<td align=\"center\">".$poledat[$pom][7]."</td></tr>\n";
    $suma1=$suma1+$poledat[$pom][1];
    $suma2=$suma2+$poledat[$pom][2];
    $suma3=$suma3+$poledat[$pom][3];
    $suma4=$suma4+$poledat[$pom][4];
    $suma5=$suma5+$poledat[$pom][5];
    $suma6=$suma6+$poledat[$pom][6];
    $suma7=$suma7+$poledat[$pom][7];
  endfor;
  // souctovy radek
  echo "<tr bgcolor=\"#E6E6E6\" class=\"txt\">\n";
  echo "<td align=\"center\"><b>".RS_STA_ST_2_CELKEM."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma1."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma2."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma3."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma4."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma5."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma6."</b></td>\n";
  echo "<td align=\"center\"><b>".$suma7."</b></td></tr>\n";
  echo "</table>\n";
else:
  // neexistuji data
  echo "<p align=\"center\" class=\"txt\">".RS_STA_ST_ZADNA_DATA."</p>\n";
endif;

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_STA_ST_PRESNOST_INFO."</p>\n";

echo "<br>\n";
}

?>