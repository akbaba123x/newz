<?php

######################################################################
# phpRS Administration Engine - Reader's section 1.3.6
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ctenari, rs_levely

/*
  Tento soubor zajistuje spravu registrovanych ctenaru.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // ctenari
     case "ShowReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2>";
          ShowReaders();
          break;
     case "AcReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2>";
          AcReaders();
          break;
     case "EditReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_EDIT_CTENAR."</h2>";
          EditReaders();
          break;
     case "AcEditReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_EDIT_CTENAR."</h2>";
          AcEditReaders();
          break;
     case "EditAllReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2>";
          EditAllReaders();
          break;
     case "AcEditAllReaders": AdminMenu();
          echo "<h2 align=\"center\">".RS_CTE_ROZ_SPRAVA_CTENARU."</h2>";
          AcEditAllReaders();
          break;
endswitch;

// ---[pomocne fce - ctenari]-------------------------------------------------------

// funkce vraci aktualni pocet vsech registrovanych ctenaru
function KolikCtenaru()
{
$dotazctenari=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."ctenari",$GLOBALS["dbspojeni"]);
if ($dotazctenari!==false&&phprs_sql_num_rows($dotazctenari)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazctenari);
  return $pole_data['pocet'];
else:
  return 0;
endif;
}

function OptLevely($hledam = 0)
{
$str='';

$dotazsab=phprs_sql_query("select idl,nazev_levelu,hodnota from ".$GLOBALS["rspredpona"]."levely order by hodnota,nazev_levelu",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);

if ($pocetsab==0):
  $str.="<option value=\"0\">".RS_CTE_POM_ERR_ZADNY_LEVEL."</option>\n"; // neni definovan zadny level
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
    $str.="<option value=\"".$pole_data['idl']."\"";
    if ($hledam==$pole_data['idl']): $str.=" selected"; endif;
    $str.=">".$pole_data['nazev_levelu']." (".$pole_data['hodnota'].")</option>\n";
  endwhile;
endif;

return $str;
}

// ---[hlavni fce - ctenari]--------------------------------------------------------

/*
  ShowReaders()
  AcReaders()
  EditReaders()
  AcEditReaders()
  EditAllReaders()
  AcEditAllReaders()
*/

// vypis vsech ctenaru nalezicich do zadaneho limitu
function ShowReaders()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=60; endif;
if (!isset($GLOBALS["prorderby"])): $GLOBALS["prorderby"]='reg'; endif;
if (!isset($GLOBALS["promezitret"])): $GLOBALS["promezitret"]=''; endif;
if (!isset($GLOBALS["promezitco"])): $GLOBALS["promezitco"]='user'; endif;

// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditAllReaders&amp;modul=ctenari\" class=\"navigace\">".RS_CTE_SC_HROMADNA_UPRAVA."</a></p>\n";

$pocet=KolikCtenaru(); // celkovy pocet ctenaru

echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"ShowReaders\"><input type=\"hidden\" name=\"modul\" value=\"ctenari\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_CTE_SC_ZOBRAZ_CTE." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_CTE_SC_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_CTE_SC_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
".RS_CTE_SC_TRIDIT." <select name=\"prorderby\" size=\"1\">\n";
switch ($GLOBALS["prorderby"]):
  case 'reg': echo "<option value=\"reg\" selected>".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\">".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\">".RS_CTE_SC_PREZDIVKY."</option>"; break;
  case 'posledni': echo "<option value=\"reg\">".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\" selected>".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\">".RS_CTE_SC_PREZDIVKY."</option>"; break;
  case 'user': echo "<option value=\"reg\">".RS_CTE_SC_DATA_REG."</option><option value=\"posledni\">".RS_CTE_SC_DATA_POSL_AKT."</option><option value=\"user\" selected>".RS_CTE_SC_PREZDIVKY."</option>"; break;
