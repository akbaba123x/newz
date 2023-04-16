<?php
######################################################################
# phpRS Plug-in modul: Nej články v1.2.0
######################################################################

// Copyright (c) 2001-2007 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// jmeno plug-inu
$plugin_nazev="Nej články";
// pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
$pi_pristup="1";
// pridat polozku do hlavniho administracniho menu; ano = 1, ne = 0
$pi_menu="0";
// nazev noveho tlacitka v admin. menu
$pi_nazev_menu="";
// identifikacni retezec modulu (max. 15 znaku)
$pi_indent_modulu="";
// relativni cesta k souboru s "rozcestnikem" k admin. sekci
$pi_inclakce_menu="";
// volaci link zakladni funce
$pi_link_menu="";
// pridat aktivacni polozku do seznamu systemovych bloku; ano = 1, ne = 0
$pi_sys_blok="1";
// nazev systemoveho bloku
$pi_nazev_blok="Systémový blok: Nejčtenější články";
// identifikacni zkratka systemoveho bloku (3 znaky)
$pi_zkratka_blok="ncl";
// relativni cesta k vykonnemu soubour
$pi_inclsb_blok="plugin/nejclanky/nej_clanky.php";
// nazev vyvolane systemove funkce - nutno zapisovat bez prazdnych kulatych zavorek na konci
$pi_funkce_blok="NejClanky";
?>
