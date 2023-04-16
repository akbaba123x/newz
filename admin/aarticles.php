<?php

######################################################################
# phpRS Administration Engine - Article's section 1.8.6
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_clanky, rs_topic, rs_user, rs_skup_cl, rs_levely, rs_config, rs_ankety

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // clanky
     case "Articles": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_SHOW_CLA."</h2>\n";
          VypisClanku();
          break;
     case "AddForm": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_ADD_CLA."</h2>\n";
          Clanky();
          break;
     case "AddArticle": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_ADD_CLA."</h2>";
          PridejClanek();
          break;
     case "ShowArticles": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_SHOW_CLA."</h2>";
          VypisClanku();
          break;
     case "ArticleDelete": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_DEL_CLA."</h2>";
          SmazClanek();
          break;
     case "ArticleEdit": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_EDIT_CLA."</h2>";
          FormUpClanek();
          break;
     case "AcArticleEdit": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_EDIT_CLA."</h2>";
          UpravClanek();
          break;
     // skupiny souvisejicich clanku
     case "ShowArtGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_SHOW_SKUP."</h2>";
          VypisSkupCl();
          break;
     case "AddArtGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_ADD_SKUP."</h2>";
          FormPrSkupCl();
          break;
     case "AcAddGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_ADD_SKUP."</h2>";
          PridejSkupCl();
          break;
     case "AcEdGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_EDIT_SKUP."</h2>";
          FormUprSkupCl();
          break;
     case "Ac2EdGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_EDIT_SKUP."</h2>";
          UpravSkupCl();
          break;
     case "AcDelGroup": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_DEL_SKUP."</h2>";
          SmazSkupCl();
          break;
     // informacni mail
     case "InfoDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_POSTA_CLA."</h2>";
          InfoDopis();
          break;
     case "AcInfoDopis": AdminMenu();
          echo "<h2 align=\"center\">".RS_CLA_ROZ_POSTA_CLA."</h2>";
          include_once('admin/astdlib_mail.php'); // vlozeni STD. MAIL LIBRARY
          AcInfoDopis();
          break;
endswitch;

// ---[pomocne fce - clanky]--------------------------------------------------------

function OptSkupCla($hledam = 0)
{
$str=''; // inic.

$dotazskup=phprs_sql_query("select ids,nazev_skup from ".$GLOBALS["rspredpona"]."skup_cl order by nazev_skup",$GLOBALS["dbspojeni"]);
$pocetskup=phprs_sql_num_rows($dotazskup);

if ($pocetskup==0):
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_ZADNA_SKUPINA."</option>\n"; // neni definovana zadna skupina
else:
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_BEZ_ZARAZENI."</option>\n"; // bez zarazeni
  while ($pole_data = phprs_sql_fetch_assoc($dotazskup)):
    $str.="<option value=\"".$pole_data["ids"]."\"";
    if ($hledam==$pole_data["ids"]): $str.=" selected"; endif;
    $str.=">".$pole_data["nazev_skup"]."</option>\n";
  endwhile;
endif;

return $str;
}

function OptClaSab($hledam = 0)
{
$str='';

$dotazsab=phprs_sql_query("select ids,nazev_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by nazev_cla_sab",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);

if ($pocetsab==0):
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_ZADNA_SAB."</option>\n"; // neni definovana zadna cla. sablona
else:
  $nalezl=0;
  while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
    $str.="<option value=\"".$pole_data['ids']."\"";
    if ($hledam==$pole_data['ids']): $str.=" selected"; $nalezl=1; endif;
    $str.=">".$pole_data['nazev_cla_sab']."</option>\n";
  endwhile;
  // test na vysledek; 0 znamena pouze prazdny vstup nikoli neplatne id sablona
  if ($nalezl==0&&$hledam>0):
    $str.="<option value=\"".$hledam."\" selected>".RS_CLA_POM_ERR_NEEXIST_SAB."</option>\n";
  endif;
endif;

return $str;
}

function OptLevely($hledam = 0)
{
$str='';

$dotazsab=phprs_sql_query("select idl,nazev_levelu,hodnota from ".$GLOBALS["rspredpona"]."levely order by hodnota,nazev_levelu",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);

if ($pocetsab==0):
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_ZADNY_LEVEL."</option>\n"; // neni definovan zadny level
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
    $str.="<option value=\"".$pole_data['idl']."\"";
    if ($hledam==$pole_data['idl']): $str.=" selected"; endif;
    $str.=">".$pole_data['nazev_levelu']." (".$pole_data['hodnota'].")</option>\n";
  endwhile;
endif;

return $str;
}

function OptAnkety($hledam = 0)
{
$str='';

$dotazpol=phprs_sql_query("select ida,titulek from ".$GLOBALS["rspredpona"]."ankety order by titulek",$GLOBALS["dbspojeni"]);
$pocetpol=phprs_sql_num_rows($dotazpol);

if ($pocetpol==0):
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_ZADNA_ANKETA."</option>\n"; // neni definovana zadna anketa
else:
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_BEZ_ZARAZENI."</option>\n"; // bez zarazeni
  while($pole_data = phprs_sql_fetch_assoc($dotazpol)):
    $str.="<option value=\"".$pole_data["ida"]."\"";
    if ($pole_data["ida"]==$hledam): $str.=" selected"; endif;
    $str.=">".$pole_data["titulek"]."</option>\n";
  endwhile;
endif;

return $str;
}

function OptRubriky($hledam = 0, $zobrazit_vyzvu = 0)
{
$str='';

$poletopic=GenerujSeznamSCestou();
if (!is_array($poletopic)):
  $str.="<option value=\"0\">".RS_CLA_POM_ERR_ZADNA_RUBRIKA."</option>\n"; // neni definova zadna rubrika
else:
  if ($zobrazit_vyzvu==1):
    $str.="<option value=\"0\">".RS_CLA_POM_ZVOLTE_RUBRIKU."</option>\n"; // zvolte rubriku
  endif;
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    $str.="<option value=\"".$poletopic[$pom][0]."\"";
    if ($poletopic[$pom][0]==$hledam): $str.=" selected"; endif;
    $str.=">".$poletopic[$pom][1]."</option>\n";
  endfor;
endif;

return $str;
}

function ClaNavigBox()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=25; endif;
if (!isset($GLOBALS["prorderby"])): $GLOBALS["prorderby"]='datum'; endif;
if (!isset($GLOBALS["promezitret"])): $GLOBALS["promezitret"]=''; endif;
if (!isset($GLOBALS["promezitco"])): $GLOBALS["promezitco"]='vse'; endif;
if (!isset($GLOBALS["promezitrub"])): $GLOBALS["promezitrub"]=0; endif;
if (!isset($GLOBALS["promezitautor"])): $GLOBALS["promezitautor"]=0; endif;

