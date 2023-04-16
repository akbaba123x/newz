<?php

######################################################################
# phpRS Layout Engine 2.7.1 - verze: "freestyle2006"
######################################################################

// Copyright (c) 2002-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// nazev a verze phpRS layoutu
$layoutversion='Layout Engine: freestyle2006 verze 2.7.1';
// HTML META tag LINK pro pripojeni zakladniho CSS souboru
$layoutcss='<link rel="stylesheet" href="image/freestyle2006/freestyle2006.css" type="text/css">';
// kodovani phpRS layoutu
$layoutkodovani=$GLOBALS['rsconfig']['kodovani'];

// ----------- [priprava na generovani stranky] -----------

if (!isset($rs_main_sablona)): $rs_main_sablona=""; endif;

$vzhledwebu = new CLayout(); // inic. vzhledove tridy

switch ($rs_main_sablona):
  case 'base': // zakladni sablona
    $vzhledwebu->NactiFileSablonu('image/freestyle2006/fs_base.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    $vzhledwebu->UlozPro('banner1',Banners_str(1));
    $vzhledwebu->UlozPro('banner2',Banners_str(2));
    break;
  case 'download': // download sablona
    $vzhledwebu->NactiFileSablonu('image/freestyle2006/fs_download.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    $vzhledwebu->UlozPro('banner1',Banners_str(1));
    $vzhledwebu->UlozPro('banner2',Banners_str(2));
    break;
  default: // defaultni sablona - je shodna s jednou z vyse uvedenych sablon
    $vzhledwebu->NactiFileSablonu('image/freestyle2006/fs_base.sab');
    $vzhledwebu->UlozPro('title',$wwwname);
    $vzhledwebu->UlozPro('datum',Date("d. m. Y"));
    break;
endswitch;

$vzhledwebu->Inic();

// ------- [konec - priprava na generovani stranky] -------

function Blok1($bnadpis = '',$bdata = '')
{
echo "\n<!-- Blok 1-->\n\t\t<div class=\"blok1\">".$bnadpis."\n\t\t</div><div class=\"blok1obs\">".$bdata."</div>\n";
}

function Blok2($bnadpis = '',$bdata = '')
{
echo "\n<!-- Blok 2-->\n\t\t<div class=\"blok2\">".$bnadpis."\n\t\t</div><div class=\"blok2obs\">".$bdata."</div>\n";
}

function Blok3($bnadpis = '',$bdata = '')
{
echo "\n<!-- Blok 3-->\n\t\t<div class=\"blok3obs\">".$bdata."</div>\n";
}

function Blok4($bnadpis = '',$bdata = '')
{
echo "\n<!-- Blok 4-->\n\t\t<div class=\"blok4\">".$bnadpis."\n\t\t</div><div class=\"blok4obs\">".$bdata."</div>\n";
}

function Blok5($bnadpis = '',$bdata = '')
{
echo "\n<!-- Blok 5-->\n\t\t<div class=\"blok5\">".$bnadpis."\n\t\t</div><div class=\"blok5obs\">".$bdata."</div>\n";
}

function ObrTabulka()
{
echo "\n<div class=\"ram z\">\n";
}

function KonecObrTabulka()
{
echo "\n</div><p></p>\n";
}
?>