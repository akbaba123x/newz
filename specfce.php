<?php

######################################################################
# phpRS Specialni Funkce 1.7.0
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_rekl, rs_klik_ban, rs_config, rs_levely, rs_ankety, rs_odpovedi, rs_news, rs_topic, rs_clanky, rs_kontrola_ip

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

// ------------------------------------------------- nacteni nastaveni z DB ------------------------------------------

// nacteni systemovych promennych do pole "rsconfig"
$dotaz="select promenna,hodnota from ".$GLOBALS["rspredpona"]."config where promenna not in ('default_level','default_reg_level')";
$dotazconfig=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazconfig!==false):
 while ($pole_data = phprs_sql_fetch_assoc($dotazconfig)):
   $GLOBALS['rsconfig']['rs_nastaveni'][$pole_data['promenna']]=$pole_data['hodnota'];
 endwhile;
endif;
// nacteni systemovych promennych do pole "rsconfig" - zamereno pouze na levelove promenne
$dotaz="select c.promenna,l.hodnota from ".$GLOBALS["rspredpona"]."config as c,".$GLOBALS["rspredpona"]."levely as l ";
$dotaz.="where c.promenna in ('default_level','default_reg_level') and c.hodnota=l.idl";
$dotazconfig=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazconfig!==false):
 while ($pole_data = phprs_sql_fetch_assoc($dotazconfig)):
   $GLOBALS['rsconfig']['rs_nastaveni'][$pole_data['promenna']]=$pole_data['hodnota'];
 endwhile;
endif;

// ------------------------------------------------- systemove bloky -------------------------------------------------

// systemovy blok: ankety
function Anketa()
{
// zjisteni aktivni ankety
$zjistanketa=NactiConfigProm('aktivni_anketa',0);
// podminka zobrazeni -> nalezeni aktivni ankety; ve starsich verzich "false" = anketa neexistuje
if ($zjistanketa>0&&$zjistanketa!='false'):
  $dotazotazka=phprs_sql_query("select otazka from ".$GLOBALS["rspredpona"]."ankety where ida='".$zjistanketa."'",$GLOBALS["dbspojeni"]);
  if ($dotazotazka!==false&&phprs_sql_num_rows($dotazotazka)>0):
    list($ankotazka)=phprs_sql_fetch_row($dotazotazka); // anketni otazka
  endif;
  $dotazcelkem=phprs_sql_query("select sum(pocitadlo) as celkem from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$zjistanketa."'",$GLOBALS["dbspojeni"]);
  if ($dotazcelkem!==false&&phprs_sql_num_rows($dotazcelkem)>0):
    list($celkemhlasu)=phprs_sql_fetch_row($dotazcelkem); // celkem hlasu
  endif;

  if ($celkemhlasu==0): $jednoproc=0; else: $jednoproc=140/$celkemhlasu; endif; // zjisteni poctu dilku na jeden hlas

  // nacteni prehledu moznych odpovedi
  $dotazodp=phprs_sql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$zjistanketa."' order by ido",$GLOBALS["dbspojeni"]);
  $pocetodp=phprs_sql_num_rows($dotazodp);

  $barva_prouzku=1; // barva procentualniho prouzku u odpovedi

  $txt_anketa="<div class=\"anketa-blok-z\">".$ankotazka."</div><br>\n";
  $txt_anketa.="<div class=\"anketa-blok-odpovedi\">\n";
  while($akt_pole_data = phprs_sql_fetch_assoc($dotazodp)):
    $velikost=ceil($jednoproc*$akt_pole_data["pocitadlo"]);
    $txt_anketa.="<a href=\"ankety.php?akce=hlasuj&amp;hlas=".$akt_pole_data["ido"]."&amp;cil=".$GLOBALS['rsconfig']['anketa_cil_str']."&amp;anketa=".$zjistanketa."\">".$akt_pole_data["odpoved"]."</a>&nbsp;(".$akt_pole_data["pocitadlo"]."&nbsp;".RS_SP_POCET_HLA.")<br>\n";
    // pruh generovany skrze PHP skript (GD knihovna)
    //$txt_anketa.="<img src=\"pictures.php?rvel=".$velikost."&amp;barva=".$barva_prouzku."\" height=\"8\" width=\"".$velikost."\" alt=\"".$akt_pole_data["pocitadlo"]."\"> (".$akt_pole_data["pocitadlo"]." ".RS_SP_POCET_HLA.")<br>\n";
    // pruh generovany skrze CSS styly
    $txt_anketa.="<div class=\"anketa-blok-odp-cara\"><div class=\"anketa-blok-odp-cara-hlasy\" style=\"width: ".$velikost."px;\" title=\"".$akt_pole_data["pocitadlo"]."\">&nbsp;</div></div>\n";
    $barva_prouzku++;
  endwhile;
  $txt_anketa.="</div><br>\n";
  $txt_anketa.="<div align=\"center\" class=\"anketa-blok-z\">".RS_SP_CELKEM_HLA.": ".$celkemhlasu."</div>\n";

  // zobrazeni menu
  switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
    case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
    default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_anketa); break;
  endswitch;
endif;
}