$dotazpocet=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky",$GLOBALS["dbspojeni"]);
if ($dotazpocet!==false&&phprs_sql_num_rows($dotazpocet)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazpocet);
else:
  $pole_data['pocet']=0;
endif;

echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"ShowArticles\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_CLA_NB_TL_ZOBRAZ." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_CLA_NB_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_CLA_NB_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
".RS_CLA_NB_TRIDIT." <select name=\"prorderby\" size=\"1\">";
switch ($GLOBALS["prorderby"]):
  case 'datum': echo "<option value=\"datum\" selected>".RS_CLA_NB_TRIDIT_DATUM."</option><option value=\"nazev\">".RS_CLA_NB_TRIDIT_NAZVU_CLA."</option><option value=\"link\">".RS_CLA_NB_TRIDIT_LINK."</option><option value=\"autor\">".RS_CLA_NB_TRIDIT_AUTOR."</option>\n"; break;
  case 'nazev': echo "<option value=\"datum\">".RS_CLA_NB_TRIDIT_DATUM."</option><option value=\"nazev\" selected>".RS_CLA_NB_TRIDIT_NAZVU_CLA."</option><option value=\"link\">".RS_CLA_NB_TRIDIT_LINK."</option><option value=\"autor\">".RS_CLA_NB_TRIDIT_AUTOR."</option>\n"; break;
  case 'link': echo "<option value=\"datum\">".RS_CLA_NB_TRIDIT_DATUM."</option><option value=\"nazev\">".RS_CLA_NB_TRIDIT_NAZVU_CLA."</option><option value=\"link\" selected>".RS_CLA_NB_TRIDIT_LINK."</option><option value=\"autor\">".RS_CLA_NB_TRIDIT_AUTOR."</option>\n"; break;
  case 'autor': echo "<option value=\"datum\">".RS_CLA_NB_TRIDIT_DATUM."</option><option value=\"nazev\">".RS_CLA_NB_TRIDIT_NAZVU_CLA."</option><option value=\"link\">".RS_CLA_NB_TRIDIT_LINK."</option><option value=\"autor\" selected>".RS_CLA_NB_TRIDIT_AUTOR."</option>\n"; break;
  default: echo "<option value=\"datum\" selected>".RS_CLA_NB_TRIDIT_DATUM."</option><option value=\"nazev\">".RS_CLA_NB_TRIDIT_NAZVU_CLA."</option><option value=\"link\">".RS_CLA_NB_TRIDIT_LINK."</option><option value=\"autor\">".RS_CLA_NB_TRIDIT_AUTOR."</option>\n"; break;
endswitch;
echo "</select> - ".RS_CLA_NB_CELKEM_CLA." ".$pole_data['pocet']."<br>
".RS_CLA_NB_HLEDAT_TEXT." <input type=\"text\" name=\"promezitret\" size=\"30\" value=\"".$GLOBALS["promezitret"]."\" class=\"textpole\"> ".RS_CLA_NB_V." <select name=\"promezitco\" size=\"1\">";
switch ($GLOBALS["promezitco"]):
  case 'nazev': echo "<option value=\"nazev\" selected>".RS_CLA_NB_HLEDAT_V_NAZVU_CLA."</option><option value=\"uvod\">".RS_CLA_NB_HLEDAT_V_ANOTACI."</option><option value=\"hlavni\">".RS_CLA_NB_HLEDAT_V_HLA_TEXT."</option><option value=\"link\">".RS_CLA_NB_HLEDAT_V_LINKACH."</option>"; break;
  case 'uvod': echo "<option value=\"nazev\">".RS_CLA_NB_HLEDAT_V_NAZVU_CLA."</option><option value=\"uvod\" selected>".RS_CLA_NB_HLEDAT_V_ANOTACI."</option><option value=\"hlavni\">".RS_CLA_NB_HLEDAT_V_HLA_TEXT."</option><option value=\"link\">".RS_CLA_NB_HLEDAT_V_LINKACH."</option>"; break;
  case 'hlavni': echo "<option value=\"nazev\">".RS_CLA_NB_HLEDAT_V_NAZVU_CLA."</option><option value=\"uvod\">".RS_CLA_NB_HLEDAT_V_ANOTACI."</option><option value=\"hlavni\" selected>".RS_CLA_NB_HLEDAT_V_HLA_TEXT."</option><option value=\"link\">".RS_CLA_NB_HLEDAT_V_LINKACH."</option>"; break;
  case 'link': echo "<option value=\"nazev\">".RS_CLA_NB_HLEDAT_V_NAZVU_CLA."</option><option value=\"uvod\">".RS_CLA_NB_HLEDAT_V_ANOTACI."</option><option value=\"hlavni\">".RS_CLA_NB_HLEDAT_V_HLA_TEXT."</option><option value=\"link\" selected>".RS_CLA_NB_HLEDAT_V_LINKACH."</option>"; break;
  default: echo "<option value=\"nazev\" selected>".RS_CLA_NB_HLEDAT_V_NAZVU_CLA."</option><option value=\"uvod\">".RS_CLA_NB_HLEDAT_V_ANOTACI."</option><option value=\"hlavni\">".RS_CLA_NB_HLEDAT_V_HLA_TEXT."</option><option value=\"link\">".RS_CLA_NB_HLEDAT_V_LINKACH."</option>"; break;
endswitch;
echo "</select><br>
".RS_CLA_NB_OMEZIT_NA_RUBRIKU." <select name=\"promezitrub\" size=\"1\">".OptRubriky($GLOBALS["promezitrub"],1)."</select><br>\n";
if ($GLOBALS["promezitautor"]==1):
  echo "<input type=\"checkbox\" name=\"promezitautor\" value=\"1\" checked> ";
else:
  echo "<input type=\"checkbox\" name=\"promezitautor\" value=\"1\"> ";
endif;
echo RS_CLA_NB_JEN_ME_CLA."
</td></tr>
</table>
</form>
<br>\n";
}

function NactiKonfigHod($str = '')
{
$dotazhod=phprs_sql_query("select idc,hodnota from ".$GLOBALS["rspredpona"]."config where promenna='".phprs_sql_escape_string($str)."'",$GLOBALS["dbspojeni"]);
if (phprs_sql_num_rows($dotazhod)==1):
  // promenna nactena
  $vysledek=phprs_sql_fetch_row($dotazhod);
else:
  // promenna neexistuje
  $vysledek[0]=0;
  $vysledek[1]='';
endif;

return $vysledek; // pole: 0 = id promenne, 1 = hodnota promenne
}

