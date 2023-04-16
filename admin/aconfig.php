<?php

######################################################################
# phpRS Administration Engine - Config's section 1.6.3
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: *

/*
Seznam promennych v tabulce: rs_config
- aktivni_anketa (hodnota: cislo)
- pocet_clanku (hodnota: cislo)
- posledni_zmena (hodnota: specialni retezec znaku)
- hlidat_platnost (hodnota: cislo)
- pocet_novinek (hodnta: cislo)
- global_sab (hodnota: cislo)
- povolit_str (hodnota: cislo)
- hlidat_level (hodnota: cislo)
- zobrazit_zakaz (hodnota: cislo)
- default_level (hodnota: cislo)
- default_reg_level (hodnota: cislo)
- captcha_komentare (hodnota: cislo)
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // konfigurace - zakladni nastaveni
     case "ShowConfig": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          ShowConfig();
          break;
     case "SaveConfig": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          SaveConfig();
          break;
     // konfigurace - plug-iny
     case "ShowPlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          ShowPlugin();
          break;
     case "SavePlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          SavePlugin();
          break;
     case "DelPlugin": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          DelPlugin();
          break;
     // konfigurace - globalni a clankove sablony
     case "SprSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          SpravaSab();
          break;
     case "DelGlobSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          SmazGlobalSab();
          break;
     case "DelClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          SmazClaSab();
          break;
     case "AddSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          FormPrSab();
          break;
     case "AcAddSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          PridatSab();
          break;
     case "NastClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          NastClaSab();
          break;
     case "AcNastClaSab": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AcNastClaSab();
          break;
     // konfigurace - moduly
     case "ShowModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          ShowModul();
          break;
     case "StavModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          StavModul();
          break;
     case "KonfigModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          KonfigModul();
          break;
     case "EditModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          EditModul();
          break;
     case "AcEditModul": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AcEditModul();
          break;
     // konfigurace - levely
     case "ShowLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          ShowLevel();
          break;
     case "AddLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AddLevel();
          break;
     case "AcAddLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AcAddLevel();
          break;
     case "DelLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          DelLevel();
          break;
     case "EditLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          EditLevel();
          break;
     case "AcEditLevel": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AcEditLevel();
          break;
          
     case "AddPlug": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AddPlug();
          break;          
     case "AcAddPlug": AdminMenu();
          echo "<h2 align=\"center\">".RS_CFG_ROZ_SPRAVA_CFG."</h2>";
          AcAddPlug();
          break;         
          
endswitch;

// ---[pomocne fce]-----------------------------------------------------------------

// nacteni konfiguracni hodnoty z konfig. tab
function NactiKonfigHod($promenna = '', $typ = '')
{
// bezpecnostni korekce
$promenna=phprs_sql_escape_string($promenna);

switch ($typ):
  case 'varchar': $dotaz="select hodnota from ".$GLOBALS["rspredpona"]."config where promenna='".$promenna."'"; break;
endswitch;

$dotazhod=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazhod===false):
  // promenna neexistuje
  $vysledek='';
else:
  if (phprs_sql_num_rows($dotazhod)>0):
    // promenna existuje - nacteni hodnoty
    list($vysledek)=phprs_sql_fetch_row($dotazhod);
  else:
    // promenna neexistuje
    $vysledek='';
  endif;
endif;

return $vysledek;
}

function KonfOptGlobSab($hledam = 0)
{
$vysl='';

$dotazpom=phprs_sql_query("select ids,nazev_sab,typ_sab from ".$GLOBALS["rspredpona"]."global_sab order by nazev_sab",$GLOBALS["dbspojeni"]);
$pocetpom=phprs_sql_num_rows($dotazpom);

$nalezeno=0;
while ($pole_data = phprs_sql_fetch_assoc($dotazpom)):
  $vysl.="<option value=\"".$pole_data["ids"]."\"";
  if ($pole_data["ids"]==$hledam): $vysl.=" selected"; $nalezeno=1; endif;
  $vysl.=">".$pole_data["nazev_sab"];
  if ($pole_data["typ_sab"]!=''): $vysl.=" (".$pole_data["typ_sab"].")"; endif;
  $vysl.="</option>\n";
endwhile;

// test na vysledek nastaveni glob. sablony
if ($nalezeno==0):
  $vysl.="<option value=\"".$hledam."\" selected>".RS_CFG_POM_ERR_ZADNA_GLOB_SAB."</option>\n"; // neni prirazena zadna glob. sab.
endif;

return $vysl;
}

function KonfOptAnkety($hledam = 0)
{
$vysl='';

$dotazpom=phprs_sql_query("select ida,titulek from ".$GLOBALS["rspredpona"]."ankety order by titulek",$GLOBALS["dbspojeni"]);
$pocetpom=phprs_sql_num_rows($dotazpom);

if ($hledam==0):
  $vysl.="<option value=\"0\" selected>".RS_CFG_POM_ERR_BEZ_ANKETY."</option>\n"; // bez ankety; vybrano v menu
else:
  $vysl.="<option value=\"0\">".RS_CFG_POM_ERR_BEZ_ANKETY."</option>\n"; // bez ankety
endif;

while ($pole_data = phprs_sql_fetch_assoc($dotazpom)):
  $vysl.="<option value=\"".$pole_data["ida"]."\"";
  if ($pole_data["ida"]==$hledam): $vysl.=" selected"; endif;
  $vysl.=">".$pole_data["titulek"]."</option>\n";
endwhile;

return $vysl;
}

function KonfOptLevely($hledam = 0)
{
$vysl='';

$dotazsab=phprs_sql_query("select idl,nazev_levelu,hodnota from ".$GLOBALS["rspredpona"]."levely order by hodnota,nazev_levelu",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);

if ($pocetsab==0):
  $vysl.="<option value=\"0\">".RS_CFG_POM_ERR_ZADNY_LEVEL."</option>\n"; // neni definovan zadny level
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
    $vysl.="<option value=\"".$pole_data['idl']."\"";
    if ($hledam==$pole_data['idl']): $vysl.=" selected"; endif;
    $vysl.=">".$pole_data['nazev_levelu']." (".$pole_data['hodnota'].")</option>\n";
  endwhile;
endif;

return $vysl;
}

function OptClankoveSab($hledam = 0)
{
$vysl='';

$dotazpom=phprs_sql_query("select ids,nazev_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by nazev_cla_sab",$GLOBALS["dbspojeni"]);
$pocetpom=phprs_sql_num_rows($dotazpom);

while ($pole_data = phprs_sql_fetch_assoc($dotazpom)):
  $vysl.='<option value="'.$pole_data["ids"].'"';
  if ($pole_data["ids"]==$hledam): $vysl.=' selected'; endif;
  $vysl.='>'.$pole_data["nazev_cla_sab"]."</option>\n";
endwhile;

return $vysl;
}

// ---[hlavni fce - zakladni konf.]-------------------------------------------------

function ShowConfig()
{
// linky na spravu subcasti
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=SprSab&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_NADPIS_SABLONY."</a> -
<a href=\"admin.php?akce=ShowPlugin&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_NADPIS_PLUGINY."</a> -
<a href=\"admin.php?akce=ShowModul&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_NADPIS_MODULY."</a> -
<a href=\"admin.php?akce=ShowLevel&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_NADPIS_LEVELY."</a></p>\n";

// nadpis konfigurace zakladniho nastaveni phpRS systemu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_KONFIGURACE."</big></strong></p>\n";

// start konfig. tab
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_KO_PROMENNA."</b></td><td align=\"left\"><b>".RS_CFG_KO_HODNOTA."</b></td></tr>\n";

$akt_cis_prom=0; // inic. pocitadla promennych

// zobrazeni promenne global_sab
$konf_globalsab=NactiKonfigHod('global_sab','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_GLOB_SAB."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptGlobSab($konf_globalsab)."</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"global_sab\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne pocet_clanku
$konf_pocetcl=NactiKonfigHod('pocet_clanku','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_POCET_CLA."</b></td>
<td align=\"left\">
<input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"".$konf_pocetcl."\" size=\"3\" class=\"textpole\">
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"pocet_clanku\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"1\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne aktivni_anketa
$konf_aktanket=NactiKonfigHod('aktivni_anketa','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_ANKETA."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptAnkety($konf_aktanket)."</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"aktivni_anketa\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne hlidat_platnost
$konf_platnost=NactiKonfigHod('hlidat_platnost','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_PLATNOST_CLA."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_platnost==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"hlidat_platnost\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne pocet_novinek
$konf_pocetnov=NactiKonfigHod('pocet_novinek','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_NOVINKY."</b></td>
<td align=\"left\">
<select name=\"prprepinac[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_pocetnov==0):
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option></select> ".RS_CFG_KO_KOLIK." <input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"\" size=\"3\" class=\"textpole\">\n";
else:
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option></select> ".RS_CFG_KO_KOLIK." <input type=\"text\" name=\"prhodnota[".$akt_cis_prom."]\" value=\"".$konf_pocetnov."\" size=\"3\" class=\"textpole\">\n";
endif;
echo "<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"pocet_novinek\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"1\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne povolit_str
$konf_povolitstr=NactiKonfigHod('povolit_str','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_STRANKOVANI."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_povolitstr==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"povolit_str\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne captcha_komentare
$konf_antispam=NactiKonfigHod('captcha_komentare','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_CAPTCHA_KOMENTARE."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_antispam==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"captcha_komentare\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne hlidat_level
$konf_hlidatlevel=NactiKonfigHod('hlidat_level','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_HLIDAT_LEVELY."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_hlidatlevel==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"hlidat_level\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne zobrazit_zakaz
$konf_zobrzakaz=NactiKonfigHod('zobrazit_zakaz','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_LEVEL_ZAKAZ_SAB."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">";
if ($konf_zobrzakaz==1):
  echo "<option value=\"1\" selected>".RS_TL_ANO."</option><option value=\"0\">".RS_TL_NE."</option>\n";
else:
  echo "<option value=\"1\">".RS_TL_ANO."</option><option value=\"0\" selected>".RS_TL_NE."</option>\n";
endif;
echo "</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"zobrazit_zakaz\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne default_level
$konf_defaultlevel=NactiKonfigHod('default_level','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_LEVEL."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptLevely($konf_defaultlevel)."</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"default_level\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// zobrazeni promenne default_reg_level
$konf_defaultreglevel=NactiKonfigHod('default_reg_level','varchar');
echo "<tr class=\"txt\"><td align=\"left\" width=\"250\"><b>".RS_CFG_KO_VOLBA_REG_LEVEL."</b></td>
<td align=\"left\">
<select name=\"prhodnota[".$akt_cis_prom."]\" size=\"1\">".KonfOptLevely($konf_defaultreglevel)."</select>
<input type=\"hidden\" name=\"prpromenna[".$akt_cis_prom."]\" value=\"default_reg_level\">
<input type=\"hidden\" name=\"prtyp[".$akt_cis_prom."]\" value=\"varchar\">
<input type=\"hidden\" name=\"prprepinac[".$akt_cis_prom."]\" value=\"1\">
<input type=\"hidden\" name=\"prtestint[".$akt_cis_prom."]\" value=\"0\">
</td></tr>\n";
$akt_cis_prom++;

// konec konfig. tab
echo "<tr class=\"txt\"><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\" ".RS_CFG_KO_TL_ULOZ_NASTAV." \" class=\"tl\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"SaveConfig\"><input type=\"hidden\" name=\"modul\" value=\"config\">
</form>
<br>\n";
}

function SaveConfig()
{
$chyba=0; // inic. chyby

if (!isset($GLOBALS['prhodnota'])||!isset($GLOBALS['prpromenna'])||!isset($GLOBALS['prtyp'])||!isset($GLOBALS['prprepinac'])||!isset($GLOBALS['prtestint'])):
  $chyba=1; // chybi jedna z potrebnych promennych
endif;
$pocet_radku=count($GLOBALS["prpromenna"]);
if ($pocet_radku<=0):
  $chyba=1; // chybi promenne
endif;

if ($chyba==0):
  // inic. celk. chyby
  $chyba_vse=0;
  // ulozeni jednotlivych konf. prom.
  for ($pom=0;$pom<$pocet_radku;$pom++):
    // test na integer hodnotu
    if ($GLOBALS["prtestint"][$pom]==1):
      if (!preg_match("|^\d*$|",$GLOBALS["prhodnota"][$pom])): $GLOBALS["prhodnota"][$pom]=0; endif;
    endif;
    // test na stav prepinace
    if ($GLOBALS["prprepinac"][$pom]==0):
      $GLOBALS["prhodnota"][$pom]=0;
    endif;
    // bezpec. kontrola
    $GLOBALS["prhodnota"][$pom]=phprs_sql_escape_string($GLOBALS["prhodnota"][$pom]);
    $GLOBALS["prpromenna"][$pom]=phprs_sql_escape_string($GLOBALS["prpromenna"][$pom]);
    // typ konf. prom. + sestaveni dotazu
    switch ($GLOBALS['prtyp'][$pom]):
      case 'varchar': $dotaz="update ".$GLOBALS["rspredpona"]."config set hodnota='".$GLOBALS["prhodnota"][$pom]."' where promenna='".$GLOBALS["prpromenna"][$pom]."'"; break;
      default: $dotaz='';
    endswitch;
    @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error C1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    endif;
  endfor;
  // test na celk. vysledek
  if ($chyba_vse==0):
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_KO_OK_EDIT_CFG."</p>\n"; // vse OK
  endif;
endif;

// navrat
ShowConfig();
}

// ---[hlavni fce - plug-iny]-------------------------------------------------------

function ShowPlugin()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis sprava plug-inu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_PLUGINY."</big></strong></p>\n";

// dotaz plug-iny
$dotazplug=phprs_sql_query("select idp,nazev,pristup,menu,sys_blok from ".$GLOBALS["rspredpona"]."plugin order by nazev",$GLOBALS["dbspojeni"]);
$pocetplug=phprs_sql_num_rows($dotazplug);
// vypis polozek
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SP_NAZEV."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_PRAVA."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_MENU."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_SYS_BLOK."</b></td>
<td align=\"center\"><b>".RS_CFG_SP_AKCE."</b></td>
</tr>\n";
if ($pocetplug==0):
  // zadne plug-iny
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"5\">".RS_CFG_SP_ZADNY_PLUGIN."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetplug;$pom++):
    $akt_pole_plug=phprs_sql_fetch_assoc($dotazplug);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_plug["nazev"]."</td>";
    // pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
    switch ($akt_pole_plug["pristup"]):
      case 1: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_NASTAVENI."</td>"; break;
      case 2: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_VSICHNI."</td>"; break;
      case 3: echo "<td align=\"center\">".RS_CFG_SP_PRAVA_ADMIN."</td>"; break;
    endswitch;
    if ($akt_pole_plug["menu"]): echo "<td align=\"center\">".RS_TL_ANO."</td>"; else: echo "<td align=\"center\">".RS_TL_NE."</td>"; endif;
    if ($akt_pole_plug["sys_blok"]): echo "<td align=\"center\">".RS_TL_ANO."</td>"; else: echo "<td align=\"center\">".RS_TL_NE."</td>"; endif;
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelPlugin&amp;modul=config&amp;pridp=".$akt_pole_plug["idp"]."\">".RS_CFG_SP_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// vyhledani noveho pluginu
// ToDo: objevit se jen pokud novy existuje
echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AddPlug\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<p class=\"txt\" align=\"center\"><input type=\"submit\" value=\" ".RS_CFG_PLUG_HLEDEJ." \" title =\" ".RS_CFG_PLUG_HLEDEJ_INFO." \" class=\"tl\"></p>
</form>\n";

}

function DelPlugin()
{
$GLOBALS["pridp"]=addslashes($GLOBALS["pridp"]);

// odstraneni plug-inu z registracni tabulky
@$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."plugin where idp='".$GLOBALS["pridp"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_OK_DEL_PLUGIN."</p>\n"; // vse OK
  // odstraneni zaznamu z tabulky s pristovymi pravy
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."moduly_prava where fk_id_plugin='".$GLOBALS["pridp"]."' and plugin='1'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error C7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowPlugin&amp;modul=config\">".RS_CFG_SP_ZPET_PLUGINY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

// ---[hlavni fce - sablony]--------------------------------------------------------

function SpravaSab()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis global. sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_GLOBAL_SAB."</big></strong></p>\n";

$dotazsab=phprs_sql_query("select ids,nazev_sab,typ_sab,adr_sab from ".$GLOBALS["rspredpona"]."global_sab order by nazev_sab",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);
// vypis
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SS_NAZEV_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_TYP_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_UMISTENI_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td>
</tr>\n";
if ($pocetsab==0):
  // zadna globalni sablona
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_CFG_SS_ZADNA_GLOB_SAB."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetsab;$pom++):
    $akt_pole_data=phprs_sql_fetch_assoc($dotazsab);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_data["nazev_sab"]."</td>";
    echo "<td>".TestNaNic($akt_pole_data["typ_sab"])."</td>";
    echo "<td>".$akt_pole_data["adr_sab"]."</td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelGlobSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// nadpis cla. sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_CAL_SAB."</big></strong></p>\n";

$dotazsab=phprs_sql_query("select ids,nazev_cla_sab,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by nazev_cla_sab",$GLOBALS["dbspojeni"]);
$pocetsab=phprs_sql_num_rows($dotazsab);
// vypis
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_CFG_SS_NAZEV_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_CESTA_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_PRACE_SE_SAB."</b></td>
<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td>
</tr>\n";
if ($pocetsab==0):
  // zadan clankova sablona
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_CFG_SS_ZADNA_CLA_SAB."</td></tr>\n";
else:
  for ($pom=0;$pom<$pocetsab;$pom++):
    $akt_pole_data=phprs_sql_fetch_assoc($dotazsab);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_data["nazev_cla_sab"]."</td>";
    echo "<td>".$akt_pole_data["soubor_cla_sab"]."</td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=NastClaSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_PRIRADIT_SAB."</a></td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=DelClaSab&amp;modul=config&amp;prids=".$akt_pole_data["ids"]."\">".RS_CFG_SS_SMAZ."</a></td></tr>\n";
  endfor;
endif;
echo "</table>\n";

// nadpis vyhledavani novych sablon
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NOVE_SAB."</big></strong></p>\n";
// napoveda k zadani cesty
echo "<p class=\"txt\" align=\"center\">".RS_CFG_SS_CESTA_SAB_ADR_INFO."</p>\n";
// vyhledani nove sablony
echo "<form action=\"admin.php\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AddSab\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<p class=\"txt\" align=\"center\"><b>".RS_CFG_SS_CESTA_SAB_ADR."</b> <input type=\"text\" name=\"prcesta\" size=\"30\" class=\"textpole\" value=\"image\">
<input type=\"submit\" value=\" ".RS_CFG_SS_TL_HLEDAT." \" class=\"tl\"></p>
</form>\n";

echo "<p>&nbsp;</p>";
}

function SmazGlobalSab()
{
// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// odstraneni globalni sablony
@$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."global_sab where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_DEL_GLOB_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function SmazClaSab()
{
$chyba=0; // inic. chyby

// bezpecnostni korekce
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// test na aktivni stav clankove sablony (= je prirazena nejakemu clanku)
$dotazmn=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where sablona='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($dotazmn!==false&&phprs_sql_num_rows($dotazmn)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazmn);
  if ($pole_data['pocet']>0):
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_AKTIVNI_CLA_SAB."</p>\n";
    $chyba=1;
  endif;
endif;

// odstraneni globalni sablony
if ($chyba==0):
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."cla_sab where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error C9: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_DEL_CLA_SAB."</p>\n"; // vse OK
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function FormPrSab()
{
$chyba=0; // inic. chyby

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a> -
<a href=\"admin.php?akce=SprSab&amp;modul=config\" class=\"navigace\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";

// test na vstup
if (!is_dir($GLOBALS["prcesta"])):
  // system nemuze nalezt vami zadany adresar
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_NEEXISTUJE_ADR."</p>\n";
  $chyba=1;
endif;

if ($chyba==0):
  // inic. pole adresaru
  $pole_adr_pozice=0;
  $pole_adr_pocet=1;
  $pole_adr[0]=$GLOBALS["prcesta"];
  // inic. pole vysledku
  $pole_vysl_pocet=0;
  $pole_vysl=array();

  while ($pole_adr_pozice<$pole_adr_pocet):
    $adresar=dir($pole_adr[$pole_adr_pozice]);
    //echo "Cesta: ".$adresar->path."<br>\n"; // ** ladici radek **
    while($najdi=$adresar->read()):
      //echo $najdi."<br>\n"; // ** ladici radek **
      if ($najdi!="."&&$najdi!=".."):
        // soubory a adresare
        $cela_cesta=$pole_adr[$pole_adr_pozice]."/".$najdi;
        if (is_dir($cela_cesta)):
          // polozka je adresar
          $pole_adr[$pole_adr_pocet]=$cela_cesta;
          $pole_adr_pocet++;
        else:
          // polozka je soubor
          if ($najdi=="install.php"):
            $pole_vysl[$pole_vysl_pocet]=$cela_cesta;
            $pole_vysl_pocet++;
          endif;
        endif;
        // konec - soubory a adresare
      endif;
    endwhile;
    $adresar->close();

    $pole_adr_pozice++;
  endwhile;
endif;

// nadpis nalezene sab.
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NALEZENE_SAB."</big></strong></p>\n";

if ($pole_vysl_pocet>0):
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CFG_SS_CESTA_INSTAL_SB."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SS_AKCE."</b></td></tr>\n";
  for ($pom=0;$pom<$pole_vysl_pocet;$pom++):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td>".$pole_vysl[$pom]."<input type=\"hidden\" name=\"prsablona[".$pom."]\" value=\"".$pole_vysl[$pom]."\"></td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prstav[]\" value=\"".$pom."\"> ".RS_CFG_SS_INSTALOVAT."</a></td></tr>\n";
  endfor;
  echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\" ".RS_CFG_PL_TL_NAINSTALUJ." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"AcAddSab\"><input type=\"hidden\" name=\"modul\" value=\"config\">\n";
  echo "</form>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ZADNA_SABLONA."</p>\n";
endif;
}

function PridatSab()
{
$chyba_inst=0; // inic. chyby

if (isset($GLOBALS["prstav"])):
  $pocet_sab=count($GLOBALS["prstav"]);
else:
  $pocet_sab=0;
endif;

if ($pocet_sab>0):
  for($pom=0;$pom<$pocet_sab;$pom++):
    $id_sablony=$GLOBALS["prstav"][$pom];
    if (isset($GLOBALS["prsablona"][$id_sablony])):
      if (file_exists($GLOBALS["prsablona"][$id_sablony])):
        // vlozeni instalacniho souboru
        include($GLOBALS["prsablona"][$id_sablony]);
        // instalace globalnich sablon
        if (isset($rs_gsab_nazev)):
          // --- instalace globalnich sablon ---
          $pocetsab=count($rs_gsab_nazev);
          $chyba_vse=0;

          for ($p1=0;$p1<$pocetsab;$p1++):
            $chyba_test=0;
            if ($rs_gsab_nazev[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_ident[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_soubor[$p1]==''): $chyba_test=1; endif;
            if ($rs_gsab_adresar[$p1]==''): $chyba_test=1; endif;
            // instalace sablony
            if ($chyba_test==0):
              // bezpecnostni korekce
              $rs_gsab_nazev[$p1]=phprs_sql_escape_string($rs_gsab_nazev[$p1]);
              $rs_gsab_typ[$p1]=phprs_sql_escape_string($rs_gsab_typ[$p1]);
              $rs_gsab_ident[$p1]=phprs_sql_escape_string($rs_gsab_ident[$p1]);
              $rs_gsab_soubor[$p1]=phprs_sql_escape_string($rs_gsab_soubor[$p1]);
              $rs_gsab_adresar[$p1]=phprs_sql_escape_string($rs_gsab_adresar[$p1]);
              // test na duplicitu
              $dotaz="select ids from ".$GLOBALS["rspredpona"]."global_sab ";
              $dotaz.="where nazev_sab='".$rs_gsab_nazev[$p1]."' and typ_sab='".$rs_gsab_typ[$p1]."' and ident_sab='".$rs_gsab_ident[$p1]."' and soubor_sab='".$rs_gsab_soubor[$p1]."' and adr_sab='".$rs_gsab_adresar[$p1]."'";
              $dotazshoda=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
              if (phprs_sql_num_rows($dotazshoda)==0):
                // stejna sablona neexistuje
                $dotaz="insert into ".$GLOBALS["rspredpona"]."global_sab ";
                $dotaz.="values(null,'".$rs_gsab_nazev[$p1]."','".$rs_gsab_typ[$p1]."','".$rs_gsab_ident[$p1]."','".$rs_gsab_soubor[$p1]."','".$rs_gsab_adresar[$p1]."')";
                @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
                if ($error === false):
                  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_1." ".$rs_gsab_nazev[$p1]." ".RS_CFG_SS_ERR_GLOB_SAB_NEOCEK_CHYBA_2."</p>\n";
                  $chyba_vse=1;
                endif;
              else:
                // existuje stejna sablona
                echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_1." ".$rs_gsab_nazev[$p1]." ".RS_CFG_SS_ERR_GLOB_SAB_SHODA_SAB_2."</p>\n";
                $chyba_vse=1;
              endif;
            else:
              // chybi nektery z potrebnych parametru
              echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_GLOB_SAB_CHYBI_ATR."</p>\n";
              $chyba_vse=1;
            endif;
          endfor;

          if ($chyba_vse==1):
            $chyba_inst=1;
          endif;
          // --- konec: instalace globalnich sablon ---
        endif;
        // instalace clankovych sablon
        if (isset($rs_csab_nazev)):
          // --- instalace clankovych sablon ---
          $pocetsab=count($rs_csab_nazev);
          $chyba_vse=0;

          for ($p1=0;$p1<$pocetsab;$p1++):
            $chyba_test=0;
            if ($rs_csab_nazev[$p1]==''): $chyba_test=1; endif;
            if ($rs_csab_soubor[$p1]==''): $chyba_test=1; endif;
            // instalace sablony
            if ($chyba_test==0):
              // bezpecnostni korekce
              $rs_csab_nazev[$p1]=phprs_sql_escape_string($rs_csab_nazev[$p1]);
              $rs_csab_soubor[$p1]=phprs_sql_escape_string($rs_csab_soubor[$p1]);
              // test na duplicitu
              $dotaz="select ids from ".$GLOBALS["rspredpona"]."cla_sab ";
              $dotaz.="where nazev_cla_sab='".$rs_csab_nazev[$p1]."' and soubor_cla_sab='".$rs_csab_soubor[$p1]."'";
              $dotazshoda=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
              if (phprs_sql_num_rows($dotazshoda)==0):
                // stejna sablona neexistuje
                $dotaz="insert into ".$GLOBALS["rspredpona"]."cla_sab ";
                $dotaz.="values(null,'".$rs_csab_nazev[$p1]."','".$rs_csab_soubor[$p1]."')";
                @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
                if ($error === false):
                  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_1." ".$rs_csab_nazev[$p1]." ".RS_CFG_SS_ERR_CLA_SAB_NEOCEK_CHYBA_2."</p>\n";
                  $chyba_vse=1;
                endif;
              else:
                // existuje stejna sablona
                echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_1." ".$rs_csab_nazev[$p1]." ".RS_CFG_SS_ERR_CLA_SAB_SHODA_SAB_2."</p>\n";
                $chyba_vse=1;
              endif;
            else:
              // chybi nektery z potrebnych parametru
              echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CLA_SAB_CHYBI_ATR."</p>\n";
              $chyba_vse=1;
            endif;
          endfor;

          if ($chyba_vse==1):
            $chyba_inst=1;
          endif;
          // --- konec: instalace clankovych sablon ---
        endif;
      else:
        // system nemuze nalezt instalacni soubor
        echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_ERR_CHYBI_INSTAL_SB."</p>\n";
      endif;
    endif;
  endfor;
endif;

if ($chyba_inst==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_ADD_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function NastClaSab()
{
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a> -
<a href=\"admin.php?akce=SprSab&amp;modul=config\" class=\"navigace\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";

$dotazsab=phprs_sql_query("select ids,nazev_cla_sab,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab where ids=".$GLOBALS["prids"],$GLOBALS["dbspojeni"]);
if ($dotazsab!==false&&phprs_sql_num_rows($dotazsab)==1):
  $pole_data=phprs_sql_fetch_assoc($dotazsab);
  echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_SS_NADPIS_NAZEV_CLA_SAB.":<br>\"".$pole_data['nazev_cla_sab']."\"</big></strong></p>\n";
endif;

// formular pro nastaveni
echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\">
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_CFG_SS_VYBRANA_CLA_SAB."</b></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"przpusob\" value=\"vse\"> <b>".RS_CFG_SS_PRIRADIT_VSEM."</b></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"przpusob\" value=\"vol\" checked> <b>".RS_CFG_SS_PRIRADIT_PODMINCE."</b><br>".RS_CFG_SS_VZTAH_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINKA_TEMA."</b></td>
<td align=\"left\">";
$poletopic=GenerujSeznamSCestou();
if (!is_array($poletopic)):
  echo RS_CFG_SS_ZADNA_RUBRIKA; // chyba; neexistuje rubrika
else:
  echo "<select name=\"prtema[]\" size=\"10\" multiple>";
  $pocettopic=count($poletopic);
  for ($pom=0;$pom<$pocettopic;$pom++):
    echo "<option value=\"".$poletopic[$pom][0]."\">".$poletopic[$pom][1]."</option>\n";
  endfor;
  echo "</select>";
endif;
echo "</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINKA_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor[]\" size=\"10\" multiple>".OptAutori()."</select></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SS_PODMINA_CLA_SAB."</b></td>
<td align=\"left\"><select name=\"prclasab[]\" size=\"10\" multiple>".OptClankoveSab()."</select></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcNastClaSab\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_CFG_SS_TL_NASTAVIT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcNastClaSab()
{
$chyba=0;
$chyba_nast=0;

$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["przpusob"]=phprs_sql_escape_string($GLOBALS["przpusob"]);

if (!isset($GLOBALS["przpusob"])): $chyba=1; endif;
if (!isset($GLOBALS["prids"])): $chyba=1; endif;

if ($chyba==0):
  switch($GLOBALS["przpusob"]):
    case "vse": // nastavit vsem cl.
      $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."'";
      @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
      if ($error === false):
        echo "<p align=\"center\" class=\"txt\">Error C10: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
        $chyba_nast=1;
      endif;
      break;
    case "vol": // nastavit jen vybranym cl.
      // temata
      if (isset($GLOBALS["prtema"])):
        $pocet_pol=count($GLOBALS["prtema"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=phprs_sql_escape_string($GLOBALS["prtema"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where tema in (".$prwhere.")";
        @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
        if ($error === false):
          echo "<p align=\"center\" class=\"txt\">Error C11: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      // autori
      if (isset($GLOBALS["prautor"])):
        $pocet_pol=count($GLOBALS["prautor"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=phprs_sql_escape_string($GLOBALS["prautor"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where autor in (".$prwhere.")";
        @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
        if ($error === false):
          echo "<p align=\"center\" class=\"txt\">Error C12: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      // clankove sablony
      if (isset($GLOBALS["prclasab"])):
        $pocet_pol=count($GLOBALS["prclasab"]);
        $prwhere="";
        for($pom=0;$pom<$pocet_pol;$pom++):
          if ($pom>0): $prwhere.=","; endif;
          $prwhere.=phprs_sql_escape_string($GLOBALS["prclasab"][$pom]);
        endfor;
        $dotaz="update ".$GLOBALS["rspredpona"]."clanky set sablona='".$GLOBALS["prids"]."' where sablona in (".$prwhere.")";
        @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
        if ($error === false):
          echo "<p align=\"center\" class=\"txt\">Error C13: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
          $chyba_nast=1;
        endif;
      endif;
      break;
  endswitch;
endif;

// globalni vysledek
if ($chyba_nast==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SS_OK_NASTAV_CLA_SAB."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=SprSab&amp;modul=config\">".RS_CFG_SS_ZPET_SABLONY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

// ---[hlavni fce - nastaveni modulu]-----------------------------------------------

/*
  ShowModul()
  StavModul()
  KonfigModul()
  EditModul()
  AcEditModul()
*/

