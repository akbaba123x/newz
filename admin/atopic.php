<?php

######################################################################
# phpRS Administration Engine - Topic's section 1.3.6
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_topic

/*
  Tento soubor zajistuje definici rubrik/temat.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // temata
     case "ShowTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_VIEW_TEMA."</h2>";
          ShowTopic();
          break;
     case "AddTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_ADD_TEMA."</h2>";
          AddTopic();
          break;
     case "AcAddTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_ADD_TEMA."</h2>";
          AcAddTopic();
          break;
     case "DelTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_DEL_TEMA."</h2>";
          DelTopic();
          break;
     case "EditTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_EDIT_TEMA."</h2>";
          EditTopic();
          break;
     case "AcEditTopic": AdminMenu();
          echo "<h2 align=\"center\">".RS_TOP_ROZ_EDIT_TEMA."</h2>";
          AcEditTopic();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowTopic()
  AddTopic()
  AcAddTopic()
  DelTopic()
  EditTopic()
  AcEditTopic()
*/

function ShowTopic()
{
// linky
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddTopic&amp;modul=topic\" class=\"navigace\">".RS_TOP_SR_PRIDAT_ANKETU."</a></p>\n";

// vystupni pole: 0 - id prkvu, 1 - nazev prvku, 2 - cislo urovne
$poletopic=GenerujSeznam();
if (!is_array($poletopic)): // neexistuji zadne rubriky
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_ZADNE_TEMA."</p>\n";
else:
  $pocettopic=count($poletopic); // pocet prvku v poli
  echo "<table border=\"0\" align=\"center\">\n";
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<tr class=\"txt\"><td align=\"left\">";
    echo Me($poletopic[$pom][2],3);
    if ($poletopic[$pom][2]>0): echo "<img src=\"image/strom_c.gif\" width=\"11\" height=\"11\" align=\"middle\">&nbsp;"; endif; // je kdyz je vetsi nez 0
    echo "<b>".$poletopic[$pom][1]."</b> [<a href=\"".RS_VYKONNYSOUBOR."?akce=EditTopic&amp;modul=topic&amp;pridt=".$poletopic[$pom][0]."\">".RS_TOP_SR_UPRAVIT."</a>]";
    echo "[<a href=\"".RS_VYKONNYSOUBOR."?akce=DelTopic&amp;modul=topic&amp;pridt=".$poletopic[$pom][0]."\">".RS_TOP_SR_SMAZ."</a>]</td></tr>\n";
  endfor;
  echo "</table>\n";
endif;
}

function AddTopic()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowTopic&amp;modul=topic\" class=\"navigace\">".RS_TOP_SR_ZPET."</a></p>\n";

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">

<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr class=\"txt\"><td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"8\" cols=\"50\" class=\"textbox\">".RS_TOP_SR_FORM_POPIS_INFO."</textarea></td>
<td align=\"left\" valign=\"top\">&nbsp;&nbsp;&nbsp;</td>
<td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POLOHA."</b><br>
<select size=\"7\" name=\"prpoloha\" class=\"sezobory\">
<option value=\"0-0\">".RS_TOP_SR_FORM_POLOHA_ZAKLAD."</option>\n";

// pridani rubriky:  uroven_vnoreni-id_predka
$poletopic=GenerujSeznam();
if (is_array($poletopic)):
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".($poletopic[$pom][2]+1)."-".$poletopic[$pom][0]."\">";
    echo Me($poletopic[$pom][2],1);
    if ($poletopic[$pom][2]>0): echo "+ "; endif; // je kdyz je vetsi nez 0
    echo $poletopic[$pom][1]."</option>\n";
  endfor;
endif;

echo "</select>
</td></tr>
</table>

</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_URL_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"probrazek\" value=\"image/topic/\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_HODNOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnost\" value=\"1\" size=\"5\" class=\"textpole\"> ".RS_TOP_SR_FORM_HODNOST_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_ZOBRAZIT."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddTopic\"><input type=\"hidden\" name=\"modul\" value=\"topic\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\">
</form>
<br>\n";
}

function AcAddTopic()
{
if (!isset($GLOBALS["prpoloha"])): $GLOBALS["prpoloha"]='0-0'; endif;

// bezpecnostni korekce
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);

$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prpoloha"]=phprs_sql_escape_string($GLOBALS["prpoloha"]);
$GLOBALS["probrazek"]=phprs_sql_escape_string($GLOBALS["probrazek"]);
$GLOBALS["prhodnost"]=phprs_sql_escape_string($GLOBALS["prhodnost"]);
$GLOBALS["przobrazit"]=phprs_sql_escape_string($GLOBALS["przobrazit"]);

// dekompilace promenne $prpoloha na subcasti: level-id_predka
list($prlevel,$pridpredek)=explode('-',$GLOBALS["prpoloha"]);

if ($prlevel!=0):
  phprs_sql_query("update ".$GLOBALS["rspredpona"]."topic set rodic='1' where idt='".$pridpredek."'",$GLOBALS["dbspojeni"]);
endif;

$dotaz="insert into ".$GLOBALS["rspredpona"]."topic ";
$dotaz.="values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."','".$GLOBALS["probrazek"]."','0','".$prlevel."','0','".$pridpredek."','".$GLOBALS["prhodnost"]."',";
$dotaz.="'".$GLOBALS["przobrazit"]."')";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error T3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_ADD_TEMA."</p>\n"; // vse OK
endif;

// navrat
ShowTopic();
}