endswitch;
echo "</select> - ".RS_CTE_SC_POCET_CTE." ".$pocet."<br>
".RS_CTE_SC_HLEDAT_TEXT." <input type=\"text\" name=\"promezitret\" size=\"25\" value=\"".$GLOBALS["promezitret"]."\" class=\"textpole\"> ".RS_CTE_SC_V." <select name=\"promezitco\" size=\"1\">";
switch ($GLOBALS["promezitco"]):
  case 'user': echo "<option value=\"user\" selected>".RS_CTE_SC_HLEDAT_V_PREZDIVKA."</option><option value=\"email\">".RS_CTE_SC_HLEDAT_V_EMAIL."</option>"; break;
  case 'email': echo "<option value=\"user\">".RS_CTE_SC_HLEDAT_V_PREZDIVKA."</option><option value=\"email\" selected>".RS_CTE_SC_HLEDAT_V_EMAIL."</option>"; break;
  default: echo "<option value=\"user\" selected>".RS_CTE_SC_HLEDAT_V_PREZDIVKA."</option><option value=\"email\">".RS_CTE_SC_HLEDAT_V_EMAIL."</option>"; break;
endswitch;
echo "</select>
</td></tr>
</table>
</form>
<p></p>\n";

// zpusob trizeni ctenaru
switch ($GLOBALS["prorderby"]):
  case 'reg': $dotaz_orderby=' order by c.datum desc'; break; // dle data registrace
  case 'posledni': $dotaz_orderby=' order by c.posledni_login desc,c.datum desc'; break; // dle data posledni aktivity
  case 'user': $dotaz_orderby=' order by c.prezdivka'; break; // dle prezdivky
  default: $dotaz_orderby=' order by c.datum desc';
endswitch;

// omezeni na retezec
$prwhere=''; // inic
if (!empty($GLOBALS["promezitret"])):
  // identifikace omezeneho pole
  switch ($GLOBALS["promezitco"]):
    case 'user': $prwhere_pole='c.prezdivka'; break;
    case 'email': $prwhere_pole='c.email'; break;
    default: $prwhere_pole='c.prezdivka'; break;
  endswitch;
  // zpracovani retezce
  $GLOBALS["promezitret"]=phprs_sql_escape_string($GLOBALS["promezitret"]);
  $pole_slova=explode(" ",$GLOBALS["promezitret"]);
  $pocet_slov=count($pole_slova);
  if ($pocet_slov>0):
    $prwhere=" and ("; // start
    $spojka="";
    for ($p1=0;$p1<$pocet_slov;$p1++):
      $prwhere.=$spojka.$prwhere_pole." like ('%".$pole_slova[$p1]."%')";
      $spojka=" and ";
    endfor;
    $prwhere.=")"; // konec
  endif;
endif;

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $dotaz_od=($GLOBALS["prmin"]-1); else: $dotaz_od=0; endif;
$dotaz_kolik=($GLOBALS["prmax"]-$dotaz_od);
if ($dotaz_kolik<0): $dotaz_kolik=0; endif;

// sestaveni dotazu
$dotaz="select c.idc,c.prezdivka,c.password,c.jmeno,c.email,c.datum,c.info,c.posledni_login,l.nazev_levelu ";
$dotaz.="from ".$GLOBALS["rspredpona"]."ctenari as c, ".$GLOBALS["rspredpona"]."levely as l where c.level_ctenare=l.idl";
$dotaz.=$prwhere.$dotaz_orderby." limit ".$dotaz_od.",".$dotaz_kolik;
$dotazct=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetct=phprs_sql_num_rows($dotazct);

