<?php

######################################################################
# phpRS Administration 1.7.6
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky:

/*
  Promenna $zobrazhlavicku umoznuje odstranit z vygenerovane stranky celou HTML hlavicku a ohranicujici HTML a BODY tagy.
*/

// nepovoli prihlaseni pomoci GET
if (isset($_GET['rsnewuser'])) {
  die('Nepovoleny pristup! / Hacking attempt!');
}

$rs_administrace=1; // promenna identifikujici administracni rozhrani
define('IN_CODE',true); // inic. ochranne konstanty

include_once('config.php');

/*
  test na konfiguraciu pouzitia ssl pre administraciu, 
  ak administracia vyzaduje HTTPS a HTTPS nie je momentalne aktivne, 
  presmerujem na HTTPS variant
*/ 
if (
	isset($GLOBALS['rsconfig']['ssl']) 
	&& 
	$GLOBALS['rsconfig']['ssl'] === true
	&&
	!isset($_SERVER['HTTPS'])
) {
	// presmerovanie na https
	header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);	
	die();
}



include_once('autor.php');
include_once('admin/astdlib.php');
include_once('lang/admin_cfg_sl.php');

// definice vykonneho souboru
define('RS_VYKONNYSOUBOR','admin.php');

function AdminMenu() {

	global $Uzivatel;
	
	$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin(); // test na admin opravneni
	
	// sestaveni dotazu
	if ($akt_je_admin==1):
	  // vsechny moduly
	  $dotaz="select idm,ident_modulu,all_prava_users,nazev_menu,link_menu,barva_bg from ".$GLOBALS["rspredpona"]."moduly_prava where blokovat_modul=0 order by poradi_menu";
	else:
	  // bez spec. admin modulu
	  $dotaz="select idm,ident_modulu,all_prava_users,nazev_menu,link_menu,barva_bg from ".$GLOBALS["rspredpona"]."moduly_prava where blokovat_modul=0 and jen_admin_modul=0 order by poradi_menu";
	endif;
	$dotazmoduly=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
	if ($dotazmoduly!==false):
	  $pocetmoduly=phprs_sql_num_rows($dotazmoduly); // pocet polozek
	else:
	  $pocetmoduly=0; // tabulka neexistuje
	endif;
	
	if ($pocetmoduly>0):
	  // inic. pomocnych poli
	  $pole_data=array();
	  $admenulink=array();
	  $admenutxt=array();
	  $admenubg=array();
	  $menuactive=array();
	  for ($pom=0;$pom<$pocetmoduly;$pom++):
	    $pole_data=phprs_sql_fetch_assoc($dotazmoduly);
	    if (isset($GLOBALS['modul']) && $pole_data['ident_modulu'] == $GLOBALS['modul']) {
	    	$menuactive[] = ' class="active_item"';
		} else {
			$menuactive[] = '';
		}
	    // test na moznost pristupu prihlaseneho uziv. - hromadny pristup / jednotlivy pristup
	    if ($pole_data['all_prava_users']==1): // test na hromadne povoleni
	      // vse OK
	      $admenulink[]=$pole_data['link_menu'].'&amp;modul='.$pole_data['ident_modulu'];
	      $admenutxt[]=$pole_data['nazev_menu'];
	      // test na podbarveni polozky v menu
	      if (empty($pole_data['barva_bg'])): $admenubg[]=''; else: $admenubg[]=' style="background-color:#'.$pole_data['barva_bg'].'"'; endif;
	    else:
	      if ($GLOBALS['Uzivatel']->OvereniPravBool($pole_data['ident_modulu'])==1): // test na konkretniho uziv.
	        // vse OK
	        $admenulink[]=$pole_data['link_menu'].'&amp;modul='.$pole_data['ident_modulu'];
	        $admenutxt[]=$pole_data['nazev_menu'];
	        // test na podbarveni polozky v menu
	        if (empty($pole_data['barva_bg'])): $admenubg[]=''; else: $admenubg[]=' style="background-color:#'.$pole_data['barva_bg'].'"'; endif;
	      endif;
	    endif;
	  endfor;
	endif;
	
	// inic. prom.
	$pocetpolozek=count($admenulink);
	$pocitadlo=0;
	
	// generovani menu
	$pocet = 1; // poloziek na riadok
	echo '
		<table id="menu" border="0" cellpadding="0" cellspacing="0">
	';
	for ($pom=0;$pom<$pocetpolozek;$pom++) {
		if (0==$pom%$pocet) {
			echo '
				<tr class="menu_item">
			';
		}
		echo '
					<td '.$admenubg[$pom].$menuactive[$pom].'>
						<a href="'.RS_VYKONNYSOUBOR.'?'.$admenulink[$pom].'">'.$admenutxt[$pom].'</a>
		';
	}
	if (0 != $pom%$pocet) {
		echo '
					<td colspan="'.$pom%$pocet.'" '.$style.'>&nbsp;
		';
	}
	echo '
				<tr>
					<td colspan="'.$pocet.'">
						<div class="loginprouzek">
							'.RS_ADM_NAVIG_LOGIN.": ".htmlspecialchars($Uzivatel->Ukaz("username")).' - '.Date("d.m.Y").'
						</div>
		</table>
	';
}