function ShowModul()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a></p>\n";

// nadpis sprava modulu
echo "<p align=\"center\" class=\"txt\"><strong><big>".RS_CFG_KO_NADPIS_MODULY."</big></strong></p>\n";

// sestaveni dotazu
$dotaz="select idm,nazev_modulu,all_prava_users,nazev_menu,poradi_menu,zakladni_modul,jen_admin_modul,blokovat_modul,plugin ";
$dotaz.="from ".$GLOBALS["rspredpona"]."moduly_prava order by poradi_menu";

$dotazmoduly=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetmooduly=phprs_sql_num_rows($dotazmoduly);

if ($pocetmooduly>0):
  // vypis polozek
  echo "<form action=\"admin.php\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\">\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_NAZEV_MODULU."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_NAZEV_MENU."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_PRAVA."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_TYP."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_STAV."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_PORADI."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_CFG_SM_AKCE."</b></td>\n";
  echo "</tr>\n";
  for ($pom=0;$pom<$pocetmooduly;$pom++):
    $akt_pole_moduly=phprs_sql_fetch_assoc($dotazmoduly);
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td>".$akt_pole_moduly["nazev_modulu"]."</td>";
    echo "<td>".$akt_pole_moduly["nazev_menu"]."</td>";
    // pristupova prava
    if ($akt_pole_moduly["jen_admin_modul"]==1):
      echo "<td align=\"center\">".RS_CFG_SM_JEN_ADMIN."</td>"; // jen admin
    else:
      if ($akt_pole_moduly["all_prava_users"]==1):
        echo "<td align=\"center\">".RS_CFG_SM_VSICHNI."</td>"; // vsichni uzivatele
      else:
        echo "<td align=\"center\">".RS_CFG_SM_DLE_NASTAV."</td>"; // dle nastaveni
      endif;
    endif;
    // konec - pristupova prava
    // typ modulu
    if ($akt_pole_moduly["zakladni_modul"]==1):
      echo "<td align=\"center\">".RS_CFG_SM_ZAKLADNI_MODUL."</td>"; // zakladni modul - nelze blokovat
    else:
      echo "<td align=\"center\">".RS_CFG_SM_NASTAV_MODUL."</td>"; // nastavitelny modul
    endif;
    // konec - typ modulu
    // stav modulu
    if ($akt_pole_moduly["zakladni_modul"]==1):
      echo "<td>&nbsp;</td>";
    else:
      if ($akt_pole_moduly["blokovat_modul"]==1):
        echo "<td align=\"center\"><a href=\"admin.php?akce=StavModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."&amp;prstav=0\">".RS_CFG_SM_AKTIVOVAT."</a></td>"; // aktivovat
      else:
        echo "<td align=\"center\"><a href=\"admin.php?akce=StavModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."&amp;prstav=1\">".RS_CFG_SM_BLOKOVAT."</a></td>"; // blokovat
      endif;
    endif;
    // konec - stav modulu
    echo "<td><input type=\"text\" name=\"prporadi[".$pom."]\" size=\"4\" value=\"".$akt_pole_moduly["poradi_menu"]."\" class=\"textpole\"><input type=\"hidden\" name=\"prmodul_id[".$pom."]\" value=\"".$akt_pole_moduly["idm"]."\"></td>";
    echo "<td align=\"center\"><a href=\"admin.php?akce=EditModul&amp;modul=config&amp;pridm=".$akt_pole_moduly["idm"]."\">".RS_CFG_SM_UPRAVIT."</a></td>\n";
    echo "</tr>\n";
  endfor;
  echo "<tr><td align=\"right\" colspan=\"7\"><input type=\"submit\" value=\" ".RS_CFG_SM_TL_ULOZ_NASTAV." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"KonfigModul\"><input type=\"hidden\" name=\"modul\" value=\"config\">\n";
  echo "</form>\n";
