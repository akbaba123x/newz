<?php

######################################################################
# phpRS Search 1.7.4
######################################################################

// Copyright (c) 2001-2012 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user, rs_topic, rs_clanky, rs_levely

/* mozne vstupni promenne:

  $rstext ... hledany text (jde o povinny vstup], retezec "all-phpRS-all" umozni kompletni vypis
  $rstema ... omezuje vypis pouze na zadanou rubriku/tema -> vstupem je cislo tematu, default hodnota = nic
  $rsrazeni ... urcuje zpusob serazeni vysledku, default hodnota = datum_90
      - platne hodnoty: datum_09, datum_90, nazev_az, nazev_za, priorita
  $rsautor ... omezuje vypis pouze na zvoleneho autora -> vstupem je cislo autora, default hodnota = nic
  $rsod + $rsdo ... umoznuje casove omezeni, $rsdo neni povinny, jelikoz search.php jej umi doplnit
  $rskde (titulek,uvod,text,t_slova) ... specifikuje, kde se dany $rstext vyhledava, default nastaveni ukazuje na "text"
      - platne hodnoty: tit, uvd, txt, tsl, vse
  $rskolik + $rskolikata ... umoznuje definovat rozsah vypisu: $rskolik (mnozstvi polozek) a $rskolikata prenasi informaci o pozici
  $rsvztah ... umoznuje definovat vztah mezi vetsim poctem zadanych slov k vyhledavani, default hodnota = OR
      - platne hodnoty: AND, OR
  $rsvelikost ... definuje zpusob vypisu: bud jednoradkovy vypis nebo vypis doplneni o uvodni texty u jednotlivych nalezenych polozek
      - platne hodnoty: jr, uvod, sab
*/