function DelTopic()
{
$chyba=0; // vse je OK

// bezpecnostni korekce
$GLOBALS["pridt"]=phprs_sql_escape_string($GLOBALS["pridt"]);

// overeni existence podtemat
$dotazid=phprs_sql_query("select count(idt) as pocet from ".$GLOBALS["rspredpona"]."topic where id_predka='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
if ($dotazid!==false&&phprs_sql_num_rows($dotazid)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazid);
  if ($pole_data['pocet']>0):
    // CHYBA: Akci nelze provest, jelikoz toto tema obsahuje dalsi podtemata!
    echo "<p align=\"center\" class=\"txt\">Error T1: ".RS_TOP_SR_ERR_JE_RODIC."</p>\n";
    $chyba=1;
  endif;
endif;

// overeni existence clanku s timto tematem
$dotazcl=phprs_sql_query("select count(tema) as pocet from ".$GLOBALS["rspredpona"]."clanky where tema='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
if ($dotazcl!==false&&phprs_sql_num_rows($dotazcl)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazcl);
  if ($pole_data['pocet']>0):
    // CHYBA: Akci nelze provest, jelikoz s timto tematem jsou spolejny nektere clanky!
    echo "<p align=\"center\" class=\"txt\">Error T1: ".RS_TOP_SR_ERR_AKTIVNI_TEMA."</p>\n";
    $chyba=1;
  endif;
endif;

// test na existenci chyby
if ($chyba==0):
  // zjisteni id rodice
  $dotazpredek=phprs_sql_query("select id_predka from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
  if ($dotazpredek!==false&&phprs_sql_num_rows($dotazpredek)>0):
    list($cislo_predek)=phprs_sql_fetch_row($dotazpredek);
  endif;

  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error T2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_DEL_TEMA."</p>\n"; // vse OK
  endif;

  // overeni existence rodicovstvi
  $dotazover=phprs_sql_query("select idt from ".$GLOBALS["rspredpona"]."topic where id_predka='".$cislo_predek."'",$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotazover)==0):
    // zruseni rodicovstvi
    phprs_sql_query("update ".$GLOBALS["rspredpona"]."topic set rodic='0' where idt='".$cislo_predek."'",$GLOBALS["dbspojeni"]);
  endif;
endif;

// navrat
ShowTopic();
}

function EditTopic()
{
// bezpecnostni korekce
$GLOBALS["pridt"]=phprs_sql_escape_string($GLOBALS["pridt"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowTopic&amp;modul=topic\" class=\"navigace\">".RS_TOP_SR_ZPET."</a></p>\n";

$dotaztema=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."topic where idt='".$GLOBALS["pridt"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotaztema);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" class=\"textpole\" value=\"".$pole_data["nazev"]."\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">

<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
<tr class=\"txt\"><td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"8\" cols=\"50\" class=\"textbox\">".KorekceHTML($pole_data["popis"])."</textarea></td>
<td align=\"left\" valign=\"top\">&nbsp;&nbsp;&nbsp;</td>
<td align=\"left\" valign=\"top\"><b>".RS_TOP_SR_FORM_AKT_OBR."</b><br>\n";
if ($pole_data["obrazek"]!='' || is_file($pole_data["obrazek"])):
  echo "<img src=\"".$pole_data["obrazek"]."\" alt=\"obrázek / image\">";
endif;
echo "</td></tr>
</table>

</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_URL_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"probrazek\" value=\"".$pole_data["obrazek"]."\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_HODNOST."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnost\" value=\"".$pole_data["hodnost"]."\" size=\"5\" class=\"textpole\"> ".RS_TOP_SR_FORM_HODNOST_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_TOP_SR_FORM_ZOBRAZIT."</b></td>
<td align=\"left\">";
if ($pole_data['zobrazit']==1):
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\"> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked> ".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=left><b>".RS_TOP_SR_FORM_VNORENI."</b></td>
<td align=\"left\">";
if ($pole_data["level"]==0):
  echo RS_TOP_SR_FORM_VNORENI_ZAKLAD; // zakladni uroveni
else:
  echo $pole_data["level"].". ".RS_TOP_SR_FORM_VNORENI_DALSI; // X. uroven
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditTopic\"><input type=\"hidden\" name=\"pridt\" value=\"".$pole_data["idt"]."\">
<input type=\"hidden\" name=\"modul\" value=\"topic\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \"  class=\"tl\">
</form>
<br>\n";
}

function AcEditTopic()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);

$GLOBALS["pridt"]=phprs_sql_escape_string($GLOBALS["pridt"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["probrazek"]=phprs_sql_escape_string($GLOBALS["probrazek"]);
$GLOBALS["prhodnost"]=phprs_sql_escape_string($GLOBALS["prhodnost"]);
$GLOBALS["przobrazit"]=phprs_sql_escape_string($GLOBALS["przobrazit"]);

$dotaz="update ".$GLOBALS["rspredpona"]."topic set ";
$dotaz.="nazev='".$GLOBALS["prnazev"]."', popis='".$GLOBALS["prpopis"]."', obrazek='".$GLOBALS["probrazek"]."', hodnost='".$GLOBALS["prhodnost"]."', ";
$dotaz.="zobrazit='".$GLOBALS["przobrazit"]."' ";
$dotaz.="where idt='".$GLOBALS["pridt"]."'";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error T4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_TOP_SR_OK_EDIT_TEMA."</p>\n"; // vse OK
endif;

// navrat
ShowTopic();
}

?>