<?php

######################################################################
# phpRS Administration Engine - Comment's section 1.4.7
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_clanky, rs_komentare, rs_user

/*
  Tento soubor zajistuje obsluhu komentaru prirazenych ke clankum.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit();
endif;

include_once("admin/astdlib_comment.php"); // standardni knihovna komentarovych funkci

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // komentare
     case "ShowComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_SHOW_KOMENT."</h2>";
          ShowComment();
          break;
     case "ViewComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_KOMENT."</h2>";
          ViewComment();
          break;
     case "DelComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_DEL_OBSAH."</h2>";
          DelComment();
          break;
     case "HardDelComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_DEL_KOMENT."</h2>";
          HardDelComment();
          break;
     case "EditComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_EDIT_KOMENT."</h2>";
          EditComment();
          break;
     case "AcEditComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_EDIT_KOMENT."</h2>";
          AcEditComment();
          break;
     case "ViewIPComment": AdminMenu();
          echo "<h2 align=\"center\">".RS_KOM_ROZ_KOMENT."</h2>";
          ViewIPComment();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------

/*
  ShowComment()
  ViewComment()
  DelComment()
  HardDelComment()
  EditComment()
  AcEditComment()
  ViewIPComment()
*/

function ShowComment()
{
// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazaut=phprs_sql_query("select idu,user from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
if (phprs_sql_num_rows($dotazaut)>0):
  while ($pole_data = phprs_sql_fetch_assoc($dotazaut)):
    $autori[$pole_data["idu"]]=$pole_data["user"];
  endwhile;
endif;

// pocet vsech clanku s komentari
$dotazmnozstvi=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where kom>0",$GLOBALS["dbspojeni"]);
if ($dotazmnozstvi!==false&&phprs_sql_num_rows($dotazmnozstvi)>0):
  list($pocetmnozstvi)=phprs_sql_fetch_row($dotazmnozstvi); // existuje vysledek
else:
  $pocetmnozstvi=0; // chyba
endif;

// kdyz neni definovan interval
if (!isset($GLOBALS["prmin"])):
  if($pocetmnozstvi<20):
    $GLOBALS["prmin"]=0;
    $GLOBALS["prmax"]=$pocetmnozstvi;
  else:
    $GLOBALS["prmin"]=0;
    $GLOBALS["prmax"]=20;
  endif;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"ShowComment\"><input type=\"hidden\" name=\"modul\" value=\"comment\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_KOM_SK_ZOBRAZ_KOMENT." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_KOM_SK_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_KOM_SK_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
- ".RS_KOM_SK_CELK_POCET." ".$pocetmnozstvi."
</td></tr>
</table>
</form>
<br>\n";

// informace o vypisu komentaru
echo "<p align=\"center\" class=\"txt\"><i>".RS_KOM_SK_INTO_KOMENT."</i></p>\n";

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $dotaz_od=($GLOBALS["prmin"]-1); else: $dotaz_od=0; endif;
$dotaz_kolik=($GLOBALS["prmax"]-$dotaz_od);
if ($dotaz_kolik<0): $dotaz_kolik=0; endif;

// sestaveni dotazu
$dotaz="select c.link,c.titulek,c.datum,c.autor,c.kom,max(k.datum) as posledni,k.od from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."komentare as k ";
$dotaz.="where c.link=k.clanek group by c.link ";
$dotaz.="order by posledni desc limit ".$dotaz_od.",".$dotaz_kolik;
$dotazkom=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=phprs_sql_num_rows($dotazkom);

if ($pocetkom==0):
  // CHYBA: Zadany interval (od xxx do yyy) je prazdny!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">";
  echo "<td align=\"center\"><b>".RS_KOM_SK_LINK."</b></td>";
  echo "<td align=\"center\" width=\"300\"><b>".RS_KOM_SK_TITULEK."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AKTUAL."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AUTOR."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_DAT_VYDANI."</b></td>";
  echo "<td align=\"center\"><b>".RS_KOM_SK_POCET."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_KOM_SK_AKCE."</b></td></tr>\n";
  while ($pole_data = phprs_sql_fetch_assoc($dotazkom)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"center\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\" target=\"_blank\">".$pole_data["link"]."</a></td>\n";
    echo "<td align=\"left\" width=\"300\">".$pole_data["titulek"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["posledni"])."</td>\n";
    echo "<td align=\"center\">";
    if (isset($autori[$pole_data["autor"]])):
      echo $autori[$pole_data["autor"]];
    else:
      echo $pole_data["autor"];
    endif;
    echo "</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".$pole_data["kom"]."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$pole_data["link"]."\">".RS_KOM_SK_ZOBRAZIT."</a></td></tr>\n";
  endwhile;
  echo "</table>\n";
endif;
echo "<br>\n";
}

function ViewComment()
{
$GLOBALS["prlink"]=phprs_sql_escape_string($GLOBALS["prlink"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\" class=\"navigace\">".RS_KOM_SK_ZPET."</a></p>\n";

// nalezeni nazvu clanku
$dotazcla=phprs_sql_query("select titulek from ".$GLOBALS["rspredpona"]."clanky where link='".$GLOBALS["prlink"]."'",$GLOBALS["dbspojeni"]);
if ($dotazcla!==false&&phprs_sql_num_rows($dotazcla)>0):
  $pole_data_cla=phprs_sql_fetch_assoc($dotazcla);
  echo "<p align=\"center\" class=\"txt\"><strong><big>".$pole_data_cla['titulek']."</big></strong></p>\n";
endif;

// vypis nalezenych komentaru
$dotazkom=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."komentare where clanek='".$GLOBALS["prlink"]."' order by idk desc",$GLOBALS["dbspojeni"]);
$pocetkom=phprs_sql_num_rows($dotazkom);

if ($pocetkom==0):
  // CHYBA: K vybranemu clanku nejsou aktualne prirazeny zadne komentare!
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_ZADNY_KOMENT."</p>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazkom)):
    echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\" width=\"700\" class=\"ramsedy-vypln\">\n";
    echo "<tr class=\"txt\"><td align=\"left\" width=\"180\"><b>".RS_KOM_SK_FORM_ID_KOMENT.":</b></td><td align=\"left\" width=\"520\">".$pole_data['idk'];
    if ($pole_data['reakce_na']>0): echo ' ('.RS_KOM_SK_FORM_REAKCE_NA_ID_KOMENT.' '.$pole_data['reakce_na'].')'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_DATUM.":</b></td><td align=\"left\">".$pole_data['datum']."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR.":</b></td><td align=\"left\">".$pole_data['od']." ";
    if ($pole_data['registrovany']==1): echo '[<b>'.$pole_data['reg_prezdivka'].'</b>]'; else: echo '['.RS_KOM_SK_FORM_NEREG_CTENAR.']'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_EMAIL.":</b></td><td align=\"left\"><a href=\"mailto:".$pole_data["od_mail"]."\">".$pole_data["od_mail"]."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_IP_ADR.":</b></td><td align=\"left\">".$pole_data["od_ip"]." - <a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$pole_data["od_ip"]."\">".RS_KOM_SK_FORM_ALL_IP_KOMENT."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK.":</b></td><td align=\"left\">".$pole_data["titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><hr><b>".RS_KOM_SK_FORM_OBS_KOMENT.":</b><br><div class=\"smltxt\">".$pole_data["obsah"]."</div><hr></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_EDIT_KOMENT."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_OBSAH."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=HardDelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=link&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_KOMENT."</a>";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "<br>\n";
  endwhile;
endif;
echo "<br>\n";
}

function DelComment()
{
$GLOBALS["pridk"]=phprs_sql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=phprs_sql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=phprs_sql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=phprs_sql_escape_string($GLOBALS["prip"]);

// text nahrazujici obsah komentare
$novy_text=RS_KOM_NAHRAD_OBSAH;

@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."komentare set obsah='".$novy_text."' where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_DEL_OBSAH."</p>\n";
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function HardDelComment()
{
$GLOBALS["pridk"]=phprs_sql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=phprs_sql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=phprs_sql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=phprs_sql_escape_string($GLOBALS["prip"]);

$chyba=0; // inic. chyby

// kontrolni dotaz na status rodice
$dotazdeti=phprs_sql_query("select count(idk) as pocet from ".$GLOBALS["rspredpona"]."komentare where reakce_na='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if ($dotazdeti!==false&&phprs_sql_num_rows($dotazdeti)==1):
  list($pocet_deti)=phprs_sql_fetch_row($dotazdeti);
  if ($pocet_deti>0):
    // chyba - Akci nelze provest, protoze na tento komentar jsou napojeny jedna nebo vice reakci!
    echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_ERR_DEL_KOMENT."</p>\n";
    $chyba=1;
  endif;
else:
  $chyba=1;
endif;

// test na existenci chyby
if ($chyba==0):
  // zjisteni linku na clanek
  $dotazkom=phprs_sql_query("select clanek from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
  if ($dotazkom!==false&&phprs_sql_num_rows($dotazkom)>0):
    list($cla_link)=phprs_sql_fetch_row($dotazkom);
  else:
    $cla_link='';
  endif;

  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error C2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_DEL_KOMENT."</p>\n";
    phprs_sql_query("update ".$GLOBALS["rspredpona"]."clanky set kom=(kom-1) where link='".$cla_link."'",$GLOBALS["dbspojeni"]);
  endif;
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function EditComment()
{
$GLOBALS["pridk"]=phprs_sql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=phprs_sql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=phprs_sql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=phprs_sql_escape_string($GLOBALS["prip"]);

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\" class=\"navigace\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\" class=\"navigace\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;

$dotazkom=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."komentare where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazkom);

// formular na upravu komentare
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_data['titulek']."\" size=\"63\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_JMENO."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prjmeno\" value=\"".$pole_data['od']."\" size=\"63\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_EMAIL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"premail\" value=\"".$pole_data['od_mail']."\" size=\"63\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR_IP."</b></td>
<td align=\"left\">".$pole_data['od_ip']."</td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_KOM_SK_FORM_OBS_KOMENT."</b><br>
<textarea name=\"probsah\" rows=\"14\" cols=\"85\" class=\"textbox\">".KorekceHTML($pole_data['obsah'])."</textarea></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditComment\"><input type=\"hidden\" name=\"modul\" value=\"comment\">
<input type=\"hidden\" name=\"pridk\" value=\"".$pole_data['idk']."\"><input type=\"hidden\" name=\"przpet\" value=\"".$GLOBALS["przpet"]."\">
<input type=\"hidden\" name=\"prlink\" value=\"".$GLOBALS["prlink"]."\"><input type=\"hidden\" name=\"prip\" value=\"".$GLOBALS["prip"]."\">
<p align=\"center\"><i>".RS_KOM_SK_MAX_DELKA_KOMENT.": ".$GLOBALS['rsconfig']['max_delka_komentare']." ".RS_KOM_SK_ZNAKU."; ".RS_KOM_SK_MAX_VEL_SLOVO.": ".$GLOBALS['rsconfig']['max_delka_slova']." ".RS_KOM_SK_ZNAKU."</i></p>
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditComment()
{
$GLOBALS["pridk"]=phprs_sql_escape_string($GLOBALS["pridk"]);
$GLOBALS["przpet"]=phprs_sql_escape_string($GLOBALS["przpet"]);
$GLOBALS["prlink"]=phprs_sql_escape_string($GLOBALS["prlink"]);
$GLOBALS["prip"]=phprs_sql_escape_string($GLOBALS["prip"]);
$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["prjmeno"]=phprs_sql_escape_string($GLOBALS["prjmeno"]);
$GLOBALS["premail"]=phprs_sql_escape_string($GLOBALS["premail"]);
$GLOBALS["probsah"]=KorekceVelikosti($GLOBALS["probsah"]); // korekce velikosti komentare
$GLOBALS["probsah"]=phprs_sql_escape_string($GLOBALS["probsah"]);

// uprava komentare
$dotaz="update ".$GLOBALS["rspredpona"]."komentare set ";
$dotaz.="obsah='".$GLOBALS["probsah"]."', od='".$GLOBALS["prjmeno"]."', od_mail='".$GLOBALS["premail"]."', titulek='".$GLOBALS["prtitulek"]."' ";
$dotaz.="where idk='".$GLOBALS["pridk"]."'";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_OK_EDIT_KOMENT."</p>\n";
endif;

// navrat
if ($GLOBALS["przpet"]=='link'):
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewComment&amp;modul=comment&amp;prlink=".$GLOBALS["prlink"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ViewIPComment&amp;modul=comment&amp;prip=".$GLOBALS["prip"]."\">".RS_KOM_SK_ZPET_KOMENT."</a></p>\n";
endif;
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\">".RS_KOM_SK_ZPET."</a></p>\n";
}

function ViewIPComment()
{
$GLOBALS["prip"]=phprs_sql_escape_string($GLOBALS["prip"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowComment&amp;modul=comment\" class=\"navigace\">".RS_KOM_SK_ZPET."</a></p>\n";
// nadpis
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_KOM_SK_NADPIS_IP_ADR.": ".$GLOBALS["prip"]."</big></strong></p>\n";

// sestaveni dotazu
$dotaz="select k.*,c.titulek as cla_titulek from ".$GLOBALS["rspredpona"]."komentare as k,".$GLOBALS["rspredpona"]."clanky as c ";
$dotaz.="where k.od_ip='".$GLOBALS["prip"]."' and c.link=k.clanek order by idk desc";
// vypis nalezenych komentaru
$dotazkom=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetkom=phprs_sql_num_rows($dotazkom);

if ($pocetkom==0):
  // Databaze neobsahuje zadny komentar odpovidajici zadane IP adrese!
  echo "<p align=\"center\" class=\"txt\">".RS_KOM_SK_ZADNY_KOMENT_IP."</p>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazkom)):
    echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\" width=\"700\" class=\"ramsedy-vypln\">\n";
    echo "<tr class=\"txt\"><td align=\"left\" width=\"180\"><b>".RS_KOM_SK_FORM_ID_KOMENT.":</b></td><td align=\"left\" width=\"520\">".$pole_data['idk'];
    if ($pole_data['reakce_na']>0): echo ' ('.RS_KOM_SK_FORM_REAKCE_NA_ID_KOMENT.' '.$pole_data['reakce_na'].')'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_DATUM.":</b></td><td align=\"left\">".$pole_data["datum"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_NAZEV_CLA.":</b></td><td align=\"left\">".$pole_data["cla_titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_AUTOR.":</b></td><td align=\"left\">".$pole_data["od"]." ";
    if ($pole_data["registrovany"]==1): echo '[<b>'.$pole_data["reg_prezdivka"].'</b>]'; else: echo '['.RS_KOM_SK_FORM_NEREG_CTENAR.']'; endif;
    echo "</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_EMAIL.":</b></td><td align=\"left\"><a href=\"mailto:".$pole_data["od_mail"]."\">".$pole_data["od_mail"]."</a></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_IP_ADR.":</b></td><td align=\"left\">".$pole_data["od_ip"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_KOM_SK_FORM_TITULEK.":</b></td><td align=\"left\">".$pole_data["titulek"]."</td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><hr><b>".RS_KOM_SK_FORM_OBS_KOMENT.":</b><br><div class=\"smltxt\">".$pole_data["obsah"]."</div><hr></td></tr>\n";
    echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_EDIT_KOMENT."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=DelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_OBSAH."</a> - ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=HardDelComment&amp;modul=comment&amp;pridk=".$pole_data["idk"]."&amp;przpet=ip&amp;prip=".$pole_data["od_ip"]."&amp;prlink=".$pole_data["clanek"]."\">".RS_KOM_SK_FORM_DEL_KOMENT."</a>";
    echo "</td></tr>\n";
    echo "</table>\n";
    echo "<br>\n";
  endwhile;
endif;
echo "<br>\n";
}

?>