// ---[hlavni fce - clanky]---------------------------------------------------------

/*
  Clanky()
  PridejClanek()
  VypisClanku()
  SmazClanek()
  FormUpClanek()
  UpravClanek()
*/

function Clanky()
{
$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// linky
if ($akt_je_vydavatel==1): // uzivatel s vydavatelskymi pravy
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_SPR_SOUVIS_CLA."</a> -
<a href=\"admin.php?akce=ShowInquiry&amp;modul=ankety\" class=\"navigace\">".RS_CLA_CL_SPR_ANKET."</a></p>\n";
else: // vsichni uzivatele
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowInquiry&amp;modul=ankety\" class=\"navigace\">".RS_CLA_CL_SPR_ANKET."</a></p>\n";
endif;
// navigacni box
ClaNavigBox();
//echo "<hr width=\"600\">\n";

// formular
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_LINK_CLA."</b></td>
<td align=\"left\"><input type=\"text\" size=\"40\" value=\"bude automaticky doplnen\" disabled class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aetitulek\" size=\"60\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_CLA_CL_FORM_UVOD."</b><br>
<textarea name=\"aeuvod\" rows=\"8\" cols=\"85\" class=\"textbox\" placeholder=\"".RS_CLA_CL_FORM_UVOD_INFO."\"></textarea></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_CLA_CL_FORM_HLA_TEXT."</b><br>
<textarea name=\"aetext\" rows=\"10\" cols=\"85\" class=\"textbox\" placeholder=\"".RS_CLA_CL_FORM_UVOD_INFO."\"></textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_ZNACKY."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"aeznacky\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeznacky\" value=\"0\" checked>".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TEMA."</b></td>
<td align=\"left\"><select name=\"aetema\" size=\"1\">".OptRubriky()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TYP_CLA."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"aetypcla\" value=\"1\" checked>".RS_CLA_CL_FORM_TYP_CLA_DLOUHY." &nbsp;&nbsp; <input type=\"radio\" name=\"aetypcla\" value=\"2\">".RS_CLA_CL_FORM_TYP_CLA_KRATKY."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SABLONA."</b></td>
<td align=\"left\"><select name=\"aesablona\" size=\"1\">".OptClaSab()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_ZDROJ."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aezdroj\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DATUM_VYDANI."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aedatum\" value=\"".Date("Y-m-d H:i:s")."\" size=\"25\" class=\"textpole\"><br>".RS_CLA_CL_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DATUM_STAZ." <sup>1)</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"aedatumpl\" value=\"".Date("Y-m-d H:i:s",(Time()+864000))."\" size=\"25\" class=\"textpole\"><br>".RS_CLA_CL_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"aeautor\" size=\"1\">";
if ($akt_je_admin==1):
  echo OptAutori($GLOBALS["Uzivatel"]->IdUser);
else:
  echo OptAutori($GLOBALS["Uzivatel"]->IdUser,$GLOBALS["Uzivatel"]->SeznamDostupUser());
endif;
echo "</select></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_CLA_CL_FORM_KLIC_SLOVA."</b><br>
<textarea name=\"aeslova\" rows=\"4\" cols=\"85\" class=\"textbox\" placeholder=\"".RS_CLA_CL_FORM_KLIC_SLOVA_INFO."\"></textarea></td></tr>\n";
if ($akt_je_vydavatel==1):
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_LEVEL_CLA."</b></td>\n";
  echo "<td align=\"left\"><select name=\"aelevel\" size=\"1\">".OptLevely()."</select></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DAT_NA_INDEX."</b></td>\n";
  echo "<td align=\"left\"><input type=\"radio\" name=\"aeindex\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeindex\" value=\"0\">".RS_TL_NE."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_VYDAT_CLA."</b></td>\n";
  echo "<td align=\"left\"><input type=\"radio\" name=\"aevisible\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aevisible\" value=\"0\">".RS_TL_NE."</td></tr>\n";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_PRIORITA."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aepriorita\" size=\"5\" value=\"1\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_HODNOCENI."</b></td>
<td align=\"left\">0 (".RS_CLA_CL_FORM_POC_HLAS." 0)</tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SOUVIS_ANKETA."</b></td>
<td align=\"left\"><select name=\"aeanketa\" size=\"1\">".OptAnkety()."</select>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SOUVIS_CLA."</b></td>
<td align=\"left\"><select name=\"aeskupina\" size=\"1\">".OptSkupCla()."</select>\n";
if ($akt_je_vydavatel==1):
  echo "<br>( <a href=\"admin.php?akce=AddArtGroup&amp;modul=clanky\">".RS_CLA_CL_PRIDAT_SOUVIS_SKUP."</a> )";
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AddArticle\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";

// informacni text
echo "<div align=\"center\" class=\"txt\"><div style=\"width: 800px;\">".RS_CLA_CL_UPOZORNENI."</div></div>
<br>\n";
}