if ($pocetct==0):
  // CHYBA: Zadany interval (od xxx do yyy) je prazdny!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"smltxt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CTE_SC_FORM_ID_CTE."</b></td>";
  echo "<td align=\"left\"><b>".RS_CTE_SC_FORM_PREZDIVAK."</b></td>";
  echo "<td align=\"left\"><b>".RS_CTE_SC_FORM_JMENO."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_EMAIL."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_INFO_CTE."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_REG."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_POSL_AKT."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_LEVEL_CTE."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_AKCE."</b></td>";
  echo "<td align=\"center\"><b>".RS_CTE_SC_FORM_SMAZ."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetct;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazct);
    echo "<tr class=\"smltxt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"center\">".$pole_data['idc']."</td>\n";
    echo "<td align=\"center\">".htmlspecialchars($pole_data['prezdivka'],ENT_QUOTES)."</td>\n";
    echo "<td align=\"left\">".TestNaNic(htmlspecialchars($pole_data['jmeno'],ENT_QUOTES))."</td>\n";
    echo "<td align=\"left\">".TestNaNic(htmlspecialchars($pole_data['email'],ENT_QUOTES))."</td>\n";
    echo "<td align=\"left\">";
    if ($pole_data['info']==0):
      echo "<input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"1\"> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"0\" checked> ".RS_TL_NE;
    else:
      echo "<input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo[".$pom."]\" value=\"0\"> ".RS_TL_NE;
    endif;
    echo "<input type=\"hidden\" name=\"prinfopuvodni[".$pom."]\" value=\"".$pole_data['info']."\"></td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data['datum'])."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data['posledni_login'])."</td>\n";
    echo "<td align=\"center\">".$pole_data['nazev_levelu']."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditReaders&amp;modul=ctenari&amp;pridc=".$pole_data['idc']."\">".RS_CTE_SC_UPRAVIT."</a></td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoledelid[]\" value=\"".$pole_data['idc']."\">";
    echo "<input type=\"hidden\" name=\"prpoleid[".$pom."]\" value=\"".$pole_data['idc']."\"></td>\n";
    echo "</tr>\n";
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"10\"><input type=\"submit\" value=\" ".RS_CTE_SC_AKTUALIZACE." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcReaders\"><input type=\"hidden\" name=\"modul\" value=\"ctenari\">\n";
  echo "<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\"><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\">\n";
  echo "<input type=\"hidden\" name=\"promezitret\" value=\"".$GLOBALS["promezitret"]."\"><input type=\"hidden\" name=\"promezitco\" value=\"".$GLOBALS["promezitco"]."\">\n";
  echo "<input type=\"hidden\" name=\"prorderby\" value=\"".$GLOBALS["prorderby"]."\">\n";
  echo "</form>\n";
endif;
}

