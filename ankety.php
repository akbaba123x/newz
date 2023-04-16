<?php

######################################################################
# phpRS Public Inquiry 1.5.6
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ankety, rs_odpovedi

/*
$akce: "view" - zobrazeni ankety
       "prehled" - vypis vsech anket
       "nehlasuj" - chybova hlaska pri zjisteni opakovaneho hlasovani (vnitrni presmerovani)
       "vysledek" - zobrazeni vysledku pod odhlasovani (vnitrni presmerovani)
       "chyba" - zobrazeni textu chyby (vnitrni presmerovani)
$cil:  "index" - presmerovani na home page
       "vysledek" - zobrazeni vysledku
       "url" - presmerovani na pozadovanou url adresu (urcuje promenna $cil_url)
       "ref" - presmerovani na referer - stranku ze ktere bylo hlasovani odeslano       
*/

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("myweb.php");


if (
	(isset($GLOBALS["anketa"]) && !ctype_digit($GLOBALS["anketa"]))
	||
	(isset($GLOBALS["hlas"]) && !ctype_digit($GLOBALS["hlas"]))
) {
	// chybny vstup
	echo "Nepovoleny pristup! / Hacking attempt!";
	die;
}

// test existence nezb.prom.
if (!isset($GLOBALS['akce'])): $GLOBALS['akce']='prehled'; endif;
if (!isset($GLOBALS['cil'])): $GLOBALS['cil']='vysledek'; endif;

// jen hlasovani
function Jenhlasuj($hlas = 0) {
	if ($hlas > 0) {
    	@$dotazhlasuj=phprs_sql_query("update ".$GLOBALS["rspredpona"]."odpovedi set pocitadlo=(pocitadlo+1) where ido = ".(int)$hlas, $GLOBALS["dbspojeni"]);
    	if ($dotazhlasuj === false):
      		return 0; // chyba
    	else:
      		return 1; // vse OK
    	endif;
    } else {
    	return 0; // chyba
    }
}

// test na uzamceni ankety - 1 = uzamcena, 0 = otevrena
function TestNaUzamceniAnk($ank = 0) {
	$dotazanketa=phprs_sql_query("select uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".(int)$ank."'",$GLOBALS["dbspojeni"]);
	if ($dotazanketa!==false&&phprs_sql_num_rows($dotazanketa)>0):
  		$pole_data=phprs_sql_fetch_assoc($dotazanketa);
  		return $pole_data['uzavrena']; // realny stav
	else:
  		return 1; // chyba: defaultne se hlasi "uzamcena"
	endif;
}

// nacteni ochranneho cookies
function AnkCookies_Nacti() {
	if (isset($_COOKIE["inquiry"])):
  		return base64_decode($_COOKIE["inquiry"]); // kontrolni cookies existuje
	else:
  		return ''; // kontrolni cookies neexistuje
	endif;
}

// test na opakovane volani jedne ankety
function AnkCookies_JeReload($test_str = '', $ank = 0) {
	$vysledek=0; // defaultne neobsahuje
	if ($test_str!=''):
  		$pom_pole=explode(":",$test_str);
  		if (is_array($pom_pole)):
    		if (in_array($ank,$pom_pole)):
      			$vysledek=1; // obsahuje anketu
    		endif;
  		endif;
	endif;
	return $vysledek;
}

// ulozeni ochranneho cookies
function AnkCookies_UlozAnk($test_str = '', $ank = 0) {
	$pom_cookie_str='';
	// pridani nove ankety do seznamu odhlasovanych anket
	if ($test_str==''):
  		$pom_cookie_str=$ank;
	else:
  		$pom_cookie_str.=':'.$ank;
	endif;
	// odeslani kontrolniho cookies
	$zakodovany_str=base64_encode($pom_cookie_str);
	setcookie("inquiry",$zakodovany_str,time()+315360000);
}