// systemovy blok: novinky
function HotNews()
{
// zjisteni pozadovane poctu hot news urcenych k zobrazeni; 0 = zadne
$pocetzprav=NactiConfigProm('pocet_novinek',0);
// podminka zobrazeni -> kladne mnozstvi "hot news"
if ($pocetzprav>0):
  $dotaznews=phprs_sql_query("select titulek,informace,datum,typ_nov from ".$GLOBALS["rspredpona"]."news order by datum desc limit 0,".$pocetzprav,$GLOBALS["dbspojeni"]);
  $pocetnews=phprs_sql_num_rows($dotaznews);
  if ($pocetnews==0):
    $txt_novinky='<div class="nov-text">Databáze neobsahuje žádnou novinku.</div>'."\n";
  else:
    // inic.
    $txt_novinky=''; // vysledny retezec
    $prvni=1; // test na prvni prubeh
    // vypis
    while($pole_data = phprs_sql_fetch_assoc($dotaznews)):
      if ($prvni==1): $prvni=0; else: $txt_novinky.="<br>\n"; endif;
      $txt_novinky.='<span class="nov-datum">'.MyDatetimeToDate($pole_data['datum']).':</span> ';
      // typ_nov: 0 = bezna, 1 = zvyraznena
      if ($pole_data['typ_nov']==0):
        $txt_novinky.='<span class="nov-titulek">'.$pole_data['titulek'].'</span>';
      else:
        $txt_novinky.='<span class="nov-titulek-duraz">'.$pole_data['titulek'].'</span>';
      endif;
      $txt_novinky.='<div class="nov-text">'.$pole_data['informace'].'</div>'."\n";
    endwhile;
  endif;

  // zobrazeni menu
  switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
    case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
    default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$txt_novinky); break;
  endswitch;
endif;
}

function GenHlavMenuTest($promenna)
{
if (empty($promenna)):
  return 0;
else:
  return 1;
endif;
}

