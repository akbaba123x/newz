<?php

######################################################################
# phpRS Admin System Command 1.0.3
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/

switch($GLOBALS['akce']):
  case "Logout": $Uzivatel->Odhlaseni();
       echo '
            <p align="center" class="txt">
                '.RS_SYS_ROZ_LOGOUT.'
                <br>
                <br>
                <a href="'.RS_VYKONNYSOUBOR.'">'.RS_SYS_ROZ_LOGIN.'</a>
                <br>
                <br>
                <a href="'.$GLOBALS['baseadr'].'">'.$GLOBALS['baseadr'].'</a>
            </p>
        ';
       break;
endswitch;

?>