/*
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");
Header("Expires: ".GMDate("D, d M Y H:i:s",Date("U")+3600)." GMT");
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("myweb.php");

// --[zakladni fce - vyhledavani]---------------------------------------------------------------------

function Prohledej($jaky_zpusob = '+', $co = '', $naco = '')
{
if ($jaky_zpusob=='-'):
  $jaky_zpusob_txt='NOT LIKE';
else:
  $jaky_zpusob_txt='LIKE';
endif;

$naco=phprs_sql_escape_string($naco); // bezpecnostni korekce
$naco=str_replace("%", "\%", $naco);
$naco=str_replace("_", "\_", $naco);

switch ($co):
  case 'tit': $str="c.titulek ".$jaky_zpusob_txt." ('%".$naco."%')"; break;
  case 'uvd': $str="c.uvod ".$jaky_zpusob_txt." ('%".$naco."%')"; break;
  case 'txt': $str="c.text ".$jaky_zpusob_txt." ('%".$naco."%')"; break;
  case 'tsl': $str="c.t_slova ".$jaky_zpusob_txt." ('%".$naco."%')"; break;
  case 'vse':
       if ($jaky_zpusob=='-'):
         $str="(c.titulek ".$jaky_zpusob_txt." ('%".$naco."%') AND c.uvod ".$jaky_zpusob_txt." ('%".$naco."%') AND c.text ".$jaky_zpusob_txt." ('%".$naco."%'))";
       else:
         $str="(c.titulek ".$jaky_zpusob_txt." ('%".$naco."%') OR c.uvod ".$jaky_zpusob_txt." ('%".$naco."%') OR c.text ".$jaky_zpusob_txt." ('%".$naco."%'))";
       endif;
       break;
  default: $str='';
endswitch;

return $str; // vraceni prikazu
}

function ZpracujHleStr($vstup = '')
{
if (empty($vstup)):
  // vraceni prazdneho vystupu
  return array('');
else:
  // roklad vyhledavaneho retezce - vysledkem je pole hledanych slov a retezcu $slova[]
  $slova=array();
  $p_txt=str_replace("'","\'",$vstup); // zpracovani apostrofu
  $p_txt=str_replace('"',' " ',$p_txt); // dekompilace textu na fraze a slova
  $p_pompole=explode(' ',$p_txt);
  $p_pocet_pompole=count($p_pompole); // pocet prvku v pompole
  $p_uvozovka=0; // 0 = false stav, 1 = true stav
  $p_str_uvozovka='';
  // zpracovani vyhled. retezce
  for ($pom=0;$pom<$p_pocet_pompole;$pom++):
    if ($p_uvozovka==0&&$p_pompole[$pom]!='"'&&$p_pompole[$pom]!=''): // zapis do pole hled. slov
      $slova[]=$p_pompole[$pom];
    else:
      if ($p_uvozovka==1&&$p_pompole[$pom]!='"'): // zapis v ramci uvozovek
        $p_str_uvozovka.=' '.$p_pompole[$pom];
      endif;
      if ($p_pompole[$pom]=='"'): // inicializace uvozovek
         if ($p_uvozovka==0): // prepinani mezi uvozovkami
           $p_uvozovka=1; // start vnoreneho retezce
         else:
           if ($p_str_uvozovka!=''): // test na vyprazdneni pom. retezce do pole hled. slov
             $slova[]=trim($p_str_uvozovka);
             $p_str_uvozovka=''; // vynulovani pomoc. promenne
           endif;
           $p_uvozovka=0; // konec vnoreneho retezce
         endif;
      endif;
    endif;
  endfor;
  // test na zbytkovy retezec v $p_str_uvozovka
  if (trim($p_str_uvozovka)!=''): // pom. prom. obsahuje nejaky zbytkovy retezec
    $p_pompole=explode(' ',trim($p_str_uvozovka));
    $p_pocet_pompole=count($p_pompole); // pocet prvku v pompole
    for ($pom=0;$pom<$p_pocet_pompole;$pom++):
      if ($p_pompole[$pom]!=' '): $slova[]=$p_pompole[$pom]; endif;
    endfor;
  endif;
  // vraceni vysledkoveho pole
  return $slova;
endif;
}

function Vyhledavani()
{
// *** FAZE 1 *** - test na chybejici promenne; inic. vyhledavaciho enginu

// povoluje/zakazuje hlidani levelu
$hlidatlevel=NactiConfigProm('hlidat_level',0);
// povoluje/zakazuje pouziti zakakove clankove sablony
$zakazsab=NactiConfigProm('zobrazit_zakaz',0);

/* realizovano jiz u rozcestniku
if (empty($GLOBALS["rsvelikost"])):
  $GLOBALS["rsvelikost"]='jr';
endif;
*/

if (
	(isset($GLOBALS["rskolik"]) && !ctype_digit($GLOBALS["rskolik"]))
	||
	(isset($GLOBALS["rskolikata"]) && !ctype_digit($GLOBALS["rskolikata"]))
	||
	(isset($GLOBALS["rstema"]) && $GLOBALS["rstema"] != "nic" && !ctype_digit($GLOBALS["rstema"]))
) {
	// chybny vstup
	echo "Nepovoleny pristup! / Hacking attempt!";
	return false;
}
if (isset($GLOBALS["rstema"])):
  $GLOBALS["rstema"] = (int)$GLOBALS["rstema"];
endif;

if (isset($GLOBALS["rskolik"])):
  $GLOBALS["rskolik"] = (int)$GLOBALS["rskolik"];
endif;

if (isset($GLOBALS["rskolikata"])):
  $GLOBALS["rskolikata"]=(int)$GLOBALS["rskolikata"];
endif;
if (!isset($GLOBALS["rskde"]) || $GLOBALS["rskde"]!="tit" && $GLOBALS["rskde"]!="uvd" && $GLOBALS["rskde"]!="txt" && $GLOBALS["rskde"]!="tsl"):
  // kde vsude se bude vyhledavat
  $GLOBALS["rskde"]='vse';
endif;

if (empty($GLOBALS["rskolikata"]) || $GLOBALS["rskolikata"] < 0): // zobrazena stranka z vysledku
  if ($GLOBALS["rsvelikost"]=='sab'): $GLOBALS["rskolik"]=15; else: $GLOBALS["rskolik"]=50; endif;
  $GLOBALS["rskolikata"]=1;