function PridejClanek()
{
// korekce na uvozovky
$GLOBALS["aetitulek"]=KorekceNadpisu($GLOBALS["aetitulek"]);
$GLOBALS["aezdroj"]=KorekceNadpisu($GLOBALS["aezdroj"]);
// bezpecnostni korekce
$GLOBALS["aetitulek"]=phprs_sql_escape_string($GLOBALS["aetitulek"]);
$GLOBALS["aeuvod"]=phprs_sql_escape_string($GLOBALS["aeuvod"]);
$GLOBALS["aetext"]=phprs_sql_escape_string($GLOBALS["aetext"]);
$GLOBALS["aeznacky"]=phprs_sql_escape_string($GLOBALS["aeznacky"]);
$GLOBALS["aetema"]=phprs_sql_escape_string($GLOBALS["aetema"]);
$GLOBALS["aetypcla"]=phprs_sql_escape_string($GLOBALS["aetypcla"]);
$GLOBALS["aesablona"]=phprs_sql_escape_string($GLOBALS["aesablona"]);
$GLOBALS["aezdroj"]=phprs_sql_escape_string($GLOBALS["aezdroj"]);
$GLOBALS["aedatum"]=phprs_sql_escape_string($GLOBALS["aedatum"]);
$GLOBALS["aedatumpl"]=phprs_sql_escape_string($GLOBALS["aedatumpl"]);
$GLOBALS["aeautor"]=phprs_sql_escape_string($GLOBALS["aeautor"]);
$GLOBALS["aeslova"]=phprs_sql_escape_string($GLOBALS["aeslova"]);
// $GLOBALS["aelevel"] - zpr. nize
// $GLOBALS["aevisible"] - zpr. nize
$GLOBALS["aepriorita"]=phprs_sql_escape_string($GLOBALS["aepriorita"]);
$GLOBALS["aeanketa"]=phprs_sql_escape_string($GLOBALS["aeanketa"]);
$GLOBALS["aeskupina"]=phprs_sql_escape_string($GLOBALS["aeskupina"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// vytvoreni "linku" z "$GLOBALS["aedatum"]"
list($jen_datum,$jen_cas)=explode(' ',$GLOBALS["aedatum"]);
list($jen_rok,$jen_mes,$jen_den)=explode('-',$jen_datum);
$link_start=date("Ym",mktime(0,0,0,$jen_mes,$jen_den,$jen_rok)).'0001';
$link_konec=date("Ym",mktime(0,0,0,$jen_mes,$jen_den,$jen_rok)).'9999';
$dotazzjisti=phprs_sql_query("select link from ".$GLOBALS["rspredpona"]."clanky where link>='".$link_start."' and link<'".$link_konec."' order by link desc",$GLOBALS["dbspojeni"]);
if ($dotazzjisti!==false&&phprs_sql_num_rows($dotazzjisti)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazzjisti);
  $link_start=$pole_data['link'];
  $link_start++;
endif;
$GLOBALS["aelink"]=$link_start;

$nast_kom=0;
$nast_visit=0;
$nast_hod=0;
$nast_mn_hod=0;
$nast_seo_link=VratSEOLink($GLOBALS["aetitulek"]);

// uprava vstuptu dle pridelenych prav
if ($akt_je_admin==1||$akt_je_vydavatel==1):
  $nast_visible=phprs_sql_escape_string($GLOBALS["aevisible"]);
  $nast_level=phprs_sql_escape_string($GLOBALS["aelevel"]);
  $nast_index=phprs_sql_escape_string($GLOBALS["aeindex"]);
else:
  list($config_prom_id,$config_prom_hodnota)=NactiKonfigHod('default_level'); // nacteni defaultni nastaveni promenne v systemu
  $nast_visible=0;
  $nast_level=$config_prom_hodnota;
  $nast_index=1;
endif;

// pridani clanku
$dotaz="insert into ".$GLOBALS["rspredpona"]."clanky ";
$dotaz.="values(null,'".$GLOBALS["aelink"]."','".$nast_seo_link."','".$GLOBALS["aetitulek"]."','".$GLOBALS["aeuvod"]."','".$GLOBALS["aetext"]."','".$GLOBALS["aetema"]."',";
$dotaz.="'".$GLOBALS["aedatum"]."','".$GLOBALS["aeautor"]."',".$nast_kom.",".$nast_visit.",'".$GLOBALS["aeslova"]."','".$nast_visible."','".$GLOBALS["aezdroj"]."',";
$dotaz.="'".$GLOBALS["aepriorita"]."','".$GLOBALS["aedatumpl"]."','".$GLOBALS["aeskupina"]."',".$nast_hod.",'".$nast_mn_hod."','".$GLOBALS["aeznacky"]."',";
$dotaz.="'".$GLOBALS["aetypcla"]."','".$GLOBALS["aesablona"]."','".$nast_level."','".$nast_index."','".$GLOBALS["aeanketa"]."')";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pridc=phprs_sql_insert_id($GLOBALS["dbspojeni"]); // zjisteni id clanku
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_OK_ADD_CLA."<br><br>".RS_CLA_CL_VOLACI_LINK." ".$GLOBALS["aelink"]."</p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"preview.php?cisloclanku=".$GLOBALS["aelink"]."\" target=\"preview\">".RS_CLA_CL_PREVIEW."</a></p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$pridc."&amp;bezzpet=1\">".RS_CLA_CL_ZPET_EDIT_CLA."</a></p>\n";
  if ($akt_je_vydavatel==1): // info e-mail jen v pripade "prava vydavat"
    echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=InfoDopis&amp;modul=clanky&amp;cisloclanku=".$GLOBALS["aelink"]."\">".RS_CLA_CL_POSLI_MAIL."</a></p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
}

function VypisClanku()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;
if (!isset($GLOBALS["prorderby"])): $GLOBALS["prorderby"]='datum'; endif;

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// linky
if ($akt_je_vydavatel==1): // uzivatele s pravem vydavat
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=AddForm&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_PRIDAT_CLA."</a> -
<a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_SPR_SOUVIS_CLA."</a></p>\n";
else: // vsichni uzivatele
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=AddForm&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_PRIDAT_CLA."</a></p>\n";
endif;
// navigacni box
ClaNavigBox();

// inic. promenne omezujici dotaz
$prwhere='';
// identifikace zpusobu trizeni clanku
switch ($GLOBALS["prorderby"]):
  case 'datum': $dotaz_orderby=' order by c.datum desc'; break;
  case 'nazev': $dotaz_orderby=' order by c.titulek,c.datum desc'; break;
  case 'link': $dotaz_orderby=' order by c.link desc,c.datum desc'; break;
  case 'autor': $dotaz_orderby=' order by u.user desc'; break;
  default: $dotaz_orderby=' order by c.datum desc'; break;
endswitch;
// omezeni na retezec
if (!empty($GLOBALS["promezitret"])):
  // identifikace omezeneho pole
  switch ($GLOBALS["promezitco"]):
    case 'nazev': $prwhere_pole='c.titulek'; break;
    case 'uvod': $prwhere_pole='c.uvod'; break;
    case 'hlavni': $prwhere_pole='c.text'; break;
    case 'link': $prwhere_pole='c.link'; break;
    default: $prwhere_pole='c.titulek'; break;
  endswitch;
  // zpracovani retezce
  $GLOBALS["promezitret"]=phprs_sql_escape_string($GLOBALS["promezitret"]);
  $pole_slova=explode(" ",$GLOBALS["promezitret"]);
  $pocet_slov=count($pole_slova);
  if ($pocet_slov>0):
    $prwhere.=' and ('; // start
    $spojka='';
    for ($p1=0;$p1<$pocet_slov;$p1++):
      $prwhere.=$spojka.$prwhere_pole." like ('%".$pole_slova[$p1]."%')";
      $spojka=' and ';
    endfor;
    $prwhere.=')'; // konec
  endif;
endif;
// omezeni na rubriku
if (!empty($GLOBALS["promezitrub"])):
  $prwhere.=' and c.tema='.phprs_sql_escape_string($GLOBALS["promezitrub"]);
endif;
// omezeni na konkretniho autora clanku
if (isset($GLOBALS["promezitautor"])):
  if ($GLOBALS["promezitautor"]):
    $prwhere.=' and c.autor='.$GLOBALS['Uzivatel']->IdUser; // id akt. prihlaseneho user
  endif;
endif;
// vypocet omezeni
if ($GLOBALS["prmin"]>0): $dotaz_od=($GLOBALS["prmin"]-1); else: $dotaz_od=0; endif;
$dotaz_kolik=($GLOBALS["prmax"]-$dotaz_od);
if ($dotaz_kolik<0): $dotaz_kolik=0; endif;

// dotaz
$dotaz="select c.idc,c.link,c.titulek,c.datum,c.autor,c.visible,u.user,u.jmeno,l.nazev_levelu ";
$dotaz.="from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."user as u, ".$GLOBALS["rspredpona"]."levely as l ";
$dotaz.="where c.autor=u.idu and c.level_clanku=l.idl".$prwhere.$dotaz_orderby." limit ".$dotaz_od.",".$dotaz_kolik;
$dotazcla=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetcla=phprs_sql_num_rows($dotazcla);

if ($pocetcla==0):
  // CHYBA: Zadany interval od XX do ZZ je prazdny!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"]." ".RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"smltxt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CLA_CL_LINK."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CLA_CL_TITULEK."</b></td>";
  echo "<td align=\"center\"><b>".RS_CLA_CL_DATUM_VYDANI."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CLA_CL_AUTOR."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CLA_CL_LEVEL_CLA."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CLA_CL_AKCE."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CLA_CL_SMAZ."</b></td></tr>\n";
  while ($pole_clanky = phprs_sql_fetch_assoc($dotazcla)):
    // inic. vypisu
    $akt_pristupny_cla=0; // defaultne false
    // vypis dat
    if ($pole_clanky['visible']==0):
      echo "<tr class=\"smltxt\" onmouseover=\"setPointer(this, '#FFC6C6')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    else:
      echo "<tr class=\"smltxt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    endif;
    echo "<td align=\"center\">".$pole_clanky['link']."</td>";
    echo "<td align=\"left\" width=\"300\">".$pole_clanky['titulek']."</td>";
    echo "<td align=\"left\">".MyDateTimeToDateTime($pole_clanky['datum'])." / ".TestAnoNe($pole_clanky['visible'])."</td>";
    echo "<td align=\"center\">".$pole_clanky['user']."</td>";
    echo "<td align=\"center\">".$pole_clanky['nazev_levelu']."</td>";
    echo "<td align=\"center\">";
    // start - test na dostupnost editacni funkce
    if ($akt_je_admin==1):
      // admin - muze vse
      echo "<a href=\"admin.php?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$pole_clanky['idc']."\">".RS_CLA_CL_UPRAVIT."</a>";
      $akt_pristupny_cla=1;
    else:
      // ostatni uzivatele
      if ($pole_clanky['visible']==1):
        // clanek je vydan - pro editaci musite mit vydavatelska prava
        if ($GLOBALS['Uzivatel']->JePodrizeny($pole_clanky['autor'])==1&&$akt_je_vydavatel==1):
          echo "<a href=\"admin.php?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$pole_clanky['idc']."\">".RS_CLA_CL_UPRAVIT."</a>";
          $akt_pristupny_cla=1;
        else:
          echo RS_CLA_CL_UPRAVIT;
        endif;
      else:
        // clanek je ve stadiu tvorby - lze editovat
        if ($GLOBALS['Uzivatel']->JePodrizeny($pole_clanky['autor'])==1):
          echo "<a href=\"admin.php?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$pole_clanky['idc']."\">".RS_CLA_CL_UPRAVIT."</a>";
          $akt_pristupny_cla=1;
        else:
          echo RS_CLA_CL_UPRAVIT;
        endif;
      endif;
    endif;
    // konec - test na dostupnost editacni funkce
    echo " / <a href=\"preview.php?cisloclanku=".$pole_clanky['link']."\" target=\"preview\">".RS_CLA_CL_PREVIEW."</a></td>";
    if ($akt_pristupny_cla==1):
      echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleidc[]\" value=\"".$pole_clanky['idc']."\"></td></tr>\n";
    else:
      echo "<td align=\"center\">&nbsp;</td></tr>\n";
    endif;
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"7\"><input type=\"submit\" value=\" ".RS_CLA_CL_SMAZ_OZNACENE." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"ArticleDelete\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">\n";
  echo "</form>\n";
endif;

echo "<br>\n";
}

function SmazClanek()
{
$chyba=0; // inic. chyby

// inic. pristupovych prav
$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();
$akt_seznam_podrizenych=$GLOBALS['Uzivatel']->SeznamDostupUser();

// inic. vstupu
if (!isset($GLOBALS["prpoleidc"])):
  $pocet_poleidc=0;
else:
  $pocet_poleidc=count($GLOBALS["prpoleidc"]);
endif;

// test na zakladni pravo na mazani
if ($akt_je_admin==0&&$akt_je_vydavatel==0):
  // uzivatel je autor nebo redaktor a nema pravo vydavat clanky - tudiz ani mazat
  echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_ERR_NEMATE_PRAVA."</p>\n";
  $chyba=1;
else:
  for ($pom=0;$pom<$pocet_poleidc;$pom++):
    $akt_id_clanek=phprs_sql_escape_string($GLOBALS["prpoleidc"][$pom]);
    // ziskani "link" clanku
    $dotaz="select link from ".$GLOBALS["rspredpona"]."clanky where idc='".$akt_id_clanek."'";
    $dotazlink=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    if ($dotazlink!==false&&phprs_sql_num_rows($dotazlink)==1):
      list($akt_link_clanek)=phprs_sql_fetch_row($dotazlink); // ulozeni vysledku dotazu
    else:
      $akt_link_clanek=''; // chyba; clanek neexistuje
    endif;
    // sestaveni dotazu
    if ($akt_je_admin==1):
      $dotaz="delete from ".$GLOBALS["rspredpona"]."clanky where idc='".$akt_id_clanek."'";
    else:
      $dotaz="delete from ".$GLOBALS["rspredpona"]."clanky where idc='".$akt_id_clanek."' and autor in (".$akt_seznam_podrizenych.")";
    endif;
    // provedeni dotazu
    @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    if ($error === false):
      // chyba
      echo "<p align=\"center\" class=\"txt\">Error C2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    else:
      // vse OK
      // odstraneni komentaru spojenych s odstranenym clankem
      phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."komentare where clanek='".$akt_link_clanek."'",$GLOBALS["dbspojeni"]);
    endif;
  endfor;
endif;

if ($chyba==0): // globalni koncove stavy
  if ($pocet_poleidc==0):
    echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_ERR_ZADNY_OZNAC_CLA."</p>\n"; // prazdny vyber
  else:
    if ($pocet_poleidc==1):
      echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_OK_DEL_CLA."</p>\n"; // jen jeden
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_OK_DEL_VICE_CLA."</p>\n"; // vice
    endif;
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
}

