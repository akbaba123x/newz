<?php

######################################################################
# phpRS Administration Engine - Weblinks section 1.3.2
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_links, rs_links_sekce

/*
  Tento soubor zajistuje obsluhu weblinks sekce.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // weblinks
     case "ShowLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_VIEW_LINKS."</h2>";
          ShowLink();
          break;
     case "AddLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_ADD_LINKS."</h2>";
          AddLink();
          break;
     case "AcAddLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_ADD_LINKS."</h2>";
          AcAddLink();
          break;
     case "DelLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_DEL_LINKS."</h2>";
          DelLink();
          break;
     case "EditLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_EDIT_LINKS."</h2>";
          EditLink();
          break;
     case "AcEditLink": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_EDIT_LINKS."</h2>";
          AcEditLink();
          break;
     // weblinks sekce
     case "ShowWebSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          ShowWebSek();
          break;
     case "AcAddWebSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          AcAddWebSek();
          break;
     case "NastWebSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          NastWebSek();
          break;
     case "DelWebSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          DelWebSek();
          break;
     case "EditWebSekt": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          EditWebSekt();
          break;
     case "AcEditWebSekt": AdminMenu();
          echo "<h2 align=\"center\">".RS_WLI_ROZ_SEKCE_LINKS."</h2>";
          AcEditWebSekt();
          break;
endswitch;

// ---[pomocne fce - weblinky]------------------------------------------------------

function OptWLSekce($hledam = 0)
{
$str='';

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."links_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);

if ($pocetsek==0):
  $str.="<option value=\"0\">".RS_WLI_WS_ERR_ZADNA_SEKCE."</option>";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    $str.="<option value=\"".$pole_data["ids"]."\"";
    if ($pole_data["ids"]==$hledam): $str.=" selected"; $nalezl=1; endif;
    $str.=">".$pole_data["nazev"]."</option>\n";;
  endwhile;
endif;

return $str;
}

// ---[hlavni fce - weblinky]-------------------------------------------------------

/*
  ShowLink()
  AddLink()
  AcAddLink()
  DelLink()
  EditLink()
  AcEditLink()
*/

function ShowLink()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;

$autori=new SezAutori();

// test na existenci nastaveni filtru
if (!isset($GLOBALS["prorderby"])): $GLOBALS["prorderby"]="datum"; endif;

// linky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddLink&amp;modul=links\" class=\"navigace\">".RS_WLI_SW_PRIDAT_LINKS."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\" class=\"navigace\">".RS_WLI_SW_SPRAVA_SEKCI."</a></p>\n";

// sestaveni navigacniho pasu
$dotazcelk=phprs_sql_query("select count(idl) as pocet from ".$GLOBALS["rspredpona"]."links",$GLOBALS["dbspojeni"]);
if ($dotazcelk!==false&&phprs_sql_num_rows($dotazcelk)):
  list($pocetcelk)=phprs_sql_fetch_row($dotazcelk);
else:
  $pocetcelk=0;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"ShowLink\"><input type=\"hidden\" name=\"modul\" value=\"links\">
<input type=\"hidden\" name=\"prorderby\" value=\"".$GLOBALS["prorderby"]."\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_WLI_SW_ZOBRAZ_LINKS." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_WLI_SW_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_WLI_SW_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
- ".RS_WLI_SW_CELK_POCET." ".$pocetcelk."
</td></tr>
</table>
</form>
<br>\n";

// zpusob trizeni weblinku
switch($GLOBALS["prorderby"]):
  case "id": $dotaz_orderby=' order by l.idl desc'; break;
  case "titulek": $dotaz_orderby=' order by l.titulek'; break;
  case "datum": $dotaz_orderby=' order by l.datum desc'; break;
  case "sekce": $dotaz_orderby=' order by s.nazev,l.datum desc'; break;
  default: $dotaz_orderby=' order by l.datum desc'; break;
endswitch;

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $dotaz_od=($GLOBALS["prmin"]-1); else: $dotaz_od=0; endif;
$dotaz_kolik=($GLOBALS["prmax"]-$dotaz_od);
if ($dotaz_kolik<0): $dotaz_kolik=0; endif;

// sestaveni dotazu
$dotaz="select l.idl,l.titulek,l.adresa,l.datum,l.kdo,s.nazev from ".$GLOBALS["rspredpona"]."links as l,".$GLOBALS["rspredpona"]."links_sekce as s ";
$dotaz.="where l.idsekce=s.ids".$dotaz_orderby." limit ".$dotaz_od.",".$dotaz_kolik;
$dotazlink=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetlink=phprs_sql_num_rows($dotazlink);

