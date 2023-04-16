<?php

######################################################################
# phpRS Administration Engine - Public inquiry's section 1.5.1
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ankety, rs_odpovedi, rs_config

/*
  Tento soubor zajistuje spravu anketniho subsystemu.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // ankety
     case "ShowInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_VIEW_AKT."</h2>";
          ShowInquiry();
          break;
     case "AddInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_ADD_AKT."</h2>";
          AddInquiry();
          break;
     case "AcAddInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_ADD_AKT."</h2>";
          AcAddInquiry();
          break;
     case "DelInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_DEL_AKT."</h2>";
          DelInquiry();
          break;
     case "EditInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2>";
          EditInquiry();
          break;
     case "AcEditInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2>";
          AcEditInquiry();
          break;
     case "AcEdit2Inquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_EDIT_AKT."</h2>";
          AcEdit2Inquiry();
          break;
     // nastaveni aktivni ankety
     case "NastInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_NASTAV_AKT."</h2>";
          NastInquiry();
          break;
     case "AcNastInquiry": AdminMenu();
          echo "<h2 align=\"center\">".RS_AKT_ROZ_NASTAV_AKT."</h2>";
          AcNastInquiry();
          break;
endswitch;

// ---[pomocne fce - ankety]--------------------------------------------------------

function AnkNavigBox()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;

$dotazpocet=phprs_sql_query("select count(ida) as pocet from ".$GLOBALS["rspredpona"]."ankety",$GLOBALS["dbspojeni"]);
if ($dotazpocet!==false&&phprs_sql_num_rows($dotazpocet)>0):
  $pole_data=phprs_sql_fetch_assoc($dotazpocet);
else:
  $pole_data['pocet']=0;
endif;

echo "<form action=\"admin.php\" method=\"post\"><p align=\"center\" class=\"txt\">
<input type=\"hidden\" name=\"akce\" value=\"ShowInquiry\"><input type=\"hidden\" name=\"modul\" value=\"ankety\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\">
<td valign=\"middle\"><input type=\"submit\" value=\" ".RS_AKT_SA_ZOBRAZ_ANKETU." \" class=\"tl\"></td>
<td valign=\"top\">
".RS_AKT_SA_OD." <input type=\"text\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\" size=\"4\" class=\"textpole\">
".RS_AKT_SA_DO." <input type=\"text\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\" size=\"4\" class=\"textpole\">
- ".RS_AKT_SA_CELK_POCET." ".$pole_data['pocet']."
</td></tr>
</table>
</form>
<br>\n";
}

function OptSezAnk($hledam = 0)
{
$str='';

$dotazpom=phprs_sql_query("select ida,titulek from ".$GLOBALS["rspredpona"]."ankety order by titulek",$GLOBALS["dbspojeni"]);
$pocetpom=phprs_sql_num_rows($dotazpom);

if ($hledam==0):
  $str.="<option value=\"0\" selected>".RS_AKT_POM_ERR_BEZ_ANKETY."</option>\n"; // bez ankety; vybrano v menu
else:
  $str.="<option value=\"0\">".RS_AKT_POM_ERR_BEZ_ANKETY."</option>\n"; // bez ankety
endif;

while($pole_data = phprs_sql_fetch_assoc($dotazpom)):
  $str.="<option value=\"".$pole_data["ida"]."\"";
  if ($pole_data["ida"]==$hledam): $str.=" selected"; endif;
  $str.=">".$pole_data["titulek"]."</option>\n";
endwhile;

return $str;
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

// ---[hlavni fce - ankety]---------------------------------------------------------

/*
  ShowInquiry()
  AddInquiry()
  AcAddInquiry()
  DelInquiry()
  EditInquiry()
  AcEditInquiry()
  AcEdit2Inquiry()
  NastInquiry()
  AcNastInquiry()
*/

