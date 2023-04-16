<?php
######################################################################
# phpRS Plug-in modul: Captcha kontrolni otazky v1.0.0 - komercni plugin
######################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// jmeno plug-inu
$plugin_nazev="Captcha kontrolní otázky";
// pristupova prava: 1 = dle nastaveni v administraci; 2 = uplne vsichni; 3 = pouze admin
$pi_pristup="1";
// pridat polozku do hlavniho administracniho menu; ano = 1, ne = 0
$pi_menu="1";
// nazev noveho tlacitka v admin. menu
$pi_nazev_menu="Captcha otázky";
// identifikacni retezec modulu (max. 15 znaku)
$pi_indent_modulu="captchaotazky";
// relativni cesta k souboru s "rozcestnikem" k admin. sekci
$pi_inclakce_menu="plugin/captcha_otazky/acaptcha_otazky.php";
// volaci link zakladni funce
$pi_link_menu="akce=ShowCaptchaOt";
// pridat aktivacni polozku do seznamu systemovych bloku; ano = 1, ne = 0
$pi_sys_blok="0";
// nazev systemoveho bloku
$pi_nazev_blok="";
// identifikacni zkratka systemoveho bloku (3 znaky)
$pi_zkratka_blok="";
// relativni cesta k vykonnemu soubour
$pi_inclsb_blok="";
// nazev vyvolane systemove funkce - nutno zapisovat bez prazdnych kulatych zavorek na konci
$pi_funkce_blok="";
?>