function Logo() {
	?>
	<br><br><br><br><br><div align="center"><img src="image/phprs_logo.gif" alt="Logo phpRS"></div><br><br><br>
	<?php
	include_once("version.php");
	echo Showphprsversion();
}

// nastaveni akt. jazyku
if (empty($GLOBALS['Uzivatel']->JazykRozhrani)):
  $GLOBALS['rsconfig']['akt_admin_lang']=$GLOBALS['rsconfig']['default_admin_lang'];
else:
  $GLOBALS['rsconfig']['akt_admin_lang']=$GLOBALS['Uzivatel']->JazykRozhrani;
endif;
// akt. adresa zakladniho administracniho slovniku
$akt_zakl_admin_sl='lang/'.$GLOBALS['rsconfig']['akt_admin_lang'].'/admin_sl_'.$GLOBALS['rsconfig']['akt_admin_lang'].'.php';
// test na existenci slovniku
if (file_exists($akt_zakl_admin_sl)==1):
  include_once($akt_zakl_admin_sl); // vlozeni slovniku
else:
  echo "<p align=\"center\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
endif;

// test na existenci promenne $zobrazhlavicku - povoluje/zakazuje zobrazeni HTML zahlavi a zapati
if (!isset($GLOBALS['zobrazhlavicku'])): $GLOBALS['zobrazhlavicku']=1; endif;

if ($GLOBALS['zobrazhlavicku']==1):
	header("X-Frame-Options: DENY"); 
	header('Content-Type: text/html; charset='.$GLOBALS['rsconfig']['kodovani']);
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
?>
<!DOCTYPE html>
<html>
<head>
 <title><?php echo RS_ADM_HTML_TITLE; ?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['rsconfig']['kodovani']; ?>">
 <meta name="author" content="Jiří Lukáš, phpRS community">
 <meta name="category" content="business">
 <link rel="stylesheet" href="image/admin.css" type="text/css">

 <script type="text/javascript" language="javascript">
 function setPointer(theRow, thePointerColor)
 {
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        var theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }

    return true;
 }
 </script>
</head>

<body>
<?php

endif; // konec $zobrazhlavicku

if (!isset($GLOBALS['akce'])||!isset($GLOBALS['modul'])):
  AdminMenu();
  Logo();
  include_once("admin/aoptimal.php"); // optimalizacni rutina
else:
  // test na volany modul
  $GLOBALS['akce']=phprs_sql_escape_string($GLOBALS['akce']);
  $GLOBALS['modul']=phprs_sql_escape_string($GLOBALS['modul']);
  $dotazmodul=phprs_sql_query("select idm,ident_modulu,fks_prava_users,all_prava_users,liclakce_menu from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$GLOBALS['modul']."' and blokovat_modul=0",$GLOBALS["dbspojeni"]);
  $pocetmodul=phprs_sql_num_rows($dotazmodul);
  // test na existenci modulu
  if ($pocetmodul==1):
    // modul existuje
    $akt_modul_pole=array();
    $akt_modul_pole=phprs_sql_fetch_assoc($dotazmodul);
    // akt. adresa k jazyk. slovniku akt. modulu
    $akt_admin_modul_sl='lang/'.$GLOBALS['rsconfig']['akt_admin_lang'].'/admin_sl_'.$akt_modul_pole['ident_modulu'].'_'.$GLOBALS['rsconfig']['akt_admin_lang'].'.php';
    // test na moznost pristupu prihlaseneho uziv. - hromadny pristup / jednotlivy pristup
    if ($akt_modul_pole['all_prava_users']==1): // test na hromadne povoleni
      // vse OK - muze se provest include souboru
      // test na existenci slovniku
      if (file_exists($akt_admin_modul_sl)==1):
        include_once($akt_admin_modul_sl); // vlozeni slovniku
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
      endif;
      // test na existenci modulu
      if (file_exists($akt_modul_pole['liclakce_menu'])==1):
        include_once($akt_modul_pole['liclakce_menu']); // vlozeni modulu
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_NE_EXIST."</p>\n";
      endif;
    else:
      if ($Uzivatel->OvereniPravBool($akt_modul_pole['ident_modulu'])==1): // test na konkretniho uziv.
        // vse OK - muze se provest include souboru
        // test na existenci slovniku
        if (file_exists($akt_admin_modul_sl)==1):
          include_once($akt_admin_modul_sl); // vlozeni slovniku
        else:
          echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_SL_NE_EXIST."</p>\n";
        endif;
        // test na existenci modulu
        if (file_exists($akt_modul_pole['liclakce_menu'])==1):
          include_once($akt_modul_pole['liclakce_menu']); // vlozeni modulu
        else:
          echo "<p align=\"center\" class=\"txt\">".RS_ADM_SB_NE_EXIST."</p>\n";
        endif;
      else:
        // uzivatel nema potrebna pristupova prava
        echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_PRAVA."</p>\n";
      endif;
    endif;
  else:
    // chyba pri identifikaci modulu
    if ($pocetmodul==0):
      // modul neexistuje
      echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_EXIST."</p>\n";
    else:
      // existuje vice modulu se stejnou identifikaci
      echo "<p align=\"center\" class=\"txt\">".RS_ADM_MODUL_NE_IDENT."</p>\n";
    endif;
  endif;
endif;

if ($GLOBALS['zobrazhlavicku']==1):
?>
</body>
</html>
<?php
endif;
?>