endif;
echo "<br>\n";
}

function StavModul()
{
$GLOBALS['pridm']=phprs_sql_escape_string($GLOBALS['pridm']);
$GLOBALS['prstav']=phprs_sql_escape_string($GLOBALS['prstav']);

// update tabulky je omezen pouze na nastavitelne moduly (zakladni_modul=0)
@$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."moduly_prava set blokovat_modul='".$GLOBALS['prstav']."' where idm='".$GLOBALS['pridm']."' and zakladni_modul=0",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C14: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_STAV_MODULU."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function KonfigModul()
{
$chyba_all=0; // inic. chyby - 0 = zadna chyba

// test na pritomnost vsech potrebnych promennych
if (!isset($GLOBALS['prporadi'])||!isset($GLOBALS['prmodul_id'])):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_ERR_CHYBI_ATR."</p>\n";
else:
  $pocet_polozek=count($GLOBALS['prmodul_id']);
  for ($pom=0;$pom<$pocet_polozek;$pom++):
    // inic.
    $akt_id_modul=phprs_sql_escape_string($GLOBALS['prmodul_id'][$pom]);
    $akt_hodnota=phprs_sql_escape_string($GLOBALS['prporadi'][$pom]);
    // nastaveni
    @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."moduly_prava set poradi_menu='".$akt_hodnota."' where idm='".$akt_id_modul."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error C15: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      $chyba_all=1; // chyba
    endif;
  endfor;
endif;

// globalni stav
if ($chyba_all==0):
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_PORADI_MODULU."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\">".RS_CFG_KO_ZPET."</a></p>\n";
}

function EditModul()
{
// bezpecnostni korekce
$GLOBALS['pridm']=phprs_sql_escape_string($GLOBALS['pridm']);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowModul&amp;modul=config\" class=\"navigace\">".RS_CFG_SM_ZPET_MODULY."</a></p>\n";

// formular pro upravu nastaveni modulu
$dotazmodul=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."moduly_prava where idm='".$GLOBALS['pridm']."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazmodul);

echo "<form action=\"admin.php\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SM_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"50\" value=\"".$pole_data['nazev_menu']."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SM_FORM_RGB."</b></td>
<td align=\"left\">#<input type=\"text\" name=\"prbarvabg\" size=\"8\" value=\"".$pole_data['barva_bg']."\" class=\"textpole\"> ".RS_CFG_SM_FORM_RGB_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditModul\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<input type=\"hidden\" name=\"pridm\" value=\"".$pole_data['idm']."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditModul()
{
// bezpecnostni korekce
$GLOBALS['pridm']=phprs_sql_escape_string($GLOBALS['pridm']);
$GLOBALS['prtitulek']=phprs_sql_escape_string($GLOBALS['prtitulek']);
$GLOBALS['prbarvabg']=phprs_sql_escape_string($GLOBALS['prbarvabg']);

// aktualizace nastaveni modulu
$dotaz="update ".$GLOBALS["rspredpona"]."moduly_prava set nazev_menu='".$GLOBALS['prtitulek']."',barva_bg='".$GLOBALS['prbarvabg']."' where idm='".$GLOBALS['pridm']."'";
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C16: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SM_OK_EDIT_MODULU."</p>\n"; // vse OK
endif;

// navrat
ShowModul();
}

// ---[hlavni fce - sprava levelu]--------------------------------------------------

/*
  ShowLevel()
  AddLevel()
  AcAddLevel()
  DelLevel()
  EditLevel()
  AcEditLevel()
*/

function ShowLevel()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=AddLevel&amp;modul=config\" class=\"navigace\">".RS_CFG_SL_PRIDAT_LEVEL."</a></p>\n";

$dotazlevel=phprs_sql_query("select idl,nazev_levelu,hodnota,zakladni from ".$GLOBALS["rspredpona"]."levely order by hodnota",$GLOBALS["dbspojeni"]);
$pocetlevel=phprs_sql_num_rows($dotazlevel);

// vypis polozek
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_CFG_SL_NAZEV."</b></td>
<td align=\"center\"><b>".RS_CFG_SL_HODNOTA."</b></td>
<td align=\"center\"><b>".RS_CFG_SL_AKCE."</b></td>
<td align=\"center\"><b>".RS_CFG_SL_SMAZ."</b></td></tr>\n";
if ($pocetlevel==0):
  // zadny level
  echo "<tr class=\"txt\"><td align=\"center\" colspan=\"4\">".RS_CFG_SL_ZADNY_LEVEL."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazlevel)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["nazev_levelu"]."</td>";
    echo "<td align=\"center\">".$pole_data["hodnota"]."</td>";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditLevel&amp;modul=config&amp;pridl=".$pole_data["idl"]."\">".RS_CFG_SL_UPRAVIT."</a></td>";
    echo "<td align=\"center\">";
    if ($pole_data["zakladni"]==1): // test  na zakladni level; nelze mazat
      echo RS_CFG_SL_SMAZ_NELZE;
    else:
      echo "<input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idl"]."\">";
    endif;
    echo "</td></tr>\n";
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_CFG_SL_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DelLevel\"><input type=\"hidden\" name=\"modul\" value=\"config\">
</form>
<br>\n";
}

