<?php

######################################################################
# phpRS Administration Engine - ImageGallery section 1.2.9
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_imggal_obr, rs_imggal_sekce, rs_user

/*
  Tento soubor zajistuje spravu interni galerie.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // galerie obrazku
     case "ImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_SHOW_GAL."</h2>";
          ZaklPrehledIG();
          break;
     case "AddImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_GAL."</h2>";
          FormPriIG();
          break;
     case "AcAddImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_GAL."</h2>";
          PridejIG();
          break;
     case "EditImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_GAL."</h2>";
          FormUprIG();
          break;
     case "AcEditImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_GAL."</h2>";
          UpravIG();
          break;
     case "DeleteImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_DEL_GAL."</h2>";
          SmazIG();
          break;
     case "OpenImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_OPEN_GAL."</h2>";
          ShowPicIG();
          break;
     case "AddObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_OBR."</h2>";
          FormPriOBRIG();
          break;
     case "AcAddObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_ADD_OBR."</h2>";
          PridejOBRIG();
          break;
     case "DeleteObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_DEL_OBR."</h2>";
          SmazOBRIG();
          break;
     case "EditObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_OBR."</h2>";
          FormUprOBRIG();
          break;
     case "AcEditObrImgGal": AdminMenu();
          echo "<h2 align=\"center\">".RS_IGA_ROZ_EDIT_OBR."</h2>";
          UpravOBRIG();
          break;
endswitch;

// --[galerie]----------------------------------------------------------------------

function GenerujZoznamGaleriiPrePresun($idgal = 0) {

    // zoznam galerii okrem aktualnej, v ktorych ma pravo na zapis
    $sql = '
        SELECT  ids, nazev
        FROM    '.$GLOBALS["rspredpona"].'imggal_sekce
        WHERE   prava LIKE("%:1:%")
                AND
                ids != '.(int)$idgal.'
        ORDER BY
                nazev
        ;
    ';
    $result = phprs_sql_query($sql, $GLOBALS["dbspojeni"]);
    $str = '';
    while ($row = phprs_sql_fetch_assoc($result)) {
        $str .= '<option value="'.$row['ids'].'">'.htmlspecialchars($row['nazev']);
    }
    return $str;
}

// zakladni rozcestnik
function ZaklPrehledIG()
{
$autori=new SezAutori();

// link - pridat gal.
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddImgGal&amp;modul=intergal\" class=\"navigace\">".RS_IGA_SG_PRIDAT_GAL."</a></p>\n";

$dotazgal=phprs_sql_query("select ids,vlastnik,nazev,prava from ".$GLOBALS["rspredpona"]."imggal_sekce order by nazev",$GLOBALS["dbspojeni"]);
if ($dotazgal===false):
  $pocetgal=0; // neexistuje databazova tabulka
else:
  $pocetgal=phprs_sql_num_rows($dotazgal); // pocet zaznamu v tabulce
endif;

// vypis
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\"><input type=\"hidden\" name=\"akce\" value=\"DeleteImgGal\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">
<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_IGA_SG_NAZEV_GAL."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_VLASTNIK."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_AKCE."</b></td>
<td align=\"center\"><b>".RS_IGA_SG_SMAZ."</b></td></tr>\n";
if ($pocetgal==0):
  // zadna galerie
  echo "<tr class=\"txt\"><td colspan=\"4\" align=\"center\"><b>".RS_IGA_SG_ZADNA_GAL."</b></td></tr>\n";
else:
  for ($pom=0;$pom<$pocetgal;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazgal);
    if ((RSAUT_IDUSER==$pole_data["vlastnik"])||(RSAUT_PRAVA==2)):
      // je vlastnik nebo admin mohou vse
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
      echo "<td align=\"left\"><img src=\"image/adr_gal.gif\" width=\"24\" height=\"30\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\"> ".$pole_data["nazev"]."</td>\n";
      echo "<td align=\"left\">".$autori->UkazUser($pole_data["vlastnik"])."</td>\n";
      echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=EditImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_UPRAVIT."</a> / ";
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_OTEVRIT."</a></td>\n";
      echo "<td align=\"center\"><input type=\"checkbox\" name=\"prpoleids[]\" value=\"".$pole_data["ids"]."\"></td></tr>\n";
    else:
      // rozklad pristupovych prav
      $prpoleprav=array();
      $prpoleprav=explode(":",$pole_data["prava"]); // 0 - cteni, 1 - zapis, 2 - mazani
      // vypis galerie
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
      echo "<td align=\"left\"><img src=\"image/adr_gal.gif\" width=\"24\" height=\"30\" align=\"absmiddle\" alt=\"galerie\"> ".$pole_data["nazev"]."</td>\n";
      echo "<td align=\"left\">".$autori->UkazUser($pole_data["vlastnik"])."</td>\n";
      echo "<td align=\"center\">".RS_IGA_SG_UPRAVIT." / ";
      // test ma moznost prohlizeni
      if ($prpoleprav[0]==1):
        echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$pole_data["ids"]."\">".RS_IGA_SG_OTEVRIT."</a></td>\n";
      else:
        echo RS_IGA_SG_OTEVRIT."</td>\n";
      endif;
      echo "<td align=\"center\">&nbsp;</td></tr>\n";
    endif;
  endfor;
  echo "<tr class=\"txt\"><td align=\"right\" colspan=\"4\"><input type=\"submit\" value=\" ".RS_IGA_SG_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
endif;
echo "</table>\n";
echo "<input type=\"hidden\" name=\"akce\" value=\"DeleteImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">\n";
echo "</form>\n";

// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_UPOZORNENI."</p>
<p></p>\n";
}

function FormPriIG()
{
// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\" class=\"navigace\">".RS_IGA_SG_ZPET."</a></p>\n";
// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_NAZEV_GAL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"54\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_SG_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"4\" cols=\"75\" class=\"textbox\"></textarea></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_IGA_SG_FORM_PRAVA."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_PROHLIZENI."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\">".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_ZAPIS."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\">".RS_TL_NE."</td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_MAZANI."</b></td>
<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\" checked>".RS_TL_NE."</td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_PRIDAT." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function PridejIG()
{
// bezpecnostni korekce
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prcteni"]=phprs_sql_escape_string($GLOBALS["prcteni"]);
$GLOBALS["przapis"]=phprs_sql_escape_string($GLOBALS["przapis"]);
$GLOBALS["prmazani"]=phprs_sql_escape_string($GLOBALS["prmazani"]);

// sestaveni prav - tri cislice za sebou oddelene dvojteckou, 0 - false, 1 - true
if (!isset($GLOBALS["prcteni"])): $GLOBALS["prcteni"]=0; endif;
if (!isset($GLOBALS["przapis"])): $GLOBALS["przapis"]=0; endif;
if (!isset($GLOBALS["prmazani"])): $GLOBALS["prmazani"]=0; endif;
$nast_prava=$GLOBALS["prcteni"].":".$GLOBALS["przapis"].":".$GLOBALS["prmazani"];

// pridani galerie
$dotaz="
	insert into ".$GLOBALS["rspredpona"]."imggal_sekce
	values (
		null,
		'".RSAUT_IDUSER."',
		'".$GLOBALS["prnazev"]."',
		'".$GLOBALS["prpopis"]."',
		'".$nast_prava."'
	)
";
@$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($error === false):
  echo "<p align=\"center\" class=\"txt\">Error G1: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
else:
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_ADD_GAL."</p>\n"; // vse OK
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

function FormUprIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=addslashes($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\" class=\"navigace\">".RS_IGA_SG_ZPET."</a></p>\n";

$dotazgal=phprs_sql_query("select ids,vlastnik,nazev,popis,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pole_data=phprs_sql_fetch_assoc($dotazgal);

// rozklad pristupovych prav
$prpoleprav=explode(":",$pole_data["prava"]); // 0 - cteni, 1 - zapis, 2 - mazani

// formular
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_NAZEV_GAL."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"54\" class=\"textpole\" value=\"".$pole_data["nazev"]."\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_SG_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"4\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["popis"])."</textarea></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"center\"><b>".RS_IGA_SG_FORM_PRAVA."</b></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_PROHLIZENI."</b></td>\n";
if ($prpoleprav[0]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\">".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"prcteni\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prcteni\" value=\"0\" checked>".RS_TL_NE."</td></tr>";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_ZAPIS."</b></td>\n";
if ($prpoleprav[1]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\">".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"przapis\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"przapis\" value=\"0\" checked>".RS_TL_NE."</td></tr>";
endif;
echo "<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_SG_FORM_PRAVA_MAZANI."</b></td>\n";
if ($prpoleprav[2]==1):
  echo "<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\" checked>".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\">".RS_TL_NE."</td></tr>";
else:
  echo "<td align=\"left\"><input type=\"radio\" name=\"prmazani\" value=\"1\">".RS_TL_ANO." &nbsp;&nbsp; <input type=\"radio\" name=\"prmazani\" value=\"0\" checked>".RS_TL_NE."</td></tr>";
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<input type=\"hidden\" name=\"prvlastnik\" value=\"".$pole_data["vlastnik"]."\"><input type=\"hidden\" name=\"prids\" value=\"".$pole_data["ids"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\"> &nbsp; <input type=\"reset\" value=\" ".RS_TL_RESET." \" class=\"tl\"></p>
</form>\n";
}

function UpravIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prvlastnik"]=phprs_sql_escape_string($GLOBALS["prvlastnik"]);
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);
$GLOBALS["prcteni"]=phprs_sql_escape_string($GLOBALS["prcteni"]);
$GLOBALS["przapis"]=phprs_sql_escape_string($GLOBALS["przapis"]);
$GLOBALS["prmazani"]=phprs_sql_escape_string($GLOBALS["prmazani"]);

// sestaveni prav - tri cislice za sebou oddelene dvojteckou, 0 - false, 1 - true
if (!isset($GLOBALS["prcteni"])): $GLOBALS["prcteni"]=0; endif;
if (!isset($GLOBALS["przapis"])): $GLOBALS["przapis"]=0; endif;
if (!isset($GLOBALS["prmazani"])): $GLOBALS["prmazani"]=0; endif;
$nast_prava=$GLOBALS["prcteni"].":".$GLOBALS["przapis"].":".$GLOBALS["prmazani"];

// uprava hodnot
if ((RSAUT_IDUSER==$GLOBALS["prvlastnik"])||(RSAUT_PRAVA==2)):
  $dotaz="update ".$GLOBALS["rspredpona"]."imggal_sekce set nazev='".$GLOBALS["prnazev"]."',popis='".$GLOBALS["prpopis"]."',prava='".$nast_prava."' where ids='".$GLOBALS["prids"]."'";
  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error G2: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_EDIT_GAL."</p>\n"; // vse OK
  endif;
endif;
// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

function SmazIG()
{
// pocet polozek
if (isset($GLOBALS["prpoleids"])):
  $pocetgal=count($GLOBALS["prpoleids"]);
else:
  $pocetgal=0;
  echo RS_IGA_SG_ERR_DEL_POCET_NULA; // prazdny vyber
endif;

// vymazani galerie
for ($pom=0;$pom<$pocetgal;$pom++):
  $akt_id_galerie=phprs_sql_escape_string($GLOBALS["prpoleids"][$pom]);
  // zjisteni jmena gal.
  $dotazjmeno=phprs_sql_query("select nazev from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
  if ($dotazjmeno!==false&&phprs_sql_num_rows($dotazjmeno)>0):
    list($jmeno_gal)=phprs_sql_fetch_row($dotazjmeno);
  endif;
  // overeni poctu obr. v gal.
  $dotazobrpoc=phprs_sql_query("select ido from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
  if (phprs_sql_num_rows($dotazobrpoc)>0):
    // chyba - galerie neni prazdna
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_ERR_NENI_PRAZDNA_C1." \"".$jmeno_gal."\" ".RS_IGA_SG_ERR_NENI_PRAZDNA_C2."</p>\n";
  else:
    // OK - prazdna gal.; lze vymazat
    @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$akt_id_galerie."'",$GLOBALS["dbspojeni"]);
    if ($error === false):
      echo "<p align=\"center\" class=\"txt\">Error G3: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
    else:
      echo "<p align=\"center\" class=\"txt\">".RS_IGA_SG_OK_DEL_GAL_C1." \"".$jmeno_gal."\" ".RS_IGA_SG_OK_DEL_GAL_C2."</p>\n"; // vse OK
    endif;
  endif;
endfor;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
}

// --[obrazky]----------------------------------------------------------------------

function ShowPicIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
// inic. str.
if (!isset($GLOBALS["prstrana"])): $GLOBALS["prstrana"]=1; endif;

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\" class=\"navigace\">".RS_IGA_SG_ZPET."</a></p>\n";

$dotazgal=phprs_sql_query("select vlastnik,nazev,popis,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prnazev,$prpopis,$prprava)=phprs_sql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

// zobrazeni
echo "<div align=\"center\">
<div style=\"width: 400px;\" class=\"ramsedy-vypln\">
<span class=\"txt\"><big><strong>".$prnazev."</strong></big><br>".$prpopis."</span>
</div>
</div>\n";

// link - pridat obr.
if ($prpoleprav[1]==1):
  echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=AddObrImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\" class=\"navigace\">".RS_IGA_PO_PRIDAT_OBR."</a></p>\n";
endif;

// sestaveni limitu + limitniho pasu
$dotazcelkobr=phprs_sql_query("select count(ido) as pocet from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
if ($dotazcelkobr!==false&&phprs_sql_num_rows($dotazcelkobr)>0):
  list($pocetcelkobr)=phprs_sql_fetch_row($dotazcelkobr);
endif;

$pocetobrnastr=20; // pocet obrazku na 1 str.
$mozneobratky=ceil($pocetcelkobr/$pocetobrnastr);
if ($GLOBALS["prstrana"]==1): $startpozice=0; else: $startpozice=$pocetobrnastr*($GLOBALS["prstrana"]-1); endif; // vypocit limitu

if ($mozneobratky>1):
  echo "<p align=\"center\" class=\"txt\"> | ";
  for ($pom=0;$pom<$mozneobratky;$pom++):
    if (($pom+1)==$GLOBALS["prstrana"]): // omezeni akt. vypisove stranky
     echo ($pom*$pocetobrnastr)."-".(($pom+1)*$pocetobrnastr)." | ";
    else:
     echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."&amp;prstrana=".($pom+1)."\">".($pom*$pocetobrnastr)."-".(($pom+1)*$pocetobrnastr)."</a> | ";
    endif;
  endfor;
  echo "</p>\n";
endif;
// konec sestaveni limitu + limitniho pasu

// vzneseni dotazu
$dotazobr=phprs_sql_query("select ido,vlastnik,nazev,obr_poloha,obr_width,obr_height,obr_vel,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where sekce='".$GLOBALS["prids"]."' order by ido limit ".$startpozice.",".$pocetobrnastr,$GLOBALS["dbspojeni"]);
$pocetobr=phprs_sql_num_rows($dotazobr);

// zobrazeni obrazku
echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
if ($pocetobr==0):
  // zadny obrazek
  echo "<tr class=\"txt\"><td colspan=\"5\" align=\"center\"><b>".RS_IGA_PO_ZADNY_OBR."</b></td></tr>\n";
else:
  $pocetprubehu=0;
  if (RSAUT_IDUSER==$prvlastnik||RSAUT_PRAVA==2): // je vlastnik nebo admin, muze vse
     for ($pom=0;$pom<$pocetobr;$pom++):
       $pole_data=phprs_sql_fetch_assoc($dotazobr);
       if ($pocetprubehu==0):
         echo "<tr class=\"txt\">\n";
       endif;
       echo "<td align=\"center\" width=\"150\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big><br>";
       // test na existenci nahledu
       if ($pole_data["nahl_poloha"]=='none'):
         echo "<br><br>".RS_IGA_PO_NENI_NAHLED."<br><br><br>\n";
       else:
         echo "<img src=\"".$pole_data["nahl_poloha"]."\" width=\"".$pole_data["nahl_width"]."\" height=\"".$pole_data["nahl_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\"><br>\n";
       endif;
       echo "<b>".$pole_data["nazev"]."</b> (<a href=\"".$pole_data["obr_poloha"]."\" target=\"_blank\">".RS_IGA_PO_ORIGINAL."</a>)<br>";
       echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"]."<br>";
       echo RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB<br>";
       echo "<input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["ido"]."\"> ".RS_IGA_PO_SMAZ." / <a href=\"".RS_VYKONNYSOUBOR."?akce=EditObrImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."&amp;prido=".$pole_data["ido"]."\">".RS_IGA_PO_UPRAVIT."</a></td>\n";
       if ($pocetprubehu==4):
         echo "</tr>\n";
         $pocetprubehu=0;
       else:
         $pocetprubehu++;
       endif;
     endfor;
  else:
     for ($pom=0;$pom<$pocetobr;$pom++):
       $pole_data=phprs_sql_fetch_assoc($dotazobr);
       if ($pocetprubehu==0):
         echo "<tr class=\"txt\">\n";
       endif;
       echo "<td align=\"center\" width=\"150\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big><br>";
       // test na existenci nahledu
       if ($pole_data["nahl_poloha"]=='none'):
         echo "<br><br>".RS_IGA_PO_NENI_NAHLED."<br><br><br>\n";
       else:
         echo "<img src=\"".$pole_data["nahl_poloha"]."\" width=\"".$pole_data["nahl_width"]."\" height=\"".$pole_data["nahl_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\"><br>\n";
       endif;
       echo "<b>".$pole_data["nazev"]."</b> (<a href=\"".$pole_data["obr_poloha"]."\" target=\"_blank\">".RS_IGA_PO_ORIGINAL."</a>)<br>";
       echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"]."<br>";
       echo RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB<br>";
       // test ma moznost mazani
       if ($prpoleprav[2]==1):
         echo "<br><input type=\"checkbox\" name=\"prpoleid[]\" value=\"".$pole_data["ido"]."\"> Smazat</td>\n";
       else:
         echo "</td>\n";
       endif;
       // konec testu na moz. mazani
       if ($pocetprubehu==4):
         echo "</tr>\n";
         $pocetprubehu=0;
       else:
         $pocetprubehu++;
       endif;
     endfor;
  endif;
  // dokonceni tabulky
  switch ($pocetprubehu):
    case 0: break;
    case 1: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 2: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 3: echo "<td width=\"150\">&nbsp;</td><td width=\"150\">&nbsp;</td></tr>"; break;
    case 4: echo "<td width=\"150\">&nbsp;</td></tr>"; break;
  endswitch;
  if (RSAUT_IDUSER==$prvlastnik||RSAUT_PRAVA==2||$prpoleprav[2]==1):
    echo "<tr class=\"txt\"><td align=\"right\" colspan=\"5\"><input type=\"submit\" value=\" ".RS_IGA_PO_SMAZ_OZNAC." \" class=\"tl\"></td></tr>\n";
  endif;
endif;
echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"DeleteObrImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\">
</form>
<p></p>\n";

// informace k aplikaci obrazku - tzv. phprs znacka
//echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_INFO_PHPRS_ZNACKY."</p>\n<p></p>\n";
}

function FormPriOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\" class=\"navigace\">".RS_IGA_PO_ZPET_PRED."</a></p>\n";

echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\" enctype=\"multipart/form-data\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_NAZEV_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"57\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_PO_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"3\" cols=\"75\" class=\"textbox\"></textarea></td></tr>
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_OBRAZEK."</b></td>
<td align=\"left\"><input type=\"file\" name=\"prsoubor[]\" accept=\"image/gif,image/jpeg,image/png\" size=\"30\" class=\"textpole\" multiple></td></tr>
</table>
<input type=\"hidden\" name=\"akce\" value=\"AcAddObrImgGal\"><input type=\"hidden\" name=\"modul\" value=\"intergal\">
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\">
<p align=\"center\"><input type=\"submit\" value=\" ".RS_IGA_PO_TL_UPLOAD." \" class=\"tl\" onclick=\"$(this).attr('disabled', 'disabled').attr('value', 'Uploading…');\"></p>
</form>\n";

// multiupload info
echo "<p align=\"center\" class=\"txt\">".RS_IGA_MULTIUPL_1." <b>".ini_get('max_file_uploads')."</b> ".RS_IGA_MULTIUPL_2." <b>".ini_get('post_max_size')."B</b> ".RS_IGA_MULTIUPL_3.".</p>\n<p></p>\n";
// upozorneni
echo "<p align=\"center\" class=\"txt\">".RS_IGA_MULTIUPL_4."</p>\n<p></p>\n";
echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_INFO_GENER_NAHLEDU."</p>\n<p></p>\n";

}

function PridejOBRIG() {
	// bezpecnostni kontrola
	$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu

	$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
	$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
	$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);

	// test na pritomnost potrebnych promennych
	if (!isset($GLOBALS['rsconfig']['img_nahled_sirka'])): $GLOBALS['rsconfig']['img_nahled_sirka']=120; endif; // sirka nahledu
	if (!isset($GLOBALS['rsconfig']['img_nahled_vyska'])): $GLOBALS['rsconfig']['img_nahled_vyska']=96; endif; // vyska nahledu

	// autorizacni pole pro formaty souboru
	$pole_platne_typy_souboru_upload = array('jpg','jpeg','png','gif');
	$pole_platne_typy_souboru_nahled = array('jpg','jpeg','png','gif');

	// navrat
	echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\" class=\"navigace\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";

	// multiupload
	$num_files = count($_FILES['prsoubor']['name']);
	for ($i=0; $i < $num_files; $i++) {

		######################################################################################################

		// test na platnost uploadu
		if (empty($_FILES['prsoubor']['tmp_name'][$i])||$_FILES['prsoubor']['tmp_name'][$i]=='none'):
		  echo "<p align=\"center\" class=\"txt\">Error G4: ".RS_IGA_PO_ERR_NELZE_UPLOADOVAT."</p>\n";
		  exit();
		endif;
		// test na nulovou velikost souboru
		if ($_FILES['prsoubor']['size'][$i]==0):
		  echo "<p align=\"center\" class=\"txt\">Error G5: ".RS_IGA_PO_ERR_NULOVA_DELKA."</p>\n";
		  exit();
		endif;
		// test na obsah uploadnuteho suboru pokusom o ziskanie rozmerov obrazku
		if (false === getimagesize($_FILES['prsoubor']['tmp_name'][$i])):
		  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_FORMAT."</p>\n";
		  exit();
		endif;

		// zakladny vypis informacii o subore
		echo ($i+1)."/".$num_files." <b>".htmlspecialchars($_FILES['prsoubor']['name'][$i])."</b>: ";

		// test na rozlisenie fotografie, podla rozmerov a memory limitu
		list($tmp_sirka,$tmp_vyska,$tmp_typ,$tmp_atr)=getimagesize($_FILES['prsoubor']['tmp_name'][$i]);
		$memory_used = memory_get_usage(true);
		$memory_limit = (ini_get('memory_limit'));
		$memory_limit = trim($memory_limit);
		switch(strtolower($memory_limit[strlen($memory_limit)-1])) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':	$memory_limit *= 1024;
			case 'm':	$memory_limit *= 1024;
			case 'k':	$memory_limit *= 1024;
		}
		switch ($tmp_typ) {
			case 1: $bytes_pre_pixel = 3; break; // format gif
			case 2: $bytes_pre_pixel = 3; break; // format jpg
			case 3: $bytes_pre_pixel = 4; break;  // format png
			default: $bytes_pre_pixel = 3; break;
		}
		$img_memory_required = $tmp_sirka * $tmp_vyska * $bytes_pre_pixel * 2;

		if ($memory_limit - $memory_used <= $img_memory_required) {
			echo " Chyba: Systém nedokáže spracovať obrázok rozmerov: ".(int)$tmp_sirka."&times;".(int)$tmp_vyska." px<br>";
		} else {
			// dekompilace jmena souboru souboru - ziskani pripony a jmena
			$jmeno_sb=explode('.',$_FILES['prsoubor']['name'][$i]); // v idealnim pripade: 0 = jmeno, 1 = pripona
			$pocet_casti_sb=count($jmeno_sb);
			if ($pocet_casti_sb>0):
			  $pripona_sb=strtolower($jmeno_sb[$pocet_casti_sb-1]);
			else:
			  $pripona_sb='';
			endif;

			// bezpecnostni kontrola na neplatny format obrazku
			if (!in_array($pripona_sb,$pole_platne_typy_souboru_upload)):
			  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_FORMAT."</p>\n";
			  exit();
			endif;

			// skratenie dlheho nazvu suboru
			if (mb_strlen($jmeno_sb[0]) > 16) {
				$jmeno_sb[0] = mb_substr($jmeno_sb[0], 0, 15);
			}
			// uprava nazvu
			$jmeno_sb[0]=VratSEOLink($jmeno_sb[0]);
			// spec. predpona pred jmeno souboru
			$predpona_pred_sb=Date("Ym").'_'.uniqid();
			// sestaveni noveho jmena souboru
			$sb_info_novy_jmeno=$GLOBALS['rsconfig']['img_adresar'].$predpona_pred_sb."_".$jmeno_sb[0].".".$pripona_sb;
			// sestaveni noveho jmena nahledu
			$nahled_jmeno=$GLOBALS['rsconfig']['img_adresar']."n".$predpona_pred_sb."_".$jmeno_sb[0].".".$pripona_sb;

			if (is_uploaded_file($_FILES['prsoubor']['tmp_name'][$i])) {
			  if (move_uploaded_file($_FILES['prsoubor']['tmp_name'][$i],$sb_info_novy_jmeno)) {
			    chmod ($sb_info_novy_jmeno,0744); // nastaveni pristupovych prav
			    // nacteni dodatecnych informaci o uploadovanem obrazku
			    // cislene typy obr.: 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF.
			    list($sb_info_novy_sirka,$sb_info_novy_vyska,$sb_info_novy_typ,$sb_info_novy_img_atr)=getimagesize($sb_info_novy_jmeno);
			    $sb_info_novy_velikost=$_FILES['prsoubor']['size'][$i];
			    // test na platnost a existenci dodatecnych informaci
			    if (empty($sb_info_novy_sirka)||empty($sb_info_novy_vyska)) {
			      // nemohu najit potrebne informace o souboru
			      echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_NEJSOU_DATA."</p>\n";
				  unlink($sb_info_novy_jmeno);
			      exit();
			    }
				// upload OK
				echo " ".RS_IGA_PO_OK_UPLOAD_OBR." ";

			  } else {
			    // chyba pri ukladani souboru
			    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_PRI_ULOZENI."</p>\n";
			    exit();
			  }
			} else {
			  // soubor nebyl uploadovan bezpecnou cestou
			  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_SECURE_ERR_BEZPECNOST."</p>\n";
			  exit();
			}

			$generovat_nahled=1; // prepinac generovani nahledu - default true

			if (($GLOBALS['rsconfig']['img_nahled_sirka']>$sb_info_novy_sirka)&&($GLOBALS['rsconfig']['img_nahled_vyska']>$sb_info_novy_vyska)):
			  $generovat_nahled=0; // zruseni generovani nahledu
			endif;

			if (!in_array($pripona_sb,$pole_platne_typy_souboru_nahled)):
			  $generovat_nahled=0; // zruseni generovani nahledu
			endif;

			// generovani nahledu
			if ($generovat_nahled==1):
			  // priprava obr.
			  if($sb_info_novy_sirka>$sb_info_novy_vyska): $prpodilobr=$sb_info_novy_sirka/$sb_info_novy_vyska; endif;
			  if($sb_info_novy_sirka<$sb_info_novy_vyska): $prpodilobr=$sb_info_novy_vyska/$sb_info_novy_sirka; endif;
			  if($sb_info_novy_sirka==$sb_info_novy_vyska): $prpodilobr=1; endif;
			  // doupraveni rozmeru nahledu pohled sirky, vysky real. obr.
			  if($sb_info_novy_sirka>$sb_info_novy_vyska):
			    $nahled_sirka=$GLOBALS['rsconfig']['img_nahled_sirka'];
			    $nahled_vyska=round($GLOBALS['rsconfig']['img_nahled_sirka']/$prpodilobr);
			  endif;
			  if($sb_info_novy_sirka<$sb_info_novy_vyska):
			    $nahled_sirka=round($GLOBALS['rsconfig']['img_nahled_vyska']/$prpodilobr);
			    $nahled_vyska=$GLOBALS['rsconfig']['img_nahled_vyska'];
			  endif;
			  if($sb_info_novy_sirka==$sb_info_novy_vyska):
			    $nahled_sirka=$GLOBALS['rsconfig']['img_nahled_sirka'];
			    $nahled_vyska=$GLOBALS['rsconfig']['img_nahled_vyska'];
			  endif;
			  // tvorba nahledu
			  switch ($sb_info_novy_typ):
			    case 1: $probrzbroj=ImageCreateFromGIF($sb_info_novy_jmeno); break; // format gif
			    case 2: $probrzbroj=ImageCreateFromJPEG($sb_info_novy_jmeno); break; // format jpg
			    case 3: $probrzbroj=ImageCreateFromPNG($sb_info_novy_jmeno); break;  // format png
			  endswitch;
			  // pro GD 1.x
			  /*
			  $probrcil=ImageCreate($nahled_sirka,$nahled_vyska);
			  imagecopyresized($probrcil,$probrzbroj,0,0,0,0,$nahled_sirka,$nahled_vyska,$sb_info_novy_sirka,$sb_info_novy_vyska);
			  */
			  // konec pro GD 1.x
			  // pro GD 2.x
			  $probrcil=imagecreatetruecolor($nahled_sirka,$nahled_vyska);
			  //imagecopyresized($probrcil,$probrzbroj,0,0,0,0,$nahled_sirka,$nahled_vyska,$sb_info_novy_sirka,$sb_info_novy_vyska);
			  imagecopyresampled($probrcil,$probrzbroj,0,0,0,0,$nahled_sirka,$nahled_vyska,$sb_info_novy_sirka,$sb_info_novy_vyska); // lepsi vysledky nez: ImageCopyResized
			  // konec pro GD 2.x
			  switch ($sb_info_novy_typ):
			    case 1: ImageGIF($probrcil,$nahled_jmeno); break; // format gif
			    case 2: ImageJPEG($probrcil,$nahled_jmeno,90); break; // format jpg
			    case 3: ImagePNG($probrcil,$nahled_jmeno); break;     // format png
			  endswitch;
			  ImageDestroy($probrzbroj);
			  ImageDestroy($probrcil);
			else:
			  // nelze generovat nahled
			  echo RS_IGA_PO_ERR_NELZE_GENER_NAHLED." \n";
			endif;

			// ulozeni obrazku do DB
			if ($generovat_nahled==1):
			  // s nahledem
			  $dotaz="insert into ".$GLOBALS["rspredpona"]."imggal_obr values ";
			  $dotaz.="(null,'".RSAUT_IDUSER."','".$GLOBALS["prids"]."','".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."',0,'".$sb_info_novy_jmeno."','".$sb_info_novy_sirka."',";
			  $dotaz.="'".$sb_info_novy_vyska."','".$sb_info_novy_velikost."','".$nahled_jmeno."','".$nahled_sirka."','".$nahled_vyska."')";

			  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
			  if ($error === false):
			    echo "Error G6: ".RS_DB_ERR_SQL_DOTAZ."<br>\n"; // chyba
			  else:
			    echo RS_IGA_PO_OK_ADD_OBR."<br>\n"; // vse OK
			  endif;
			else:
			  // bez nahledu
			  $dotaz="insert into ".$GLOBALS["rspredpona"]."imggal_obr values ";
			  $dotaz.="(null,'".RSAUT_IDUSER."','".$GLOBALS["prids"]."','".$GLOBALS["prnazev"]."','".$GLOBALS["prpopis"]."',0,'".$sb_info_novy_jmeno."','".$sb_info_novy_sirka."',";
			  $dotaz.="'".$sb_info_novy_vyska."','".$sb_info_novy_velikost."','none','0','0')";

			  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
			  if ($error === false):
			    echo RS_DB_ERR_SQL_DOTAZ."<br>\n"; // chyba
				unlink($sb_info_novy_jmeno);
			  else:
			    echo RS_IGA_PO_OK_ADD_OBR."<br>\n"; // vse OK
			  endif;
			endif;
			######################################################################################################
		}
	}
}

function SmazOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);

// nacteni gal.
$dotazgal=phprs_sql_query("select vlastnik,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prprava)=phprs_sql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

if (isset($GLOBALS["prpoleid"])):
  $pocetobr=count($GLOBALS["prpoleid"]);
else:
  $pocetobr=0; // prazdny vyber
endif;

if ($prpoleprav[2]==1): // uzivatel ma povoleni mazat
  for ($pom=0;$pom<$pocetobr;$pom++):
    $akt_id_obrazek=phprs_sql_escape_string($GLOBALS["prpoleid"][$pom]); // id obrazku
    $dotazobr=phprs_sql_query("select obr_poloha,nahl_poloha  from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$akt_id_obrazek."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
    if (phprs_sql_num_rows($dotazobr)==1):
      $pole_poloha=phprs_sql_fetch_assoc($dotazobr);
      if (unlink($pole_poloha["obr_poloha"])==0):
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_DEL_ORIG_OBR."</p>\n"; // chyba pri mazani orig.
      endif;
      if (unlink($pole_poloha["nahl_poloha"])==0):
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ERR_CHYBA_DEL_NAHLED_OBR."</p>\n"; // chyba pri mazani nahledu
      endif;
      @$error=phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".addslashes($GLOBALS["prpoleid"][$pom])."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
      if ($error === false):
        echo "<p align=\"center\" class=\"txt\">Error G8: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
      else:
        echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_DEL_OBR_C1." ".$GLOBALS["prpoleid"][$pom]." ".RS_IGA_PO_OK_DEL_OBR_C2."</p>\n"; // vse OK
      endif;
    else:
      echo "<p align=\"center\" class=\"txt\">Error G9: ".RS_IGA_PO_ERR_NEMOHU_NAJIT."</p>\n"; // nelze najit obrazek
    endif;
  endfor;
endif;

// navrat
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ImgGal&amp;modul=intergal\">".RS_IGA_SG_ZPET."</a></p>\n";
echo "<p align=\"center\" class=\"txt\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";
}

function FormUprOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prido"]=phprs_sql_escape_string($GLOBALS["prido"]);

// navrat
echo "<p align=\"center\" class=\"txt-navigace\"><a href=\"".RS_VYKONNYSOUBOR."?akce=OpenImgGal&amp;modul=intergal&amp;prids=".$GLOBALS["prids"]."\" class=\"navigace\">".RS_IGA_PO_ZPET_OBR."</a></p>\n";

// dotaz na upravu obr.
$dotazobr=phprs_sql_query("select ido,vlastnik,nazev,popis,obr_poloha,obr_width,obr_height,obr_vel,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$GLOBALS["prido"]."' and sekce='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
$pocetobr=phprs_sql_num_rows($dotazobr);

if ($pocetobr==1):
  // nacteni dat
  $pole_data=phprs_sql_fetch_assoc($dotazobr);
  // vypis
  echo "<p align=\"center\" class=\"txt\"><big><b>".RS_IGA_PO_ID." ".$pole_data["ido"]."</b></big></p>\n";
  echo "<p align=\"center\"><img src=\"".$pole_data["obr_poloha"]."\" width=\"".$pole_data["obr_width"]."\" height=\"".$pole_data["obr_height"]."\" align=\"absmiddle\" alt=\"".$pole_data["nazev"]."\"></p>\n";
  echo "<p align=\"center\" class=\"txt\"><b>".$pole_data["nazev"]."</b><br>\n";
  echo RS_IGA_PO_SIRKA_VYSKA." ".$pole_data["obr_width"]."x".$pole_data["obr_height"].", ".RS_IGA_PO_VELIKOST." ".round($pole_data["obr_vel"]/1024)." kB</p>\n";
  echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_ADR_ORIG_OBR." ".$pole_data["obr_poloha"]."<br>\n";
  if ($pole_data["nahl_poloha"]=="none"):
    echo RS_IGA_PO_ADR_NAHLED_OBR." ".RS_IGA_PO_NENI_NAHLED."</p>\n"; // nahled neexistuje
  else:
    echo RS_IGA_PO_ADR_NAHLED_OBR." ".$pole_data["nahl_poloha"]."</p>\n"; // nahled existuje
  endif;
  // formular pro upravu popisu
  echo "<form action=\"".RS_VYKONNYSOUBOR."\" method=\"post\">
<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"center\" class=\"ramsedy-vypln\">
<tr class=\"txt\"><td align=\"left\"><b>".RS_IGA_PO_FORM_NAZEV_OBR."</b></td>
<td align=\"left\"><input type=\"text\" name=\"prnazev\" size=\"57\" value=\"".$pole_data["nazev"]."\" class=\"textpole\"></td></tr>
<tr class=\"txt\"><td colspan=\"2\" align=\"left\"><b>".RS_IGA_PO_FORM_POPIS."</b><br>
<textarea name=\"prpopis\" rows=\"3\" cols=\"75\" class=\"textbox\">".KorekceHTML($pole_data["popis"])."</textarea></td></tr>";

// presun obrazku do inej galerie

// nacitanie prav aktualnej galerie
$sql = '
    SELECT  vlastnik, prava
    FROM    '.$GLOBALS["rspredpona"].'imggal_sekce
    WHERE   ids = '.(int)$GLOBALS["prids"].'
    ;
';
$result = phprs_sql_query($sql, $GLOBALS["dbspojeni"]);
list($prvlastnik, $prprava)=phprs_sql_fetch_row($result);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)) {  // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
} else {
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
}