endif;

if (empty($GLOBALS["rsautor"])): // omezeni na autora
  $GLOBALS["rsautor"]='nic';
endif;

if (empty($GLOBALS["rstema"])):
    // omezeni na tema + podtema
    $GLOBALS["rstema"]='nic';
endif;

if (empty($GLOBALS["rsrazeni"])): // zpusob zobrazeni/serazeni vysledku
  $GLOBALS["rsrazeni"]='datum_90';
endif;

if (empty($GLOBALS["rsvztah"])): // vztah mezi hledanymi retezci
  $GLOBALS["rsvztah"]='OR';
endif;

$navigace_doplnek_linku='';
if (isset($GLOBALS["stromhlmenu"])): // $GLOBALS["stromhlmenu"] ... ukazuje miru vnoreni
  $navigace_doplnek_linku.='&amp;stromhlmenu='.htmlspecialchars($GLOBALS["stromhlmenu"]);
endif;

// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazautori=phprs_sql_query("select idu,jmeno,email from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
if ($dotazautori!==false&&phprs_sql_num_rows($dotazautori)>0):
  while($pole_data_aut=phprs_sql_fetch_assoc($dotazautori)):
    $autori[$pole_data_aut["idu"]][0]=$pole_data_aut["jmeno"];
    $autori[$pole_data_aut["idu"]][1]='mailto:'.$pole_data_aut["email"];
  endwhile;
endif;

// nacteni seznamu temat do pole "rubriky"
$dotazrubriky=phprs_sql_query("select idt,nazev from ".$GLOBALS["rspredpona"]."topic order by idt",$GLOBALS["dbspojeni"]);
if ($dotazrubriky!==false&&phprs_sql_num_rows($dotazrubriky)>0):
  while($pole_data_rubriky=phprs_sql_fetch_assoc($dotazrubriky)):
    $rubriky[$pole_data_rubriky["idt"]]=$pole_data_rubriky["nazev"];
  endwhile;
endif;

// *** FAZE 2 *** - tvorba a sestaveni dotazu

$GLOBALS["rstext"]=stripslashes(trim($GLOBALS["rstext"])); // priprava hledaneho retezce - odstraneni zbytecnych mezer,tabelatoru,atd. + lomitka u spec. znaku
if ($GLOBALS["rstext"]==''):
  // prazdna promenna $rstext
  $pocetvysledek=0;
else:
  // inic. podminky
  $pole_obsah_podminka=array();

  // rozklad vyhledavaneho retezce - vysledkem je pole hledanych slov a retezcu $slova
  $slova=ZpracujHleStr($GLOBALS["rstext"]);

