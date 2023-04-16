<?php

######################################################################
# phpRS Administration Engine - Captcha kontrolni otazky v1.0.1
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_captcha_test_otazky

/*
  Tento soubor zajistuje obsluhu "spravy captcha kontrolnich otazek".
*/

// ---[rozcestnik]------------------------------------------------------------------

switch ($GLOBALS["akce"]):
     // captcha kontrolni otazky
     case "ShowCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_SPRAVA_OT."</h2>";
          ShowCaptchaOt();
          break;
     case "AddCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_ADD_OT."</h2>";
          AddCaptchaOt();
          break;
     case "AcAddCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_ADD_OT."</h2>";
          AcAddCaptchaOt();
          break;
     case "DelCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_DEL_OT."</h2>";
          DelCaptchaOt();
          break;
     case "EditCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_EDIT_OT."</h2>";
          EditCaptchaOt();
          break;
     case "AcEditCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_EDIT_OT."</h2>";
          AcEditCaptchaOt();
          break;
     case "NewIdentCaptchaOt": AdminMenu();
          echo "<h2 align=\"center\">".RS_COT_ROZ_GEN_NEW_IDENT."</h2>";
          NewIdentCaptchaOt();
          break;
endswitch;

// ---[pomocne fce]-----------------------------------------------------------------

function GenIdentOtazky($vstupni_atribut = '')
{
$vysl='';

// inic. generatoru
$interni_atribut=uniqid(rand(1,9999));
$ciselny_priznak=1;
$generuj_ident=1;

while ($generuj_ident==1):
  // vygenerovani noveho identifikatoru
  $vysl=strtolower(mb_substr(md5($interni_atribut.$vstupni_atribut.$ciselny_priznak),0,6));
  // kontrolni dotaz na duplicitu
  $dotaz="select idc from ".$GLOBALS["rspredpona"]."captcha_test_otazky where identifikator='".$vysl."'";
  $dotazpol=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotazpol)==0):
    // identifikator je jediny; mozno ukoncit generovani
    $generuj_ident=0;
  else:
    // navyseni ciselneho priznaku
    $ciselny_priznak++;
  endif;
endwhile;

return $vysl;
}

// ---[hlavni fce - captcha otazky]-------------------------------------------------

/*
  ShowCaptchaOt()
  AddCaptchaOt()
  AcAddCaptchaOt()
  DelCaptchaOt()
  EditCaptchaOt()
  AcEditCaptchaOt()
  NewIdentCaptchaOt()
*/

function ShowCaptchaOt()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddCaptchaOt&amp;modul=captchaotazky\" class=\"navigace\">".RS_COT_SC_PRIDAT_OT."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=NewIdentCaptchaOt&amp;modul=captchaotazky\" class=\"navigace\">".RS_COT_SC_GEN_IDENT_OT."</a></p>\n";

// dotaz
$dotaz="select idc,otazka,odpoved from ".$GLOBALS["rspredpona"]."captcha_test_otazky order by idc";
$dotazpol=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetpol=phprs_sql_num_rows($dotazpol);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_COT_SC_OTAZKA."</b></td>
<td align=\"center\"><b>".RS_COT_SC_ODPOVED."</b></td>
<td align=\"center\"><b>".RS_COT_SC_AKCE."</b></td>
<td align=\"center\"><b>".RS_COT_SC_SMAZ."</b></td></tr>\n";
if ($pocetpol==0):
  // chyba; databaze neobsahuje zadnou polozku
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_COT_SC_ZADNA_POLOZKA."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazpol)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\">".htmlspecialchars($pole_data["otazka"])."</td>";
    echo "<td align=\"center\">".htmlspecialchars($pole_data["odpoved"])."</td>";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditCaptchaOt&amp;modul=captchaotazky&amp;pridc=".htmlspecialchars($pole_data["idc"])."\">".RS_COT_SC_UPRAVIT."</a></td>";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".htmlspecialchars($pole_data["idc"])."\"></td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_COT_SC_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelCaptchaOt\"><input type=\"hidden\" name=\"modul\" value=\"captchaotazky\">
</form>
<br>\n";
}

function AddCaptchaOt()
{
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\" class=\"navigace\">".RS_COT_SC_ZPET."</a></p>\n";

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_KONT_OT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"protazka\" size=\"60\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_ODPOVED_OT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved\" size=\"20\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_POVOL_ZOBR."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE."</td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
<input type=\"hidden\" name=\"akce\" value=\"AcAddCaptchaOt\"><input type=\"hidden\" name=\"modul\" value=\"captchaotazky\">
</form>
<br>\n";
}

