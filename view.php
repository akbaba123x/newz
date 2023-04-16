<?php

######################################################################
# phpRS View 1.5.1
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi ke kompletnimu zobrazeni clanku, ktery je identifikovan pomoci promenne $cisloclanku.
*/

// vyuzivane tabulky:

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("myweb.php");

// test na pritomnost promenne $cisloclanku
if (!isset($GLOBALS["cisloclanku"])):
  header("HTTP/1.0 404 Not Found");
  echo '
  <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
  <html><head>
  <meta http-equiv="Content-Type" content="text/html; charset='.$GLOBALS['rsconfig']['kodovani'].'">
  <title>404 Not Found</title>
  </head><body>
  <h1>Not Found</h1>
  <p>'.RS_VW_ERR1.'</p>
  <p>The requested URL was not found on this server.</p>
  <a href="'.$baseadr.'">Main page.</a>
  </body></html>';
  die();
else:
  $GLOBALS["cisloclanku"]=phprs_sql_escape_string($GLOBALS["cisloclanku"]);
endif;

// znamkovani/hodnoceni clanku
function Znamkuj($id_clanek = '',$znamka = 0)
{
// bezpecnostni korekce
$id_clanek=phprs_sql_escape_string($id_clanek);
$znamka=(int)$znamka;
// inic.
$hlasuj=1; // true

if (isset($_COOKIE["znamkovani"])):
  // kdyz kontrolni cookie existuje
  $vstup=base64_decode($_COOKIE["znamkovani"]);
  $zakazna=explode(":",$vstup);
  $pocet_zak=count($zakazna);
  for ($pom=0;$pom<$pocet_zak;$pom++):
    if ($zakazna[$pom]==$id_clanek):
      $hlasuj=0; // false
      break;
    endif;
  endfor;
  if ($hlasuj==1):
    $str_cookie=base64_encode($vstup.":".$id_clanek);
    setcookie("znamkovani",$str_cookie,time()+315360000); // odeslani cookie
  endif;
else:
  // kdyz kontrolni cookie neexistuje
  $str_cookie=base64_encode($id_clanek);
  setcookie("znamkovani",$str_cookie,time()+315360000); // odeslani cookie
endif;

if ($hlasuj==1):
  if ($znamka>0&&$znamka<6): // test na plastnost znamky: 1 - 5
    @phprs_sql_query("update ".$GLOBALS["rspredpona"]."clanky set hodnoceni=hodnoceni+".(int)$znamka.", mn_hodnoceni=mn_hodnoceni+1 where link='".$id_clanek."'",$GLOBALS["dbspojeni"]);
  endif;
endif;
}

include_once("trclanek.php");

$clanek = new CClanek();
$clanek->HlidatLevel(NactiConfigProm('hlidat_level',0));
$clanek->NastavZakazovouSab(NactiConfigProm('zobrazit_zakaz',0));
$clanek->NastavLevelCtenare($GLOBALS["prmyctenar"]->UkazLevel());
$vysledek_dotazu=$clanek->NactiClanek($GLOBALS["cisloclanku"]);

if ($vysledek_dotazu==1):
  if (TestNaOpakujiciIP('cla'.$GLOBALS["cisloclanku"],$GLOBALS['rsconfig']['cla_delka_omezeni'],$GLOBALS['rsconfig']['cla_max_pocet_opak'])==0):
    // navyseni pocitadla pristupu u zobrazeneho clanku
    phprs_sql_query("update ".$GLOBALS["rspredpona"]."clanky set visit=(visit+1) where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
  endif;

  // hodnoceni clanku
  if (isset($GLOBALS["hlasovani"])):
    Znamkuj($GLOBALS["cisloclanku"],$GLOBALS["hlasovani"]);
  endif;

  if ($clanek->Ukaz("sablona")==''):
    // chybova hlaska: Chyba pri zobrazovani clanku cislo XXXX! Syste nemuze nalezt odpovidajici sablonu!
    echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS["cisloclanku"]."! ".RS_IN_ERR1_2."<p>\n";
  else:
    // vlozeni systemovych promennych do layoutu
    $vzhledwebu->UlozPro("title",$clanek->Ukaz("titulek"));
    $vzhledwebu->UlozPro("keywords",$clanek->Ukaz("slovni_popis"));
    // tvorba stranky
    $vzhledwebu->Generuj();
    // urceni pozadovane varianty clankove sablony
	/*
    if ($GLOBALS["clanek"]->Ukaz("zakazova_sab")==1): // test na aplikaci zakazove varianty
      $rs_typ_clanku='zakazany';
    else:
      $rs_typ_clanku='cely';
    endif;
	*/
    if ($GLOBALS["clanek"]->Ukaz("zakazova_sab")==1): // test na aplikaci zakazove varianty
      $rs_typ_clanku='zakazany';
    else:
      if ($GLOBALS["clanek"]->Ukaz("typ_clanku")==2): // 1 - standardni, 2 - kratky
        $rs_typ_clanku='kratky';
      else:
        $rs_typ_clanku='cely';
      endif;
    endif;

    // nacteni sablony
    include_once($clanek->Ukaz("sablona"));
    $vzhledwebu->Generuj();
  endif;
else:
  // chybova hlaska: Chyba! Clanek cislo XXXX neexistuje!
  header("HTTP/1.0 404 Not Found");
  echo '
  <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
  <html><head>
  <meta http-equiv="Content-Type" content="text/html; charset='.$GLOBALS['rsconfig']['kodovani'].'">
  <title>404 Not Found</title>
  </head><body>
  <h1>Not Found</h1>
  <p>'.RS_VW_ERR2_1." ".htmlspecialchars($GLOBALS["cisloclanku"])." ".RS_VW_ERR2_2.'</p>
  <p>The requested URL was not found on this server.</p>
  <a href="'.$baseadr.'">Main page.</a>
  </body></html>';
  die();
endif;
?>