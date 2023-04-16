<?php

######################################################################
# phpRS Extrakce GET a POST promennych a jejich zpracovani 1.1.0
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.phprs.net/
// This program is free software. - Toto je bezplatny a svobodny software.

// funkce pro vytvoreni reference mezi dvema poli
function Extract_prom($pole, &$target) {
	if (!is_array($pole)) {
		return false;
	} else {
		reset($pole);

		foreach ($pole as $klic => $hodnota) {
			if (is_array($hodnota)) {
				Extract_prom($hodnota,$target[$klic]);
			} else {
				$target[$klic]=$hodnota;
			}
		}

		reset($pole);
		return true;
	}
}

// je GET prazdne
if (!empty($_GET)) {
	Extract_prom($_GET,$GLOBALS);
}

// je POST prazdne
if (!empty($_POST)) {
	Extract_prom($_POST,$GLOBALS);
}

?>