function AcAddCaptchaOt()
{
$chyba=0; // inic. chyby

// bezpecnostni korekce
$GLOBALS["protazka"]=KorekceNadpisu($GLOBALS["protazka"]);

$GLOBALS["protazka"]=phprs_sql_escape_string($GLOBALS["protazka"]);
$GLOBALS["prodpoved"]=phprs_sql_escape_string($GLOBALS["prodpoved"]);
$GLOBALS["przobrazit"]=phprs_sql_escape_string($GLOBALS["przobrazit"]);

// test na platnost kontrolni otazky
if (mb_strlen($GLOBALS["protazka"])==0):
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_ERR_NEEXIST_OTAZKA."</p>\n";
  $chyba=1;
endif;
// test na platnost odpovedi
if (mb_strlen($GLOBALS["prodpoved"])==0):
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_ERR_NEEXIST_ODPOVED."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  $nast_ident=phprs_sql_escape_string(GenIdentOtazky($GLOBALS["protazka"].$GLOBALS["prodpoved"]));

  $dotaz="insert into ".$GLOBALS["rspredpona"]."captcha_test_otazky values ";
  $dotaz.="(null,'".$nast_ident."','".$GLOBALS["protazka"]."','".$GLOBALS["prodpoved"]."','".$GLOBALS["przobrazit"]."')";

  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error COT1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_OK_ADD_OT."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\">".RS_COT_SC_ZPET."</a></p>\n";
}

function DelCaptchaOt()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS["prpoleid"])):
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_DEL_POCET_NULA."</p>\n"; // chyba; neoznacili jste ani jednu polozku
  $prpocetid=0;
else:
  $prpocetid=count($GLOBALS["prpoleid"]); // pocet prvku v poli
endif;

if ($prpocetid>0): // existuji prvky k mazani
  for ($pom=0;$pom<$prpocetid;$pom++):
    @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."captcha_test_otazky where idc='".phprs_sql_escape_string($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error COT2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1; // chyba!
    endif;
  endfor;
endif;

// globalni OK vysledek
if ($chyba==0&&$prpocetid>0):
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_OK_DEL_OT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\">".RS_COT_SC_ZPET."</a></p>\n";
}

function EditCaptchaOt()
{
// bezpecnostni korekce
$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\" class=\"navigace\">".RS_COT_SC_ZPET."</a></p>\n";

$dotazpol=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."captcha_test_otazky where idc='".$GLOBALS["pridc"]."'",$GLOBALS["dbspojeni"]);
if ($dotazpol!==false&&phprs_sql_num_rows($dotazpol)==1):
  $pole_data=phprs_sql_fetch_assoc($dotazpol);
else:
  $pole_data=array();
endif;

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_IDENT."</b></td>
<td align=\"left\">".htmlspecialchars($pole_data["identifikator"])."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_KONT_OT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"protazka\" size=\"60\" value=\"".htmlspecialchars($pole_data["otazka"])."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_ODPOVED_OT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved\" size=\"20\" value=\"".htmlspecialchars($pole_data["odpoved"])."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_COT_SC_FORM_POVOL_ZOBR."</b></td>
<td align=\"left\">";
if ($pole_data["zobrazit"]==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\"> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked> ".RS_TL_NE;
endif;
echo "</td></tr>
</table>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
<input type=\"hidden\" name=\"akce\" value=\"AcEditCaptchaOt\"><input type=\"hidden\" name=\"modul\" value=\"captchaotazky\">
<input type=\"hidden\" name=\"pridc\" value=\"".htmlspecialchars($GLOBALS["pridc"])."\">
</form>
<br>\n";
}

function AcEditCaptchaOt()
{
// bezpecnostni korekce
$GLOBALS["protazka"]=KorekceNadpisu($GLOBALS["protazka"]);

$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);
$GLOBALS["protazka"]=phprs_sql_escape_string($GLOBALS["protazka"]);
$GLOBALS["prodpoved"]=phprs_sql_escape_string($GLOBALS["prodpoved"]);
$GLOBALS["przobrazit"]=phprs_sql_escape_string($GLOBALS["przobrazit"]);

// dotaz - uprava polozky
$dotaz="update ".$GLOBALS["rspredpona"]."captcha_test_otazky set ";
$dotaz.="otazka='".$GLOBALS["protazka"]."', odpoved='".$GLOBALS["prodpoved"]."', zobrazit='".$GLOBALS["przobrazit"]."' ";
$dotaz.="where idc='".$GLOBALS["pridc"]."'";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error COT3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_OK_EDIT_OT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\">".RS_COT_SC_ZPET."</a></p>\n";
}

function NewIdentCaptchaOt()
{
// inic. chyba
$chyba=0;

// dotaz
$dotaz="select idc,otazka,odpoved from ".$GLOBALS["rspredpona"]."captcha_test_otazky order by idc";
$dotazpol=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetpol=phprs_sql_num_rows($dotazpol);

if ($pocetpol==0):
  // chyba; databaze neobsahuje zadnou polozku
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_ZADNA_POLOZKA."</p>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazpol)):
    $nast_ident=GenIdentOtazky($pole_data["otazka"].$pole_data["odpoved"]);
    // dotaz - uprava polozky
    $dotaz="update ".$GLOBALS["rspredpona"]."captcha_test_otazky set identifikator='".$nast_ident."' where idc='".$pole_data["idc"]."'";
    @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error COT4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endwhile;
endif;

// globalni OK vysledek
if ($chyba==0):
  echo "<p align=\"center\" class=\"txt\">".RS_COT_SC_OK_GEN_IDENT_OT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCaptchaOt&amp;modul=captchaotazky\">".RS_COT_SC_ZPET."</a></p>\n";
}

?>