// zpracovani pole hledanych slov; kdyz je text "all-phpRS-all" tak se pozaduje uplny vypis a neni nutne dale retezec zpracovavat
  if (!in_array('all-phpRS-all',$slova)):
    // inic.
    $GLOBALS["rsvztah"]=phprs_sql_escape_string($GLOBALS["rsvztah"]);
    $pocet_slova=count($slova);
    if (($pocet_slova) != 0):
        $vysl_str_slova='';
        // vyhledavani omezene na nektera slova
        $vysl_str_slova.='(';
        for ($pom=0;$pom<$pocet_slova;$pom++):
            if ($pom>0): // vztah se neuvadi u prvniho prubehu
                $vysl_str_slova.=' '.$GLOBALS["rsvztah"].' ';
            endif;
            if (mb_substr($slova[$pom],0,1)=='-'): // test na negaci vyhledavaneho retezce
                $vysl_str_slova.=Prohledej('-',$GLOBALS["rskde"],mb_substr($slova[$pom],1,(mb_strlen($slova[$pom])-1)));
            else:
                $vysl_str_slova.=Prohledej('+',$GLOBALS["rskde"],$slova[$pom]);
            endif;
        endfor;
        $vysl_str_slova.=')';
        // ulozeni vysledku do podminkoveho pole
        $pole_obsah_podminka[]=$vysl_str_slova;
    endif;
  endif;


  // omezeni na tema
  if (isset($GLOBALS["rstema"])&&($GLOBALS["rstema"]!='nic')):
    // inic. $pomseznamu
    $pomseznam[0][0]=phprs_sql_escape_string($GLOBALS["rstema"]);
    $pomseznam[0][1]=1;  // umele zapnute rodicovstvi z duvodu neznamosti stavu
    $poc_pomseznam=1; // celkovy pocet polozek v $pomseznam
    $akt_poz_pomseznam=0; // akt. pozice v $pomseznam
    $vysl_str_temata='';
    $spojka_temata='';
    // sestaveni stromu temat
    while($poc_pomseznam>$akt_poz_pomseznam):
      // zapis temata do vysledku
      $vysl_str_temata.=$spojka_temata.$pomseznam[$akt_poz_pomseznam][0];
      $spojka_temata=",";
      // test na rodicovstvi ID_tema
      if ($pomseznam[$akt_poz_pomseznam][1]==1):
        $dotazpodtema=phprs_sql_query("select idt,rodic from ".$GLOBALS["rspredpona"]."topic where id_predka='".$pomseznam[$akt_poz_pomseznam][0]."'",$GLOBALS["dbspojeni"]);
        // zapis nove nalezenych podsekci do pole $pomseznam
        if ($dotazpodtema!==false&&phprs_sql_num_rows($dotazpodtema)):
          while ($pole_podtema = phprs_sql_fetch_assoc($dotazpodtema)):
            $pomseznam[$poc_pomseznam][0]=$pole_podtema['idt'];
            $pomseznam[$poc_pomseznam][1]=$pole_podtema['rodic'];
            $poc_pomseznam++;
          endwhile;
        endif;
      endif;
      // posunuti na dalsi pozici v seznamu
      $akt_poz_pomseznam++;
    endwhile;
    // ulozeni vysledku do podminkoveho pole
    $pole_obsah_podminka[]='c.tema IN ('.$vysl_str_temata.')';
  endif;

  // omezeni na autora
  if (isset($GLOBALS["rsautor"])&&($GLOBALS["rsautor"]!='nic')):
    // ulozeni vysledku do podminkoveho pole
    $pole_obsah_podminka[]="c.autor='".phprs_sql_escape_string($GLOBALS["rsautor"])."'";
  endif;

  // omezeni na datum
  if (isset($GLOBALS["rsod"])&&($GLOBALS["rsod"]!='nic')):
    if (!isset($GLOBALS["rsdo"])): $GLOBALS["rsdo"]=date("Y-m-d"); endif;
    // ulozeni vysledku do podminkoveho pole
    $pole_obsah_podminka[]="c.datum>='".phprs_sql_escape_string($GLOBALS["rsod"])."' AND c.datum<='".phprs_sql_escape_string($GLOBALS["rsdo"])."'";
  endif;

  // test na aktivnost ctenarskeho leveloveho subsystemu; zaroven musi byt vypnute pouziti zakazove sablony
  if ($hlidatlevel==1&&$zakazsab==0):
    $pole_obsah_podminka[]="l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."'"; // level ctenare musi byt vyssi nebo roven levelu clanku
  endif;

  // finalni vyhodnoceni podminky
  if (empty($pole_obsah_podminka)):
    $obsah_podminky=''; // neexistuji zadne podminky
  else:
    $obsah_podminky=implode(' AND ',$pole_obsah_podminka).' AND '; // existuje alespon jedna podminka
  endif;

  // dnesni datum - vypis je omezen pouze na clanky, ktere jsou starsi nez toto datum
  $dnesni_datum=Date("Y-m-d H:i:s");

  // dotaz na vsechny polozky odpovidajici zadane podmince
  $dotaz="SELECT count(c.idc) as pocet FROM ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l WHERE ".$obsah_podminky." c.visible=1 AND c.datum<='".$dnesni_datum."' AND c.level_clanku=l.idl";

  // debug
  //echo "<br>".$dotaz."<br>"; // testovanie
	
  $dotazpocet=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if ($dotazpocet!==false&&phprs_sql_num_rows($dotazpocet)==1):
    list($celkem_nalezeno_polozek)=phprs_sql_fetch_row($dotazpocet);
  else:
    $celkem_nalezeno_polozek=0;
  endif;

  if ($celkem_nalezeno_polozek==0):
    // vyhledavacimu dotazu neodpovida zadna polozka
    $pocetvysledek=0;
  else:
    // vypocet poctu obratek a akt. limitu
    if ($GLOBALS["rskolikata"]==1):
      $limit_start=0;
      $limit_kolik=phprs_sql_escape_string($GLOBALS["rskolik"]);
    else:
      $limit_start=$GLOBALS["rskolik"]*($GLOBALS["rskolikata"]-1);
      $limit_kolik=phprs_sql_escape_string($GLOBALS["rskolik"]);
    endif;

    // zpusob zobrazeni/serazeni vysledku
    switch($GLOBALS["rsrazeni"]):
      case 'datum_09': $dotaz_tridit='c.datum'; break;
      case 'datum_90': $dotaz_tridit='c.datum desc'; break;
      case 'nazev_az': $dotaz_tridit='c.titulek'; break;
      case 'nazev_za': $dotaz_tridit='c.titulek desc'; break;
      case 'priorita': $dotaz_tridit='c.priority desc, c.datum desc'; break;
      default: $dotaz_tridit='c.datum desc'; break;
    endswitch;
    // sestaveni kompletniho dotazu
    if ($GLOBALS['rsvelikost']=='sab'):
      $dotaz="SELECT c.idc,c.link,c.seo_link,c.titulek,c.uvod,c.text,c.tema,date_format(c.datum,'%d. %m. %Y') as vyslden,c.autor,c.kom,c.visit,c.t_slova,c.visible,c.zdroj,c.skupina_cl,c.znacky,c.typ_clanku,c.sablona,c.level_clanku,c.anketa_cl,l.hodnota as level_hodnota ";
      $dotaz.="FROM ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l WHERE ".$obsah_podminky." c.visible=1 AND c.datum<='".$dnesni_datum."' AND c.level_clanku=l.idl ORDER BY ".$dotaz_tridit." LIMIT ".(int)$limit_start.",".(int)$limit_kolik;
    else:
      $dotaz="SELECT c.link,c.seo_link,c.titulek,c.uvod,date_format(c.datum,'%d.%m.%Y') as vyslden,c.tema,c.autor,c.znacky ";
      $dotaz.="FROM ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l WHERE ".$obsah_podminky." c.visible=1 AND c.datum<='".$dnesni_datum."' AND c.level_clanku=l.idl ORDER BY ".$dotaz_tridit." LIMIT ".(int)$limit_start.",".(int)$limit_kolik;
    endif;
    // debug
    // echo "<br>".$dotaz."<br>"; // testovanie

    $dotazvysledek=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    $pocetvysledek=phprs_sql_num_rows($dotazvysledek);
  endif;