// systemovy blok: seznam rubrik (navigacniho menu)
function GenHlavMenu() // vstup do fce: $stromhlmenu
{
// dekompilace stromu vnoreni
if (isset($GLOBALS["stromhlmenu"])):
  $pole_predek=explode(':',$GLOBALS["stromhlmenu"]);
  // projduti pole - test na korektnost vstupu
  $korektni_pole_predek=array();
  reset($pole_predek);
  while (list($klic,$hodnota)=each($pole_predek)):
    if (preg_match('|^\d*$|',$hodnota)):
      $korektni_pole_predek[]=$hodnota;
    endif;
  endwhile;
  // sestaveni podminky
  $podminka=' where zobrazit=1 and id_predka in ('.phprs_sql_escape_string(implode(',',$korektni_pole_predek)).') or level=0';
  $atribut_jen_hlavni=0;
else:
  $podminka=' where zobrazit=1 and level=0';
  $atribut_jen_hlavni=1;
endif;

$dotazmenu=phprs_sql_query("select idt,nazev,level,rodic,id_predka from ".$GLOBALS["rspredpona"]."topic".$podminka." order by level,hodnost desc,nazev",$GLOBALS["dbspojeni"]);
$pocetmenu=phprs_sql_num_rows($dotazmenu);

$prmenu=''; // inic. vysledneho stringu

if ($pocetmenu>0):
  if ($atribut_jen_hlavni==1):
    // -- vypis jen zakladni urovne --
    $prmenu.="<ul style=\"margin-left: 14px; margin-top: 0px; margin-bottom: 0px; padding-left: 2px; list-style-image: url('./".$GLOBALS['adrobrlayoutu']."minus.gif');\">\n";
    while ($pole_data = phprs_sql_fetch_assoc($dotazmenu)):
      if ($pole_data['rodic']==1): // test na rodicovstvi
        $prmenu.="<li style=\"margin-left: 0px; list-style-image: url('./".$GLOBALS['adrobrlayoutu']."plus.gif');\">";
        $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_data['idt']."&amp;stromhlmenu=".$pole_data['idt']."\">".$pole_data['nazev']."</a></li>\n";
      else:
        $prmenu.="<li style=\"margin-left: 0px;\">";
        $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_data['idt']."\">".$pole_data['nazev']."</a></li>\n";
      endif;
    endwhile;
    $prmenu.="</ul>\n";
  else:
    // -- vypis obsahuje otevrene vnorene polozky --
    // nacteni dat do pomocneho pole
    while ($pole_data = phprs_sql_fetch_assoc($dotazmenu)):
      if (!isset($pole_nastav[$pole_data['level']]['celkem'])):
        $pole_nastav[$pole_data['level']]['celkem']=0;
        $pole_nastav[$pole_data['level']]['hledam']=0;
        $pole_nastav[$pole_data['level']]['cesta']='';
      endif;
      $pole_polozky[$pole_data['level']][$pole_nastav[$pole_data['level']]['celkem']]=$pole_data;
      $pole_polozky[$pole_data['level']][$pole_nastav[$pole_data['level']]['celkem']]['stav']=1;
      $pole_nastav[$pole_data['level']]['celkem']++;
    endwhile;

    // setrizeni a vypis
    $rotuj=1;
    $akt_level=0;
    $pole_nastav[$akt_level]['hledam']=0;

    $prmenu.="<ul style=\"margin-left: 14px; margin-top: 0px; margin-bottom: 0px; padding-left: 2px; list-style-image: url('./".$GLOBALS['adrobrlayoutu']."minus.gif');\">\n";
    while ($rotuj==1):
      $rotuj=0; // konec rotace
      if (!isset($pole_nastav[$akt_level]['celkem'])): // test na existenci promenne v poli
        $pole_nastav[$akt_level]['celkem']=0;
      endif;
      for ($pom=0;$pom<$pole_nastav[$akt_level]['celkem'];$pom++):
        if ($pole_polozky[$akt_level][$pom]['stav']==1&&$pole_nastav[$akt_level]['hledam']==$pole_polozky[$akt_level][$pom]['id_predka']):
          $velikost_li=$akt_level*14;
          if ($pole_polozky[$akt_level][$pom]['rodic']==1): // test na rodicovstvi
            $prmenu.="<li style=\"margin-left: ".$velikost_li."px; list-style-image: url('./".$GLOBALS['adrobrlayoutu']."plus.gif');\">";
            if ($akt_level>0):
              $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_polozky[$akt_level][$pom]['idt']."&amp;stromhlmenu=".$pole_nastav[$akt_level]['cesta'].":".$pole_polozky[$akt_level][$pom]['idt']."\">".$pole_polozky[$akt_level][$pom]['nazev']."</a></li>\n";
            else:
              $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_polozky[$akt_level][$pom]['idt']."&amp;stromhlmenu=".$pole_polozky[$akt_level][$pom]['idt']."\">".$pole_polozky[$akt_level][$pom]['nazev']."</a></li>\n";
            endif;
          else:
            $prmenu.="<li style=\"margin-left: ".$velikost_li."px;\">";
            if ($akt_level>0):
              $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_polozky[$akt_level][$pom]['idt']."&amp;stromhlmenu=".$pole_nastav[$akt_level]['cesta']."\">".$pole_polozky[$akt_level][$pom]['nazev']."</a></li>\n";
            else:
              $prmenu.="<a href=\"search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=".$pole_polozky[$akt_level][$pom]['idt']."\">".$pole_polozky[$akt_level][$pom]['nazev']."</a></li>\n";
            endif;
          endif;
          $pole_polozky[$akt_level][$pom]['stav']=0; // deaktivovani polozky
          if (in_array($pole_polozky[$akt_level][$pom]['idt'],$pole_predek)): // test aktivni vnoreni za pomoci pole vsech aktivnich predku
            $pole_nastav[$akt_level+1]['hledam']=$pole_polozky[$akt_level][$pom]['idt']; // do vyssiho levelu se ulozeni akt. id, ktere predstavuje v dalsim levelu id predka
            if ($akt_level==0): // ulozeni mezi-cesty do $pole_nastav
              $pole_nastav[$akt_level+1]['cesta']=$pole_polozky[$akt_level][$pom]['idt'];
            else:
              $pole_nastav[$akt_level+1]['cesta']=$pole_nastav[$akt_level]['cesta'].':'.$pole_polozky[$akt_level][$pom]['idt'];
            endif;
            $akt_level++;
          endif;
          $rotuj=1; // pokracovani v rotaci
          break; // ukonceni cyklu
        endif;
      endfor;
      // test na snizeni zanoreni
      if ($rotuj==0&&$akt_level>0):
        $akt_level--;
        $rotuj=1; // pokracovani v rotaci
      endif;
    endwhile;
    $prmenu.="</ul>\n";
  endif;
endif;

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
endswitch;
}

// systemovy blok: kalendar
function Kalendar()
{
// vstup do fce: $kalendarmes, $kalendarrok
// inic. vstupnych promennych
if(isset($GLOBALS["kalendarmes"])): $mesic=phprs_sql_escape_string($GLOBALS["kalendarmes"]); else: $mesic=date("m"); endif;
if(isset($GLOBALS["kalendarrok"])): $rok=phprs_sql_escape_string($GLOBALS["kalendarrok"]); else: $rok=date("Y"); endif;

// inic. datumovych omezeni
$dnesni_datum=date("Y-m-d");
$start_datum_clanky=date("Y-m-d",mktime(0,1,1,$mesic,1,$rok));
$konec_datum_clanky=date("Y-m-d",mktime(0,1,1,($mesic+1),1,$rok));

// naplneni pomocneho clankoveho pole
$akt_pole_clanku=array();

$dotaz="select date_format(datum,'%Y-%m-%d') as vyslden from ".$GLOBALS["rspredpona"]."clanky ";
$dotaz.="where datum>='".$start_datum_clanky."' and datum<'".$konec_datum_clanky."' and datum<='".$dnesni_datum."' and visible='1' group by vyslden";
$dotazcla=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazcla!==false&&phprs_sql_num_rows($dotazcla)>0):
  while($pole_data = phprs_sql_fetch_assoc($dotazcla)):
    $akt_pole_clanku[]=$pole_data['vyslden'];
  endwhile;
endif;

