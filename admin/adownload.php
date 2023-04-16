<?php

######################################################################
# phpRS Administration Engine - Download's section 1.5.8
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_download, rs_download_sekce

/*
  Tento soubor zajistuje obsluhu "download sekce".
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // download
     case "ShowFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SHOW_FILES."</h2>";
          ShowFile();
          break;
     case "AddFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_ADD_FILES."</h2>";
          AddFile();
          break;
     case "AcAddFile": AdminMenu();
          include_once('admin/astdlib_file.php');
          echo "<h2 align=\"center\">".RS_FIL_ROZ_ADD_FILES."</h2>";
          AcAddFile();
          break;
     case "DelFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_DEL_FILES."</h2>";
          DelFile();
          break;
     case "EditFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_EDIT_FILES."</h2>";
          EditFile();
          break;
     case "AcEditFile": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_EDIT_FILES."</h2>";
          AcEditFile();
          break;
     // download sekce
     case "ShowFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          ShowFileSek();
          break;
     case "AcAddFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          AcAddFileSek();
          break;
     case "NastFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          NastFileSek();
          break;
     case "DelFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          DelFileSek();
          break;
     case "EditFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          EditFileSek();
          break;
     case "AcEditFileSek": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_SEKCE_FILES."</h2>";
          AcEditFileSek();
          break;
     // informacni mail
     case "InfoDownDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_POSTA_FILES."</h2>";
          InfoDownDopis();
          break;
     case "AcInfoDownDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_FIL_ROZ_POSTA_FILES."</h2>";
          include_once('admin/astdlib_mail.php'); // vlozeni STD. MAIL LIBRARY
          AcInfoDownDopis();
          break;
endswitch;

// ---[download - pomocne fce]------------------------------------------------------

function OptDwnSek($hledam = 0)
{
$vysl='';

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);
if ($pocetsek==0):
  $vysl.="<option value=\"0\">".RS_FIL_POM_ERR_ZADNA_SEKCE."</option>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    $vysl.="<option value=\"".$pole_data["ids"]."\"";
    if ($pole_data["ids"]==$hledam): $vysl.=" selected"; endif;
    $vysl.=">".$pole_data["nazev"]."</option>\n";
  endwhile;
endif;

return $vysl;
}

function OptDwnSekPlusNic($hledam = 0)
{
$vysl='';

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);
if ($pocetsek==0):
  $vysl.="<option value=\"0\">".RS_FIL_POM_ERR_ZADNA_SEKCE."</option>\n";
else:
  $vysl.="<option value=\"0\">".RS_FIL_POM_VSECHNY_SEKCE."</option>\n";
  while ($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    $vysl.="<option value=\"".$pole_data["ids"]."\"";
    if ($pole_data["ids"]==$hledam): $vysl.=" selected"; endif;
    $vysl.=">".$pole_data["nazev"]."</option>\n";
  endwhile;
endif;

return $vysl;
}

function OptLevely($hledam = 0)
{
$str='';

$dotazsab=phprs_sql_query("select idl,nazev_levelu,hodnota from ".$GLOBALS["rspredpona"]."levely order by hodnota,nazev_levelu",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);

if ($pocetsab==0):
  $str.="<option value=\"0\">".RS_FIL_POM_ERR_ZADNY_LEVEL."</option>\n"; // neni definovan zadny level
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
    $str.="<option value=\"".$pole_data['idl']."\"";
    if ($hledam==$pole_data['idl']): $str.=" selected"; endif;
    $str.=">".$pole_data['nazev_levelu']." (".$pole_data['hodnota'].")</option>\n";
  endwhile;
endif;

return $str;
}

// ---[download - hlavni fce]-------------------------------------------------------

/*
  ShowFile()
  AddFile()
  AcAddFile()
  DelFile()
  EditFile()
  AcEditFile()
*/