function ShowInquiry()
{
if (!isset($GLOBALS["prmin"])): $GLOBALS["prmin"]=0; endif;
if (!isset($GLOBALS["prmax"])): $GLOBALS["prmax"]=20; endif;

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

$autori=new SezAutori();

// linky
if ($akt_je_admin==1): // pouze admin
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=NastInquiry&amp;modul=ankety\" class=\"navigace\">".RS_AKT_SA_NASTAV_ANKETU."</a> -
<a href=\"".RS_VYKONNYSOUBOR."?akce=AddInquiry&amp;modul=ankety\" class=\"navigace\">".RS_AKT_SA_PRIDAT_ANKETU."</a></p>\n";
else: // vsichni uzivatele
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddInquiry&amp;modul=ankety\" class=\"navigace\">".RS_AKT_SA_PRIDAT_ANKETU."</a></p>\n";
endif;
// navigacni box
AnkNavigBox();

// vypocet omezeni
if ($GLOBALS["prmin"]>0): $dotaz_od=($GLOBALS["prmin"]-1); else: $dotaz_od=0; endif;
$dotaz_kolik=($GLOBALS["prmax"]-$dotaz_od);
if ($dotaz_kolik<0): $dotaz_kolik=0; endif;

// dotaz
$dotazank=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."ankety order by datum desc limit ".$dotaz_od.",".$dotaz_kolik,$GLOBALS["dbspojeni"]);
$pocetank=phprs_sql_num_rows($dotazank);

if ($pocetank==0):
  // CHYBA: Zadany interval (od xxx do yyy) je prazdny!
  echo "<p align=\"center\" class=\"txt\">".RS_ADM_INTERVAL_C1." ".$GLOBALS["prmin"]." ".RS_ADM_INTERVAL_C2." ".$GLOBALS["prmax"].RS_ADM_INTERVAL_C3."</p>\n";
else:
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr bgcolor=\"#E6E6E6\" class=\"txt\"><td align=\"center\"><b>".RS_AKT_SA_TITULEK."</b></td>\n";
  echo "<td align=\"center\" width=\"300\"><b>".RS_AKT_SA_OTAZKA."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_DATUM."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_ZOBRAZIT."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_UZAMCENO."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_AUTOR."</b></td>\n";
  echo "<td align=\"center\"><b>".RS_AKT_SA_AKCE."</b></td>";
  echo "<td align=\"center\"><b>".RS_AKT_SA_SMAZ."</b></td></tr>\n";
  while ($pole_data = phprs_sql_fetch_assoc($dotazank)):
    // inic.
    $akt_pristupna_anketa=0;
    // vypis dat
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"left\">".$pole_data["titulek"]."</td>\n";
    echo "<td align=\"left\" width=\"300\">".$pole_data["otazka"]."</td>\n";
    echo "<td align=\"center\">".MyDatetimeToDate($pole_data["datum"])."</td>\n";
    echo "<td align=\"center\">".TestAnoNe($pole_data["zobrazit"])."</td>\n";
    echo "<td align=\"center\">".TestAnoNe($pole_data["uzavrena"])."</td>\n";
    echo "<td align=\"center\">".$autori->UkazUser($pole_data["kdo"])."</td>\n";
    echo "<td align=\"center\">";
    // start - test na dostupnost editacni funkce
    if ($akt_je_admin==1):
      // admin - muze vse
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditInquiry&amp;modul=ankety&amp;prida=".$pole_data["ida"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_UPRAVIT."</a>";
      $akt_pristupna_anketa=1;
    else:
      // ostatni uzivatele
      if ($pole_data['zobrazit']==1):
        // anketa je vydana (zobrazena) - pro editaci musite mit vydavatelska prava
        if ($GLOBALS['Uzivatel']->JePodrizeny($pole_data['kdo'])==1&&$akt_je_vydavatel==1):
          echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditInquiry&amp;modul=ankety&amp;prida=".$pole_data["ida"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_UPRAVIT."</a>";
          $akt_pristupna_anketa=1;
        else:
          echo RS_AKT_SA_UPRAVIT;
        endif;
      else:
        // anketa je ve stadiu tvorby (zakazano zobrazeni) - lze editovat
        if ($GLOBALS['Uzivatel']->JePodrizeny($pole_data['kdo'])==1):
          echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=EditInquiry&amp;modul=ankety&amp;prida=".$pole_data["ida"]."&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_UPRAVIT."</a>";
          $akt_pristupna_anketa=1;
        else:
          echo RS_AKT_SA_UPRAVIT;
        endif;
      endif;
    endif;
    // konec - test na dostupnost editacni funkce
    echo "</td>\n";
    if ($akt_pristupna_anketa==1):
      echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["ida"]."\"></td></tr>\n";
    else:
      echo "<td align=\"center\">&nbsp;</td></tr>\n";
    endif;
  endwhile;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"8\"><input type=\"submit\" value=\" ".RS_AKT_SA_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
  echo "</table>\n";
  echo "<input type=\"hidden\" name=\"akce\" value=\"DelInquiry\"><input type=\"hidden\" name=\"modul\" value=\"ankety\">\n";
  echo "</form>\n";