function FormUpClanek()
{
// bezpecnostni kontrola
$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// navrat
if (isset($GLOBALS["bezzpet"])):
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowArticles&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_ZPET_VYPIS_CLA."</a></p>\n";
else:
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"javascript:window.history.back();\" class=\"navigace\">".RS_CLA_CL_ZPET_PRED."</a></p>\n";
endif;

// dotaz na data
if ($akt_je_admin==1): // je admin
  $dotaz="select * from ".$GLOBALS["rspredpona"]."clanky where idc='".$GLOBALS["pridc"]."'";
else: // je autor nebo redaktor
  $dotaz="select * from ".$GLOBALS["rspredpona"]."clanky where idc='".$GLOBALS["pridc"]."' and autor in (".$GLOBALS['Uzivatel']->SeznamDostupUser().")";
  if ($akt_je_vydavatel==0):
    // nema vydavatelska prava - muze upravovat pouze otevrene clanky
    $dotaz.=" and visible=0";
  endif;
endif;
$dotazcla=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$data_clanek=phprs_sql_fetch_assoc($dotazcla);

// promenne "uvod", "text" a "t_slova" mohou pri nekterych nastaveni MySQL databaze vyzadovat jeste korekci funkci - stripslashes
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_LINK_CLA."</b></td>
<td align=\"left\"><input type=\"text\" value=\"".$data_clanek["link"]."\" size=\"40\" disabled class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aetitulek\" value=\"".$data_clanek["titulek"]."\" size=\"60\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=2><b>".RS_CLA_CL_FORM_UVOD."</b><br>
<textarea name=\"aeuvod\" rows=\"8\" cols=\"85\" class=\"textbox\">".KorekceHTML($data_clanek["uvod"])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=2><b>".RS_CLA_CL_FORM_HLA_TEXT."</b><br>
<textarea name=\"aetext\" rows=\"10\" cols=\"85\" class=\"textbox\">".KorekceHTML($data_clanek["text"])."</textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_ZNACKY."</b></td>
<td align=\"left\">\n";
if ($data_clanek["znacky"]==1):
  echo "<input type=\"radio\" name=\"aeznacky\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeznacky\" value=\"0\">".RS_TL_NE;
else:
  echo "<input type=\"radio\" name=\"aeznacky\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeznacky\" value=\"0\" checked>".RS_TL_NE;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TEMA."</b></td>
<td align=\"left\"><select name=\"aetema\" size=\"1\">".OptRubriky($data_clanek["tema"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_TYP_CLA."</b></td>
<td align=\"left\">\n";
if ($data_clanek["typ_clanku"]==2):
  echo "<input type=\"radio\" name=\"aetypcla\" value=\"1\">".RS_CLA_CL_FORM_TYP_CLA_DLOUHY." &nbsp;&nbsp; <input type=\"radio\" name=\"aetypcla\" value=\"2\" checked>".RS_CLA_CL_FORM_TYP_CLA_KRATKY;
else:
  echo "<input type=\"radio\" name=\"aetypcla\" value=\"1\" checked>".RS_CLA_CL_FORM_TYP_CLA_DLOUHY." &nbsp;&nbsp; <input type=\"radio\" name=\"aetypcla\" value=\"2\">".RS_CLA_CL_FORM_TYP_CLA_KRATKY;
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SABLONA."</b></td>
<td align=\"left\"><select name=\"aesablona\" size=\"1\">".OptClaSab($data_clanek["sablona"])."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_ZDROJ."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aezdroj\" value=\"".$data_clanek["zdroj"]."\" size=\"40\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DATUM_VYDANI."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aedatum\" value=\"".$data_clanek["datum"]."\" size=\"25\" class=\"textpole\"><br>".RS_CLA_CL_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DATUM_STAZ." <sup>1)</sup></b></td>
<td align=\"left\"><input type=\"text\" name=\"aedatumpl\" value=\"".$data_clanek["datum_pl"]."\" size=\"25\" class=\"textpole\"><br>".RS_CLA_CL_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"aeautor\" size=\"1\">";
if ($akt_je_admin==1):
  echo OptAutori($data_clanek["autor"]);
else:
  echo OptAutori($data_clanek["autor"],$GLOBALS["Uzivatel"]->SeznamDostupUser());
endif;
echo "</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_POC_CTENI."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aevisit\" value=\"".$data_clanek["visit"]."\" size=\"5\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_CLA_CL_FORM_KLIC_SLOVA."</b><br>
<textarea name=\"aeslova\" rows=\"4\" cols=\"85\" class=\"textbox\">".KorekceHTML($data_clanek["t_slova"])."</textarea></td></tr>\n";
if ($akt_je_vydavatel==1):
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_LEVEL_CLA."</b></td>\n";
  echo "<td align=\"left\"><select name=\"aelevel\" size=\"1\">".OptLevely($data_clanek["level_clanku"])."</select></td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_DAT_NA_INDEX."</b></td>\n";
  if ($data_clanek["zobr_na_indexu"]==1):
    echo "<td align=\"left\"><input type=\"radio\" name=\"aeindex\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeindex\" value=\"0\">".RS_TL_NE."</td></tr>\n";
  else:
    echo "<td align=\"left\"><input type=\"radio\" name=\"aeindex\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aeindex\" value=\"0\" checked>".RS_TL_NE."</td></tr>\n";
  endif;
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_VYDAT_CLA."</b></td>\n";
  if ($data_clanek["visible"]==1):
    echo "<td align=\"left\"><input type=\"radio\" name=\"aevisible\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aevisible\" value=\"0\">".RS_TL_NE."</td></tr>\n";
  else:
    echo "<td align=\"left\"><input type=\"radio\" name=\"aevisible\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"aevisible\" value=\"0\" checked>".RS_TL_NE."</td></tr>\n";
  endif;
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_PRIORITA."</b></td>
<td align=\"left\"><input type=\"text\" name=\"aepriorita\" size=\"5\" value=\"".$data_clanek["priority"]."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_HODNOCENI."</b></td>
<td align=\"left\">";
if ($data_clanek["mn_hodnoceni"]==0):
  echo "0";
else:
  echo number_format(($data_clanek["hodnoceni"]/$data_clanek["mn_hodnoceni"]),2,',','');
endif;
echo " (".RS_CLA_CL_FORM_POC_HLAS." ".$data_clanek["mn_hodnoceni"].")</tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SOUVIS_ANKETA."</b></td>
<td align=\"left\"><select name=\"aeanketa\" size=\"1\">".OptAnkety($data_clanek["anketa_cl"])."</select>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_CL_FORM_SOUVIS_CLA."</b></td>
<td align=\"left\"><select name=\"aeskupina\" size=\"1\">".OptSkupCla($data_clanek["skupina_cl"])."</select>\n";
if ($akt_je_vydavatel==1):
  echo "<br>( <a href=\"admin.php?akce=AddArtGroup&amp;modul=clanky\">".RS_CLA_CL_PRIDAT_SOUVIS_SKUP."</a> )";
endif;
echo "</td></tr>
</table>
<input type=\"hidden\" name=\"pridc\" value=\"".$data_clanek["idc"]."\"><input type=\"hidden\" name=\"aelink\" value=\"".$data_clanek["link"]."\">
<input type=\"hidden\" name=\"akce\" value=\"AcArticleEdit\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>
<br>\n";

// informacni text
echo "<div align=\"center\" class=\"txt\"><div style=\"width: 800px;\">".RS_CLA_CL_UPOZORNENI."</div></div>
<br>\n";
}

function UpravClanek()
{
// korekce na uvozovky
$GLOBALS["aetitulek"]=KorekceNadpisu($GLOBALS["aetitulek"]);
$GLOBALS["aezdroj"]=KorekceNadpisu($GLOBALS["aezdroj"]);
// bezpecnostni kontrola
$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);
$GLOBALS["aetitulek"]=phprs_sql_escape_string($GLOBALS["aetitulek"]);
$GLOBALS["aeuvod"]=phprs_sql_escape_string($GLOBALS["aeuvod"]);
$GLOBALS["aetext"]=phprs_sql_escape_string($GLOBALS["aetext"]);
$GLOBALS["aeznacky"]=phprs_sql_escape_string($GLOBALS["aeznacky"]);
$GLOBALS["aetema"]=phprs_sql_escape_string($GLOBALS["aetema"]);
$GLOBALS["aetypcla"]=phprs_sql_escape_string($GLOBALS["aetypcla"]);
$GLOBALS["aesablona"]=phprs_sql_escape_string($GLOBALS["aesablona"]);
$GLOBALS["aezdroj"]=phprs_sql_escape_string($GLOBALS["aezdroj"]);
$GLOBALS["aedatum"]=phprs_sql_escape_string($GLOBALS["aedatum"]);
$GLOBALS["aedatumpl"]=phprs_sql_escape_string($GLOBALS["aedatumpl"]);
$GLOBALS["aeautor"]=phprs_sql_escape_string($GLOBALS["aeautor"]);
$GLOBALS["aeslova"]=phprs_sql_escape_string($GLOBALS["aeslova"]);
// $GLOBALS["aelevel"] - zpr. nize
// $GLOBALS["aevisible"] - zpr. nize
$GLOBALS["aepriorita"]=phprs_sql_escape_string($GLOBALS["aepriorita"]);
$GLOBALS["aeanketa"]=phprs_sql_escape_string($GLOBALS["aeanketa"]);
$GLOBALS["aeskupina"]=phprs_sql_escape_string($GLOBALS["aeskupina"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// uprava vstuptu dle pridelenych prav
if ($akt_je_admin==1||$akt_je_vydavatel==1):
  $prwhere_nast_visible="visible='".phprs_sql_escape_string($GLOBALS["aevisible"])."',";
  $prwhere_nast_level="level_clanku='".phprs_sql_escape_string($GLOBALS["aelevel"])."',";
  $prwhere_nast_index="zobr_na_indexu='".phprs_sql_escape_string($GLOBALS["aeindex"])."',";
else:
  $prwhere_nast_visible='';
  $prwhere_nast_level='';
  $prwhere_nast_index='';
endif;

$nast_seo_link=VratSEOLink($GLOBALS["aetitulek"]);

// sestaveni dotazu
$dotaz="update ".$GLOBALS["rspredpona"]."clanky ";
$dotaz.="set seo_link='".$nast_seo_link."',titulek='".$GLOBALS["aetitulek"]."',uvod='".$GLOBALS["aeuvod"]."',text='".$GLOBALS["aetext"]."',tema='".$GLOBALS["aetema"]."',";
$dotaz.="datum='".$GLOBALS["aedatum"]."',autor='".$GLOBALS["aeautor"]."',visit=".$GLOBALS["aevisit"].",t_slova='".$GLOBALS["aeslova"]."',".$prwhere_nast_visible;
$dotaz.="zdroj='".$GLOBALS["aezdroj"]."',priority='".$GLOBALS["aepriorita"]."',datum_pl='".$GLOBALS["aedatumpl"]."',skupina_cl='".$GLOBALS["aeskupina"]."',";
$dotaz.="znacky='".$GLOBALS["aeznacky"]."',typ_clanku='".$GLOBALS["aetypcla"]."',sablona='".$GLOBALS["aesablona"]."',".$prwhere_nast_level.$prwhere_nast_index;
$dotaz.="anketa_cl='".$GLOBALS["aeanketa"]."' ";
$dotaz.="where idc='".$GLOBALS["pridc"]."'";
// dotvoreni dotazu dle uziv. prav
if ($akt_je_admin==0):
  // je autor nebo redaktor
  $dotaz.=" and autor in (".$GLOBALS['Uzivatel']->SeznamDostupUser().")";
endif;

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CLA_CL_OK_EDIT_CLA."</p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"preview.php?cisloclanku=".$GLOBALS["aelink"]."\" target=\"preview\">".RS_CLA_CL_PREVIEW."</a></p>\n";
  echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$GLOBALS["pridc"]."&amp;bezzpet=1\">".RS_CLA_CL_ZPET_EDIT_CLA."</a></p>\n";
  if ($akt_je_vydavatel==1): // info e-mail jen v pripade "prava vydavat"
    echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=InfoDopis&amp;modul=clanky&amp;cisloclanku=".$GLOBALS["aelink"]."\">".RS_CLA_CL_POSLI_MAIL."</a></p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
}

// ---[hlavni fce - skupiny souvisejicich clanku]-----------------------------------

/*
  VypisSkupCl()
  FormPrSkupCl()
  PridejSkupCl()
  FormUprSkupCl()
  UpravSkupCl()
  SmazSkupCl()
*/

function VypisSkupCl()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_ZPET."</a> -
<a href=\"admin.php?akce=AddArtGroup&amp;modul=clanky\" class=\"navigace\">".RS_CLA_SS_PRIDAT_SKUP."</a></p>\n";

$dotazskup=phprs_sql_query("select ids,nazev_skup from ".$GLOBALS["rspredpona"]."skup_cl order by nazev_skup",$GLOBALS["dbspojeni"]); // detekce cl. skupin
$pocetskup=phprs_sql_num_rows($dotazskup);

// vypis vsech skupin
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"left\"><b>".RS_CLA_SS_NAZEV_SKUP."</b></td>
<td align=\"left\"><b>".RS_CLA_SS_AKCE."</b></td></tr>\n";
if ($pocetskup==0):
  // zadny zaznam
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\">".RS_CLA_SS_ZADNY_ZAZNAM."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazskup)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data['nazev_skup']."&nbsp;</td>";
    echo "<td align=\"left\"><a href=\"admin.php?akce=AcEdGroup&amp;modul=clanky&amp;prids=".$pole_data['ids']."\">".RS_CLA_SS_UPRAVIT."</a> / ";
    echo "<a href=\"admin.php?akce=AcDelGroup&amp;modul=clanky&amp;prids=".$pole_data['ids']."\">".RS_CLA_SS_SMAZ."</a></td></tr>\n";
  endwhile;
endif;
echo "</table>\n";
}

function FormPrSkupCl()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_ZPET."</a> -
<a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\" class=\"navigace\">".RS_CLA_SS_ZPET_PREHLED."</a></p>\n";
// formular
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_SS_FORM_NAZEV_SKUP."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"\" size=\"40\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddGroup\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function PridejSkupCl()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);

// pridani skup. clanku
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."skup_cl values(null,'".$GLOBALS["prnazev"]."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CLA_SS_OK_ADD_SKUP."</p>\n"; // vse OK
endif;
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\">".RS_CLA_SS_ZPET_PREHLED."</a></p>\n";
}

function FormUprSkupCl()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\" class=\"navigace\">".RS_CLA_SS_ZPET_PREHLED."</a></p>\n";

$dotazcla=phprs_sql_query("select ids,nazev_skup from ".$GLOBALS["rspredpona"]."skup_cl where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]); // nacteni nazvu skupiny
$pole_data=phprs_sql_fetch_assoc($dotazcla);

echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CLA_SS_FORM_NAZEV_SKUP."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" value=\"".$pole_data['nazev_skup']."\" size=\"40\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"Ac2EdGroup\"><input type=\"hidden\" name=\"prids\" value=\"".$pole_data['ids']."\">
<input type=\"hidden\" name=\"modul\" value=\"clanky\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>";
}

function UpravSkupCl()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);