endif;

if (!is_int($pocetvysledek)):
  $pocetvysledek=ceil($pocetvysledek);
endif;

// *** FAZE 3 *** - tvorba vypisu

// test na vysledek vyhledavani
if ($pocetvysledek==0):
  // prazdny vysledek
  echo "<p align=\"center\" class=\"z\"><strong>".RS_VY_NULL."</strong></p>\n";
else:
  // existuje alespon jedna odpovidajici polozka
  echo "<p align=\"center\" class=\"z\"><strong>".RS_VY_VYSLEDEK_1." ".$pocetvysledek." ".RS_VY_VYSLEDEK_2." ".$celkem_nalezeno_polozek." ".RS_VY_VYSLEDEK_3."</strong></p>\n";

  // sestaveni navigacniho pasu
  $pom_vsechny_atributy='rstext='.htmlspecialchars($GLOBALS["rstext"]).'&amp;rsautor='.htmlspecialchars($GLOBALS["rsautor"]).'&amp;rstema='.$GLOBALS["rstema"].'&amp;rskde='.$GLOBALS["rskde"].'&amp;rsvelikost='.htmlspecialchars($GLOBALS["rsvelikost"]).'&amp;rsrazeni='.htmlspecialchars($GLOBALS["rsrazeni"]).$navigace_doplnek_linku;
  $navigace=SestavNavigacniListu($celkem_nalezeno_polozek,$GLOBALS["rskolik"],$GLOBALS["rskolikata"],'rskolik','rskolikata','search.php',$pom_vsechny_atributy);

  // 1. navigacni lista
  echo $navigace;

  // test na zpusob vypis vysledku vyhledavani
  switch ($GLOBALS['rsvelikost']):
    case 'sab':
      // *** vypis pres sablonu ***
      include_once("trclanek.php"); // nacteni tridy clanky

      $GLOBALS["clanek"] = new CClanek();
      $GLOBALS["clanek"]->NactiZdrojCla($dotazvysledek);
      $GLOBALS["clanek"]->HlidatLevel($hlidatlevel);
      $GLOBALS["clanek"]->NastavZakazovouSab($zakazsab);
      $GLOBALS["clanek"]->NastavLevelCtenare($GLOBALS["prmyctenar"]->UkazLevel());

      for ($pom=0;$pom<$GLOBALS["clanek"]->Ukaz("pocetclanku");$pom++):
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
        // volani sablony
        if ($GLOBALS["clanek"]->Ukaz("sablona")==""):
          // chybova hlaska: Chyba pri zobrazovani clanku cislo xxxx! System nemuze nalezt odpovidajici sablonu!
          echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["clanek"]->Ukaz("link")."! ".RS_IN_ERR1_2."<p>\n";
        else:
          include($GLOBALS["clanek"]->Ukaz("sablona")); // vlozeni sablony; pozor, musi byt povoleno vice-nasobne vlozeni sablony
        endif;
        $GLOBALS["clanek"]->DalsiRadek(); // prechod na dalsi radek
      endfor;
      // *** konec: vypis pres sablonu ***
    break;
    case 'uvod':
      // *** vypis s uvodnim textem ***
      include_once("trclanek.php"); // nacteni tridy clanky

      $GLOBALS["clanek"] = new CClanek();

      while ($pole_data = phprs_sql_fetch_assoc($dotazvysledek)):
        echo "<div class=\"z\">\n";
        echo "<strong><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></strong><br>\n";
        echo "(<i>";
        // kompilace autora
        if (isset($autori[$pole_data["autor"]][0])):
          echo "<a href=\"".$autori[$pole_data["autor"]][1]."\">".$autori[$pole_data["autor"]][0]."</a>, ";
        else:
          echo "<a href=\"".$GLOBALS["redakceadr"]."\">".RS_VY_REDAKCE."</a>, ";
        endif;
        // kompilace tematu
        if (isset($rubriky[$pole_data["tema"]])):
          echo $rubriky[$pole_data["tema"]].", ";
        endif;
        echo $pole_data["vyslden"]."</i>)<br>\n";
        if ($pole_data["znacky"]==1):
          echo $GLOBALS["clanek"]->Dekoduj($pole_data["uvod"]);
        else:
          echo $pole_data["uvod"];
        endif;
        echo "</div><br>\n";
      endwhile;
      // *** konec: vypis s uvodnim textem ***
    break;
    case 'jr':
      // *** jednoradkovy vypis ***
      echo "<table cellpadding=\"5\" border=\"0\" align=\"center\" class=\"z\">\n";
      if ($pocetvysledek>0): // prazdne vyhl.
        echo "<tr class=\"z\"><td align=\"left\"><b>".RS_VY_NAZEV_CLA."</b></td><td align=\"center\"><b>".RS_VY_DATUM_VYD."</b></td><td align=\"center\"><b>".RS_VY_AUTOR."</b></td><td align=\"left\"><b>".RS_VY_TEMA."</b></td></tr>\n";
      endif;
      for($pro=0;$pro<$pocetvysledek;$pro++):
        $pole_data=phprs_sql_fetch_assoc($dotazvysledek);
        echo "<tr class=\"z\"><td align=\"left\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a></td>\n";
        echo "<td align=\"center\">".$pole_data["vyslden"]."</td>\n";
        // kompilace autora
        if (isset($autori[$pole_data["autor"]][0])):
          echo "<td align=\"center\"><a href=\"".$autori[$pole_data["autor"]][1]."\">".$autori[$pole_data["autor"]][0]."</a></td>\n";
        else:
          echo "<td align=\"center\"><a href=\"".$GLOBALS["redakceadr"]."\">".RS_VY_REDAKCE."</a></td>\n";
        endif;
        // kompilace tematu
        if (isset($rubriky[$pole_data["tema"]])):
          echo "<td align=\"left\">".$rubriky[$pole_data["tema"]]."</td>";
        else:
          echo "<td align=\"left\">&nbsp;</td>";
        endif;
        echo "</tr>\n";
      endfor;
      echo "</table>\n";
      // *** konec: jednoradkovy vypis ***
    break;
  endswitch;

  // 2. navigacni lista
  echo $navigace;
  echo "<br>\n";
