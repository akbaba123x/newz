<?php
######################################################################
# phpRS Plug-in modul: LogMenu v1.0.5
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// logovaci dialog
function LoginMenu()
{
$retezec="<form action=\"readers.php\" method=\"post\" style=\"margin:0px;\">
<div align=\"center\"><div class=\"z\">
<br />
".RS_PLUG_LOGIN_USERNAME."<br /><input type=\"text\" size=\"15\" name=\"rjmeno\" class=\"textpole\" /><br />
".RS_PLUG_LOGIN_PASSWORD."<br /><input type=\"password\" size=\"15\" name=\"rheslo\" class=\"textpole\" /><br /><br />
<input type=\"submit\" value=\"  ".RS_PLUG_LOGIN_ODESLAT."  \" class=\"tl\" /><br />
<br />
<a href=\"readers.php?akce=new\">".RS_PLUG_LOGIN_REGISTRACE."</a><br />
<br />
</div></div>
<input type=\"hidden\" name=\"akce\" value=\"quicklog\" />
</form>\n";

// zobrazeni menu
switch ($GLOBALS["vzhledwebu"]->AktBlokTyp()):
  case 1: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 2: Blok2($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 3: Blok3($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 4: Blok4($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  case 5: Blok5($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
  default: Blok1($GLOBALS["vzhledwebu"]->AktBlokNazev(),$retezec); break;
endswitch;
}
?>