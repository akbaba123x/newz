<?php

######################################################################
# phpRS Administration Engine - Advertisement system section 1.4.2
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_klik_kampan, rs_klik_ban, rs_klik_rekl

/*
tabulka rs_klik_rekl: pozice -> 1 = horni pozice, 2 = dolni pozice
tabulka rs_klik_ban: druh -> 0 = banner, 1 = text, 2 = reklamni kod
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
AdminMenu();
switch($GLOBALS['akce']):
     // reklamni subsystem
     case "ShowAdvert":
          echo "<h2 align=\"center\">".RS_REK_SUBSYSTEM."</h2>"; 
          AdvertMenu();
          break;
     case "UpAdvert": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_HORNI_POZICE."</h2>";
          AdvertMenu();
          HorniReklama();
          break;
     case "SaveUpAdvert": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_HORNI_POZICE."</h2>";
          AdvertMenu();
          UlozReklamu();
          break;
     case "DownAdvert": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_DOLNI_POZICE."</h2>";
          AdvertMenu();
          DolniReklama();
          break;
     case "SaveDownAdvert": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_DOLNI_POZICE."</h2>";
          AdvertMenu();
          UlozReklamu();
          break;
     // kampan
     case "ShowCamp": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_SHOW_KAMPAN."</h2>";
          AdvertMenu();
          ShowCamp();
          break;
     case "AddCamp": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_KAMPAN."</h2>";
          AdvertMenu();
          AddCamp();
          break;
     case "DelCamp": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_DEL_KAMPAN."</h2>";
          AdvertMenu();
          DelCamp();
          break;
     // bannery
     case "Banner":
          echo "<h2 align=\"center\">".RS_REK_ROZ_SHOW_REKL."</h2>";
          AdvertMenu();
          VypisBannery();
          break;
     case "AddBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_REKL."</h2>";
          AdvertMenu();
          PridejBanner();
          break;
     case "AcAddBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_ADD_REKL."</h2>";
          AdvertMenu();
          AcPridejBanner();
          break;
     case "EditBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_EDIT_REKL."</h2>";
          AdvertMenu();
          UpravBanner();
          break;
     case "AcEditBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_EDIT_REKL."</h2>";
          AdvertMenu();
		  AcUpravBanner();
          break;
     case "UseBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_USE_REKL."</h2>";
          PouzijBanner();
          break;
     case "AcUseBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_USE_REKL."</h2>";
          AdvertMenu();
          AcPouzijBanner();
          break;
     case "DeleteBanner": 
          echo "<h2 align=\"center\">".RS_REK_ROZ_DEL_REKL."</h2>";
          AdvertMenu();
          SmazBanner();
          break;
endswitch;

// ---[pomocne fce]-----------------------------------------------------------------

function AdvertMenu()
{
echo "
	<table border=\"0\" cellpadding=\"5\" cellspacing=\"0\" align=\"center\">
		<tr id=\"sub_menu\">
		<td><a href=\"".RS_VYKONNYSOUBOR."?akce=UpAdvert&amp;modul=reklama\">".RS_REK_SUB_HORNI_POZICE."</a></td>
		<td><a href=\"".RS_VYKONNYSOUBOR."?akce=DownAdvert&amp;modul=reklama\">".RS_REK_SUB_DOLNI_POZICE."</a></td>
		<td><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCamp&amp;modul=reklama\">".RS_REK_SUB_KAMPAN."</a></td>
		<td><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\">".RS_REK_SUB_REKLAMA."</a></td>
		</tr>
	</table>\n";
}

function VsechnyKorekce($text = '')
{
// tento radek umoznuje spravne zobrazit v editacnim poli vsechny zvlastni znaky zapsane jako &X;
$text=str_replace('&','&amp;',$text);
// tento radek nahrazuje uvozovky v nadpise za - &quot;
return str_replace('"','&quot;',$text);
}

function OptKampan($hledam = 0)
{
$vysl='';

$dotazkamp=phprs_sql_query("select idk,alias from ".$GLOBALS["rspredpona"]."klik_kampan order by alias",$GLOBALS["dbspojeni"]);

if (phprs_sql_num_rows($dotazkamp)==0):
  $vysl.='<option value="0">'.RS_REK_POM_ERR_BEZ_KAMPANE."</option>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazkamp)):
    $vysl.='<option value="'.$pole_data['idk'].'"';
    if ($pole_data['idk']==$hledam): $vysl.=' selected'; endif;
    $vysl.='>'.htmlspecialchars($pole_data['alias'])."</option>\n";
  endwhile;
endif;

return $vysl;
}

// ---[hlavni fce]------------------------------------------------------------------

function HorniReklama()
{
$dotazkod=phprs_sql_query("select idr,kod,typ_reklamy from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='1'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazkod);

// test na pozadavek noveho typu
if (isset($GLOBALS['prnovy'])):
  if ($pole_data['typ_reklamy']!=$GLOBALS['prtyp']):
    $pole_data['kod']='';
    $pole_data['typ_reklamy']=$GLOBALS['prtyp'];
  endif;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
switch ($pole_data['typ_reklamy']):
  case 'kod':
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=UpAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kampan\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KAMPAN."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KOD."<br><br>\n";
       echo "<textarea name=\"prhodnota\" rows=\"8\" cols=\"70\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kod\">\n";
       echo "</p>\n";
       break;
  case 'kampan':
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=UpAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kod\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KOD."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KAMPAN."<br><br>\n";
       if (!preg_match('|^\d+$|',$pole_data['kod'])): $pole_data['kod']=0; endif;
       echo "<select name=\"prhodnota\" value>".OptKampan($pole_data['kod'])."</select>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kampan\">\n";
       echo "</p>\n";
       break;
  default:
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=UpAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kampan\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KAMPAN."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KOD."<br><br>\n";
       echo "<textarea name=\"prhodnota\" rows=\"8\" cols=\"70\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kod\">\n";
       echo "</p>\n";
       break;
endswitch;
echo "</p>
<input type=\"hidden\" name=\"akce\" value=\"SaveUpAdvert\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"pridr\" value=\"".$pole_data['idr']."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\">
</form>\n";
}

function DolniReklama()
{
$dotazkod=phprs_sql_query("select idr,kod,typ_reklamy from ".$GLOBALS["rspredpona"]."klik_rekl where pozice='2'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazkod);

// test na pozadavek noveho typu
if (isset($GLOBALS['prnovy'])):
  if ($pole_data['typ_reklamy']!=$GLOBALS['prtyp']):
    $pole_data['kod']='';
    $pole_data['typ_reklamy']=$GLOBALS['prtyp'];
  endif;
endif;

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">\n";
switch ($pole_data['typ_reklamy']):
  case 'kod':
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=DownAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kampan\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KAMPAN."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KOD."<br><br>\n";
       echo "<textarea name=\"prhodnota\" rows=\"8\" cols=\"70\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kod\">\n";
       echo "</p>\n";
       break;
  case 'kampan':
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=DownAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kod\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KOD."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KAMPAN."<br><br>\n";
       if (!preg_match('|^\d+$|',$pole_data['kod'])): $pole_data['kod']=0; endif;
       echo "<select name=\"prhodnota\" value>".OptKampan($pole_data['kod'])."</select>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kampan\">\n";
       echo "</p>\n";
       break;
  default:
       echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=DownAdvert&amp;modul=reklama&amp;prnovy=1&amp;prtyp=kampan\" class=\"navigace\">".RS_REK_HD_AKTIVUJ_KAMPAN."</a></p>\n";
       echo "<p align=\"center\" class=\"txt\">\n";
       echo "<strong>".RS_REK_HD_ZPUSOB_REKL."</strong> ".RS_REK_HD_ZPUSOB_KOD."<br><br>\n";
       echo "<textarea name=\"prhodnota\" rows=\"8\" cols=\"70\" class=\"textbox\">".KorekceHTML($pole_data['kod'])."</textarea>\n";
       echo "<input type=\"hidden\" name=\"prtyp\" value=\"kod\">\n";
       echo "</p>\n";
       break;
endswitch;
echo "</p>
<input type=\"hidden\" name=\"akce\" value=\"SaveDownAdvert\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"pridr\" value=\"".$pole_data['idr']."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\">
</form>\n";
}

function UlozReklamu()
{
// bezpecnostni kontrola
$GLOBALS["pridr"]=(int)$GLOBALS["pridr"];
$GLOBALS["prhodnota"]=phprs_sql_escape_string($GLOBALS["prhodnota"]);
$GLOBALS["prtyp"]=phprs_sql_escape_string($GLOBALS["prtyp"]);

$dotaz="update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$GLOBALS["prhodnota"]."', typ_reklamy='".$GLOBALS["prtyp"]."' where idr='".$GLOBALS["pridr"]."'";
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error AS101: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_HD_OK_EDIT_REKL_KOD."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=UpAdvert&amp;modul=reklama\">".RS_REK_HD_UPRAVIT_HORNI."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=DownAdvert&amp;modul=reklama\">".RS_REK_HD_UPRAVIT_DOLNI."</a></p>\n";
}

// --- [Kampan] --------------------------------------------------------------------------

function ShowCamp()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"#pridejkampan\" class=\"navigace\">".RS_REK_KM_PRIDAT."</a><p>\n";

// vypis
$dotazkamp=phprs_sql_query("select idk,alias,email,info from ".$GLOBALS["rspredpona"]."klik_kampan",$GLOBALS["dbspojeni"]);
$pocetkamp=phprs_sql_num_rows($dotazkamp);
echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_REK_KM_KAMPAN."</b></td>
<td align=\"center\"><b>".RS_REK_KM_EMAIL."</b></td>
<td align=\"center\"><b>".RS_REK_KM_POZNAMKA."</b></td>
<td align=\"center\"><b>".RS_REK_KM_AKCE."</b></td></tr>\n";
if ($pocetkamp==0):
  // zadna kampan
  echo "<tr class=\"txt\"><td colspan=\"4\" align=\"center\">".RS_REK_KM_ZADNA_KAMPAN."</td></tr>\n";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazkamp)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"left\">".htmlspecialchars($pole_data["alias"])."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data["email"])."</td>\n";
    echo "<td align=\"left\">".TestNaNic($pole_data["info"])."</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=DelCamp&amp;modul=reklama&amp;pridk=".$pole_data["idk"]."\">".RS_REK_KM_SMAZ."</a></td></tr>\n";
  endwhile;
endif;
echo "</table>\n";
echo "<br>\n";
echo "<hr width=\"600\">\n";

// pridavaci formular
echo "<a name=\"pridejkampan\"></a>
<p align=\"center\" class=\"txt\"><big><strong>".RS_REK_KM_NADPIS_ADD_KAMPAN."</strong></big></p>
<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_KAMPAN."</b></td><td><input type=\"text\" name=\"pralias\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_POZNAMKA."</b></td><td><input type=\"text\" name=\"prinfo\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_KM_FORM_EMAIL."</b></td><td><input type=\"text\" name=\"premail\" value=\"@\" size=\"30\" class=\"textpole\"></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AddCamp\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AddCamp()
{
// uprava vstupu
if ($GLOBALS["premail"]=='@'): $GLOBALS["premail"]=''; endif;
// bezpecnostni korekce
$GLOBALS["pralias"]=phprs_sql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prinfo"]=phprs_sql_escape_string($GLOBALS["prinfo"]);
$GLOBALS["premail"]=phprs_sql_escape_string($GLOBALS["premail"]);

// pridani polozky
@$error=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."klik_kampan values(null,'".$GLOBALS["pralias"]."','".$GLOBALS["prinfo"]."','".$GLOBALS["premail"]."')",$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error AS102: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_OK_ADD_KAMPAN."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCamp&amp;modul=reklama\">".RS_REK_KM_ZPET."</a></p>\n";
}

function DelCamp()
{
// bezpecnostni korekce
$GLOBALS["pridk"]=phprs_sql_escape_string($GLOBALS["pridk"]);

$chyba=0; // default false

// integrigni kontrola
$dotazkontr=phprs_sql_query("select count(idb) as pocet from ".$GLOBALS["rspredpona"]."klik_ban where id_kampan='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
if ($dotazkontr!==false):
  $pole_data=phprs_sql_fetch_assoc($dotazkontr);
  if ($pole_data['pocet']>0):
    // kampan je aktivni - obsahuje nejake reklamni polozky
    echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_ERR_AKTIVNI_KAMPAN."</p>\n";
    $chyba=1; // chyba
  endif;
endif;

// test na chybu
if ($chyba==0):
  @$error= phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."klik_kampan where idk='".$GLOBALS["pridk"]."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error AS103: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_REK_KM_OK_DEL_KAMPAN."</p>\n"; // vse Ok
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ShowCamp&amp;modul=reklama\">".RS_REK_KM_ZPET."</a></p>\n";
}

// --- [Bannery] -------------------------------------------------------------------------

function VypisBannery()
{
// link
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddBanner&amp;modul=reklama\" class=\"navigace\">".RS_REK_RP_PRIDAT."</a></p>\n";

// sestaveni dotazu
$dotaz="select b.idb,b.id_kampan,b.datum,b.alias,b.pocitadlo,b.pocitadlo_zobr,k.alias as alias_kampan ";
$dotaz.="from ".$GLOBALS["rspredpona"]."klik_ban as b, ".$GLOBALS["rspredpona"]."klik_kampan as k ";
$dotaz.="where b.id_kampan=k.idk order by b.id_kampan,b.datum desc";
$dotazban=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetban=phprs_sql_num_rows($dotazban);
// vypis banneru
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\">
<td align=\"center\"><b>".RS_REK_RP_ID."</b></td>
<td align=\"center\"><b>".RS_REK_RP_BANNER."</b></td>
<td align=\"center\"><b>".RS_REK_RP_DATUM."</b></td>
<td align=\"center\"><b>".RS_REK_RP_POC_ZOBR."</b></td>
<td align=\"center\"><b>".RS_REK_RP_POC_KLIKU."</b></td>
<td align=\"center\"><b>".RS_REK_RP_USPESNOST."</b></td>
<td align=\"center\"><b>".RS_REK_RP_AKCE."</b></td>
<td align=\"center\"><b>".RS_REK_RP_SMAZ."</b></td></tr>\n";
if ($pocetban==0):
  // zadna polozka
  echo "<tr class=\"txt\"><td colspan=\"8\" align=\"center\">".RS_REK_RP_ZADNA_POLOZKA."</td></tr>\n";
else:
  $akt_kampan=0; // inic. aktivni kampane
  for ($pom=0;$pom<$pocetban;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazban);
    if ($pole_data["id_kampan"]!=$akt_kampan):
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#E9EC7D')\" onmouseout=\"setPointer(this, '#FFFFFF')\"><td align=\"center\" colspan=\"8\">";
      echo "<b>".RS_REK_RP_ID_KAMPAN." ".$pole_data["id_kampan"]." - ".htmlspecialchars($pole_data["alias_kampan"])." - <a href=\"".RS_VYKONNYSOUBOR."?akce=UseBanner&amp;modul=reklama&amp;prtyp=kam&amp;pridtyp=".$pole_data["id_kampan"]."\">".RS_REK_RP_POUZIJ."</a></b>";
      echo "</td></tr>\n";
      $akt_kampan=$pole_data["id_kampan"]; // nastaveni nove aktivni kampane
    endif;
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">\n";
    echo "<td align=\"center\">".$pole_data["idb"]."</td>\n";
    echo "<td align=\"left\">".htmlspecialchars($pole_data["alias"])."</td>\n";
    echo "<td align=\"center\">".TestNaNic(MyDateToDate($pole_data["datum"]))."</td>\n";
    echo "<td align=\"center\">".$pole_data["pocitadlo_zobr"]."</td>\n";
    echo "<td align=\"center\">".$pole_data["pocitadlo"]."</td>\n";
    echo "<td align=\"center\">";
    if ($pole_data["pocitadlo_zobr"]>0):
      echo number_format((($pole_data["pocitadlo"]/$pole_data["pocitadlo_zobr"])*100),3,'.','').' %';
    else:
      echo "0";
    endif;
    echo "</td>\n";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditBanner&amp;modul=reklama&amp;pridb=".$pole_data["idb"]."\">".RS_REK_RP_UPRAVIT."</a> / ";
    echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=UseBanner&amp;modul=reklama&amp;prtyp=ban&amp;pridtyp=".$pole_data["idb"]."\">".RS_REK_RP_POUZIJ."</a></td>\n";
    echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["idb"]."\"></td></tr>\n";
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"8\"><input type=\"submit\" value=\" ".RS_REK_RP_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DeleteBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
</form>\n";

// informace k aplikaci reklamnich prvku skrze - tzv. phprs znacku
echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_INFO_PHPRS_ZNACKY."</p>\n<br>\n";
}

function PridejBanner()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\" class=\"navigace\">".RS_REK_RP_ZPET."</a></p>";

// pridavaci formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_KAMPAN."</b></td><td><select name=\"prkampan\" size=\"1\">".OptKampan(0)."</select></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_NAZEV_REKL."</b></td><td><input type=\"text\" name=\"pralias\" size=\"30\" class=\"textpole\"></td></tr>
<tr><td colspan=\"2\"><hr></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\" checked>".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" size=\"30\" class=\"textpole\"></td></tr>
<tr><td colspan=\"2\"><hr></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\">".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" size=\"30\" class=\"textpole\"></td></tr>
<tr><td colspan=\"2\"><hr></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\">".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\"></textarea></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function AcPridejBanner()
{
$GLOBALS["pralias"]=KorekceNadpisu($GLOBALS["pralias"]);

$GLOBALS["prkampan"]=phprs_sql_escape_string($GLOBALS["prkampan"]);
$GLOBALS["pralias"]=phprs_sql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prdruh"]=phprs_sql_escape_string($GLOBALS["prdruh"]);
$GLOBALS["prbanner1"]=phprs_sql_escape_string($GLOBALS["prbanner1"]);
$GLOBALS["prbanner2"]=phprs_sql_escape_string($GLOBALS["prbanner2"]);
$GLOBALS["prbanner3"]=phprs_sql_escape_string($GLOBALS["prbanner3"]);
$GLOBALS["prcil1"]=phprs_sql_escape_string($GLOBALS["prcil1"]);
$GLOBALS["prcil2"]=phprs_sql_escape_string($GLOBALS["prcil2"]);
$GLOBALS["prtext1"]=phprs_sql_escape_string($GLOBALS["prtext1"]);
$GLOBALS["prtext2"]=phprs_sql_escape_string($GLOBALS["prtext2"]);
$GLOBALS["prwidth"]=phprs_sql_escape_string($GLOBALS["prwidth"]);
$GLOBALS["prheight"]=phprs_sql_escape_string($GLOBALS["prheight"]);

// prednastaveni defaultniho stavu
$nast_datum=Date("Y-m-d");
$nast_pocet=0;
$nast_pocet_zobr=0;
$nast_datum_reset=$nast_datum;
$nast_celk_pocet=0;
$nast_celk_pocet_zobr=0;

// inic.
$dotaz='';
$pom_text='';

switch($GLOBALS["prdruh"]):
  case 0: // 0 = banner
    $dotaz="insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$nast_datum."','".$GLOBALS["pralias"]."','".$GLOBALS["prtext1"]."','".$GLOBALS["prbanner1"]."','".$GLOBALS["prcil1"]."','".$GLOBALS["prwidth"]."','".$GLOBALS["prheight"]."','".$GLOBALS["prdruh"]."','".$nast_pocet."','".$nast_pocet_zobr."','".$nast_datum_reset."','".$nast_celk_pocet."','".$nast_celk_pocet_zobr."')";
    $pom_text=RS_REK_RP_OK_ADD_REKL_C2B; // reklamni banner
    break;
  case 1: // 1 = text
    $dotaz="insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$nast_datum."','".$GLOBALS["pralias"]."','".$GLOBALS["prtext2"]."','".$GLOBALS["prbanner2"]."','".$GLOBALS["prcil2"]."','0','0','".$GLOBALS["prdruh"]."','".$nast_pocet."','".$nast_pocet_zobr."','".$nast_datum_reset."','".$nast_celk_pocet."','".$nast_celk_pocet_zobr."')";
    $pom_text=RS_REK_RP_OK_ADD_REKL_C2B; // reklamni text
    break;
  case 2: // 2 = reklamni kod
    $dotaz="insert into ".$GLOBALS["rspredpona"]."klik_ban values(null,'".$GLOBALS["prkampan"]."','".$nast_datum."','".$GLOBALS["pralias"]."','','".$GLOBALS["prbanner3"]."','','0','0','".$GLOBALS["prdruh"]."','".$nast_pocet."','".$nast_pocet_zobr."','".$nast_datum_reset."','".$nast_celk_pocet."','".$nast_celk_pocet_zobr."')";
    $pom_text=RS_REK_RO_OK_ADD_REKL_C2C; // reklamni kod
    break;
endswitch;

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error AS104: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_ADD_REKL_C1." ".$pom_text."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function UpravBanner()
{
// bezpecnostni kontrola
$GLOBALS["pridb"]=(int)$GLOBALS["pridb"];

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\" class=\"navigace\">".RS_REK_RP_ZPET."</a></p>";

$dotazban=phprs_sql_query("select * from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridb"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazban);

// pomocne statisticke vypocty
$pom_celk_zobr=($pole_data["celk_pocet_zobr"]+$pole_data["pocitadlo_zobr"]);
$pom_celk_kliku=($pole_data["celk_pocet"]+$pole_data["pocitadlo"]);

if ($pom_celk_zobr>0):
  $pom_celk_uspesnost=number_format((($pom_celk_kliku/$pom_celk_zobr)*100),3,'.','').' %';
else:
  $pom_celk_uspesnost='0';
endif;

if ($pole_data["pocitadlo_zobr"]>0):
  $pom_akt_uspesnost=number_format((($pole_data["pocitadlo"]/$pole_data["pocitadlo_zobr"])*100),3,'.','').' %';
else:
  $pom_akt_uspesnost='0';
endif;

// editacni formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_DATUM."</b></td><td>".MyDateToDate($pole_data["datum"])."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CELK_POC_ZOBR."</b></td><td>".$pom_celk_zobr."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CELK_POC_KLIKU."</b></td><td>".$pom_celk_kliku."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CELK_USPESNOST."</b></td><td>".$pom_celk_uspesnost."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_DATUM_RESET."</b></td><td>".MyDateToDate($pole_data["datum_reset"])."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_AKT_POC_ZOBR."</b></td><td>".$pole_data["pocitadlo_zobr"]."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_AKT_POC_KLIKU."</b></td><td>".$pole_data["pocitadlo"]."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_AKT_USPESNOST."</b></td><td>".$pom_akt_uspesnost."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_RESET_POCITADLA."</b></td><td><input type=\"checkbox\" name=\"prreset\" value=\"1\">".RS_TL_ANO."</td></tr>
</table>
<br>
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_KAMPAN."</b></td><td><select name=\"prkampan\" size=\"1\">".OptKampan($pole_data["id_kampan"])."</select></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_NAZEV_REKL."</b></td><td><input type=\"text\" name=\"pralias\" value=\"".htmlspecialchars($pole_data["alias"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr><td colspan=\"2\"><hr></td></tr>\n";
// banner
if ($pole_data["druh"]==0):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\" checked>".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" value=\"".htmlspecialchars($pole_data["banner"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" value=\"".htmlspecialchars($pole_data["cil"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" value=\"".htmlspecialchars($pole_data["text"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" value=\"".$pole_data["width"]."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" value=\"".$pole_data["height"]."\" size=\"30\" class=\"textpole\"></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"0\">".RS_REK_RP_FORM_BANNER."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_URL_ADR."</b></td><td><input type=\"text\" name=\"prbanner1\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil1\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_PRIDAV_TEXT."</b></td><td><input type=\"text\" name=\"prtext1\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_SIRKA."</b></td><td><input type=\"text\" name=\"prwidth\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_VYSKA."</b></td><td><input type=\"text\" name=\"prheight\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>\n";
endif;
// konec banneru
echo "<tr><td colspan=\"2\"><hr></td></tr>\n";
// text
if ($pole_data["druh"]==1):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\" checked>".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" value=\"".htmlspecialchars($pole_data["banner"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" value=\"".htmlspecialchars($pole_data["cil"])."\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" value=\"".htmlspecialchars($pole_data["text"])."\" size=\"30\" class=\"textpole\"></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"1\">".RS_REK_RP_FORM_TEXT."</td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_HLA_TEXT."</b></td><td><input type=\"text\" name=\"prbanner2\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_CIL_ADR."</b></td><td><input type=\"text\" name=\"prcil2\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_BUBL_TEXT."</b></td><td><input type=\"text\" name=\"prtext2\" value=\"\" size=\"30\" class=\"textpole\"></td></tr>\n";
endif;
// konec textu
echo "<tr><td colspan=\"2\"><hr></td></tr>\n";
// reklamni kod
if ($pole_data["druh"]==2):
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\" checked>".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\">".KorekceHTML($pole_data["banner"])."</textarea></td></tr>\n";
else:
  echo "<tr class=\"txt\"><td><b>".RS_REK_RP_FORM_FORMA_REKL."</b></td><td><input type=\"radio\" name=\"prdruh\" value=\"2\">".RS_REK_RP_FORM_REKL_KOD."</td></tr>
<tr class=\"txt\"><td colspan=\"2\"><textarea name=\"prbanner3\" rows=\"5\" cols=\"55\" class=\"textbox\"></textarea></td></tr>\n";
endif;
// konec reklamni kod
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"pridb\" value=\"".$GLOBALS["pridb"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>
<br>\n";
}

function AcUpravBanner()
{
// bezpecnostni kontrola
$GLOBALS["pralias"]=KorekceNadpisu($GLOBALS["pralias"]);

$GLOBALS["pridb"]=phprs_sql_escape_string($GLOBALS["pridb"]);
$GLOBALS["prkampan"]=phprs_sql_escape_string($GLOBALS["prkampan"]);
$GLOBALS["pralias"]=phprs_sql_escape_string($GLOBALS["pralias"]);
$GLOBALS["prdruh"]=phprs_sql_escape_string($GLOBALS["prdruh"]);
$GLOBALS["prbanner1"]=phprs_sql_escape_string($GLOBALS["prbanner1"]);
$GLOBALS["prbanner2"]=phprs_sql_escape_string($GLOBALS["prbanner2"]);
$GLOBALS["prbanner3"]=phprs_sql_escape_string($GLOBALS["prbanner3"]);
$GLOBALS["prcil1"]=phprs_sql_escape_string($GLOBALS["prcil1"]);
$GLOBALS["prcil2"]=phprs_sql_escape_string($GLOBALS["prcil2"]);
$GLOBALS["prtext1"]=phprs_sql_escape_string($GLOBALS["prtext1"]);
$GLOBALS["prtext2"]=phprs_sql_escape_string($GLOBALS["prtext2"]);
$GLOBALS["prwidth"]=phprs_sql_escape_string($GLOBALS["prwidth"]);
$GLOBALS["prheight"]=phprs_sql_escape_string($GLOBALS["prheight"]);

// inic.
$dotaz='';
$pom_text='';

switch ($GLOBALS["prdruh"]):
  case 0: // 0 = banner
    $dotaz="update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', text='".$GLOBALS["prtext1"]."', banner='".$GLOBALS["prbanner1"]."', cil='".$GLOBALS["prcil1"]."', width='".$GLOBALS["prwidth"]."', height='".$GLOBALS["prheight"]."', druh='0' where idb='".$GLOBALS["pridb"]."'";
    $pom_text=RS_REK_RP_OK_EDIT_REKL_C2A; // reklamni banner
    break;
  case 1: // 1 = text
    $dotaz="update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', text='".$GLOBALS["prtext2"]."', banner='".$GLOBALS["prbanner2"]."', cil='".$GLOBALS["prcil2"]."', druh='1' where idb='".$GLOBALS["pridb"]."'";
    $pom_text=RS_REK_RP_OK_EDIT_REKL_C2B; // reklamni text
    break;
  case 2: // 2 = reklamni kod
    $dotaz="update ".$GLOBALS["rspredpona"]."klik_ban set id_kampan='".$GLOBALS["prkampan"]."', alias='".$GLOBALS["pralias"]."', banner='".$GLOBALS["prbanner3"]."', druh='2' where idb='".$GLOBALS["pridb"]."'";
    $pom_text=RS_REK_RO_OK_EDIT_REKL_C2C; // reklamni kod
    break;
endswitch;

@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error AS105: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_EDIT_REKL_C1." ".$pom_text."</p>\n"; // vse OK
  // zpracovani resetu pocitadla
  if (isset($GLOBALS['prreset'])&&$GLOBALS['prreset']==1):
    $dotaz="update ".$GLOBALS["rspredpona"]."klik_ban set ";
    $dotaz.="datum_reset='".date("Y-m-d")."', celk_pocet=(celk_pocet+pocitadlo), celk_pocet_zobr=(celk_pocet_zobr+pocitadlo_zobr), pocitadlo=0, pocitadlo_zobr=0 ";
    $dotaz.="where idb='".$GLOBALS["pridb"]."'";
    @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error AS105R: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    endif;
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function PouzijBanner()
{
// bezpecnostni kontrola
$GLOBALS["pridtyp"]=(int)$GLOBALS["pridtyp"];

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\" class=\"navigace\">".RS_REK_RP_ZPET."</a></p>";

// inic.
$pom_text='';

switch($GLOBALS["prtyp"]):
  case 'ban': // banner
       $dotazali=phprs_sql_query("select alias from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
       list($pralias)=phprs_sql_fetch_row($dotazali);
       $pom_text=RS_REK_RP_TYP_REKL." - ".$pralias;
       break;
  case 'kam': // kampan
       $dotazali=phprs_sql_query("select alias from ".$GLOBALS["rspredpona"]."klik_kampan where idk='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
       list($pralias)=phprs_sql_fetch_row($dotazali);
       $pom_text=RS_REK_RP_TYP_KAMPAN." - ".$pralias;
       break;
endswitch;

echo "<p align=\"center\" class=\"txt\"><strong>\"".$pom_text."\"</strong></p>
<div align=\"center\">
<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"prco\" value=\"horni\"><input type=\"hidden\" name=\"prtyp\" value=\"".htmlspecialchars($GLOBALS["prtyp"])."\">
<input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\">
<input type=\"submit\" value=\" ".RS_REK_RP_TL_APL_HORNI_POZICE." \" class=\"tl\">
</form>
<br>
<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"prco\" value=\"dolni\"><input type=\"hidden\" name=\"prtyp\" value=\"".htmlspecialchars($GLOBALS["prtyp"])."\">
<input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\">
<input type=\"submit\" value=\" ".RS_REK_RP_TL_APL_DOLNI_POZICE." \" class=\"tl\">
</form>\n";
if ($GLOBALS["prtyp"]=='ban'):
  echo "<br>
<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<input type=\"hidden\" name=\"akce\" value=\"AcUseBanner\"><input type=\"hidden\" name=\"modul\" value=\"reklama\">
<input type=\"hidden\" name=\"prco\" value=\"generuj\"><input type=\"hidden\" name=\"prtyp\" value=\"".htmlspecialchars($GLOBALS["prtyp"])."\">
<input type=\"hidden\" name=\"pridtyp\" value=\"".$GLOBALS["pridtyp"]."\">
<input type=\"submit\" value=\" ".RS_REK_RP_TL_GENERUJ_KOD." \" class=\"tl\">
</form>\n";
endif;
echo "</div>\n";
}

function AcPouzijBanner()
{
/*
  (rs_klik_ban) druhy: banner = 0, text = 1, reklamni kod = 2
  (rs_klik_rekl) pozice: 1 = horni pozice, 2 = dolni pozice
  $prco: horni, dolni, generuj
  $prtyp: ban, kam
  $pridtyp: id -> $prtyp
  smerovaci soubor: direct.php?kam=id_cislo_banneru
*/