endif;

echo "<br>\n";
}

function AddInquiry()
{
$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\" class=\"navigace\">".RS_AKT_SA_ZPET."</a></p>\n";

// definice ankety
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" size=\"45\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\">".RS_AKT_SA_FORM_TITULEK_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_AKT_SA_FORM_OTAZKA."</b><br>
<textarea name=\"protazka\" rows=\"8\" cols=\"65\" class=\"textbox\">".RS_AKT_SA_FORM_OTAZKA_INFO."</textarea></td></tr>\n";
if ($akt_je_vydavatel==1):
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ZOBRAZIT."</b></td>\n";
  echo "<td align=\"left\"><input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE."</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_STAV."</b></td>\n";
  echo "<td align=\"left\"><input type=\"radio\" name=\"pruzamcena\" value=\"0\" checked> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"przobrazit\" value=\"1\"> ".RS_AKT_SA_FORM_STAV_CLOSE."</td></tr>\n";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".Date("Y-m-d H:i:s")."\" size=\"25\" class=\"textpole\"><br>".RS_AKT_SA_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor\" size=\"1\">";
if ($akt_je_admin==1):
  echo OptAutori($GLOBALS["Uzivatel"]->IdUser);
else:
  echo OptAutori($GLOBALS["Uzivatel"]->IdUser,$GLOBALS["Uzivatel"]->SeznamDostupUser());
endif;
echo "</select></td></tr>
</table>\n";
// nadpis
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_AKT_SA_ADD_NADPIS_ODPOVEDI."</strong></big><br>".RS_AKT_SA_ADD_ODPOVEDI_INFO."</p>\n";
// definice anketnich odpovedi
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 1</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 2</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 3</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 4</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 5</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 6</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ODPOVED_CIS." 7</b></td>
<td align=\"left\"><input type=\"text\" name=\"prodpoved[]\" size=\"43\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddInquiry\"><input type=\"hidden\" name=\"modul\" value=\"ankety\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>
<br>\n";
}

function AcAddInquiry()
{
// bezpecnostni korekce
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);