// sestaveni celkoveho stavoveho vysledkoveho pole
$datum=array();
$pomocny_time=mktime(3,2,1,$mesic,1,$rok)-86400; // odcita se jeden den, protoze se s nim pocita v mktime
for($pom=1;$pom<32;$pom++):
  if (checkdate($mesic,$pom,$rok)):
    // stavy v poly "$datum": 0 = zadny clanek, 1 = vydan alespon jeden clanek, 2 = dnesni datum
    $datum[$pom]=0; // defaultne = zadny clanek
    $porovnani_datum=date("Y-m-d",(86400*$pom)+$pomocny_time); // sestaveni data k porovnani se clanky
    // $porovnani_datum=date("Y-m-d",mktime(0,0,1,$mesic,$pom,$rok));
    if (in_array($porovnani_datum,$akt_pole_clanku)):
      $datum[$pom]=1; // vydan alespon jeden clanek
    endif;
    if ($porovnani_datum==$dnesni_datum): $datum[$pom]=2; endif; // test na dnesni datum
  endif;
endfor;
$pocet_dnu_mesic=count($datum);

$cislodne=date("w",mktime(0,0,1,$mesic,1,$rok)); // 0 - nedele, ..., 6 - sobota
$pristimes=date("m",mktime(0,0,1,($mesic+1),1,$rok));
$pristirok=date("Y",mktime(0,0,1,($mesic+1),1,$rok));
$pristiod=$pristirok."-".$pristimes."-01+00:00:00";
$pristido=date("Y-m-d+H:i:s",mktime(23,59,59,$mesic+2,1,$rok)-86400);
$predeslymes=date("m",mktime(0,0,1,($mesic-1),1,$rok));
$predeslyrok=date("Y",mktime(0,0,1,($mesic-1),1,$rok));
$predeslyod=$predeslyrok."-".$predeslymes."-01+00:00:00";
$predeslydo=date("Y-m-d+H:i:s",(mktime(23,59,59,$mesic,1,$rok)-86400));

// hlavicka tabulky dnu
$prmenu="<table border=\"1\" align=\"center\" cellspacing=\"0\" cellpadding=\"1\">
<tr class=\"kal-text\"><td colspan=\"7\" align=\"center\"><b>
<a href=\"search.php?kalendarmes=$predeslymes&amp;kalendarrok=$predeslyrok&amp;rsod=$predeslyod&amp;rsdo=$predeslydo&amp;rstext=all-phpRS-all\">&lt;&lt;</a>&nbsp;
<a href=\"search.php?kalendarmes=$mesic&amp;kalendarrok=$rok&amp;rsod=$rok-$mesic-01+00:00:00&amp;rsdo=$rok-$mesic-$pocet_dnu_mesic+23:59:59&amp;rstext=all-phpRS-all\">".TextMesic($mesic)."</a>
&nbsp;<a href=\"search.php?kalendarmes=$pristimes&amp;kalendarrok=$pristirok&amp;rsod=$pristiod&amp;rsdo=$pristido&amp;rstext=all-phpRS-all\">&gt;&gt;</a>
</b></td></tr>
<tr class=\"kal-text\"><td>".RS_SP_KAL_PO."</td><td>".RS_SP_KAL_UT."</td><td>".RS_SP_KAL_ST."</td><td>".RS_SP_KAL_CT."</td><td>".RS_SP_KAL_PA."</td><td>".RS_SP_KAL_SO."</td><td>".RS_SP_KAL_NE."</td></tr>\n";

// priprava zobrazeni prvniho dne v mesici
switch($cislodne):
  case 0: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n"; break;
  case 1: break;
  case 2: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td>"; break;
  case 3: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 4: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 5: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
  case 6: $prmenu.="<tr class=\"kal-text\"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; break;
endswitch;

// vypis vsech dnu do tabulky
for($pom=1;$pom<=$pocet_dnu_mesic;$pom++):
  if ($cislodne==1): $prmenu.="<tr class=\"kal-text\">"; endif;
  // vzhled
  switch ($datum[$pom]):
    case 0: $prmenu.="<td align=\"center\">".$pom."</td>\n"; break;
    case 1: $prmenu.="<td align=\"center\" class=\"kal-clanek\"><a href=\"search.php?kalendarmes=".$mesic."&amp;kalendarrok=".$rok."&amp;rsod=".$rok."-".$mesic."-".$pom."+00:00:00&amp;rsdo=".$rok."-".$mesic."-".$pom." 23:59:59&amp;rstext=all-phpRS-all\">".$pom."</a></td>\n"; break;
    case 2: $prmenu.="<td align=\"center\" class=\"kal-dnesni\"><a href=\"index.php\">".$pom."</a></td>\n"; break;
    default: $prmenu.="<td align=\"center\">".$pom."</td>\n"; break;
  endswitch;
  // test na typ dne
  if ($cislodne==0): $prmenu.="</tr>\n"; endif;
  if ($cislodne==6): $cislodne=0; else: $cislodne=($cislodne+1); endif;
endfor;

// dokonceni tabulky dnu
switch($cislodne):
  case 0: $prmenu.="<td>&nbsp;</td></tr>\n"; break;
  case 1: break;
  case 2: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 3: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 4: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 5: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
  case 6: $prmenu.="<td>&nbsp;</td><td>&nbsp;</td></tr>\n"; break;
endswitch;
$prmenu.="</table>\n";

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$prmenu); break;
endswitch;
}

