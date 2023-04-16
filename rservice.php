<?php

######################################################################
# phpRS Reader's service 1.3.9
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  Tento script slouzi k obsluze specialni ctenarskych sluzeb: zasilani informacniho mailu a tisk clanku.
*/

// vyuzivane tabulky: rs_clanky

define('IN_CODE',true); // inic. ochranne konstanty

include_once("config.php");
include_once("myweb.php");

// test na pritomnost promenne $akce
if (!isset($GLOBALS["akce"])):
  echo "<html><body><div align=\"center\">".RS_AKCE_ERR."</div></body></html>\n";
  exit();
endif;
// test na pritomnost promenne $cisloclanku
if (!isset($GLOBALS["cisloclanku"])):
  echo "<html><body><div align=\"center\">".RS_VW_ERR1."</div></body></html>\n";
  exit();
endif;

function CtenariTestNaAdresu($mail = '')
{
// tato funkce testuje platnost zadaneho e-mailu
if (preg_match('|^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$|',$mail)):
  return 1; // spravna struktura
else:
  return 0; // chybna struktura
endif;
}

function TiskClanku()
{
// bezpecnostni korekce
$GLOBALS['cisloclanku']=phprs_sql_escape_string($GLOBALS['cisloclanku']);

include_once('trclanek.php'); // vlozeni tridy na zpracovani clanku

$GLOBALS['clanek'] = new CClanek();
$GLOBALS['clanek']->HlidatLevel(NactiConfigProm('hlidat_level',0));
$GLOBALS['clanek']->NastavZakazovouSab(NactiConfigProm('zobrazit_zakaz',0));
$GLOBALS['clanek']->NastavLevelCtenare($GLOBALS["prmyctenar"]->UkazLevel());
$vysledek_dotazu=$GLOBALS['clanek']->NactiClanek($GLOBALS['cisloclanku']);

if ($vysledek_dotazu==1): // test na existenci clanku
  if ($GLOBALS['clanek']->Ukaz('zakazova_sab')==0): // test na uplatneni "zakazove sablony"
    // tvorba print stranky
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['rsconfig']['kodovani']; ?>">
  <title><?php echo $GLOBALS['wwwname']; ?></title>
  <?php echo $GLOBALS['layoutcss']; ?>
</head>

<body bgcolor="#FFFFFF">

<?php
    // sestaveni specialni tiskove clankove sablony
    $spec_tisk_sablona=$GLOBALS['adrobrlayoutu'].'cla_tisk.php';
    // test na existenci tiskove sablony
    if (file_exists($spec_tisk_sablona)):
      // vlozeni specialni tiskove clankove sablony
      include_once($spec_tisk_sablona);
    else:
      // CHYBA: Chyba pri zobrazovani clanku cislo XXX! System nemuze nalezt odpovidajici sablonu!
      echo "<p align=\"center\" class=\"z\">".RS_IN_ERR1_1." ".$GLOBALS['cisloclanku']."! ".RS_IN_ERR1_2."<p>\n";;
    endif;
?>
<p></p>

<div align="center">
<form>
<input type="button" value="<?php echo RS_CS_TISK; ?>" onclick="if (window.print()==0) { alert('<?php echo RS_CS_ERR_TISK; ?>'); }" class="tl" />
</form>
</div>
<p></p>

</body>
</html>
<?php
    // konec - tvorba print stranky
  else:
    // CHYBA: Chyba! Clanek cislo XXX neexistuje!
    echo "<p align=\"center\" class=\"z\">".RS_VW_ERR2_1." ".$GLOBALS['cisloclanku']." ".RS_VW_ERR2_2."<p>\n";
  endif;
else:
  // CHYBA: Chyba! Clanek cislo XXX neexistuje!
  echo "<p align=\"center\" class=\"z\">".RS_VW_ERR2_1." ".$GLOBALS['cisloclanku']." ".RS_VW_ERR2_2."<p>\n";
endif;
}

// test na typ akce
if ($GLOBALS["akce"]=='tisk'):
  TiskClanku();
endif;
?>