if ($pocetlink==0):
  // CHYBA: Zadany interval (od xxx do yyy) je prazdny!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" width=\"96%\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">\n";
  echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links&amp;prorderby=titulek&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\" class=\"zahlavi\"><b>".RS_WLI_SW_TITULEK."</b></a></td>\n";
  echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links&amp;prorderby=datum&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\" class=\"zahlavi\"><b>".RS_WLI_SW_DATUM."</b></a></td>\n";
  echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links&amp;prorderby=sekce&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\" class=\"zahlavi\"><b>".RS_WLI_SW_SEKCE."</b></a></td>\n";
  echo "<td align=\"center\"><b>".RS_WLI_SW_AUTOR."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_WLI_SW_AKCE."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_WLI_SW_SMAZ."</b></td>\n";
  echo "</tr>\n";
  for ($pom=0;$pom<$pocetlink;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazlink);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\"><a href=\"".$pole_data["adresa"]."\" target=\"_blank\">".$pole_data["titulek"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["datum"]."</td>";
    echo "<td align=\"center\">".$pole_data["nazev"]."</td>";
    echo "<td align=\"center\">";
    echo $autori->UkazUser($pole_data["kdo"]);
    echo "</td>";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditLink&amp;modul=links&amp;pridl=".$pole_data["idl"]."&amp;prorderby=".$GLOBALS["prorderby"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_WLI_SW_UPRAVIT."</a></td>";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idl"]."\"></td></tr>\n";
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"6\"><input type=\"submit\" value=\" ".RS_WLI_SW_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"DelLink\"><input type=\"hidden\" name=\"modul\" value=\"links\">\n";
  echo "</form>";
endif;
echo "<br>\n";
}

function AddLink()
{
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\" class=\"navigace\">".RS_WLI_SW_ZPET."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\" class=\"navigace\">".RS_WLI_SW_SPRAVA_SEKCI."</a></p>\n";

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_URL_ADR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"pradresa\" value=\"http://\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptWLSekce()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_WLI_SW_FORM_OBSAH."</b><br>
<textarea name=\"prkomentar\" rows=\"8\" cols=\"75\" class=\"textbox\">".RS_WLI_SW_FORM_OBSAH_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZOBRAZ_ZDROJ."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrzdr\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrzdr\" value=\"0\"> ".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZDROJ_DAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZDROJ_URL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojurl\" value=\"http://\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".Date("Y-m-d H:i:s")."\" size=\"25\" class=\"textpole\"><br>".RS_WLI_SW_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor\" size=\"1\">".OptAutori($GLOBALS["Uzivatel"]->IdUser)."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddLink\"><input type=\"hidden\" name=\"modul\" value=\"links\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcAddLink()
{
$chyba=0; // inic. chyby

// test na platnost hodnoce a jejich pripadna uprava
$bezpec_datum=OverDatum($GLOBALS["prdatum"]);
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);
if ($GLOBALS["przdrojurl"]=='http://'): $GLOBALS["przdrojurl"]=''; endif;

// bezpecnostni kontrola
$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["pradresa"]=phprs_sql_escape_string($GLOBALS["pradresa"]);
$GLOBALS["prkomentar"]=phprs_sql_escape_string($GLOBALS["prkomentar"]);
$GLOBALS["przdrojjm"]=phprs_sql_escape_string($GLOBALS["przdrojjm"]);
$GLOBALS["przdrojurl"]=phprs_sql_escape_string($GLOBALS["przdrojurl"]);

// test na platnost autora
if ($GLOBALS["prautor"]==0):
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_ERR_NEEXIST_AUTOR."</p>\n";
  $chyba=1;
endif;
// test na platnost sekce
if ($GLOBALS["prsekce"]==0):
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_ERR_NEEXIST_SEKCE."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  // sestaveni dotazu
  $dotaz="insert into ".$GLOBALS["rspredpona"]."links ";
  $dotaz.="values(null,'".$GLOBALS["prtitulek"]."','".$GLOBALS["pradresa"]."','".$GLOBALS["prkomentar"]."','".$GLOBALS["przdrojjm"]."',";
  $dotaz.="'".$GLOBALS["przdrojurl"]."','".$bezpec_datum."','".$GLOBALS["prautor"]."','".$GLOBALS["przobrzdr"]."','".$GLOBALS["prsekce"]."')";
  // dotaz
  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error W1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_OK_ADD_LINKS."</p>\n";
    // aktualizace polozky: posledni_zmena
    $nastaktdat=Date("U");
    @phprs_sql_query("update ".$GLOBALS["rspredpona"]."config set hodnota='".$nastaktdat."' where promenna='posledni_zmena'",$GLOBALS["dbspojeni"]);
    // konec aktualizace
  endif;
endif;

// navrat
ShowLink();
}