// zobrazeni ankety bez moznosti hlasovat
function ZobrazAnketu() {
	// zjisteni anketni otazky
	$dotazotazka=phprs_sql_query("select otazka,zobrazit,uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".(int)$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
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
  		$dotazcelkem=phprs_sql_query("select sum(pocitadlo) as soucet from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".(int)$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
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
  		$dotazodp=phprs_sql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".(int)$GLOBALS["anketa"]."' order by ido",$GLOBALS["dbspojeni"]);
		$pocetodp=phprs_sql_num_rows($dotazodp);
		// vypis odpovedi
		echo "<p class=\"anketa-std-otazka\">".$akt_pole_anketa['otazka']."</p>\n";
		echo "<div align=\"center\"><div class=\"anketa-std-ram\">\n";
		while ($pole_data = phprs_sql_fetch_assoc($dotazodp)):
			$akt_procento=$jedno_proc*$pole_data["pocitadlo"];
			echo "<div class=\"anketa-std-odpovedi\">".htmlspecialchars($pole_data["odpoved"])." <i>(".RS_AN_POCET_HLA.": ".htmlspecialchars($pole_data["pocitadlo"]).")</i><br />\n";
			echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\" />";
			echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\" />";
			echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\" /> (".Zo($akt_procento)." %)</div><br />\n";
		endwhile;
		echo "</div></div>\n";
		echo "<div align=\"center\" class=\"z\"><strong>".RS_AN_CELKEM_HLA.": ".htmlspecialchars($celkem_hlasu)."</strong></div>\n";
	else:
  		// chyba: Anketni subsystem neni schopen identifikovat nebo zobrazit vybranou anketu!
  		echo "<p align=\"center\" class=\"z\">".RS_AN_ERR2."</p>\n";
	endif;

	// navrat na prehled vsech anket
	echo "<p align=\"center\" class=\"z\"><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
	echo "<br>\n";
}

// zobrazeni ankety + moznost hlasovani
function ZobrazHlasAnketu()
{
// zjisteni anketni otazky
$dotazotazka=phprs_sql_query("select otazka,zobrazit,uzavrena from ".$GLOBALS["rspredpona"]."ankety where ida='".(int)$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
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
  $dotazcelkem=phprs_sql_query("select sum(pocitadlo) as soucet from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".(int)$GLOBALS["anketa"]."'",$GLOBALS["dbspojeni"]);
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
  $dotazodp=phprs_sql_query("select ido,odpoved,pocitadlo from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".(int)$GLOBALS["anketa"]."' order by ido",$GLOBALS["dbspojeni"]);
  $pocetodp=phprs_sql_num_rows($dotazodp);
  // vypis odpovedi
  echo "<form action=\"ankety.php\" method=\"post\">\n";
  echo "<p class=\"anketa-std-otazka\">".htmlspecialchars($akt_pole_anketa['otazka'])."</p>\n";
  echo "<div align=\"center\"><div class=\"anketa-std-ram\">\n";
  $checked_prvni_pol=0;
  while ($pole_data = phprs_sql_fetch_assoc($dotazodp)):
    $akt_procento=$jedno_proc*$pole_data["pocitadlo"];
    echo "<div class=\"anketa-std-odpovedi\">";
    if ($checked_prvni_pol==0):
      echo "<input type=\"radio\" name=\"hlas\" value=\"".htmlspecialchars($pole_data["ido"])."\" checked /> ";
      $checked_prvni_pol=1;
    else:
      echo "<input type=\"radio\" name=\"hlas\" value=\"".htmlspecialchars($pole_data["ido"])."\" /> ";
    endif;
    echo $pole_data["odpoved"]." <i>(".RS_AN_POCET_HLA.": ".htmlspecialchars($pole_data["pocitadlo"]).")</i><br />\n";
    echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_a.gif\" width=\"8\" height=\"15\" alt=\"\" />";
    echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_b.gif\" width=".ceil(3*$akt_procento)." height=\"15\" alt=\"\" />";
    echo "<img src=\"".$GLOBALS["adrobrlayoutu"]."line_c.gif\" width=\"8\" height=\"15\" alt=\"\" /> (".htmlspecialchars(Zo($akt_procento))." %)</div><br />\n";
  endwhile;
  echo "</div></div>\n";
  echo "<div align=\"center\" class=\"z\"><input type=\"submit\" value=\" ".RS_AN_TL_HLASUJ." \" class=\"tl\" /><br /><br /><strong>".RS_AN_CELKEM_HLA.": ".$celkem_hlasu."</strong></div>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"hlasuj\" /><input type=\"hidden\" name=\"anketa\" value=\"".htmlspecialchars($GLOBALS['anketa'])."\" />\n";
  echo "</form>\n";
else:
  // chyba: Anketni subsystem neni schopen identifikovat nebo zobrazit vybranou anketu!
  echo "<p align=\"center\" class=\"z\">".RS_AN_ERR2."</p>\n";
endif;

// navrat na prehled vsech anket
echo "<p align=\"center\" class=\"z\"><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
echo "<br>\n";
}

function Prehled()
{
// vypis vsech anket
$dotazankety=phprs_sql_query("select ida,otazka,datum,uzavrena from ".$GLOBALS["rspredpona"]."ankety where zobrazit=1 order by datum desc",$GLOBALS["dbspojeni"]);
$pocetankety=phprs_sql_num_rows($dotazankety);

echo "<div class=\"z\">\n";
for ($pom=0;$pom<$pocetankety;$pom++):
  $pole_data=phprs_sql_fetch_assoc($dotazankety);
  echo "<p>".htmlspecialchars($pole_data["otazka"])." (".MyDatetimeToDate($pole_data["datum"]).")";
  if ($pole_data["uzavrena"]==0):
    // moznost hlasovani
    echo " -> <b><a href=\"ankety.php?akce=view&amp;anketa=".(int)$pole_data["ida"]."\">".RS_AN_HLASUJ."</a></b>";
  else:
    // anketa je uzavrena
    echo " -> <b><a href=\"ankety.php?akce=vysledek&amp;anketa=".(int)$pole_data["ida"]."\">".RS_AN_BLOKACE."</a></b>";
  endif;
  echo "</p>\n";
endfor;
echo "</div>\n";
echo "<br>\n";
}

function Nehlasuj()
{
echo "<p align=\"center\" class=\"z\">".RS_AN_NELZE_HLASOVAT."<br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
echo "<br>\n";
}

function ZobrazChybu($info_str = '')
{
echo "<p align=\"center\" class=\"z\">".htmlspecialchars($info_str)."<br /><br /><a href=\"ankety.php\">".RS_AN_ZOBRAZ_VSE."</a></p>\n";
echo "<br>\n";
}

// inic. text chyba
$GLOBALS['ankteta_chyba_txt']='';

// odchyceni hlasovani
if ($GLOBALS['akce']=='hlasuj'):
  if (!isset($GLOBALS['hlas'])||!isset($GLOBALS['anketa'])):
    // chyba inic. faze
    $GLOBALS['akce']='chyba';
    $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR2; // chyba: Anketni subsystem neni schopen identifikovat nebo zobrazit vybranou anketu!
  else:
    $GLOBALS['hlas']=(int)$GLOBALS['hlas']; // id odpoved
    $GLOBALS['anketa']=(int)$GLOBALS['anketa']; // id anketa
    // test na zamceni ankety; 1 = zamcena, 0 = otevrena
    if (TestNaUzamceniAnk($GLOBALS['anketa'])==1):
      $GLOBALS['akce']='chyba';
      $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR3; // chyba: Vybrana anketa je jiz uzavrena!
    else:
      // test na opakujici se hlasovani
      $akt_obsah_cookies=AnkCookies_Nacti(); // nacteni ochranneho cookie
      // testovano pres cookies a pocitani IP adres
      if (AnkCookies_JeReload($akt_obsah_cookies,$GLOBALS['anketa'])==0&&TestNaOpakujiciIP('ank'.$GLOBALS['anketa'],$GLOBALS['rsconfig']['anketa_delka_omezeni'],$GLOBALS['rsconfig']['anketa_max_pocet_opak'])==0):
        // hlasovani povoleno
        if (Jenhlasuj($GLOBALS['hlas'])==1):
          // odhlasovano v poradku
          AnkCookies_UlozAnk($akt_obsah_cookies,$GLOBALS['anketa']);
          switch ($GLOBALS['cil']):
            case 'index':
              header('Location: index.php');
              exit();
              break;
            case 'ref':
              if (!empty($_SERVER['HTTP_REFERER'])):
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit();
              else:
                $GLOBALS['akce']='vysledek';
              endif;
              break;
            case 'vysledek':
              $GLOBALS['akce']='vysledek';
              break;
            case 'url':
              header('Location: '.$GLOBALS['cil_url']);
              exit();
              break;
          endswitch;
        else:
          // chyba pri hlasovani
          $GLOBALS['akce']='chyba';
          $GLOBALS['ankteta_chyba_txt']=RS_AN_ERR1;
        endif;
      else:
        // zjisteno opakujici se hlasovani
        $GLOBALS['akce']='nehlasuj';
      endif;
    endif;
  endif;
endif;

// tvorba stranky
$vzhledwebu->Generuj();
ObrTabulka();  // Vlozeni layout prvku

echo "<p class=\"nadpis\">".RS_AN_NADPIS."</p>\n";
// volba akce
switch ($GLOBALS['akce']):
  case 'view': ZobrazHlasAnketu(); break;
  case 'vysledek': ZobrazAnketu(); break;
  case 'prehled': Prehled(); break;
  case 'nehlasuj': Nehlasuj(); break;
  case 'chyba': ZobrazChybu($GLOBALS['ankteta_chyba_txt']); break;
endswitch;

// dokonceni tvorby stranky
KonecObrTabulka();   // Vlozeni layout prvku
$vzhledwebu->Generuj();
?>