<?php

######################################################################
# phpRS Administration Engine - Alias section 1.1.10
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_alias

/*
  Tento soubor zajistuje obsluhu funkce stankovy alias.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // strankovy alias
     case "ShowAlias": AdminMenu();
          echo "<h2 align=\"center\">".RS_ALI_ROZ_SPRAVA_ALI."</h2>";
          ShowAlias();
          break;
     case "AcAddAlias": AdminMenu();
          echo "<h2 align=\"center\">".RS_ALI_ROZ_ADD_ALI."</h2>";
          AcAddAlias();
          break;
     case "DelAlias": AdminMenu();
          echo "<h2 align=\"center\">".RS_ALI_ROZ_DEL_ALI."</h2>";
          DelAlias();
          break;
     case "EditAlias": AdminMenu();
          echo "<h2 align=\"center\">".RS_ALI_ROZ_EDIT_ALI."</h2>";
          EditAlias();
          break;
     case "AcEditAlias": AdminMenu();
          echo "<h2 align=\"center\">".RS_ALI_ROZ_EDIT_ALI."</h2>";
          AcEditAlias();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowAlias()
  AcAddAlias()
  DelAlias()
  EditAlias()
  AcEditAlias()
*/

function ShowAlias()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"#pri\" class=\"navigace\">".RS_ALI_SA_PRIDAT_ALI."</a></p>\n";

// vypis existujich aliasu
$dotazali=phprs_sql_query("select ida,alias,hodnota from ".$GLOBALS["rspredpona"]."alias order by ida desc",$GLOBALS["dbspojeni"]);
$pocetali=phprs_sql_num_rows($dotazali);
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr bgcolor=\"#E6E6E6\" class=\"txt\">
<td align=\"center\"><b>".RS_ALI_SA_ALIAS."</b></td>
<td align=\"center\"><b>".RS_ALI_SA_ADR_SB."</b></td>
<td align=\"center\"><b>".RS_ALI_SA_AKCE."</b></td></tr>\n";
if ($pocetali==0):
  // CHYBA: Databaze neobsahuje zadny odpovidajici zaznam!
  echo "<tr class=\"txt\"><td colspan=\"3\" align=\"center\"><b>".RS_ALI_SA_ZADNY_ALI."</b></td></tr>\n";
else:
  for ($pom=0;$pom<$pocetali;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazali);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["alias"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["hodnota"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditAlias&amp;modul=alias&amp;prida=".$pole_data["ida"]."\">".RS_ALI_SA_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelAlias&amp;modul=alias&amp;prida=".$pole_data["ida"]."\">".RS_ALI_SA_SMAZ."</a> / ";
    echo "<a href=\"showpage.php?name=".$pole_data["alias"]."\" target=\"_blank\">".RS_ALI_SA_PREVIEW."</a></b></td></tr>\n";
  endfor;
endif;
echo "</table>
<br>
<hr width=\"600\">\n";

// nadpis
echo "<a name=\"pri\"></a>
<p align=\"center\" class=\"txt\"><big><strong>".RS_ALI_SA_NADPIS_ADD_ALI."</strong></big></p>\n";

// formular pro pridani noveho aliasu - typ = sablona
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_ALI_SA_FORM_ALIAS."</b></td>
<td align=\"left\"><input type=\"text\" name=\"pralias\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_ALI_SA_FORM_ADR_SB." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnota\" size=\"45\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddAlias\"><input type=\"hidden\" name=\"modul\" value=\"alias\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>
<p align=\"center\" class=\"txt\"><sup>*</sup> ".RS_ALI_SA_FORM_INFO."</p>
<br>\n";
}

function AcAddAlias()
{
// bezpecnostni korekce
$GLOBALS["pralias"]=phprs_sql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);

// pridani polozky
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."alias values(null,'".$GLOBALS["pralias"]."','".$GLOBALS["prhodnota"]."','sablona')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error A1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_ALI_SA_OK_ADD_ALI."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowAlias&amp;modul=alias\">".RS_ALI_SA_ZPET."</a></p>\n";
}

function DelAlias()
{
// bezpecnostni korekce
$GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);

@$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."alias where ida='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error A2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_ALI_SA_OK_DEL_ALI."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowAlias&amp;modul=alias\">".RS_ALI_SA_ZPET."</a></p>\n";
}

function EditAlias()
{
// bezpecnostni kontrola
$GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);

// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowAlias&amp;modul=alias\" class=\"navigace\">".RS_ALI_SA_ZPET."</a></p>\n";

$dotazali=phprs_sql_query("select ida,alias,hodnota from ".$GLOBALS["rspredpona"]."alias where ida='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazali);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_ALI_SA_FORM_ALIAS."</b></td>
<td align=\"left\" width=\"300\"><input type=\"text\" name=\"pralias\" value=\"".$pole_data["alias"]."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_ALI_SA_FORM_ADR_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnota\" value=\"".$pole_data["hodnota"]."\" size=\"45\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditAlias\"><input type=\"hidden\" name=\"modul\" value=\"alias\">
<input type=\"hidden\" name=\"prida\" value=\"".$pole_data["ida"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditAlias()
{
// bezpecnostni korekce
$GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);
$GLOBALS["pralias"]=phprs_sql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);

// uprava polozky
@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."alias set alias='".$GLOBALS["pralias"]."', hodnota='".$GLOBALS["prhodnota"]."' where ida='".$GLOBALS["prida"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error A3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_ALI_SA_OK_EDIT_ALI."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowAlias&amp;modul=alias\">".RS_ALI_SA_ZPET."</a></p>\n";
}

?>