// bezpecnostni kontrola
$GLOBALS["pridtyp"]=(int)$GLOBALS["pridtyp"];

// inic.
$prkod='';
$prtyp_reklamy='';

// aplikace reklamniho prvku
if ($GLOBALS["prtyp"]=='ban'):
  $dotaztyp=phprs_sql_query("select text,banner,width,height,druh from ".$GLOBALS["rspredpona"]."klik_ban where idb='".$GLOBALS["pridtyp"]."'",$GLOBALS["dbspojeni"]);
  if ($dotaztyp!==false&&phprs_sql_num_rows($dotaztyp)>0):
    list($prtext,$prbanner,$prwidth,$prheight,$prdruh)=phprs_sql_fetch_row($dotaztyp);
  endif;

  // typ reklamniho prvku
  switch ($prdruh):
    case 0: // reklama - banner
      $prkod='<div class="banner-img"><a href="direct.php?kam='.$GLOBALS["pridtyp"].'" target="_blank"><img src="'.htmlspecialchars($prbanner).'" border="0" width="'.$prwidth.'" height="'.$prheight.'" alt="'.htmlspecialchars($prtext).'" title="'.htmlspecialchars($prtext).'">';
      if ($prtext!=''): $prkod.='<br>'.$prtext; endif;
      $prkod.='</a></div>';
      break;
    case 1: // reklama - text
      $prkod='<span class="banner-text"><a href="direct.php?kam='.$GLOBALS["pridtyp"].'" title="'.htmlspecialchars($prtext).'" target="_blank">'.htmlspecialchars($prbanner).'</a></span>';
      break;
    case 2: // reklama - reklamni kod
      $prkod='<span class="banner-text">'.$prbanner.'</span>';
      break;
  endswitch;

  $prtyp_reklamy='kod';