function DelLink()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS["prpoleid"])): // prazdny vyber
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_DEL_POCET_NULA."</p>\n";
  $pocet_id=0;
else:
  $pocet_id=count($GLOBALS["prpoleid"]); // pocet prvku v poli
endif;

if ($pocet_id>0): // existuji prvky k mazani
  for ($pom=0;$pom<$pocet_id;$pom++):
    @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."links where idl='".phprs_sql_escape_string($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error W2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1; // chyba!
    endif;
  endfor;
endif;

// globalni OK vysledek
if (($chyba==0)&&($pocet_id>0)):
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_OK_DEL_LINKS."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\">".RS_WLI_SW_ZPET."</a></p>\n";
}

function EditLink()
{
// bezpecnostni kontrola
$GLOBALS["pridl"]=phprs_sql_escape_string($GLOBALS["pridl"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links&amp;prorderby=".$GLOBALS["prorderby"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\" class=\"navigace\">".RS_WLI_SW_ZPET."</a></p>\n";

$dotazlink=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."links where idl='".$GLOBALS["pridl"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazlink);

// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_data['titulek']."\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_URL_ADR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"pradresa\" value=\"".$pole_data['adresa']."\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptWLSekce($pole_data['idsekce'])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_WLI_SW_FORM_OBSAH."</b><br>
<textarea name=\"prkomentar\" rows=\"8\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data['komentar'])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZOBRAZ_ZDROJ."</b></td>
<td align=\"left\">";
if ($pole_data['zobraz_zdroj']==1):
  echo "<input type=\"radio\" name=\"przobrzdr\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrzdr\" value=\"0\"> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrzdr\" value=\"1\"> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrzdr\" value=\"0\" checked> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZDROJ_DAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" value=\"".$pole_data['zdroj_jm']."\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_ZDROJ_URL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojurl\" value=\"".$pole_data['zdroj_url']."\" size=\"51\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_data['datum']."\" size=\"25\" class=\"textpole\"><br>".RS_WLI_SW_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_SW_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prkdo\" size=\"1\">".OptAutori($pole_data['kdo'])."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditLink\"><input type=\"hidden\" name=\"modul\" value=\"links\">
<input type=\"hidden\" name=\"pridl\" value=\"".$pole_data['idl']."\"><input type=\"hidden\" name=\"prorderby\" value=\"".$GLOBALS["prorderby"]."\">
<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\"><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditLink()
{
// test na platnost hodnoce a jejich pripadna uprava
$bezpec_datum=OverDatum($GLOBALS["prdatum"]);
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);

// bezpecnostni kontrola
$GLOBALS["pridl"]=phprs_sql_escape_string($GLOBALS["pridl"]);
$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["pradresa"]=phprs_sql_escape_string($GLOBALS["pradresa"]);
$GLOBALS["prkomentar"]=phprs_sql_escape_string($GLOBALS["prkomentar"]);
$GLOBALS["przdrojjm"]=phprs_sql_escape_string($GLOBALS["przdrojjm"]);
$GLOBALS["przdrojurl"]=phprs_sql_escape_string($GLOBALS["przdrojurl"]);

// sestaveni dotazu
$dotaz="update ".$GLOBALS["rspredpona"]."links ";
$dotaz.="set titulek='".$GLOBALS["prtitulek"]."',adresa='".$GLOBALS["pradresa"]."',komentar='".$GLOBALS["prkomentar"]."',zdroj_jm='".$GLOBALS["przdrojjm"]."',";
$dotaz.="zdroj_url='".$GLOBALS["przdrojurl"]."',datum='".$bezpec_datum."',kdo='".$GLOBALS["prkdo"]."',zobraz_zdroj='".$GLOBALS["przobrzdr"]."',";
$dotaz.="idsekce='".$GLOBALS["prsekce"]."' ";
$dotaz.="where idl='".$GLOBALS["pridl"]."'";
// dotaz
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error W3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_SW_OK_EDIT_LINKS."</p>\n";
endif;

// navrat
ShowLink();
}

// ---[hlavni fce - weblinks sekce]-------------------------------------------------

/*
  ShowWebSek()
  AcAddWebSek()
  NastWebSek()
  DelWebSek()
  EditWebSekt()
  AcEditWebSekt()
*/

function ShowWebSek()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\" class=\"navigace\">".RS_WLI_SW_ZPET."</a></p>\n";

$dotazsek=phprs_sql_query("select ids,nazev,hlavnisekce from ".$GLOBALS["rspredpona"]."links_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_WLI_WS_NAZEV."</b></td>
<td align=\"center\"><b>".RS_WLI_WS_HL_SEKCE."</b></td>
<td align=\"center\"><b>".RS_WLI_WS_AKCE."</b></td></tr>\n";
if ($pocetsek==0):
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"3\">".RS_WLI_WS_ZADNA_SEKCE."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["nazev"]."</td>\n";
    echo "<td align=\"center\"><input type=\"radio\" name=\"prids\" value=\"".$pole_data["ids"]."\"";
    if ($pole_data["hlavnisekce"]==1): echo " checked"; endif; // test na hlavni sekci
    echo "></td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditWebSekt&amp;modul=links&amp;prids=".$pole_data["ids"]."\">".RS_WLI_WS_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelWebSek&amp;modul=links&amp;prids=".$pole_data["ids"]."\">".RS_WLI_WS_SMAZ."</a></td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"3\"><input type=\"submit\" value=\" ".RS_WLI_WS_NASTAV_HL_SEKCI." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"NastWebSek\"><input type=\"hidden\" name=\"modul\" value=\"links\">
</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_WLI_WS_NASTAV_INFO."</p>\n";

// nadpis
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_WLI_WS_NADPIS_ADD."</strong></big></p>\n";
// formular na pridani sekce
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_WS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_WS_FORM_HL_SEKCE."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prhlavni\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"prhlavni\" value=\"0\"> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddWebSek\"><input type=\"hidden\" name=\"modul\" value=\"links\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcAddWebSek()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekrece
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prhlavni"]=phprs_sql_escape_string($GLOBALS["prhlavni"]);

@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."links_sekce values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prhlavni"]."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error W4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_WS_OK_ADD_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\">".RS_WLI_WS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\">".RS_WLI_SW_ZPET."</a></p>\n";
}

function NastWebSek()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=addslashes($GLOBALS["prids"]);

$chyba=0; // inic. chyby

@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."links_sekce set hlavnisekce='0'",$GLOBALS["dbspojeni"]);
if ($error === false): $chyba=1; endif;
@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."links_sekce set hlavnisekce='1' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false): $chyba=1; endif;