function ShowFile()
{
// test na existenci promennych
if (!isset($GLOBALS['prorderby'])): $GLOBALS['prorderby']='id'; endif;
if (!isset($GLOBALS['prsekce'])): $GLOBALS['prsekce']=0; endif;

// linky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddFile&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_PRIDAT_FILES."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_SPRAVA_SEKCI."</a></p>\n";

// sestaveni navigacniho pasu
$dotazcelk=phprs_sql_query("select count(idd) as pocet from ".$GLOBALS["rspredpona"]."download",$GLOBALS["dbspojeni"]);
if ($dotazcelk!==false&&phprs_sql_num_rows($dotazcelk)):
  list($pocetcelk)=phprs_sql_fetch_row($dotazcelk);
else:
  $pocetcelk=0;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"ShowFile\"><input type=\"hidden\" name=\"modul\" value=\"files\">
<input type=\"hidden\" name=\"prorderby\" value=\"".$GLOBALS['prorderby']."\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_FIL_SS_ZOBRAZ_SOUBORY." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_FLI_SS_OMEZIT_SEKCE." <select name=\"prsekce\" size=\"1\">".OptDwnSekPlusNic($GLOBALS['prsekce'])."</select>
- ".RS_FLI_SS_CELK_POCET." ".$pocetcelk."
</td></tr>
</table>
</form>
<br>\n";

// zpusob trizeni souboru
switch($GLOBALS['prorderby']):
  case "id": $dotaz_orderby=' order by d.idd desc'; break;
  case "nazev": $dotaz_orderby=' order by d.nazev'; break;
  case "soubor": $dotaz_orderby=' order by d.fjmeno'; break;
  case "datum": $dotaz_orderby=' order by d.datum desc'; break;
  case "pocet": $dotaz_orderby=' order by d.pocitadlo desc'; break;
  case "sekce": $dotaz_orderby=' order by s.nazev,d.datum desc'; break;
  default: $dotaz_orderby=' order by d.idd desc'; break;
endswitch;

// omezeni vypisu na sekci
$dotaz_where='';
if ($GLOBALS['prsekce']>0):
  $dotaz_where.=" and d.idsekce='".phprs_sql_escape_string($GLOBALS['prsekce'])."'";
endif;

// sestaveni dotazu
$dotaz="select d.idd,d.nazev,d.furl,d.fjmeno,d.fsize,date_format(d.datum,'%d. %m. %Y') as updatum,d.verze,d.pocitadlo,s.nazev as jmenosek,l.nazev_levelu ";
$dotaz.="from ".$GLOBALS['rspredpona']."download as d,".$GLOBALS['rspredpona']."download_sekce as s,".$GLOBALS['rspredpona']."levely as l ";
$dotaz.="where d.idsekce=s.ids and d.level_souboru=l.idl".$dotaz_where.$dotaz_orderby;
// dotaz na DB
$dotazsoubory=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetsoubory=phprs_sql_num_rows($dotazsoubory);

// vypis existujich souboru
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" width=\"96%\" class=\"ramsedy\">
<tr class=\"smltxt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prorderby=nazev&amp;prsekce=".$GLOBALS['prsekce']."\" class=\"zahlavi\"><b>".RS_FIL_SS_NAZEV."</b></a></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prorderby=soubor&amp;prsekce=".$GLOBALS['prsekce']."\" class=\"zahlavi\"><b>".RS_FIL_SS_SOUBOR."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_VELIKOST."</b></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prorderby=datum&amp;prsekce=".$GLOBALS['prsekce']."\" class=\"zahlavi\"><b>".RS_FIL_SS_DATUM."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_VERZE."</b></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prorderby=pocet&amp;prsekce=".$GLOBALS['prsekce']."\" class=\"zahlavi\"><b>".RS_FIL_SS_POCET_STAZ."</b></a></td>
<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files&amp;prorderby=sekce&amp;prsekce=".$GLOBALS['prsekce']."\" class=\"zahlavi\"><b>".RS_FIL_SS_SEKCE."</b></a></td>
<td align=\"center\"><b>".RS_FIL_SS_LEVEL_SB."</b></td>
<td align=\"center\"><b>".RS_FIL_SS_AKCE."</b></td>
<td align=\"center\"><b>".RS_FIL_SS_SMAZ."</b></td></tr>\n";
if ($pocetsoubory==0):
  echo "<tr class=\"smltxt\"><td colspan=\"10\" align=\"center\"><b>".RS_FIL_SS_ZADNY_FILES."</b></td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsoubory)):
    echo "<tr class=\"smltxt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["nazev"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".$pole_data["furl"]."\" target=\"_blank\">".$pole_data["fjmeno"]."</a></td>\n";
    echo "<td align=\"center\">".$pole_data["fsize"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["updatum"]."</td>\n";
    echo "<td align=\"center\">".TestNaNic($pole_data["verze"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["pocitadlo"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["jmenosek"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["nazev_levelu"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditFile&amp;modul=files&amp;pridd=".$pole_data["idd"]."\">".RS_FIL_SS_UPRAVIT."</a></td>";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idd"]."\"></td></tr>\n";
  endwhile;
  echo "<tr class=\"smltxt\"><td align=\"right\" colspan=\"10\"><input type=\"submit\" value=\" ".RS_FIL_SS_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelFile\"><input type=\"hidden\" name=\"modul\" value=\"files\">
</form>
<br>\n";
}

function AddFile()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_ZPET."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_SPRAVA_SEKCI."</a></p>\n";

$pom_zdroj_adresa=$GLOBALS['baseadr'].'storage/'; // pomocna zdrojova adresa

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\" enctype=\"multipart/form-data\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptDwnSek(0)."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_FIL_SS_FORM_OBSAH."</b><br>
<textarea name=\"prkomentar\" rows=\"6\" cols=\"75\" class=\"textbox\">".RS_FIL_SS_FORM_OBSAH_INFO."</textarea></td></tr>
<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><hr></td></tr>
<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><b>".RS_FIL_SS_FORM_VLOZENI_SB_INFO."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_URL_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" value=\"".$pom_zdroj_adresa."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_UPLOAD_SB."</b></td>
<td align=\"left\"><input type=\"file\" name=\"prupload\" value=\"\" size=\"35\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><hr></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VELIKOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prsize\" value=\"kB\" size=\"10\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" value=\"-\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ADR_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojadr\" value=\"http:// nebo mailto:\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VERZE."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prverze\" size=\"10\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SLOVNI_KAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prkat\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_LEVEL_SB."</b></td>
<td align=\"left\"><select name=\"prlevel\" size=\"1\">".OptLevely()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ZOBRAZIT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddFile\"><input type=\"hidden\" name=\"modul\" value=\"files\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\"></p>
</form>
<p align=\"center\" class=\"txt\"><sup>*</sup> ".RS_FIL_SS_ZADNY_ZDROJ_INFO."</p>
<br>\n";
}

function AcAddFile()
{
// bezpecnostni korekce
$GLOBALS['prnazev']=phprs_sql_escape_string($GLOBALS['prnazev']);
$GLOBALS['prkomentar']=phprs_sql_escape_string($GLOBALS['prkomentar']);
$GLOBALS['prurl']=phprs_sql_escape_string($GLOBALS['prurl']);
$GLOBALS['prjmeno']=phprs_sql_escape_string($GLOBALS['prjmeno']);
$GLOBALS['prsize']=phprs_sql_escape_string($GLOBALS['prsize']);
$GLOBALS['przdrojjm']=phprs_sql_escape_string($GLOBALS['przdrojjm']);
$GLOBALS['przdrojadr']=phprs_sql_escape_string($GLOBALS['przdrojadr']);
$GLOBALS['prverze']=phprs_sql_escape_string($GLOBALS['prverze']);
$GLOBALS['prkat']=phprs_sql_escape_string($GLOBALS['prkat']);
$GLOBALS['prlevel']=phprs_sql_escape_string($GLOBALS['prlevel']);
$GLOBALS['przobrazit']=phprs_sql_escape_string($GLOBALS['przobrazit']);
$GLOBALS['prsekce']=phprs_sql_escape_string($GLOBALS['prsekce']);

$dnesnidatum=Date("Y-m-d H:i:s");

// zpracovani uploadu souboru
$nast_furl='';
if (isset($_FILES['prupload'])):
  $vysledek_uploadu=StdUploadSoubor('prupload',$GLOBALS['rsconfig']['file_adresar'],'sb');
  if ($vysledek_uploadu['stav']==1):
    $nast_furl=$vysledek_uploadu['cesta_sb']; // vse OK; soubor uploadovan
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_ADD_FILES_UPLOAD."</p>\n";
  else:
    if($vysledek_uploadu['chyba']==1):
      echo $vysledek_uploadu['chyba_popis']; // chyba pri uploadu nebo zpracovani
    else:
      $nast_furl=$GLOBALS['prurl']; // nepouzit primy upload; plati zapis URL adresy
    endif;
  endif;
else:
  $nast_furl=$GLOBALS['prurl']; // nepouzit primy upload; plati zapis URL adresy
endif;
if(!isset($vysledek_uploadu['chyba']) || $vysledek_uploadu['chyba']!=1) {
	// sestaveni dotazu
	$dotaz="insert into ".$GLOBALS['rspredpona']."download values ";
	$dotaz.="(null,'".$GLOBALS['prnazev']."','".$GLOBALS['prkomentar']."','".$nast_furl."','".$GLOBALS['prjmeno']."','".$GLOBALS['prsize']."','".$GLOBALS['przdrojjm']."',";
	$dotaz.="'".$GLOBALS['przdrojadr']."','".$dnesnidatum."','".$GLOBALS['prverze']."','".$GLOBALS['prkat']."',0,'".$GLOBALS['przobrazit']."','".$GLOBALS['prsekce']."',";
	$dotaz.="'".$GLOBALS['prlevel']."')";
	// pridani polozky
	@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
	if ($error === false):
	  echo "<p align=\"center\" class=\"txt\">Error D1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
	else:
	  $cislosouboru=phprs_sql_insert_id();
	  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_ADD_FILES."</p>\n";
	  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=InfoDownDopis&amp;modul=files&amp;cislosouboru=".$cislosouboru."\">".RS_FIL_SS_ODESLAT_MAIL."</a></p>\n";
	endif;
}
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function DelFile()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS["prpoleid"])): // test na prazdny vyber
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_DEL_POCET_NULA."</p>\n";
  $chyba=1;
else:
  $pocet_id=count($GLOBALS["prpoleid"]); // pocet prvku v poli
  for ($pom=0;$pom<$pocet_id;$pom++):
    @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."download where idd=".phprs_sql_escape_string($GLOBALS["prpoleid"][$pom]),$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error D2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endfor;
endif;

// globalni vysledek
if ($chyba==0):
  if ($pocet_id>1): // mnoz. cislo
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_DEL_FILES_VICE."</p>\n";
  else: // jednotne cislo
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_DEL_FILES."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function EditFile()
{
// bezpecnostni korekce
$GLOBALS["pridd"]=phprs_sql_escape_string($GLOBALS["pridd"]);

// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_ZPET."</a></p>\n";

// dotaz na data
$dotazsoubor=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."download where idd=".$GLOBALS["pridd"],$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazsoubor);
// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data["nazev"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SEKCE."</b></td>
<td align=\"left\"><select name=\"prsekce\" size=\"1\">".OptDwnSek($pole_data["idsekce"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_FIL_SS_FORM_OBSAH."</b><br>
<textarea name=\"prkomentar\" rows=\"6\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["komentar"])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_URL_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prurl\" value=\"".$pole_data["furl"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_SB."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".$pole_data["fjmeno"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VELIKOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prsize\" value=\"".$pole_data["fsize"]."\" size=\"10\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_JMENO_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojjm\" value=\"".$pole_data["zdroj_jm"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ADR_ZDROJ." <sup>*</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"przdrojadr\" value=\"".$pole_data["zdroj_adr"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_data["datum"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_VERZE."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prverze\" value=\"".$pole_data["verze"]."\" size=\"10\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_SLOVNI_KAT."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prkat\" value=\"".$pole_data["kat"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_POCET."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prpocitadlo\" value=\"".$pole_data["pocitadlo"]."\" size=\"10\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_LEVEL_SB."</b></td>
<td align=\"left\"><select name=\"prlevel\" size=\"1\">".OptLevely($pole_data["level_souboru"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_SS_FORM_ZOBRAZIT."</b></td>
<td align=\"left\">";
if ($pole_data["zobraz"]==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\"> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked> ".RS_TL_NE;
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditFile\"><input type=\"hidden\" name=\"modul\" value=\"files\">
<input type=\"hidden\" name=\"pridd\" value=\"".$pole_data["idd"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>
<p align=\"center\" class=\"txt\"><sup>*</sup> ".RS_FIL_SS_ZADNY_ZDROJ_INFO."</p>
<br>\n";
}

function AcEditFile()
{
// bezpecnostni korekce
$GLOBALS["pridd"]=phprs_sql_escape_string($GLOBALS["pridd"]);
$GLOBALS['prnazev']=phprs_sql_escape_string($GLOBALS['prnazev']);
$GLOBALS['prkomentar']=phprs_sql_escape_string($GLOBALS['prkomentar']);
$GLOBALS['prurl']=phprs_sql_escape_string($GLOBALS['prurl']);
$GLOBALS['prjmeno']=phprs_sql_escape_string($GLOBALS['prjmeno']);
$GLOBALS['prsize']=phprs_sql_escape_string($GLOBALS['prsize']);
$GLOBALS['przdrojjm']=phprs_sql_escape_string($GLOBALS['przdrojjm']);
$GLOBALS['przdrojadr']=phprs_sql_escape_string($GLOBALS['przdrojadr']);
$GLOBALS['prverze']=phprs_sql_escape_string($GLOBALS['prverze']);
$GLOBALS['prkat']=phprs_sql_escape_string($GLOBALS['prkat']);
$GLOBALS['prlevel']=phprs_sql_escape_string($GLOBALS['prlevel']);
$GLOBALS['przobrazit']=phprs_sql_escape_string($GLOBALS['przobrazit']);
$GLOBALS['prsekce']=phprs_sql_escape_string($GLOBALS['prsekce']);

// kontrola datumu
if (isset($GLOBALS["prdatum"])):
  $upr_datum=OverDatum($GLOBALS["prdatum"]);
else:
  $upr_datum=date("Y-m-d H:i:s");
endif;

// uprava polozky
$dotaz="update ".$GLOBALS["rspredpona"]."download set ";
$dotaz.="nazev='".$GLOBALS["prnazev"]."', komentar='".$GLOBALS["prkomentar"]."', furl='".$GLOBALS["prurl"]."', fjmeno='".$GLOBALS["prjmeno"]."', ";
$dotaz.="fsize='".$GLOBALS["prsize"]."', zdroj_jm='".$GLOBALS["przdrojjm"]."', zdroj_adr='".$GLOBALS["przdrojadr"]."', datum='".$upr_datum."', ";
$dotaz.="verze='".$GLOBALS["prverze"]."', kat='".$GLOBALS["prkat"]."', pocitadlo='".$GLOBALS["prpocitadlo"]."', zobraz='".$GLOBALS["przobrazit"]."', ";
$dotaz.="idsekce='".$GLOBALS["prsekce"]."', level_souboru='".$GLOBALS['prlevel']."' ";
$dotaz.="where idd=".$GLOBALS["pridd"];

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\">Error D3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_SS_OK_EDIT_FILES."</p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=InfoDownDopis&amp;modul=files&amp;cislosouboru=".$GLOBALS["pridd"]."\">".RS_FIL_SS_ODESLAT_MAIL."</a></p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

// -----[download sekce - hlavni fce]---------------------------------------------------------------------------

/*
  ShowFileSek()
  AcAddFileSek()
  NastFileSek()
  DelFileSek()
  EditFileSek()
  AcEditFileSek()
*/

function ShowFileSek()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_ZPET."</a></p>\n";

// dotaz na data
$dotazsek=phprs_sql_query("select ids,nazev,hlavnisekce from ".$GLOBALS["rspredpona"]."download_sekce order by nazev",$GLOBALS["dbspojeni"]);
$pocetsek=phprs_sql_num_rows($dotazsek);

// vypis download sekci
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_FIL_DS_NAZEV."</b></td>
<td align=\"center\"><b>".RS_FIL_DS_HL_SEKCE."</b></td>
<td align=\"center\"><b>".RS_FIL_DS_AKCE."</b></td></tr>\n";
if ($pocetsek==0):
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"3\">".RS_FIL_DS_ZADNA_SEKCE."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsek)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["nazev"]."</td>\n";
    echo "<td align=\"center\"><input type=\"radio\" name=\"prids\" value=\"".$pole_data["ids"]."\"";
    if ($pole_data["hlavnisekce"]==1): echo " checked"; endif; // test na hlavni sekci
    echo "></td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditFileSek&amp;modul=files&amp;prids=".$pole_data["ids"]."\">".RS_FIL_DS_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelFileSek&amp;modul=files&amp;prids=".$pole_data["ids"]."\">".RS_FIL_DS_SMAZ."</a></td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"3\"><input type=\"submit\" value=\" ".RS_FIL_DS_NASTAV_HL_SEKCI." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"NastFileSek\"><input type=\"hidden\" name=\"modul\" value=\"files\">
</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_NASTAV_INFO."</p>\n";

// delici cara
echo "<hr width=\"600\">\n";

// nadpis
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_FIL_DS_NADPIS_ADD."</strong></big></p>\n";
// formular na pridani nove sekce
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_FORM_HL_SEKCE."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prhlavni\" value=\"1\" checked> ".RS_TL_ANO." &nbsp; <input type=\"radio\" name=\"prhlavni\" value=\"0\"> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddFileSek\"><input type=\"hidden\" name=\"modul\" value=\"files\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcAddFileSek()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekce
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prhlavni"]=phprs_sql_escape_string($GLOBALS["prhlavni"]);

// zmena nastaveni hl. sekce
if ($GLOBALS["prhlavni"]==1):
  @phprs_sql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='0'",$GLOBALS["dbspojeni"]);
endif;
// pridani nove polozky
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."download_sekce values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prhlavni"]."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error D4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_ADD_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function NastFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

$chyba=0; // inic. chyby

@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='0'",$GLOBALS["dbspojeni"]);
if ($error === false): $chyba=1; endif;
@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."download_sekce set hlavnisekce='1' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false): $chyba=1; endif;

if ($chyba==1): // chyba
  echo "<p align=\"center\" class=\"txt\">Error D5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else: // vse OK
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_NASTAV_SEKCE."</p>\n";
endif;

// navrat
ShowFileSek();
}

function DelFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

$chyba=0;

$dotazpol=phprs_sql_query("select count(idd) as pocet from ".$GLOBALS["rspredpona"]."download where idsekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($dotazpol!==false&&phprs_sql_num_rows($dotazpol)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazpol);
  if ($pole_data['pocet']>0):
    // download sekce neni prazdna
    echo "<p align=\"center\" class=\"txt\">Error D6: ".RS_FIL_DS_ERR_PLNA_SEKCE."</p>\n";
    $chyba=1; // chyba
  endif;
endif;


if ($chyba==0):
  // lze vymazat sekci; je prazdna
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."download_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error D7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_DEL_SEKCE."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

function EditFileSek()
{
// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// linky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\" class=\"navigace\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";

$dotazsek=phprs_sql_query("select ids,nazev from ".$GLOBALS["rspredpona"]."download_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazsek);

// formular na upravu sekce
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_FIL_DS_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data['nazev']."\" size=\"40\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditFileSek\"><input type=\"hidden\" name=\"modul\" value=\"files\">
<input type=\"hidden\" name=\"prids\" value=\"".$pole_data['ids']."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditFileSek()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);
// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);

@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."download_sekce set nazev='".$GLOBALS["prnazev"]."' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error D8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_FIL_DS_OK_EDIT_SEKCE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFileSek&amp;modul=files\">".RS_FIL_DS_ZPET_SEKCE."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

// ---[download - posta]------------------------------------------------------------

/*
  InfoDownDopis()
  AcInfoDownDopis()
*/

function InfoDownDopis()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\" class=\"navigace\">".RS_FIL_SS_ZPET."</a></p>\n";

// bezpecnostni korekce
$GLOBALS["cislosouboru"]=phprs_sql_escape_string($GLOBALS["cislosouboru"]);

$dotazdown=phprs_sql_query("select nazev,komentar from ".$GLOBALS["rspredpona"]."download where idd='".$GLOBALS["cislosouboru"]."'",$GLOBALS["dbspojeni"]);
if ($dotazdown!==false):
  $pocetdown=phprs_sql_num_rows($dotazdown);
else:
  $pocetdown=0;
endif;

if ($pocetdown==1): // test na jedinecnost clanku
  // nacteni dat
  $pole_data=phprs_sql_fetch_assoc($dotazdown);
  // sestaveni - predmet mailu
  $mail_titulek=$GLOBALS['wwwname'].' '.RS_FIL_PC_PREDMET_MAIL.' '.date("d.m.Y");
  // sestaveni - obsah mailu
  $mail_obsah=RS_FIL_PC_OBS_MAIL_1.":\n";
  $mail_obsah.="==========\n\n";
  $mail_obsah.=$pole_data['nazev'].":\n";
  $mail_obsah.=$pole_data['komentar']."\n";
  $mail_obsah.=$GLOBALS['baseadr']."download.php?soubor=".$GLOBALS["cislosouboru"]."\n\n";
  $mail_obsah.=RS_FIL_PC_OBS_MAIL_2."\n".RS_FIL_PC_OBS_MAIL_3;
  // formular
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<p align=\"center\" class=\"txt\">\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcInfoDownDopis\"><input type=\"hidden\" name=\"modul\" value=\"files\">\n";
  echo RS_FIL_PC_FROM_PREDMET.":<br><input type=\"text\" name=\"prtitulek\" value=\"".$mail_titulek."\" size=\"60\" class=\"textpole\"><br><br>\n";
  echo RS_FIL_PC_FORM_OBSAH.":<br><textarea name=\"probsah\" rows=\"7\" cols=\"60\" class=\"textbox\">".$mail_obsah."</textarea><br><br>\n";
  echo "<input type=\"submit\" value=\" ".RS_FIL_PC_TL_ODELAT_MAIL." \" class=\"tl\">\n";
  echo "</p>\n";
  echo "</form>\n";
else:
  echo "<p align=\"center\" class=\"txt\">Error P1: ".RS_FIL_DS_ERR_NEEXISTUJE_SB."</p>\n";
endif;
}

function AcInfoDownDopis()
{
// odeslani e-mailu
$odeslani_posty = new CPosta();
$odeslani_posty->NastavInfoMail();
$odeslani_posty->Nastav("predmet",$GLOBALS['prtitulek']);
$odeslani_posty->Nastav("obsah",$GLOBALS['probsah']);
$odeslani_posty->Odesilac();

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowFile&amp;modul=files\">".RS_FIL_SS_ZPET."</a></p>\n";
}

?>