function AcReaders()
{
$chyba=0; // inic. chyby

// ------ uloz zmeny ------
if (!isset($GLOBALS["prpoleid"])): // kdyz neexistuje vstup
  $pocetpole=0;
else:
  $pocetpole=count($GLOBALS["prpoleid"]);
endif;

for ($pom=0;$pom<$pocetpole;$pom++):
  $GLOBALS["prinfo"][$pom]=addslashes($GLOBALS["prinfo"][$pom]); // korekce vstupu

  if ($GLOBALS["prinfopuvodni"][$pom]!=$GLOBALS["prinfo"][$pom]): // porovnani akt. nastaveni s puvodnim
    @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."ctenari set info='".$GLOBALS["prinfo"][$pom]."' where idc='".addslashes($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error R1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endif;
endfor;

// ------ smazani ------
if (!isset($GLOBALS["prpoledelid"])): // inic. prom.
  $pocetpoledel=0;
else:
  $pocetpoledel=count($GLOBALS["prpoledelid"]);
endif;

for ($pom=0;$pom<$pocetpoledel;$pom++):
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."ctenari where idc='".addslashes($GLOBALS["prpoledelid"][$pom])."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error R2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
    $chyba=1;
  endif;
endfor;

// ------ vysledek ------
if ($chyba==0): // vysledek globalniho stavu
  echo "<p align=\"center\" class=\"txt\">".RS_CTE_SC_OK_AKTUAL_CTE."</p>\n";
endif;

// navrat
ShowReaders();
}

function EditReaders()
{
// bezpecnostni kontrola
$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);

// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowReaders&amp;modul=ctenari\" class=\"navigace\">".RS_CTE_SC_ZPET."</a></p>\n";

$dotazctenari=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."ctenari where idc='".$GLOBALS["pridc"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazctenari);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_PREZDIVAK."</b></td>
<td align=\"left\">".htmlspecialchars($pole_data["prezdivka"],ENT_QUOTES)."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_REG."</b></td>
<td align=\"left\">".MyDateTimeToDateTime($pole_data["datum"])."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_POSL_AKT."</b></td>
<td align=\"left\">".MyDateTimeToDateTime($pole_data["posledni_login"])."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_JMENO."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".htmlspecialchars($pole_data["jmeno"],ENT_QUOTES)."\" size=\"60\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_EMAIL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"premail\" value=\"".htmlspecialchars($pole_data["email"],ENT_QUOTES)."\" size=\"60\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_INFO_CTE."</b></td>
<td align=\"left\">";
if ($pole_data['info']==1):
  echo "<input type=\"radio\" name=\"prinfo\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo\" value=\"0\"> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"prinfo\" value=\"1\"> ".RS_TL_ANO." <input type=\"radio\" name=\"prinfo\" value=\"0\" checked> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CTE_SC_FORM_LEVEL_CTE."</b></td>
<td align=\"left\"><select name=\"prlevel\" size=\"1\">".OptLevely($pole_data["level_ctenare"])."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditReaders\"><input type=\"hidden\" name=\"modul\" value=\"ctenari\">
<input type=\"hidden\" name=\"pridc\" value=\"".$pole_data["idc"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditReaders()
{
// bezpecnostni korekce
$GLOBALS["prjmeno"]=KorekceNadpisu($GLOBALS["prjmeno"]);
$GLOBALS["premail"]=KorekceNadpisu($GLOBALS["premail"]);

$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);
$GLOBALS["prjmeno"]=phprs_sql_escape_string($GLOBALS["prjmeno"]);
$GLOBALS["premail"]=phprs_sql_escape_string($GLOBALS["premail"]);
$GLOBALS["prinfo"]=phprs_sql_escape_string($GLOBALS["prinfo"]);
$GLOBALS["prlevel"]=phprs_sql_escape_string($GLOBALS["prlevel"]);

// uprava polozky
$dotaz="update ".$GLOBALS["rspredpona"]."ctenari set jmeno='".$GLOBALS["prjmeno"]."', email='".$GLOBALS["premail"]."', info='".$GLOBALS["prinfo"]."', ";
$dotaz.="level_ctenare='".$GLOBALS["prlevel"]."' ";
$dotaz.="where idc='".$GLOBALS["pridc"]."'";
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CTE_SC_OK_EDIT_CTE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowReaders&amp;modul=ctenari\">".RS_CTE_SC_ZPET."</a></p>\n";
}

function EditAllReaders()
{
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowReaders&amp;modul=ctenari\" class=\"navigace\">".RS_CTE_SC_ZPET."</a></p>\n";

// hromadne nastaveni levelu vsem ctenarum
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<div align=\"center\" class=\"txt\">
<strong>".RS_CTE_SC_FORM_VSEM_STEJNY_LEVEL.":</strong> <select name=\"prlevel\" size=\"1\">".OptLevely()."</select>
<input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\">
<input type=\"hidden\" name=\"akce\" value=\"AcEditAllReaders\"><input type=\"hidden\" name=\"modul\" value=\"ctenari\">
</div>
</form>\n";
}

function AcEditAllReaders()
{
// bezpecnostni korekce
$GLOBALS["prlevel"]=phprs_sql_escape_string($GLOBALS["prlevel"]);

// uprava polozky
$dotaz="update ".$GLOBALS["rspredpona"]."ctenari set level_ctenare='".$GLOBALS["prlevel"]."'";
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CTE_SC_OK_AKTUAL_CTE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowReaders&amp;modul=ctenari\">".RS_CTE_SC_ZPET."</a></p>\n";
}

?>