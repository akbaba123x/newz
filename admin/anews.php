<?php

######################################################################
# phpRS Administration Engine - News section 1.2.8
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_news

/*
  Tento soubor zajistuje obsluhu "news systemu".
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // novinky
     case "ShowNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_SPRAVA_NEWS."</h2>";
          ShowNews();
          break;
     case "AddNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_ADD_NEWS."</h2>";
          AddNews();
          break;
     case "AcAddNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_ADD_NEWS."</h2>";
          AcAddNews();
          break;
     case "DelNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_DEL_NEWS."</h2>";
          DelNews();
          break;
     case "EditNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_EDIT_NEWS."</h2>";
          EditNews();
          break;
     case "AcEditNews": AdminMenu();
          echo "<h2 align=\"center\">".RS_NEW_ROZ_EDIT_NEWS."</h2>";
          AcEditNews();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowNews()
  AddNews()
  AcAddNews()
  DelNews()
  EditNews()
  AcEditNews()
*/

function ShowNews()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddNews&amp;modul=news\" class=\"navigace\">".RS_NEW_SN_PRIDAT_NEWS."</a></p>\n";

$dotaznews=phprs_sql_query("select idn,titulek,datum from ".$GLOBALS["rspredpona"]."news order by datum desc",$GLOBALS["dbspojeni"]);
$pocetnews=phprs_sql_num_rows($dotaznews);

// vypis novinek
if ($pocetnews==0):
  // CHYBA: Databaze neobsahuje zadny odpovidajici zaznam!
  echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_ZADNY_NEWS."</p>\n";
else:
  // celk. pocet novinek
  echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_CELK_POCET.": ".$pocetnews."</p>\n";
  // vypis novinek
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_NEW_SN_TITULEK."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_NEW_SN_DATUM."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_NEW_SN_AKCE."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_NEW_SN_SMAZ."</b></td></tr>\n";
  for ($pom=0;$pom<$pocetnews;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotaznews);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["titulek"]."</td>";
    echo "<td align=\"center\">".$pole_data["datum"]."</td>";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditNews&amp;modul=news&amp;pridn=".$pole_data["idn"]."\">".RS_NEW_SN_UPRAVIT."</a></td>";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idn"]."\"></td></tr>\n";
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_NEW_SN_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"DelNews\"><input type=\"hidden\" name=\"modul\" value=\"news\">\n";
  echo "</form>\n";
endif;

echo "<br>\n";
}

function AddNews()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowNews&amp;modul=news\" class=\"navigace\">".RS_NEW_SN_ZPET."</a></p>\n";

// pridavaci formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"52\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_NEW_SN_FORM_OBSAH."</b><br>
<textarea name=\"prtext\" rows=\"10\" cols=\"78\" class=\"textbox\">".RS_NEW_SN_FORM_OBSAH_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_TYP_NEWS."</b></td>
<td align=\"left\"><select name=\"prtypnov\" size=\"1\"><option value=\"0\">".RS_NEW_SN_FORM_BEZNA."</option><option value=\"1\">".RS_NEW_SN_FORM_VYRAZNA."</option></select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".MyDnesniDatum()."\" size=\"25\" class=\"textpole\"><br>".RS_NEW_SN_FORM_DATUM_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddNews\"><input type=\"hidden\" name=\"modul\" value=\"news\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcAddNews()
{
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);

$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["prtext"]=phprs_sql_escape_string($GLOBALS["prtext"]);
$GLOBALS["prtypnov"]=phprs_sql_escape_string($GLOBALS["prtypnov"]);
$GLOBALS["prdatum"]=phprs_sql_escape_string($GLOBALS["prdatum"]);

// pridani novinky
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."news values(null,'".$GLOBALS["prtitulek"]."','".$GLOBALS["prtext"]."','".$GLOBALS["prdatum"]."','".$GLOBALS["prtypnov"]."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error N1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_OK_ADD_NEWS."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowNews&amp;modul=news\">".RS_NEW_SN_ZPET."</a></p>\n";
}

function DelNews()
{
$chyba=0; // inic. chyby
if (!isset($GLOBALS["prpoleid"])): // inic. pole
 $pocet_pole_id=0;
else:
 $pocet_pole_id=count($GLOBALS["prpoleid"]);
endif;

// vymazani novinky
for ($pom=0;$pom<$pocet_pole_id;$pom++):
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."news where idn='".phprs_sql_escape_string($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error N2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
    $chyba=1;
  endif;
endfor;

// vyhodnoceni globalniho stavu
if ($chyba==0):
  if ($pocet_pole_id==0):
    echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_OK_DEL_NEWS_NIC."</p>\n";
  else:
    if ($pocet_pole_id==1):
      echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_OK_DEL_NEWS_JEDNA."</p>\n";
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_OK_DEL_NEWS_VICE."</p>\n";
    endif;
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowNews&amp;modul=news\">".RS_NEW_SN_ZPET."</a></p>\n";
}

function EditNews()
{
$GLOBALS["pridn"]=phprs_sql_escape_string($GLOBALS["pridn"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowNews&amp;modul=news\" class=\"navigace\">".RS_NEW_SN_ZPET."</a></p>\n";

// formular na upravu
$dotaznews=phprs_sql_query("select idn,titulek,informace,datum,typ_nov from ".$GLOBALS["rspredpona"]."news where idn='".$GLOBALS["pridn"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotaznews);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_data["titulek"]."\" size=\"52\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_NEW_SN_FORM_OBSAH."</b><br>
<textarea name=\"prtext\" rows=\"10\" cols=\"78\" class=\"textbox\">".KorekceHTML($pole_data["informace"])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_TYP_NEWS."</b></td>
<td align=\"left\"><select name=\"prtypnov\" size=\"1\">";
switch ($pole_data["typ_nov"]):
  case 0: echo "<option value=\"0\" selected>".RS_NEW_SN_FORM_BEZNA."</option><option value=\"1\">".RS_NEW_SN_FORM_VYRAZNA."</option>"; break;
  case 1: echo "<option value=\"0\">".RS_NEW_SN_FORM_BEZNA."</option><option value=\"1\" selected>".RS_NEW_SN_FORM_VYRAZNA."</option>"; break;
endswitch;
echo "</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_NEW_SN_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_data["datum"]."\" size=\"25\" class=\"textpole\"><br>".RS_NEW_SN_FORM_DATUM_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditNews\"><input type=\"hidden\" name=\"modul\" value=\"news\">
<input type=\"hidden\" name=\"pridn\" value=\"".$pole_data["idn"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditNews()
{
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);

$GLOBALS["pridn"]=phprs_sql_escape_string($GLOBALS["pridn"]);
$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["prtext"]=phprs_sql_escape_string($GLOBALS["prtext"]);
$GLOBALS["prtypnov"]=phprs_sql_escape_string($GLOBALS["prtypnov"]);
$GLOBALS["prdatum"]=phprs_sql_escape_string($GLOBALS["prdatum"]);

// uprava novinky
$dotaz="update ".$GLOBALS["rspredpona"]."news set ";
$dotaz.="titulek='".$GLOBALS["prtitulek"]."', informace='".$GLOBALS["prtext"]."', datum='".$GLOBALS["prdatum"]."', typ_nov='".$GLOBALS["prtypnov"]."' ";
$dotaz.="where idn='".$GLOBALS["pridn"]."'";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error N3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_NEW_SN_OK_EDIT_NEWS."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddNews&amp;modul=news\">".RS_NEW_SN_PRIDAT_NEWS."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowNews&amp;modul=news\">".RS_NEW_SN_ZPET."</a></p>\n";
}

?>