endif;

// aplikace kampane kampane
if ($GLOBALS["prtyp"]=='kam'):
  $prkod=$GLOBALS["pridtyp"];
  $prtyp_reklamy='kampan';
endif;

// proved akci X
switch($GLOBALS["prco"]):
  case "horni":
       @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$prkod."', typ_reklamy='".$prtyp_reklamy."' where pozice='1'",$GLOBALS["dbspojeni"]);
       if ($error === false):
         echo "<p align=\"center\" class=\"txt\">Error AS106: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_USE_HORNI_POZICE."</p>\n"; // vse OK
       endif;
       break;
  case "dolni":
       @$error=phprs_sql_query("update ".$GLOBALS["rspredpona"]."klik_rekl set kod='".$prkod."', typ_reklamy='".$prtyp_reklamy."' where pozice='2'",$GLOBALS["dbspojeni"]);
       if ($error === false):
         echo "<p align=\"center\" class=\"txt\">Error AS107: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
       else:
         echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_USE_DOLNI_POZICE."</p>\n"; // vse OK
       endif;
       break;
  case "generuj":
       echo "<p align=\"center\" class=\"txt\"><strong>".RS_REK_RP_REKL_KOD."</strong><br>\n";
       echo "<textarea name=\"prkod\" rows=\"8\" cols=\"70\" class=\"textbox\">".KorekceHTML($prkod)."</textarea></p>\n";
       break;
endswitch;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

function SmazBanner()
{
$chyba=0; // inic. chyby
if (!isset($GLOBALS["prpoleid"])): // inic. pole
 $pocet_pole_id=0;
else:
 $pocet_pole_id=count($GLOBALS["prpoleid"]);
endif;

// vymazani polozky
for ($pom=0;$pom<$pocet_pole_id;$pom++):
  @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."klik_ban where idb='".phprs_sql_escape_string($GLOBALS["prpoleid"][$pom])."'",$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error AS108: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    $chyba=1;
  endif;
endfor;

// vyhodnoceni globalniho stavu
if ($chyba==0):
  if ($pocet_pole_id==0):
    echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_DEL_REKL_NIC."</p>\n"; // prazdny vyber
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_REK_RP_OK_DEL_REKL."</p>\n";
  endif;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=Banner&amp;modul=reklama\">".RS_REK_RP_ZPET."</a></p>";
}

?>