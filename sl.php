<?php

#####################################################################
# phpRS Slovnikovy manager - version 1.0.6
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

if (isset($GLOBALS["prmyctenar"])&&$GLOBALS["prmyctenar"]->ctenarstav==1):
  if ($GLOBALS["prmyctenar"]->Ukaz("jazyk")!=''):
    $vybranyjazyk=$GLOBALS["prmyctenar"]->Ukaz("jazyk");
  else:
    $vybranyjazyk='cz';
  endif;
else:
  $vybranyjazyk='cz';
endif;

$jazykadresa='lang/sl_'.$vybranyjazyk.'.php';
include($jazykadresa); // vlozeni jakykoveho slovniku
?>