endif;
}

// --[zakladni fce - formular]------------------------------------------------------------------------

function VyhledavaniSeznamTemat($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik; vystup obsahuje uplnou cestu k jednotlivym rubrikam
$dotazsez=phprs_sql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic where zobrazit=1 order by level,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=phprs_sql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++): // nacteni pole informaci
    $pole_data=phprs_sql_fetch_assoc($dotazsez);
    $vstdata[$pom][0]=$pole_data['idt'];       // id
    $vstdata[$pom][1]=$pole_data['nazev'];     // nazev polozky
    $vstdata[$pom][2]=$pole_data['id_predka']; // id rodice
    $vstdata[$pom][3]=0;                       // prepinace pouzito pole
endfor;

if ($pocetsez>0): $trideni=1; else: $trideni=0; endif;

$polehist[0]=$pocatecnihodnota; // historie prohledavani
$polecesta[0]="";
$polex=0; // poloha v poly historie prohledavani

$vysledekcislo=0; // akt. volna posledni pozice ve vysledkovem poli

while ($trideni==1):
  $nasel=0; // 0 = prvek nenalezen, 1 = prvek nalezen

  for ($pom=0;$pom<$pocetsez;$pom++):
    if ($vstdata[$pom][3]==0): // kdyz nebylo akt. radek jeste pouzit
      if ($vstdata[$pom][2]==$polehist[$polex]): // kdyz nalezi hledanemu predku
            // ulozeni vysledku
            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0]; // id prvku
            $vysledek[$vysledekcislo][1]=$polecesta[$polex].$vstdata[$pom][1]; // nazev prvku
            $vysledek[$vysledekcislo][2]=$polex; // uroven vnoreni prvku
            // nastaveni dalsich promennych
            $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
            $vstdata[$pom][3]=1; // nastaveni prepinace na pouzito
            $polex++; // prechod na vyssi uroven v historii
            $polehist[$polex]=$vstdata[$pom][0];
            $polecesta[$polex]=$polecesta[$polex-1].$vstdata[$pom][1]." - ";
            $nasel=1;
            break;
      endif;
    endif;
  endfor;

  if ($nasel==0): // kdyz nebyl v celem poli nalezen zadny odpovidajici prvek
    if ($polehist[$polex]==$pocatecnihodnota):
      // vysledek hledani na zakladni urovni, ktera byla stanovena na zacatku, je prazdny -> neexistuje zadna dalsi vetev
      $trideni=0;
    else:
      $polex--; // prechod na nizsi uroven v historii
    endif;
  endif;