function AddLevel()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLevel&amp;modul=config\" class=\"navigace\">".RS_CFG_SL_ZPET_LEVELY."</a></p>\n";

// pridavaci formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SL_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SL_FORM_HODNOTA."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnota\" size=\"5\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">".RS_CFG_SL_FORM_HODNOTA_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddLevel\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcAddLevel()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);

$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);

$nast_zakladni=0; // defaultne false

// pridani levelu
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."levely values(null,'".$GLOBALS["prnazev"]."','".$GLOBALS["prhodnota"]."','".$nast_zakladni."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C17: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_OK_ADD_LEVEL."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLevel&amp;modul=config\">".RS_CFG_SL_ZPET_LEVELY."</a></p>\n";
}

function DelLevel()
{
$chyba=0; // inic. chyby
if (!isset($GLOBALS["prpoleid"])): // inic. pole
 $pocet_pole_id=0;
else:
 $pocet_pole_id=count($GLOBALS["prpoleid"]);
endif;

// vymazani levelu
for ($pom=0;$pom<$pocet_pole_id;$pom++):
  // inic.
  $akt_chyba=0;
  $akt_id_level=phprs_sql_escape_string($GLOBALS["prpoleid"][$pom]);
  // test na aktivni aplikaci levelu - clanky
  $dotazlevel=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."clanky where level_clanku='".$akt_id_level."'",$GLOBALS["dbspojeni"]);
  if ($dotazlevel!==false&&phprs_sql_num_rows($dotazlevel)==1):
    list($pocet_clanku)=phprs_sql_fetch_row($dotazlevel);
    if ($pocet_clanku>0):
      // chyba - Akci nelze provest, protoze k tomuto levelu je prirazen jeden nebo vice clanku!
      echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_ERR_DEL_LEVEL_CLANKY."</p>\n";
      $akt_chyba=1;
      $chyba=1;
    endif;
  else:
    $akt_chyba=1;
  endif;
  // test na aktivni aplikaci levelu - bloky
  $dotazlevel=phprs_sql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."bloky where level_blok='".$akt_id_level."'",$GLOBALS["dbspojeni"]);
  if ($dotazlevel!==false&&phprs_sql_num_rows($dotazlevel)==1):
    list($pocet_bloku)=phprs_sql_fetch_row($dotazlevel);
    if ($pocet_bloku>0):
      // chyba - Akci nelze provest, protoze k tomuto levelu je prirazen jeden nebo vice bloku!
      echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_ERR_DEL_LEVEL_BLOKY."</p>\n";
      $akt_chyba=1;
      $chyba=1;
    endif;
  else:
    $akt_chyba=1;
  endif;
  // test na aktivni aplikaci levelu - soubory
  $dotazlevel=phprs_sql_query("select count(idd) as pocet from ".$GLOBALS["rspredpona"]."download where level_souboru='".$akt_id_level."'",$GLOBALS["dbspojeni"]);
  if ($dotazlevel!==false&&phprs_sql_num_rows($dotazlevel)==1):
    list($pocet_souboru)=phprs_sql_fetch_row($dotazlevel);
    if ($pocet_souboru>0):
      // chyba - Akci nelze provest, protoze k tomuto levelu je prirazen jeden nebo vice souboru!
      echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_ERR_DEL_LEVEL_SOUBORY."</p>\n";
      $akt_chyba=1;
      $chyba=1;
    endif;
  else:
    $akt_chyba=1;
  endif;
  // test na aktivni aplikaci levelu - ctenari
  $dotazlevel=phprs_sql_query("select count(idc) as pocet from ".$GLOBALS["rspredpona"]."ctenari where level_ctenare='".$akt_id_level."'",$GLOBALS["dbspojeni"]);
  if ($dotazlevel!==false&&phprs_sql_num_rows($dotazlevel)==1):
    list($pocet_ctenaru)=phprs_sql_fetch_row($dotazlevel);
    if ($pocet_ctenaru>0):
      // chyba - Akci nelze provest, protoze k tomuto levelu je prirazen jeden nebo vice ctenaru!
      echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_ERR_DEL_LEVEL_CTENARI."</p>\n";
      $akt_chyba=1;
      $chyba=1;
    endif;
  else:
    $akt_chyba=1;
  endif;
  // test na existenci chyby
  if ($akt_chyba==0):
    // dotaz - vymazani levu
    @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."levely where idl='".$akt_id_level."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error C18: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
      $chyba=1;
    endif;
  endif;