if ($chyba==1): // chyba
  echo "<p align=\"center\" class=\"txt\">Error W5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else: // vse OK
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_WS_OK_NASTAV_SEKCE."</p>\n";
endif;

// navrat
ShowWebSek();
}

function DelWebSek()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

$dotazpoclinku=phprs_sql_query("select count(idl) as pocet from ".$GLOBALS["rspredpona"]."links where idsekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($pocetpoclinku)=phprs_sql_fetch_row($dotazpoclinku); // pocet weblinku nalezicich do $prids sekce

if ($pocetpoclinku>0):
  // chyba - sekce neni prazdna
  echo "<p align=\"center\" class=\"txt\">Error W6: ".RS_WLI_WS_ERR_PLNA_SEKCE."</p>\n";
else:
  // neexistuje zadny odpovidajici weblink
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."links_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error W7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_WLI_WS_OK_DEL_SEKCE."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\">".RS_WLI_WS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\">".RS_WLI_SW_ZPET."</a></p>\n";
}

function EditWebSekt()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\" class=\"navigace\">".RS_WLI_WS_ZPET_SEKCE."</a></p>\n";

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."links_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazsek);

// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_WLI_WS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data['nazev']."\" size=\"40\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditWebSekt\"><input type=\"hidden\" name=\"modul\" value=\"links\">
<input type=\"hidden\" name=\"prids\" value=\"".$pole_data['ids']."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditWebSekt()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekrece
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);

@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."links_sekce set nazev='".$GLOBALS["prnazev"]."' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error W8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_WLI_WS_OK_EDIT_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowWebSek&amp;modul=links\">".RS_WLI_WS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLink&amp;modul=links\">".RS_WLI_SW_ZPET."</a></p>\n";
}

?>