<?php
######################################################################
# phpRS Plug-in modul: Banner - 1. pozice v1.0.1
######################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vlozeni banneru definovaneho na 1. pozici
function PlugBanner1()
{
// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),Banners(1)); break;
endswitch;
}
?>