// uprava skup. clanku
@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."skup_cl set nazev_skup='".$GLOBALS["prnazev"]."' where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CLA_SS_OK_EDIT_SKUP."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\">".RS_CLA_SS_ZPET_PREHLED."</a></p>\n";
}

function SmazSkupCl()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

$chyba=0;

// kontrola moznosti vymazani radku
$dotazmnoz=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where skupina_cl='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($dotazmnoz!==false&&phprs_sql_num_rows($dotazmnoz)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazmnoz);
  if ($pole_data['pocet']>0):
    // nelze skupinu vymazat - propojena s jednim nebo vice clanky
    echo "<p align=\"center\" class=\"txt\">".RS_CLA_SS_ERR_SKUP_JE_AKTIVNI."</p>\n";
    $chyba=1; // chyba
  endif;
endif;

if ($chyba==0):
  // vymazani skupiny clanku
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."skup_cl where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error C7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_CLA_SS_OK_DEL_SKUP."</p>\n"; // vse OK
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowArtGroup&amp;modul=clanky\">".RS_CLA_SS_ZPET_PREHLED."</a></p>\n";
}

// ---[hlavni fce - posta]----------------------------------------------------------

/*
  InfoDopis()
  AcInfoDopis()
*/

function InfoDopis()
{
// bezpecnostni korekce
$GLOBALS["cisloclanku"]=phprs_sql_escape_string($GLOBALS["cisloclanku"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\" class=\"navigace\">".RS_CLA_CL_ZPET."</a></p>\n";

$dotazcla=phprs_sql_query("select titulek from ".$GLOBALS["rspredpona"]."clanky where link='".$GLOBALS["cisloclanku"]."'",$GLOBALS["dbspojeni"]);
if ($dotazcla!==false):
  $pocetcla=phprs_sql_num_rows($dotazcla);
else:
  $pocetcla=0;
endif;

if ($pocetcla==1): // test na jedinecnost clanku
  // nacteni titulku clanku
  $pole_data=phprs_sql_fetch_assoc($dotazcla);
  // sestaveni - predmet mailu
  $mail_titulek=$GLOBALS['wwwname']." ".RS_CLA_PC_PREDMET_MAIL." ".date("d.m.Y");
  // sestaveni - obsah mailu
  $mail_obsah=RS_CLA_PC_OBS_MAIL_1."\n";
  $mail_obsah.="==========\n\n";
  $mail_obsah.=$pole_data['titulek'].":\n";
  $mail_obsah.=$GLOBALS['baseadr']."view.php?cisloclanku=".$GLOBALS["cisloclanku"]."\n\n";
  $mail_obsah.=RS_CLA_PC_OBS_MAIL_2."\n".RS_CLA_PC_OBS_MAIL_3;
  // formular
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<p align=\"center\" class=\"txt\">\n";
  echo RS_CLA_PC_FROM_PREDMET.":<br><input type=\"text\" name=\"prtitulek\" value=\"".$mail_titulek."\" size=\"60\" class=\"textpole\"><br><br>\n";
  echo RS_CLA_PC_FORM_OBSAH.":<br><textarea name=\"probsah\" rows=\"7\" cols=\"60\" class=\"textbox\">".$mail_obsah."</textarea><br><br>\n";
  echo "<input type=\"submit\" value=\" ".RS_CLA_PC_TL_ODELAT_MAIL." \" class=\"tl\">\n";
  echo "</p>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcInfoDopis\"><input type=\"hidden\" name=\"modul\" value=\"clanky\">\n";
  echo "</form>\n";
else:
  // nelze identifikovat clanek
  echo "<p align=\"center\" class=\"txt\">Error P1: ".RS_CLA_PC_ERR_NEEXISTUJE_CLA."</p>\n";
endif;
}

function AcInfoDopis()
{
// odeslani e-mailu
$odeslani_posty = new CPosta();
$odeslani_posty->NastavInfoMail();
$odeslani_posty->Nastav("predmet",$GLOBALS['prtitulek']);
$odeslani_posty->Nastav("obsah",$GLOBALS['probsah']);
$odeslani_posty->Odesilac();

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=Articles&amp;modul=clanky\">".RS_CLA_CL_ZPET."</a></p>\n";
}

?>