$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["protazka"]=phprs_sql_escape_string($GLOBALS["protazka"]);
// $GLOBALS["przobrazit"] - zpr. nize
// $GLOBALS["pruzamcena"] - zpr. nize
$GLOBALS["prdatum"]=phprs_sql_escape_string($GLOBALS["prdatum"]);
$GLOBALS["prautor"]=phprs_sql_escape_string($GLOBALS["prautor"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// uprava vstuptu dle pridelenych prav
if ($akt_je_admin==1||$akt_je_vydavatel==1):
  $nast_zobrazit=phprs_sql_escape_string($GLOBALS["przobrazit"]);
  $nast_uzamcena=phprs_sql_escape_string($GLOBALS["pruzamcena"]);
else:
  $nast_zobrazit=0;
  $nast_uzamcena=0;
endif;

// pridani ankety
$dotaz="insert into ".$GLOBALS["rspredpona"]."ankety ";
$dotaz.="values(null,'".$GLOBALS["prtitulek"]."','".$GLOBALS["protazka"]."','".$GLOBALS["prdatum"]."','".$GLOBALS["prautor"]."','".$nast_zobrazit."',";
$dotaz.="'".$nast_uzamcena."')";

@$prankety=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($prankety === false):
   echo "<p align=\"center\" class=\"txt\">Error Q1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
   echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ADD_AKT."</p>\n"; // vse OK
endif;

// zjisteni id prave vlozene ankety
$dotaznaid=phprs_sql_query("select ida from ".$GLOBALS["rspredpona"]."ankety where titulek='".$GLOBALS["prtitulek"]."' and datum='".$GLOBALS["prdatum"]."'",$GLOBALS["dbspojeni"]);
if ($dotaznaid!==false&&phprs_sql_num_rows($dotaznaid)==1):
  list($anketa_id)=phprs_sql_fetch_row($dotaznaid); // nacteni "ida"
else:
  $anketa_id=0;
endif;

// vlozeni odpovedi
if (isset($GLOBALS["prodpoved"])&&$anketa_id>0):
  $pocet_odp=count($GLOBALS["prodpoved"]);
  for ($pom=0;$pom<$pocet_odp;$pom++):
    if (!empty($GLOBALS["prodpoved"][$pom])):
      $akt_odpoved=phprs_sql_escape_string(trim($GLOBALS["prodpoved"][$pom]));
      phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."odpovedi values(null,".$anketa_id.",'".$akt_odpoved."',0)",$GLOBALS["dbspojeni"]);
    endif;
  endfor;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function DelInquiry()
{
$chyba=0; // inic. chyby

// inic. pristupovych prav
$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();
$akt_seznam_podrizenych=$GLOBALS['Uzivatel']->SeznamDostupUser();

// inic. pole
if (!isset($GLOBALS["prpoleid"])):
 $pocet_pole_id=0;
else:
 $pocet_pole_id=count($GLOBALS["prpoleid"]);
endif;

// vymazani ankety
for ($pom=0;$pom<$pocet_pole_id;$pom++):
  // bezpecnostni korekce
  $akt_id_anketa=phprs_sql_escape_string($GLOBALS["prpoleid"][$pom]);
  // inic.
  $chyba_nelze_mazat=0;
  // test na pouziti ankety
  list($config_prom_id,$config_prom_hodnota)=NactiKonfigHod('aktivni_anketa');
  if ($config_prom_hodnota==$akt_id_anketa):
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_AKTIVNI_AKT."</p>\n";
    echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=NastInquiry&amp;modul=ankety\">".RS_AKT_SA_DEAKTIV_ANKETU."</a></p>\n";
    $chyba_nelze_mazat=1;
    $chyba=1;
  endif;
  // test na pouziti ankety ve vazba se clanky
  $dotazcla=phprs_sql_query("select idc from ".$GLOBALS["rspredpona"]."clanky where anketa_cl='".$akt_id_anketa."'",$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotazcla)>0):
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_DEL_AKT_VAZBA_CLA."</p>\n"; // chyba: existuje vazba se clankem
    $chyba_nelze_mazat=1;
    $chyba=1;
  endif;
  // test na pristupnost ankety z pohledu prav
  if ($akt_je_admin==0):
    if ($akt_je_vydavatel==1):
      $dotaztest=phprs_sql_query("select ida from ".$GLOBALS["rspredpona"]."ankety where ida='".$akt_id_anketa."' and kdo in (".$akt_seznam_podrizenych.")",$GLOBALS["dbspojeni"]);
      if (phprs_sql_num_rows($dotaztest)==0):
        echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_DEL_AKT_PRAVA."</p>\n"; // chyba: nemate pristupova prava
        $chyba_nelze_mazat=1;
        $chyba=1;
      endif;
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_DEL_AKT_PRAVA."</p>\n"; // chyba: nemate pristupova prava
      $chyba_nelze_mazat=1;
      $chyba=1;
    endif;
  endif;
  // test na moznost smazani
  if ($chyba_nelze_mazat==0):
    // vymazani ankety
    @$smanketu=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."ankety where ida='".$akt_id_anketa."'",$GLOBALS["dbspojeni"]);
    if ($smanketu === false):
      echo "<p align=\"center\" class=\"txt\">Error Q2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      $chyba=1;
    endif;
    // vymazani odpovedi
    @$smodpovedi=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$akt_id_anketa."'",$GLOBALS["dbspojeni"]);
    if ($smodpovedi === false):
      echo "<p align=\"center\" class=\"txt\">Error Q3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      $chyba=1;
    endif;
  endif;
endfor;

