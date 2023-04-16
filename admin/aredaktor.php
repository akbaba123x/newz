<?php

######################################################################
# phpRS Administration Engine - Redaktor section 1.0.6
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_clanky

/*
  Tento soubor zajistuje obsluhu vydavatelske nastenky.
*/

if ($Uzivatel->StavSession!=1):
  echo "<html><body><div align=\"center\">Tento soubor neni urcen k vnejsimu spousteni!</div></body></html>";
  exit;
endif;

// ---[rozcestnik]------------------------------------------------------------------
switch($GLOBALS['akce']):
     // uzivatele
     case "PubClanku": AdminMenu();
          echo "<h2 align=\"center\">".RS_RED_ROZ_PREHLED_CLA."</h2>\n";
          PubClanku();
          break;
endswitch;

// ---[hlavni fce]------------------------------------------------------------------
function PubClanku()
{
// nacteni seznamu uzivatelu(autoru) do pole "autori"
$dotazaut=phprs_sql_query("select idu,user from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
if (phprs_sql_num_rows($dotazaut)>0):
  while($pole_data = phprs_sql_fetch_assoc($dotazaut)):
    $autori[$pole_data['idu']]=$pole_data['user'];
  endwhile;
endif;

// sestaveni dotazu
$dnesnidatum=Date("Y-m-d H:i:s"); // akt. datum

$akt_je_admin=$GLOBALS['Uzivatel']->JeAdmin();
$akt_je_vydavatel=$GLOBALS['Uzivatel']->MuzeVydavat();

if ($akt_je_admin==1):
  // dotaz c. 1
  $dotaz1="select idc,link,titulek,datum,autor,visible from ".$GLOBALS["rspredpona"]."clanky where datum>'".$dnesnidatum."' order by datum";
  // dotaz c. 2
  $dotaz2="select idc,link,titulek,datum,autor,visible from ".$GLOBALS["rspredpona"]."clanky where visible=0 order by datum";
else:
  $akt_seznam_podrizenych=$GLOBALS['Uzivatel']->SeznamDostupUser();
  // dotaz c. 1
  $dotaz1="select idc,link,titulek,datum,autor,visible from ".$GLOBALS["rspredpona"]."clanky ";
  $dotaz1.="where datum>'".$dnesnidatum."' and autor in (".$akt_seznam_podrizenych.") order by datum";
  // dotaz c. 2
  $dotaz2="select idc,link,titulek,datum,autor,visible from ".$GLOBALS["rspredpona"]."clanky ";
  $dotaz2.="where visible=0 and autor in (".$akt_seznam_podrizenych.") order by datum";
endif;

// seznam clanku omezenych datem vydani
$dotazcla=phprs_sql_query($dotaz1,$GLOBALS["dbspojeni"]);
$pocetcla=phprs_sql_num_rows($dotazcla);

echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_RED_SC_NADPIS_BUD_VYDANI.": ".$dnesnidatum."</strong></big></p>\n";
if ($pocetcla==0):
  // CHYBA: Teto podmince neodpovida zadny clanek!
  echo "<p align=\"center\" class=\"txt\">".RS_RED_SC_ZADNY_CLA."</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_RED_SC_LINK."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_TITULEK."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_DATUM_VYDANI."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_AUTOR."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_AKCE."</b></td></tr>\n";
  while ($data_cla = phprs_sql_fetch_assoc($dotazcla)):
    if ($data_cla['visible']==0):
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#FFC6C6')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    else:
      echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#CCFFCC')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    endif;
    echo "<td align=\"center\">".$data_cla["link"]."</td>";
    echo "<td align=\"left\" width=\"300\">".$data_cla["titulek"]."</td>";
    echo "<td align=\"center\">".$data_cla["datum"]." / ";
    echo TestAnoNe($data_cla["visible"]);
    echo "</td>";
    echo "<td align=\"center\">";
    if (isset($autori[$data_cla["autor"]])):
      echo $autori[$data_cla["autor"]];
    else:
      echo $data_cla["autor"];
    endif;
    echo "</td>";
    echo "<td align=\"center\">";
    if ($data_cla['visible']==1):
      // clanek nastaven jako vydany
      if ($akt_je_vydavatel==1):
        // opravneni k editaci
        echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$data_cla["idc"]."\">".RS_RED_SC_UPRAVIT."</a>";
      else:
        echo RS_RED_SC_UPRAVIT;
      endif;
    else:
      // clanek ve stadiu tvorby
      echo "<a href=\"".RS_VYKONNYSOUBOR."?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$data_cla["idc"]."\">".RS_RED_SC_UPRAVIT."</a>";
    endif;
    echo " / <a href=\"preview.php?cisloclanku=".$data_cla["link"]."\" target=\"preview\">".RS_RED_SC_PREVIEW."</a></td></tr>\n";
  endwhile;
  echo "</table>\n";
endif;

// seznam clanku omezeny parametrem "visible" - hodnota 0
$dotazcla=phprs_sql_query($dotaz2,$GLOBALS["dbspojeni"]);
$pocetcla=phprs_sql_num_rows($dotazcla);

echo "<p align=\"center\" class=\"txt\"><big><strong>".RS_RED_SC_NADPIS_NEPOVOL_VYDANI."</strong></big></p>\n";
if ($pocetcla==0):
  // CHYBA: Teto podmince neodpovida zadny clanek!
  echo "<p align=\"center\" class=\"txt\">".RS_RED_SC_ZADNY_CLA."</p>\n";
else:
  echo "<table cellspacing=\"0\" cellpadding=\"5\" border=\"1\" align=\"center\" class=\"ramsedy\">\n";
  echo "<tr class=\"txt\" bgcolor=\"#E6E6E6\"><td align=\"center\"><b>".RS_RED_SC_LINK."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_TITULEK."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_DATUM_VYDANI."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_AUTOR."</b></td>";
  echo "<td align=\"center\"><b>".RS_RED_SC_AKCE."</b></td></tr>\n";
  while ($data_cla = phprs_sql_fetch_assoc($dotazcla)):
    echo "<tr class=\"txt\" onmouseover=\"setPointer(this, '#FFC6C6')\" onmouseout=\"setPointer(this, '#FFFFFF')\">";
    echo "<td align=\"center\">".$data_cla["link"]."</td>";
    echo "<td align=\"left\" width=\"300\">".$data_cla["titulek"]."</td>";
    echo "<td align=\"center\">".$data_cla["datum"]." / ";
    echo TestAnoNe($data_cla["visible"]);
    echo "</td>";
    echo "<td align=\"center\">";
    if (isset($autori[$data_cla["autor"]])):
      echo $autori[$data_cla["autor"]];
    else:
      echo $data_cla["autor"];
    endif;
    echo "</td>";
    echo "<td align=\"center\"><a href=\"".RS_VYKONNYSOUBOR."?akce=ArticleEdit&amp;modul=clanky&amp;pridc=".$data_cla["idc"]."\">".RS_RED_SC_UPRAVIT."</a> / ";
    echo "<a href=\"preview.php?cisloclanku=".$data_cla["link"]."\" target=\"preview\">".RS_RED_SC_PREVIEW."</a></td></tr>\n";
  endwhile;
  echo "</table>\n";
endif;

echo "<br>\n";
}
?>
