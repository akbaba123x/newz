<?php

######################################################################
# phpRS Version 2.8.3
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// phpRS verze - textovy popis
$phprsversion='phpRS 2.8.3';
// phpRS verze - pevny identifikator verze
$phprsversion_kod='283';

// funkce, ktera vraci veskere informace o verzi
function Showphprsversion()
{
	return '
		<div align="center">
			phpRS verze číslo: 2.8.3<br>
			Datum vydání: 15.05.2019<br>
			phpRS community<br>
			php verze: '.phpversion().'
		</div>
	';
}

?>