// vyhodnoceni globalniho stavu
if ($chyba==0):
  if ($pocet_pole_id==0):
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_DEL_AKT_NIC."</p>\n";
  else:
    if ($pocet_pole_id==1):
      echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_DEL_AKT_JEDNA."</p>\n";
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_DEL_AKT_VICE."</p>\n";
    endif;
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function EditInquiry()
{
// bezpecnostni korekce
$GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);
$GLOBALS["prmin"]=phprs_sql_escape_string($GLOBALS["prmin"]);
$GLOBALS["prmax"]=phprs_sql_escape_string($GLOBALS["prmax"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\" class=\"navigace\">".RS_AKT_SA_ZPET."</a></p>\n";

// dotaz na data
if ($akt_je_admin==1): // je admin
  $dotaz="select * from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["prida"]."'";
else: // je autor nebo redaktor
  $dotaz="select * from ".$GLOBALS["rspredpona"]."ankety where ida='".$GLOBALS["prida"]."' and kdo in (".$GLOBALS['Uzivatel']->SeznamDostupUser().")";
  if ($akt_je_vydavatel==0):
    // nema vydavatelska prava - muze upravovat pouze otevrene (nezobrazene) ankety
    $dotaz.=" and zobrazit=0";
  endif;
endif;
$dotazank=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);

if ($dotazank!==false&&phprs_sql_num_rows($dotazank)==1): // start - reakce na vysledek dotazu na anketu

// nacte dat do pole
$pole_anketa=phprs_sql_fetch_assoc($dotazank);

// uprava ankety
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_TITULEK."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prtitulek\" value=\"".$pole_anketa['titulek']."\" size=\"45\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td align=\"left\" colspan=\"2\"><b>".RS_AKT_SA_FORM_OTAZKA."</b><br>
<textarea name=\"protazka\" rows=\"8\" cols=\"65\" class=\"textbox\">".KorekceHTML($pole_anketa['otazka'])."</textarea></td></tr>\n";
if ($akt_je_vydavatel==1):
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_ZOBRAZIT."</b></td>\n";
  echo "<td align=\"left\">";
  if ($pole_anketa['zobrazit']==1):
    echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\" checked> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\"> ".RS_TL_NE;
  else:
    echo "<input type=\"radio\" name=\"przobrazit\" value=\"1\"> ".RS_TL_ANO." <input type=\"radio\" name=\"przobrazit\" value=\"0\" checked> ".RS_TL_NE;
  endif;
  echo "</td></tr>\n";
  echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_STAV."</b></td>\n";
  echo "<td align=\"left\">";
  if ($pole_anketa['uzavrena']==0):
    echo "<input type=\"radio\" name=\"pruzamcena\" value=\"0\" checked> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"pruzamcena\" value=\"1\"> ".RS_AKT_SA_FORM_STAV_CLOSE;
  else:
    echo "<input type=\"radio\" name=\"pruzamcena\" value=\"0\"> ".RS_AKT_SA_FORM_STAV_OPEN." <input type=\"radio\" name=\"pruzamcena\" value=\"1\" checked> ".RS_AKT_SA_FORM_STAV_CLOSE;
  endif;
  echo "</td></tr>\n";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_DATUM."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prdatum\" value=\"".$pole_anketa['datum']."\" size=\"25\" class=\"textpole\"><br>".RS_AKT_SA_FORM_DATUM_INFO."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_AKT_SA_FORM_AUTOR."</b></td>
<td align=\"left\"><select name=\"prautor\" size=\"1\">";
if ($akt_je_admin==1):
  echo OptAutori($pole_anketa['kdo']);
else:
  echo OptAutori($pole_anketa['kdo'],$GLOBALS["Uzivatel"]->SeznamDostupUser());
endif;
echo "</select></td></tr>
</table>
<input type=\"hidden\" name=\"prida\" value=\"".$pole_anketa['ida']."\">
<input type=\"hidden\" name=\"akce\" value=\"AcEditInquiry\"><input type=\"hidden\" name=\"modul\" value=\"ankety\">
<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\"><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";

// uprava anketnich odpovedi
echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_AKT_SA_EDIT_NADPIS_ODPOVEDI."</strong></big><br>".RS_AKT_SA_EDIT_ODPOVEDI_INFO."</p>\n";

$dotazodp=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."odpovedi where anketa='".$GLOBALS["prida"]."' order by ido",$GLOBALS["dbspojeni"]);
$pocetodp=phprs_sql_num_rows($dotazodp);

echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">\n";
for ($pom=0;$pom<$pocetodp;$pom++):
  $pole_odpovedi=phprs_sql_fetch_assoc($dotazodp);
  echo "<tr class=\"txt\">\n";
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
  echo "<input type=\"hidden\" name=\"modul\" value=\"ankety\"><input type=\"hidden\" name=\"akce\" value=\"AcEdit2Inquiry\">\n";
  echo "<input type=\"hidden\" name=\"prida\" value=\"".$pole_odpovedi["anketa"]."\"><input type=\"hidden\" name=\"prido\" value=\"".$pole_odpovedi["ido"]."\">\n";
  echo "<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\"><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\">\n";
  echo "<td align=\"left\"><input type=\"text\" name=\"prodp\" value=\"".KorekceNadpisu($pole_odpovedi["odpoved"])."\" size=\"50\" class=\"textpole\"></td>\n";
  echo "<td align=\"left\"> ".RS_AKT_SA_FORM_POCET_HLA.": ".$pole_odpovedi["pocitadlo"]."</td>\n";
  echo "<td align=\"right\"><select name=\"prukol\" size=\"1\"><option value=\"save\">".RS_AKT_SA_FORM_ULOZ_ZMENY."</option><option value=\"del\">".RS_AKT_SA_FORM_VYMAZ_ODPOVED."</option></select> &nbsp;<input type=\"submit\" value=\" ".RS_TL_OK." \" class=\"tl\"></td>\n";
  echo "</form></tr>\n";
endfor;

// pridani nove odpovedi
echo "<tr><form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"modul\" value=\"ankety\"><input type=\"hidden\" name=\"prukol\" value=\"insert\">
<input type=\"hidden\" name=\"akce\" value=\"AcEdit2Inquiry\"><input type=\"hidden\" name=\"prida\" value=\"".$GLOBALS["prida"]."\">
<input type=\"hidden\" name=\"prmin\" value=\"".$GLOBALS["prmin"]."\"><input type=\"hidden\" name=\"prmax\" value=\"".$GLOBALS["prmax"]."\">
<td align=\"left\" colspan=\"2\"><input type=\"text\" name=\"prodp\" value=\"".RS_AKT_SA_FORM_VLOZ_INFO."\" size=\"50\" class=\"textpole\"></td>
<td align=\"right\"> &nbsp;<input type=\"submit\" value=\" ".RS_AKT_SA_TL_VLOZ_ODPOVED." \" class=\"tl\"></td>
</form></tr>
</table>
<br>\n";

endif; // konec - reakce na vysledek dotazu na anketu
}

function AcEditInquiry()
{
// bezpecnostni korekce
$GLOBALS["prtitulek"]=KorekceNadpisu($GLOBALS["prtitulek"]);

$GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);
$GLOBALS["prtitulek"]=phprs_sql_escape_string($GLOBALS["prtitulek"]);
$GLOBALS["protazka"]=phprs_sql_escape_string($GLOBALS["protazka"]);
// $GLOBALS["przobrazit"] - zpr. nize
// $GLOBALS["pruzamcena"] - zpr. nize
$GLOBALS["prdatum"]=phprs_sql_escape_string($GLOBALS["prdatum"]);
$GLOBALS["prautor"]=phprs_sql_escape_string($GLOBALS["prautor"]);

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

// uprava vstuptu dle pridelenych prav
if ($akt_je_admin==1||$akt_je_vydavatel==1):
  $prwhere_nast_zobrazit=",zobrazit='".phprs_sql_escape_string($GLOBALS["przobrazit"])."'";
  $prwhere_nast_uzamcena=",uzavrena='".phprs_sql_escape_string($GLOBALS["pruzamcena"])."'";
else:
  $prwhere_nast_zobrazit='';
  $prwhere_nast_uzamcena='';
endif;

// sestaveni dotazu
$dotaz="update ".$GLOBALS["rspredpona"]."ankety ";
$dotaz.="set titulek='".$GLOBALS["prtitulek"]."',otazka='".$GLOBALS["protazka"]."',datum='".$GLOBALS["prdatum"]."',kdo='".$GLOBALS["prautor"]."' ";
$dotaz.=$prwhere_nast_zobrazit.$prwhere_nast_uzamcena." ";
$dotaz.="where ida='".$GLOBALS["prida"]."'";
// dotvoreni dotazu dle uziv. prav
if ($akt_je_admin==0):
  // je autor nebo redaktor
  $dotaz.=" and kdo in (".$GLOBALS['Uzivatel']->SeznamDostupUser().")";
endif;

// upraveni ankety
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error Q4: ".RS_DB_ERR_SQL_DOTAZ."</p>\n";
else:
  echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_EDIT_AKT."</p>\n";
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_ZPET."</a></p>\n";
}

