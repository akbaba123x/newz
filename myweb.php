<?php

######################################################################
# phpRS MyWeb 1.5.8
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: *

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

include_once("specfce.php");
include_once("trwebstat.php"); // funkci statistika navstevnosti webu lze vypnout "zakomentovanim" tohoto radku
include_once("trmyreader.php");
include_once("sl.php");
include_once("trlayout.php");
include_once($adrlayoutu);
?>