// ------------------------------------------------- clankove funkce -------------------------------------------------

function SouvisejiciCl($id_clanek = 0)
{
$id_clanek=phprs_sql_escape_string($id_clanek); // bezpecnostni korekce

$dotazskup=phprs_sql_query("select skupina_cl from ".$GLOBALS["rspredpona"]."clanky where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);

if (phprs_sql_num_rows($dotazskup)>0): // clanek existuje
  list($id_skupiny)=phprs_sql_fetch_row($dotazskup); // identifikacni cislo skupiny
  if ($id_skupiny>0): // 0 = bez zarazeni
    $dotazclanky=phprs_sql_query("select link,seo_link,titulek,datum from ".$GLOBALS["rspredpona"]."clanky where skupina_cl='".$id_skupiny."' and link!='".$id_clanek."' and visible='1' and datum<='".Date("Y-m-d H:i:s")."' order by datum desc",$GLOBALS["dbspojeni"]);
    $pocetclanky=phprs_sql_num_rows($dotazclanky);
    if ($pocetclanky>0):
      echo "<div class=\"souvis-cla-celkovy-ram\">\n";
      echo "<strong>".RS_SP_SOUVIS_CLA.":</strong><br>\n";
      while ($pole_data = phprs_sql_fetch_assoc($dotazclanky)):
        echo "<a href=\"view.php?nazevclanku=".$pole_data['seo_link']."&amp;cisloclanku=".$pole_data['link']."\">".$pole_data['titulek']."</a> (".MyDatetimeToDate($pole_data['datum']).")<br>\n";
      endwhile;
      echo "</div>\n";
    endif;
  endif;
endif;
}

function HodnoceniCl($id_clanek = 0)
{
$id_clanek=phprs_sql_escape_string($id_clanek); // bezpecnostni korekce

$dotazhod=phprs_sql_query("select hodnoceni,mn_hodnoceni from ".$GLOBALS["rspredpona"]."clanky where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);

if (phprs_sql_num_rows($dotazhod)>0): // clanek existuje
  list($hodnoceni,$mnozstvi)=phprs_sql_fetch_row($dotazhod); // ziskani: hodnoceni a mnozstvi hodnot

  echo "<div class=\"hodnoceni-celkovy-ram\"><form action=\"view.php\" method=\"post\" style=\"margin: 0px;\">\n";
  if ($mnozstvi>0):
    echo "[".RS_SP_AKT_ZNAMKA.": ".number_format(($hodnoceni/$mnozstvi),2,',','')." / ".RS_SP_POCET_ZNAMEK.": ".$mnozstvi."] ";
  else:
    echo "[".RS_SP_AKT_ZNAMKA.": 0 / ".RS_SP_POCET_ZNAMEK.": 0] ";
  endif;
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"1\">1 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"2\">2 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"3\">3 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"4\">4 ";
  echo "<input type=\"radio\" name=\"hlasovani\" value=\"5\">5 ";
  echo "<input type=\"submit\" value=\" ".RS_SP_TL_ZNAMKA." \" class=\"tl\">\n";
  echo "<input type=\"hidden\" name=\"cisloclanku\" value=\"".$id_clanek."\">\n";
  echo "</form></div>\n";
endif;
}

function SouvisejiciAnketyCl($id_anketa = 0, $navratova_url = '')
{
$id_anketa=phprs_sql_escape_string($id_anketa); // bezpecnostni korekce

// zjisteni anketni otazky
$dotazotazka=phprs_sql_query("select otazka,zobrazit,uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".$id_anketa."'",$GLOBALS["dbspojeni"]);
if ($dotazotazka!==false&&phprs_sql_num_rows($dotazotazka)==1):
  $akt_pole_anketa=phprs_sql_fetch_assoc($dotazotazka); // nacteni ankety
else:
  $akt_pole_anketa['zobrazit']=0; // chyba - anketa nenalezena
endif;

// test na aktivni stav ankety
if ($akt_pole_anketa['zobrazit']==1):
  // inic.
  $celkem_hlasu=0;
  $jedno_proc=0;
  // zjisteni celkoveho poctu hlasu
  $dotazcelkem=phprs_sql_query("select sum(pocitadlo) as soucet from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$id_anketa."'",$GLOBALS["dbspojeni"]);
  if ($dotazcelkem!==false&&phprs_sql_num_rows($dotazcelkem)>0):
    // celkovy pocet hlasu
    list($celkem_hlasu)=phprs_sql_fetch_row($dotazcelkem);
    // kolik dilku pripada na jden hlas
    if ($celkem_hlasu==0):
      $jedno_proc=0;
    else:
      $jedno_proc=(100/$celkem_hlasu);
    endif;
  endif;
  // nacteni odpovedi
  $dotazodp=phprs_sql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$id_anketa."' order by ido",$GLOBALS["dbspojeni"]);
  $pocetodp=phprs_sql_num_rows($dotazodp);
  // vypis odpovedi
  echo "<div align=\"center\"><div class=\"anketa-cla-celkovy-ram\">\n";
  if ($akt_pole_anketa['uzavrena']==1):
    // anketa bez moznosti hlasovani
    echo "<p class=\"anketa-cla-otazka\">".$akt_pole_anketa['otazka']."</p>\n";
    echo "<div class=\"anketa-cla-ram\">\n";
    while ($pole_data = phprs_sql_fetch_assoc($dotazodp)):
      $akt_procento=$jedno_proc*$pole_data["pocitadlo"];
      echo "<div class=\"anketa-cla-odpovedi\">".$pole_data["odpoved"]." <i>(".RS_AN_POCET_HLA.": ".$pole_data["pocitadlo"].")</i><br>\n";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\">";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\">";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\"> (".Zo($akt_procento)." %)</div><br>\n";
    endwhile;
    echo "</div>\n";
    echo "<strong>".RS_AN_CELKEM_HLA.": ".$celkem_hlasu."</strong>\n";
  else:
    // anketa s moznosti hlasovani
    echo "<form action=\"ankety.php\" method=\"post\">\n";
    echo "<p class=\"anketa-cla-otazka\">".$akt_pole_anketa['otazka']."</p>\n";
    echo "<div class=\"anketa-cla-ram\">\n";
    $checked_prvni_pol=0;
    while ($pole_data = phprs_sql_fetch_assoc($dotazodp)):
      $akt_procento=$jedno_proc*$pole_data["pocitadlo"];
      echo "<div class=\"anketa-cla-odpovedi\">";
      if ($checked_prvni_pol==0):
        echo "<input type=\"radio\" name=\"hlas\" value=\"".$pole_data["ido"]."\" checked> ";
        $checked_prvni_pol=1;
      else:
        echo "<input type=\"radio\" name=\"hlas\" value=\"".$pole_data["ido"]."\"> ";
      endif;
      echo $pole_data["odpoved"]." <i>(".RS_AN_POCET_HLA.": ".$pole_data["pocitadlo"].")</i><br>\n";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\">";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\">";
      echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\"> (".Zo($akt_procento)." %)</div><br>\n";
    endwhile;
    echo "</div>\n";
    echo "<input type=\"submit\" value=\" ".RS_AN_TL_HLASUJ." \" class=\"tl\"><br><br><strong>".RS_AN_CELKEM_HLA.": ".$celkem_hlasu."</strong>\n";
    echo "<input type=\"hidden\" name=\"akce\" value=\"hlasuj\"><input type=\"hidden\" name=\"anketa\" value=\"".$id_anketa."\">\n";
    echo "<input type=\"hidden\" name=\"cil\" value=\"url\"><input type=\"hidden\" name=\"cil_url\" value=\"".$navratova_url."\">\n";
    echo "</form>\n";
  endif;
  echo "</div></div>\n";
endif;
}

// ------------------------------------------------- pomocne funkce --------------------------------------------------

function Banners($poloha = 0)
{
return (Banners_str($poloha));
}
function Banners_str($poloha = 0)
{
// od verze phpRS 1.4.0 je reklamni system zcela automaticky a nastavuje se z administracniho modulu
$vysledek=''; // inic. vysledek

// pozice (= vstupni atribut "poloha"): 1 - horni, 2 - dolni
switch($poloha):
  // horni pozice
  case 1: $dotaz="select kod,typ_reklamy from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='1'"; break;
  // dolni pozice
  case 2: $dotaz="select kod,typ_reklamy from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='2'"; break;
endswitch;

$dotazprvek=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazprvek!==false&&phprs_sql_num_rows($dotazprvek)>0):
  // nacteni definice reklamniho prvku pro prislusnou pozici
  $reklamni_prvek=phprs_sql_fetch_assoc($dotazprvek);
  // test na nulovy obsah reklamniho kodu
  if (!empty($reklamni_prvek['kod'])):
    // rozhodnuti o zpusobu vygenerovani reklamniho kodu
    if ($reklamni_prvek['typ_reklamy']=='kampan'):
      // reklamni kampan
      $vysledek.=Banners_kampan($reklamni_prvek['kod']);
    else:
      // cisty reklamni kod
      $vysledek.="\n<!-- Misto pro banner -->\n";
      $vysledek.='<div align="center">'.$reklamni_prvek['kod'].'</div>';
      $vysledek.="\n<!-- Konec: Misto pro banner -->\n";
    endif;
  endif;
endif;

return $vysledek;
}

function Banners_kampan($id_kampan = 0)
{
$vysledek=''; // inic. vysledek
$id_kampan=phprs_sql_escape_string($id_kampan); // bezpecnostni korekce

$vysledek.="\n<!-- Misto pro banner -->\n";
// dotaz na reklamni kampan
$dotazkampan=phprs_sql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$id_kampan."'",$GLOBALS["dbspojeni"]);
list($pocet_banneru)=phprs_sql_fetch_row($dotazkampan); // pocet reklamnich prvku v kampani
if ($pocet_banneru>0): // kampan obsahuje alespon jeden reklamni prvek
  $prktery=(rand(1,$pocet_banneru)-1); // nutne odecist 1, protoze se poradi pocita od nuly
  $dotazbanner=phprs_sql_query("select idb,text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$id_kampan."' order by idb limit ".$prktery.",1",$GLOBALS["dbspojeni"]);
  $pole_banner=phprs_sql_fetch_assoc($dotazbanner);
  // zapocitani zobrazeni reklamy
  @phprs_sql_query("update ".$GLOBALS["rspredpona"]."klik_ban set pocitadlo_zobr=(pocitadlo_zobr+1) where idb='".$pole_banner['idb']."' and id_kampan='".$id_kampan."'",$GLOBALS["dbspojeni"]);
  // vypis reklamy
  $vysledek.='<div align="center">';
  switch($pole_banner['druh']):
    case 0: // forma reklamy: banner
      $vysledek.='<div class="banner-img"><a href="direct.php?kam='.$pole_banner['idb'].'" target="_blank"><img src="'.$pole_banner['banner'].'" border="0" width="'.$pole_banner['width'].'" height="'.$pole_banner['height'].'" alt="'.$pole_banner['text'].'" title="'.$pole_banner['text'].'">';
      if ($pole_banner['text']!=''): $vysledek.='<br>'.$pole_banner['text']; endif; // kdyz existuje doplnkovy text
      $vysledek.='</a></div>';
      break;
    case 1: // forma reklamy: text
      $vysledek.='<span class="banner-text"><a href="direct.php?kam='.$pole_banner['idb'].'" title="'.$pole_banner['text'].'" target="_blank">'.$pole_banner['banner'].'</a></span>';
      break;
    case 2: // forma reklamy: reklamni kod
      $vysledek.='<span class="banner-text">'.$pole_banner['banner'].'</span>';
      break;
  endswitch;
  $vysledek.='</div>';
endif;
$vysledek.="\n<!-- Konec: Misto pro banner -->\n";

return $vysledek;
}

function Banners_prvek($id_prvek = 0)
{
$vysledek=''; // inic. vysledek
$id_prvek=phprs_sql_escape_string($id_prvek); // bezpecnostni korekce

$vysledek.="\n<!-- Misto pro banner -->\n";
// dotaz na reklmani prvek
$dotazbanner=phprs_sql_query("select idb,text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$id_prvek."'",$GLOBALS["dbspojeni"]);
if ($dotazbanner!==false&&phprs_sql_num_rows($dotazbanner)>0): // test na existenci vysledku
  $pole_banner=phprs_sql_fetch_assoc($dotazbanner);
  // zapocitani zobrazeni reklamy
  @phprs_sql_query("update ".$GLOBALS["rspredpona"]."klik_ban set pocitadlo_zobr=(pocitadlo_zobr+1) where idb='".$pole_banner['idb']."'",$GLOBALS["dbspojeni"]);
  // vypis reklamy
  $vysledek.='<div align="center">';
  switch($pole_banner['druh']):
    case 0: // forma reklamy: banner
      $vysledek.='<div class="banner-img"><a href="direct.php?kam='.$pole_banner['idb'].'" target="_blank"><img src="'.$pole_banner['banner'].'" border="0" width="'.$pole_banner['width'].'" height="'.$pole_banner['height'].'" alt="'.$pole_banner['text'].'" title="'.$pole_banner['text'].'">';
      if ($pole_banner['text']!=''): $vysledek.='<br>'.$pole_banner['text']; endif; // kdyz existuje doplnkovy text
      $vysledek.='</a></div>';
      break;
    case 1: // forma reklamy: text
      $vysledek.='<span class="banner-text"><a href="direct.php?kam='.$pole_banner['idb'].'" title="'.$pole_banner['text'].'" target="_blank">'.$pole_banner['banner'].'</a></span>';
      break;
    case 2: // forma reklamy: reklamni kod
      $vysledek.='<span class="banner-text">'.$pole_banner['banner'].'</span>';
      break;
  endswitch;
  $vysledek.='</div>';
endif;
$vysledek.="\n<!-- Konec: Misto pro banner -->\n";

return $vysledek;
}

function TextMesic($cismes = 0)
{
switch($cismes):
  case 1: $txt=RS_SP_KAL_M01; break;
  case 2: $txt=RS_SP_KAL_M02; break;
  case 3: $txt=RS_SP_KAL_M03; break;
  case 4: $txt=RS_SP_KAL_M04; break;
  case 5: $txt=RS_SP_KAL_M05; break;
  case 6: $txt=RS_SP_KAL_M06; break;
  case 7: $txt=RS_SP_KAL_M07; break;
  case 8: $txt=RS_SP_KAL_M08; break;
  case 9: $txt=RS_SP_KAL_M09; break;
  case 10: $txt=RS_SP_KAL_M10; break;
  case 11: $txt=RS_SP_KAL_M11; break;
  case 12: $txt=RS_SP_KAL_M12; break;
  default: $txt='';
endswitch;

return $txt;
}

// generator pevne mezery
function Me($vel = 1)
{
return str_repeat("&nbsp;",$vel);
}

// zaokrouhledni cisla
function Zo($x = 0)
{
return number_format($x,2,".",",");
}

// preved MySQL datetime typ do formy bezneho teckou oddeleneho datumu
function MyDatetimeToDate($mysql_datum)
{
return date('d.m.Y', strtotime($mysql_datum));
/*
$rozlozenedatum=explode(' ',trim($mysql_datum)); // [0] - datum, [1] - cas
$vysledek=explode('-',$rozlozenedatum[0]);
return $vysledek[2].'.'.$vysledek[1].'.'.$vysledek[0]; // dd.mm.rrrr
*/
}

// prevede MySQL datetime typ na unixovy cas (cislo)
function MyDatetimeToInt($mysql_datum)
{
return strtotime($mysql_datum);
/*
list($datum,$cas)=explode(' ',$mysql_datum);
list($rok,$mes,$dat)=explode('-',$datum);
list($hod,$min,$sek)=explode(':',$cas);
return date("U",mktime($hod,$min,$sek,$mes,$dat,$rok)); // int
*/
}

// preved MySQL datetime do standarni zobrazovaci formy
function MyDatetimeToStd($mysql_datum)
{
return date('d.m.Y H:i:s', strtotime($mysql_datum));
/*
list($datum,$cas)=explode (' ',$mysql_datum);
list($rok,$mes,$den)=explode ('-',$datum);
return $den.'.'.$mes.'.'.$rok.' '.$cas; // dd.mm.rrrr hh:mm:ss
*/
}

// test na opakujici se IP adresu
function TestNaOpakujiciIP($akt_typ_kontr_str = '', $akt_delka_omezeni = 0, $akt_max_pocet_opak = 0)
{
$vysledek=1; // default true; 1 = opakuje se, 0 = neopakuje se

$akt_ip_adresa=$_SERVER["REMOTE_ADDR"]; // ip adresa ctenare
$akt_cas=Date("Y-m-d H:i:s");

// inic. tabulky - ocisteni od starych dat
@phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."kontrola_ip where cas<'".$akt_cas."'",$GLOBALS["dbspojeni"]);
// testovani dotaz
$dotazip=phprs_sql_query("select idk,pocet from ".$GLOBALS["rspredpona"]."kontrola_ip where cas>='".$akt_cas."' and ip_adresa='".$akt_ip_adresa."' and typ='".$akt_typ_kontr_str."'",$GLOBALS["dbspojeni"]);
if ($dotazip!==false):
  if (phprs_sql_num_rows($dotazip)==0):
    // neexistuje zaznam - nutno vytvorit
    $akt_ttl_cas=Date("Y-m-d H:i:s",time()+$akt_delka_omezeni); // stanoveni casove platnosti omezeni
    @phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."kontrola_ip values (null,'".$akt_ip_adresa."','".$akt_ttl_cas."',1,'".$akt_typ_kontr_str."')",$GLOBALS["dbspojeni"]);
    $vysledek=0; // false; 0 = neopakuje se
  else:
    // nacteni ziskanych dat
    $akt_pole_data=phprs_sql_fetch_assoc($dotazip);
    // test na pocet opakovani
    if ($akt_pole_data['pocet']<$akt_max_pocet_opak):
      $vysledek=0; // false; 0 = neopakuje se
      @phprs_sql_query("update ".$GLOBALS["rspredpona"]."kontrola_ip set pocet=pocet+1 where idk=".$akt_pole_data['idk']." and typ='".$akt_typ_kontr_str."'",$GLOBALS["dbspojeni"]);
    endif;
  endif;
endif;

return $vysledek;
}