function AcEdit2Inquiry()
{
// dotaz na anketu - zjisteni moznosti pristupu z pohledu prav
if ($GLOBALS['Uzivatel']->JeAdmin()==0):
  // uzivatel neni admin - nutno zjistit jeho pristupova prava
  $dotaz="select ida from ".$GLOBALS["rspredpona"]."ankety where ida='".phprs_sql_escape_string($GLOBALS["prida"])."' and kdo in (".$GLOBALS['Uzivatel']->SeznamDostupUser().")";
  $dotaztest=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotaztest)==0):
    $GLOBALS["prukol"]='chyba_prava'; // uzivatel nema zadna prava pro pristup
  endif;
endif;

// rozcestnik ukolu
switch ($GLOBALS["prukol"]):
  case 'save':
       // bezpecnostni korekce
       $GLOBALS["prido"]=phprs_sql_escape_string($GLOBALS["prido"]);
       $GLOBALS["prodp"]=phprs_sql_escape_string($GLOBALS["prodp"]);
       // dotaz
       @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."odpovedi set odpoved='".$GLOBALS["prodp"]."' where ido='".$GLOBALS["prido"]."'",$GLOBALS["dbspojeni"]);
       if ($error === false):
         echo "<p align=\"center\" class=\"txt\">Error Q5: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_EDIT_AKT."</p>\n"; // vse OK
       endif;
       break;
  case 'del':
       // bezpecnostni korekce
       $GLOBALS["prido"]=phprs_sql_escape_string($GLOBALS["prido"]);
       // dotaz
       @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."odpovedi where ido='".$GLOBALS["prido"]."'",$GLOBALS["dbspojeni"]);
       if ($error === false):
         echo "<p align=\"center\" class=\"txt\">Error Q6: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_DEL_AKT."</p>\n"; // vse OK
       endif;
       break;
  case 'insert':
       // bezpecnostni korekce
       $GLOBALS["prida"]=phprs_sql_escape_string($GLOBALS["prida"]);
       $GLOBALS["prodp"]=phprs_sql_escape_string($GLOBALS["prodp"]);
       // dotaz
       @$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."odpovedi values(null,'".$GLOBALS["prida"]."','".$GLOBALS["prodp"]."',0)",$GLOBALS["dbspojeni"]);
       if ($error === false):
         echo "<p align=\"center\" class=\"txt\">Error Q7: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_ODP_ADD_AKT."</p>\n"; // vse OK
       endif;
       break;
  case 'chyba_prava':
       echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_ERR_ODP_PRAVA."</p>\n"; // chyba
       break;
endswitch;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety&amp;prmin=".$GLOBALS["prmin"]."&amp;prmax=".$GLOBALS["prmax"]."\">".RS_AKT_SA_ZPET."</a></p>\n";
}

// ---[hlavni fce - nastaveni ankety]-----------------------------------------------

/*
  NastInquiry()
  AcNastInquiry()
*/

function NastInquiry()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\" class=\"navigace\">".RS_AKT_SA_ZPET."</a></p>\n";

// inic. konfig. promenne "aktivni_anketa"
list($config_prom_id,$config_prom_hodnota)=NactiKonfigHod('aktivni_anketa');

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<div align=\"center\">
<strong>".RS_AKT_SA_FORM_AKTIV_AKT.":</strong> <select name=\"prhodnota\" size=\"1\">".OptSezAnk($config_prom_hodnota)."</select>
<input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\">
<input type=\"hidden\" name=\"akce\" value=\"AcNastInquiry\"><input type=\"hidden\" name=\"modul\" value=\"ankety\">
<input type=\"hidden\" name=\"pridc\" value=\"".$config_prom_id."\">
</div>
</form>\n";
}

function AcNastInquiry()
{
$GLOBALS["pridc"]=phprs_sql_escape_string($GLOBALS["pridc"]);
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);

// test na pristupova prava - uzivatel musi byt admin
if ($GLOBALS['Uzivatel']->JeAdmin()==1):
  @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."config set hodnota='".$GLOBALS["prhodnota"]."' where idc='".$GLOBALS["pridc"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error Q8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_AKT_SA_OK_NASTAV_AKT."</p>\n"; // vse OK
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowInquiry&amp;modul=ankety\">".RS_AKT_SA_ZPET."</a></p>\n";
}

?>