endwhile;

/*
   $vysledek[X][0] - id prkvu
               [1] - nazev prvku
               [2] - cislo urovne
*/
if ($pocetsez>0):
  return $vysledek;
else:
  return 0;
endif;
}

function VyhledavaniSeznamAutori()
{
$vysl='';

$dotazusr=phprs_sql_query("select idu,jmeno from ".$GLOBALS["rspredpona"]."user order by jmeno",$GLOBALS["dbspojeni"]);

if ($dotazusr!==false&&phprs_sql_num_rows($dotazusr)>0):
  while ($pole_data = phprs_sql_fetch_assoc($dotazusr)):
    $vysl.="<option value=\"".$pole_data['idu']."\">".$pole_data['jmeno']."</option>\n";
  endwhile;
endif;

return $vysl;
}

function VyhledavaniFormular()
{
echo "<p class=\"nadpis\">".RS_VY_NADPIS."</p>
<form action=\"search.php\" method=\"post\">
<table border=\"0\" align=\"center\">
<tr class=\"z\"><td>".RS_VY_HLE_TEXT."</td><td><input type=\"text\" name=\"rstext\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_AUTOR."</td><td><select name=\"rsautor\" size=\"1\">
<option value=\"nic\">".RS_VY_BEZ_OMEZENI."</option>\n";
echo VyhledavaniSeznamAutori();
echo "</select></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_TEMA."</td><td><select name=\"rstema\" size=\"1\">
<option value=\"nic\">".RS_VY_BEZ_OMEZENI."</option>";
$poletopic=VyhledavaniSeznamTemat();
if (is_array($poletopic)): // jen kdyz existuje vysledkove pole
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".$poletopic[$pom][0]."\">".$poletopic[$pom][1]."</option>\n";
  endfor;
endif;
echo "</select></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_OMEZIT_NA."</td><td><select name=\"rskde\" size=\"1\"><option value=\"vse\" selected>".RS_VY_CELY_CLA."</option><option value=\"txt\">".RS_VY_HLAVNI_CAST."</option><option value=\"tit\">".RS_VY_TITULEK."</option><option value=\"uvd\">".RS_VY_UVOD."</option><option value=\"tsl\">".RS_VY_DB_KLICU."</option></select></td></tr>
<tr class=\"z\"><td>".RS_VY_HLE_RAZENI."&nbsp; </td><td><select name=\"rsrazeni\" size=\"1\"><option value=\"datum_09\">".RS_VY_RADIT_DATUM_09."</option><option value=\"datum_90\" selected>".RS_VY_RADIT_DATUM_90."</option><option value=\"nazev_az\">".RS_VY_RADIT_NAZEV_AZ."</option><option value=\"nazev_za\">".RS_VY_RADIT_NAZEV_ZA."</option></select></td></tr>
<tr class=\"z\"><td>".RS_VY_VZTAH."&nbsp; </td><td><select name=\"rsvztah\" size=\"1\"><option value=\"OR\" selected>".RS_VY_VZT_NEBO."</option><option value=\"AND\">".RS_VY_VZT_A."</option></select></td></tr>
</table>
<div align=\"center\"><input type=\"submit\" value=\" ".RS_VY_TL_HLEDAT." \" class=\"tl\"></div>
</form>
<p></p>\n";
}

// test na pritomnost promenne "rsvelikost"
if (empty($GLOBALS["rsvelikost"])):
  $GLOBALS["rsvelikost"]='jr';
endif;

// Tvorba stranky
$vzhledwebu->Generuj();
if ($GLOBALS["rsvelikost"]!='sab'):
  ObrTabulka();  // Vlozeni layout prvku krome 'sab' modu
endif;

// detekce existence $rstext
if (isset($GLOBALS["rstext"])):
  Vyhledavani();
else:
  VyhledavaniFormular();
endif;

if ($GLOBALS["rsvelikost"]!='sab'):
  KonecObrTabulka();  // Vlozeni layout prvku krome 'sab' modu
endif;
$vzhledwebu->Generuj();
?>