// ak ma aktivny uzivatel pravo mazat, povolim presun obrazku do inej galerie
if ($prpoleprav[2] == 1) {
    echo '
    <tr class="txt">
        <td align="left">
            <b>'.RS_IGA_PRESUN_DO_JINE_GAL.':</b>
        </td>
        <td align="left">
            <select name="id_target_gallery"><option value="0">&nbsp;'.GenerujZoznamGaleriiPrePresun($GLOBALS["prids"]).'</select>
        </td>
    </tr>';
}

echo "</table>
<input type=\"hidden\" name=\"akce\" value=\"AcEditObrImgGal\" /><input type=\"hidden\" name=\"modul\" value=\"intergal\" />
<input type=\"hidden\" name=\"prids\" value=\"".$GLOBALS["prids"]."\" /><input type=\"hidden\" name=\"prido\" value=\"".$GLOBALS["prido"]."\" />
<p align=\"center\"><input type=\"submit\" value=\" ".RS_TL_ULOZ." \" class=\"tl\" /></p>
</form>
<p></p>\n";
endif;
}

function UpravOBRIG()
{
// bezpecnostni kontrola
$GLOBALS["prnazev"]=KorekceNadpisu($GLOBALS["prnazev"]); // korekce nadpisu

$GLOBALS["prids"]=phprs_sql_escape_string($GLOBALS["prids"]);
$GLOBALS["prido"]=phprs_sql_escape_string($GLOBALS["prido"]);
$GLOBALS["prnazev"]=phprs_sql_escape_string($GLOBALS["prnazev"]);
$GLOBALS["prpopis"]=phprs_sql_escape_string($GLOBALS["prpopis"]);

$dotazgal=phprs_sql_query("select vlastnik,prava from ".$GLOBALS["rspredpona"]."imggal_sekce where ids='".$GLOBALS["prids"]."'",$GLOBALS["dbspojeni"]);
list($prvlastnik,$prprava)=phprs_sql_fetch_row($dotazgal);

// rozklad pristupovych prav
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)): // pro vlastnika a admina vsechna true
  $prpoleprav[0]=1; // cteni
  $prpoleprav[1]=1; // zapis
  $prpoleprav[2]=1; // mazani
