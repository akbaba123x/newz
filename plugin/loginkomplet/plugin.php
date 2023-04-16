<?php
######################################################################
# phpRS Plug-in modul: LoginKomplet v1.1.0
# spojuje v sobe pluginy 'logmenu' a 'showlogin'
######################################################################

// phpRS:
// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// Plug-in:
// Copyright (c) 2003 by Jan Tichavsky (vlakpage@vlakpage.cz) a uprava na v1.0.4 (c)2005-JaV administrator(at)hades.cz
// Copyright (c) 2005 uplny rebuild na v1.1.0 Jiri Lukas (jirilukas@supersvet.cz)

// jmeno plug-inu
$plugin_nazev="Kompletní Login Blok";
// pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
$pi_pristup="1";
// pridat polozku do hlavniho administracniho menu; ano = 1, ne = 0
$pi_menu="0";
// nazev noveho tlacitka v admin. menu
$pi_nazev_menu="";
// relativni cesta k souboru s "rozcestnikem" k admin. sekci
$pi_inclakce_menu="";
// identifikacni retezec modulu (max. 15 znaku)
$pi_indent_modulu = "logkomplet";
// volaci link zakladni funkce
$pi_link_menu="";
// pridat aktivacni polozku do seznamu systemovych bloku; ano = 1, ne = 0
$pi_sys_blok="1";
// nazev systemoveho bloku
$pi_nazev_blok="Systémový blok: Kompletní Login";
// identifikacni zkratka systemoveho bloku (3 znaky)
$pi_zkratka_blok="klb";
// relativni cesta k vykonnemu soubour
$pi_inclsb_blok="plugin/loginkomplet/loginkomplet.php";
// nazev vyvolane systemove funkce - nutno zapisovat bez prazdnych kulatych zavorek na konci
$pi_funkce_blok="LoginKomplet";
?>