// standardni funkce pro sestaveni navigacni listy
function SestavNavigacniListu($celkem_polozek = 0, $pocet_na_str = 0, $akt_str = 1, $atr_pocet_na_str = 'pocet', $atr_akt_str = 'str', $cil_soubor = '', $cil_atributy = '')
{
$vysl='';

if ($celkem_polozek>0&&$pocet_na_str>0): // celkovy pocet polozek musi byt vetsi nez 0 a pocet polozek na jednu stranku take
  $mozne_obratky=ceil($celkem_polozek/$pocet_na_str);
  if ($mozne_obratky>1): // musi byt vice jak jedna obratka, aby melo smysl vytvaret navigacni listu
    // pomocne upravy
    if ($cil_atributy!=''): $cil_atributy='&amp;'.$cil_atributy; endif;
    // sestaveni navigacni listy
    $vysl.='<div class="std-navig">| ';
    for ($pom=0;$pom<$mozne_obratky;$pom++):
      $pom_vysl_strana=$pom+1;
      if ($pom_vysl_strana==$akt_str): // test na akt. platnou stranku
        $vysl.='<span class="std-navig-akt-str">'.($pom*$pocet_na_str+1).'-'.min(($pom_vysl_strana*$pocet_na_str),$celkem_polozek).'</span> | ';
      else:
        $vysl.='<a href="'.$cil_soubor.'?'.$atr_pocet_na_str.'='.$pocet_na_str.'&amp;'.$atr_akt_str.'='.$pom_vysl_strana.$cil_atributy.'">'.($pom*$pocet_na_str+1).'-'.min(($pom_vysl_strana*$pocet_na_str),$celkem_polozek).'</a> | ';
      endif;
    endfor;
    $vysl.="</div>\n";
  endif;
endif;

return $vysl;
}

// nacteni hodnoty konfiguracni promenne
function NactiConfigProm($promenna = '', $chyba = 0)
{
if (isset($GLOBALS['rsconfig']['rs_nastaveni'][$promenna])):
  return $GLOBALS['rsconfig']['rs_nastaveni'][$promenna]; // hledana promenna existuje
else:
  return $chyba; // chyba
endif;
}

?>