else:
  $prpoleprav=explode(":",$prprava); // 0 - cteni, 1 - zapis, 2 - mazani
endif;

// uprava hodnot
if ((RSAUT_IDUSER==$prvlastnik)||(RSAUT_PRAVA==2)):
  $dotaz="update ".$GLOBALS["rspredpona"]."imggal_obr set nazev='".$GLOBALS["prnazev"]."',popis='".$GLOBALS["prpopis"]."' where ido='".$GLOBALS["prido"]."' and sekce='".$GLOBALS["prids"]."'";
  @$error=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
  if ($error === false):
    echo "<p align=\"center\" class=\"txt\">Error G10: ".RS_DB_ERR_SQL_DOTAZ."</p>\n"; // chyba
  else:
    echo "<p align=\"center\" class=\"txt\">".RS_IGA_PO_OK_EDIT_OBR."</p>\n"; // vse OK
  endif;
endif;

// presun

if (isset($GLOBALS['id_target_gallery']) && $GLOBALS['id_target_gallery'] != 0) {
    // bola odoslana poziadavka na presun obrazku, vykonaj kontrolu prav zapisu do cielovej galerie

    // test existencie cielovej galerie, ktorej je aktualny uzivatel vlastnikom, alebo je povoleny zapis
    $sql = '
        SELECT  1
        FROM    '.$GLOBALS["rspredpona"].'imggal_sekce
        WHERE   ids = '.(int)$GLOBALS['id_target_gallery'].'
                AND
                (
                    prava LIKE("%:1:%")
                    OR
                    vlastnik = '.RSAUT_IDUSER.'
                )
        ;
    ';
    if ($GLOBALS['Uzivatel']->JeAdmin()) {
        // aktualy uzivatel je admin, ma prava
        $pravo_na_zapis = TRUE;
    } else {
        $result = phprs_sql_query($sql, $GLOBALS["dbspojeni"]);
        if (FALSE !== $result && phprs_sql_num_rows($result) == 1) {
            // do cielovej galerie je povoleny zapis
            $pravo_na_zapis = TRUE;
        } else {
            // cielova galeria neexistuje alebo nema povoleny zapis
            $pravo_na_zapis = FALSE;
        }
    }

    if ($pravo_na_zapis === TRUE) {
        // zapis je povoleny, vykonaj presun obrazku
        $sql = '
            UPDATE  '.$GLOBALS["rspredpona"].'imggal_obr
            SET     sekce = '.(int)$GLOBALS['id_target_gallery'].'
            WHERE   ido = '.(int)$GLOBALS["prido"].'
                    AND
                    sekce = '.(int)$GLOBALS["prids"].'
            ;
        ';
        $result = phprs_sql_query($sql, $GLOBALS["dbspojeni"]);
        if (!$result) {
            // presun sa nepodaril
            echo '<p align="center" class="txt">'.RS_IGA_PRESUN_ERR.'</p>';
            return;
        } else {
            // presun sa podaril
            echo '<p align="center" class="txt">'.RS_IGA_PRESUN_OK1.' <a href="'.RS_VYKONNYSOUBOR.'?akce=OpenImgGal&modul=intergal&prids='.(int)$GLOBALS['id_target_gallery'].'">'.RS_IGA_PRESUN_OK2.'</a>.</p>';
            // spat na detail galerie
            echo '<p align="center" class="txt"><a href="'.RS_VYKONNYSOUBOR.'?akce=OpenImgGal&modul=intergal&prids='.(int)$GLOBALS["prids"].'">'.RS_IGA_PO_ZPET_OBR.'</a></p>';
            return;
        }
    } else {
        // zapis nie je povoleny, porusenie prav!
        echo '<p align="center" class="txt">'.RS_IGA_PRESUN_ERR_PRAVA.'</p>';
        return;
    }
}

// navrat
FormUprOBRIG();
}

?>