endfor;

// vyhodnoceni globalniho stavu
if ($chyba==0):
  if ($pocet_pole_id==0):
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_OK_DEL_LEVEL_NIC."</p>\n";
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_OK_DEL_LEVEL."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLevel&amp;modul=config\">".RS_CFG_SL_ZPET_LEVELY."</a></p>\n";
}

function EditLevel()
{
$GLOBALS["pridl"]=phprs_sql_escape_string($GLOBALS["pridl"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLevel&amp;modul=config\" class=\"navigace\">".RS_CFG_SL_ZPET_LEVELY."</a></p>\n";

// formular na upravu
$dotazlevel=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."levely where idl='".$GLOBALS["pridl"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazlevel);

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SL_FORM_NAZEV."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"50\" value=\"".$pole_data["nazev_levelu"]."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_CFG_SL_FORM_HODNOTA."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prhodnota\" size=\"5\" value=\"".$pole_data["hodnota"]."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">".RS_CFG_SL_FORM_HODNOTA_INFO."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditLevel\"><input type=\"hidden\" name=\"modul\" value=\"config\">
<input type=\"hidden\" name=\"pridl\" value=\"".$pole_data["idl"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcEditLevel()
{
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]);

$GLOBALS["pridl"]=phprs_sql_escape_string($GLOBALS["pridl"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);

// uprava levelu
$dotaz="update ".$GLOBALS["rspredpona"]."levely set nazev_levelu='".$GLOBALS["prnazev"]."', hodnota='".$GLOBALS["prhodnota"]."' ";
$dotaz.="where idl='".$GLOBALS["pridl"]."'";

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error C19: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_CFG_SL_OK_EDIT_LEVE."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddLevel&amp;modul=config\">".RS_CFG_SL_PRIDAT_LEVEL."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowLevel&amp;modul=config\">".RS_CFG_SL_ZPET_LEVELY."</a></p>\n";
}

function AddPlug()
{
$pole_vysl=DejVsechnyPluginy();
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"admin.php?akce=ShowPlugin&amp;modul=config\" class=\"navigace\">".RS_CFG_SP_ZPET_PLUGINY."</a> - \n";
echo " <a href=\"admin.php?akce=ShowConfig&amp;modul=config\" class=\"navigace\">".RS_CFG_KO_ZPET."</a></p>\n";
  if (count($pole_vysl)>0) {
    echo '<form action="admin.php" method="post">' . "\r\n";
    echo '<table cellspacing="0" cellpadding="5" border="1" align="center" class="ramsedy">'."\r\n";
    echo '<tr class="txt" bgcolor="#E6E6E6"><td><b>', RS_CFG_SP_NAZEV, '</b></td><td align="center"><b>', RS_CFG_PL_ADRESAR_PLUGIN, "</b></td>\r\n";
    echo '<td align="center"><b>', RS_CFG_SS_AKCE, "</b></td><td><b>", RS_CFG_PL_CHYBA_PLUGIN, "</b></td></tr>\r\n";
    foreach($pole_vysl as $adr => $detaily) {
      echo '<tr class="txt" onmouseover="setPointer(this, \'#CCFFCC\')" onmouseout="setPointer(this, \'#FFFFFF\')">' . "\r\n";
      echo "<td>", htmlspecialchars($detaily['nazev']), "</td>\r\n";
      echo "<td>plugin/", $adr, "</td>\r\n";
      if ($detaily['instalovan']) {
          echo '<td align="center"> ', RS_CFG_PL_NAINSTALOVAN_PLUGIN, "</td>\r\n";
          }
        elseif ($detaily['chyba'] > '') {
          echo '<td align="center">&nbsp;</td>' . "\r\n";
          }
        else {
          echo '<td align="center"><input type="checkbox" name="prstav[]" value="' . $adr . '"> '. RS_CFG_SS_INSTALOVAT . "</td>\r\n";
          }
      echo '<td>&nbsp;', htmlspecialchars($detaily['chyba']), "</td></tr>\r\n";
      }
    echo '<tr><td colspan="4" align="center"><input type="submit" value=" '.RS_CFG_PL_TL_NAINSTALUJ.' " class="tl"></td></tr>' . "\r\n";
    echo "</table>\r\n";
    echo '<input type="hidden" name="akce" value="AcAddPlug"><input type="hidden" name="modul" value="config">' . "\r\n";
    echo "</form>\r\n";
  }  
  else {
    echo '<p align="center" class="txt">'.RS_CFG_SS_ZADNY_PLUGIN."</p>\r\n";
  }
 
}

function AcAddPlug() {
  $pocet_instal=0;
  if (isset($_POST["prstav"]) and (count($_POST["prstav"]) > 0)) {
      foreach($_POST["prstav"] as $plugin) {
        $instalovan=0;
        if (file_exists("plugin/$plugin/plugin.php")) {
          $pocet_instal+=SavePlugins("plugin/$plugin/plugin.php");
          }
        }
      echo '<p align="center" class="txt"> ', RS_CFG_SP_OK_ADD_PLUGINS_1, $pocet_instal, RS_CFG_SP_OK_ADD_PLUGINS_2, " </p>\r\n";
      }
    else {
      echo '<p align="center" class="txt"> ', RS_CFG_SP_OK_ADD_PLUGINS_NIC, " </p>\r\n";
      }
  ShowPlugin();
  }

function DejVsechnyPluginy() {
  $vysledek=array();
  $path='./plugin';
  if ($handle=opendir($path)) {
    while ($file=readdir($handle)) {
      if ( ($file == '.') or ($file == '..') ) {
          continue;
          }
        elseif ( is_dir($path.'/'.$file) ) {
          if (file_exists($path.'/'.$file.'/plugin.php')) {
              include $path.'/'.$file.'/plugin.php';
              $pi_indent_modulu=strtolower(trim($pi_indent_modulu));
              $pi_zkratka_blok=strtolower(trim($pi_zkratka_blok));
              $kontrola_indent_modulu=false;
              $kontrola_zkratka_blok=false;
              if ($pi_indent_modulu > '') {
                foreach ($vysledek as $key => $hodnoty) {
                  if (isset($hodnoty['id_mod']) and strtolower(trim($hodnoty['id_mod'])) == $pi_indent_modulu) {
                    $kontrola_indent_modulu=$key;
                    break;
                    }
                  }
                }
              if ($pi_zkratka_blok > '') {
                foreach ($vysledek as $key => $hodnoty) {
                  if (isset($hodnoty['zkr_blok']) and strtolower(trim($hodnoty['zkr_blok'])) == $pi_zkratka_blok) {
                    $kontrola_zkratka_blok=$key;
                    break;
                    }
                  }
                }
              if ($kontrola_indent_modulu !== false) {
                $vysledek[$file]['nazev']=$plugin_nazev;
                $vysledek[$file]['instalovan']=false;
                $vysledek[$file]['chyba']=RS_CFG_PL_CHYBA_DUPL_MODUL . $vysledek[$kontrola_indent_modulu]['nazev'];
                continue;
                }
              if ($kontrola_zkratka_blok !== false) {
                $vysledek[$file]['nazev']=$plugin_nazev;
                $vysledek[$file]['instalovan']=false;
                $vysledek[$file]['chyba']=RS_CFG_PL_CHYBA_DUPL_BLOK . $vysledek[$kontrola_zkratka_blok]['nazev'];
                continue;
                }
              $vysledek[$file]['nazev']=$plugin_nazev;
              $vysledek[$file]['instalovan']=false;
              $vysledek[$file]['id_mod']=$pi_indent_modulu;
              $vysledek[$file]['zkr_blok']=$pi_zkratka_blok;
              $vysledek[$file]['chyba']='';
              }
            else {
              $vysledek[$file]['nazev']='Neznámý';
              $vysledek[$file]['instalovan']=false;
              $vysledek[$file]['chyba']=RS_CFG_PL_CHYBA_NENI_PLUGIN;
              }
          }
      }
    closedir($handle);
    $dotaz='SELECT CONCAT(inclakce_menu, inclsb_blok) as plugin FROM '.$GLOBALS["rspredpona"]."plugin";
    if ($zeptatse=@phprs_sql_query($dotaz,$GLOBALS["dbspojeni"])) {
      while ($line=@phprs_sql_fetch_assoc($zeptatse)) {
        list(, $file, ) = explode('/', $line['plugin'], 3);
        if (isset($vysledek[$file]['id_mod'])) {
            $vysledek[$file]['instalovan']=true;
            }
          else {
            $vysledek[$file]['nazev']='Neznámý';
            $vysledek[$file]['instalovan']=true;
            $vysledek[$file]['chyba']=RS_CFG_PL_CHYBA_NENI_ADR .$file;
            }
        }
      }
    }
  return $vysledek;
  }

function SavePlugins($prcesta='')
{
$chyba=0; // inic. chyby
$chyba_integr=0; // inic. chyba integrity
$plugOK = 0;

if ($prcesta!=""): // odstraneni prazdneho pole
  if (file_exists($prcesta)): // kontrola existence konf. souboru
    include($prcesta); // soubor existuje
    // kontrola existence zakl. promennych
    if (!isset($plugin_nazev)): $chyba=1; endif;
    if (!isset($pi_pristup)): $chyba=1; endif;
    if (!isset($pi_pristup)): $chyba=1; endif;
    if (!isset($pi_sys_blok)): $chyba=1; endif;
    if (!isset($pi_indent_modulu)): $chyba=1; endif;
  else: // soubor neexistuje
    $chyba=1;
  endif;
else:
  $chyba=1;
endif;

if ($chyba==0):
  // bezpecnostni korekce
  $plugin_nazev=phprs_sql_escape_string($plugin_nazev);
  $pi_pristup=phprs_sql_escape_string($pi_pristup);
  $pi_menu=phprs_sql_escape_string($pi_menu);
  $pi_nazev_menu=phprs_sql_escape_string($pi_nazev_menu);
  $pi_indent_modulu=phprs_sql_escape_string($pi_indent_modulu);
  $pi_inclakce_menu=phprs_sql_escape_string($pi_inclakce_menu);
  $pi_link_menu=phprs_sql_escape_string($pi_link_menu);
  $pi_sys_blok=phprs_sql_escape_string($pi_sys_blok);
  $pi_nazev_blok=phprs_sql_escape_string($pi_nazev_blok);
  $pi_zkratka_blok=phprs_sql_escape_string($pi_zkratka_blok);
  $pi_inclsb_blok=phprs_sql_escape_string($pi_inclsb_blok);
  $pi_funkce_blok=phprs_sql_escape_string($pi_funkce_blok);
  // test integrity - indet. modulu
  $dotaztestmodul=phprs_sql_query("SELECT idm FROM ".$GLOBALS["rspredpona"]."moduly_prava WHERE ident_modulu='".$pi_indent_modulu."'",$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotaztestmodul)>0):
    $chyba_integr=1;
  endif;
  // test integrity - zkratka bloku, testuje se pouze v pripade aktivniho systemoveho bloku
  if ($pi_sys_blok==1):
    $dotaztestplugin=phprs_sql_query("SELECT idp FROM ".$GLOBALS["rspredpona"]."plugin WHERE zkratka_blok='".$pi_zkratka_blok."'",$GLOBALS["dbspojeni"]);
    if (phprs_sql_num_rows($dotaztestplugin)>0):
      $chyba_integr=1;
    endif;
  endif;
  // test na chybu integrity
  if ($chyba_integr==0):
    // ulozeni plug-inu
    @$error=phprs_sql_query("INSERT INTO ".$GLOBALS["rspredpona"]."plugin VALUES (null,'".$plugin_nazev."','".$pi_pristup."','".$pi_menu."','".$pi_nazev_menu."','".$pi_inclakce_menu."','".$pi_link_menu."','".$pi_sys_blok."','".$pi_nazev_blok."','".$pi_zkratka_blok."','".$pi_inclsb_blok."','".$pi_funkce_blok."')",$GLOBALS["dbspojeni"]);
    if ($error === false):
      // chyba pri vlozeni do registracni tabulky
      echo "<p align=\"center\" class=\"txt\">Error C3: ".RS_DB_ERR_SQL_DOTAZ." ".RS_CFG_SP_ERR_REGISTR_TAB."</p>\n";
    else:
      $idpluginu=phprs_sql_insert_id();
      //echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_OK_ADD_PLUGIN."</p>\n"; // vse OK
      $plugOK = 1; // vse OK
      // pridani plug-inu do tabulky s pristupovymi pravy
      if ($pi_menu=='1'):
        $akt_barva_txt='';
        $akt_barva_bg='';
        // pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
        switch ($pi_pristup):
          case 1: $akt_all_prava_users=0; $akt_jen_admin_modul=0; break;
          case 2: $akt_all_prava_users=1; $akt_jen_admin_modul=0; break;
          case 3: $akt_all_prava_users=0; $akt_jen_admin_modul=1; break;
          default: $akt_all_prava_users=0; $akt_jen_admin_modul=0; break;
        endswitch;
        // sestaveni dotazu
        $dotaz="insert into ".$GLOBALS["rspredpona"]."moduly_prava values ";
        $dotaz.="(null,'Modul ".$plugin_nazev."','".$pi_indent_modulu."','','".$akt_all_prava_users."','".$pi_nazev_menu."','".$pi_inclakce_menu."','".$pi_link_menu."',";
        $dotaz.="'30','0','".$akt_jen_admin_modul."','0','".$akt_barva_txt."','".$akt_barva_bg."','1','".$idpluginu."')";
        @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
        if ($error === false):
          // chyba pri vlozeni do tabulky s pristupovymi pravy
          echo "<p align=\"center\" class=\"txt\">Error C4: ".RS_DB_ERR_SQL_DOTAZ." ".RS_CFG_SP_ERR_TAB_PRISTUP_PRAV."</p>\n";
        endif;
      endif;
    endif;
  else:
    // system jiz obsahuje shodny plug-iny (plug-in se shodnou identifikaci)
    echo "<p align=\"center\" class=\"txt\">".RS_CFG_SP_WAR_CHYBA_INTEGRITY."</p>\n";
  endif;
else:
  // chyba pri importu
  echo "<p align=\"center\" class=\"txt\">Error C5: ".RS_CFG_SP_ERR_IMPORT."</p>\n";
endif;

return $plugOK; // pridano 0,1 pluginu
// navrat
}
?>
