<?php
######################################################################
# phpRS Plug-in modul: Nej články v1.2.1
######################################################################

// Copyright (c) 2001-2007 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// prehled nejctenejsich clanku
function NejClanky()
{
// ---[ definice zakladnich parametru ]-----------------------
$mnozstviclanku=6; // pocet zobrazenych hodnot (clanku)
$pocetmesicu=36;    // tato promenna omezuje vyhledavaci proces pouze na clanky stare maximalne zadany pocet mesicu
// -----------------------------------------------------------

// sestaveni casoveho omezeni
$dnesnidatum=date("Y-m-d H:i:s");
list($dnrok,$dnmes,$dnden)=explode("-",date("Y-m-d"));
$omezujicidatum=date("Y-m-d",mktime(0,0,0,$dnmes-$pocetmesicu,$dnden,$dnrok));

// test na aktivnost ctenarskeho leveloveho subsystemu
$hlidatlevel=NactiConfigProm('hlidat_level',0);
if ($hlidatlevel==1):
  $dotaz_where=" and l.hodnota<='".$GLOBALS["prmyctenar"]->UkazLevel()."'";
else:
  $dotaz_where="";
endif;

// vypis X nejctenejsich clanku
$dotaz="select c.link,c.titulek,date_format(c.datum,'%d. %m. %Y') as vyslden,c.visit from ".$GLOBALS["rspredpona"]."clanky as c,  ".$GLOBALS["rspredpona"]."levely as l ";
$dotaz.="where c.visible=1 and c.visit>0 and c.datum>='".$omezujicidatum."' and c.datum<='".$dnesnidatum."' and c.level_clanku=l.idl".$dotaz_where." order by c.visit desc limit 0,".$mnozstviclanku;

$dotazclanky=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
if ($dotazclanky===false):
  $retezec="<p align=\"center\" class=\"z\">Chybí zdrojova databaze!</p>\n";
  $pocetclanky=0;
else:
  $pocetclanky=phprs_sql_num_rows($dotazclanky);
endif;

// overeni pritomnosti clanku
if ($pocetclanky==0):
  $retezec="<p align=\"center\" class=\"z\">Neexistuji vhodna data!</p>\n";
else:
  // vypis clanku
  $retezec='';
  for ($pom=0;$pom<$pocetclanky;$pom++):
    $pole_data=phprs_sql_fetch_assoc($dotazclanky);
    $retezec.="<div class=\"z\"><a href=\"view.php?cisloclanku=".$pole_data["link"]."\">".$pole_data["titulek"]."</a><br />\n";
    $retezec.="(".$pole_data["vyslden"].", ".$pole_data["visit"]."x)</div>\n";
  endfor;
endif;

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
endswitch;
}
?>