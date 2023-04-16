<?php
######################################################################
# phpRS Plug-in modul: ShowLogin v1.0.4
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

function PrelozZnacky($txt = '')
{
if ($txt!=''):
  // pole "komentarovych znacek"
  $hledam = array( // pouzitelne prepinace na konci regularniho vyrazu: "i" - neni citlivy na mala/velka pismena; "e" - umoznuje v ramci vysledku spustit PHP kod
        "/\[odkaz\]((http:\/\/|https:\/\/|ftp:\/\/)([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/odkaz\]/is",
        "/\[url\]((http:\/\/|https:\/\/|ftp:\/\/)([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~]*?)\[\/url\]/is",
        "/\[url=((http:\/\/|https:\/\/|ftp:\/\/)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+?)\](.+?)\[\/url\]/is",
        "/\[email\]([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\[\/email\]/is",
        "/\[color=([\#a-z0-9]+?)\](.+?)\[\/color\]/is",
        "/\[b\](.+?)\[\/b\]/is",
        "/\[u\](.+?)\[\/u\]/is",
        "/\[i\](.+?)\[\/i\]/is",
        "/\[code\](.+?)\[\/code\]/is");
  // pole prekladovych HTML alternativ
  $nahrazuji = array(
        "<a href=\"$1\">$1</a>",
        "<a href=\"$1\">$1</a>",
        "<a href=\"$1\">$3</a>",
        "<a href=\"mailto:$1\">$1</a>",
        "<span style=\"color: $1\">$2</span>",
        "<b>$1</b>",
        "<u>$1</u>",
        "<i>$1</i>",
        "<pre>$1</pre>");
  $txt=preg_replace($hledam,$nahrazuji,$txt);
endif;

return $txt;
}

// zobrazeni loginu + vypis osobniho menu
function ShowLogin()
{
if ($GLOBALS["prmyctenar"]->ctenarstav==1):
  // ctenar je nalogovan
  if ($GLOBALS["prmyctenar"]->Ukaz("jmeno")==""): // zjisteni jmena ctenare
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("username");
  else:
    $prjmeno=$GLOBALS["prmyctenar"]->Ukaz("jmeno");
  endif;
  $retezec="<strong>".RS_PLUG_LOGIN_VITEJ." ".$prjmeno."</strong><br />\n";
  $retezec.="(<a href=\"readers.php?akce=logout\">".RS_PLUG_LOGIN_LOGOUT."</a>)\n";
  if ($GLOBALS["prmyctenar"]->Ukaz("zobrazitdata")==1): // test na zobrazeni osobniho menu
    $retezec.="<br /><br />".nl2br(PrelozZnacky(strip_tags($GLOBALS["prmyctenar"]->Ukaz("databox"))))."\n";
  endif;
else:
  // ctenar neni nalogovan
  $retezec=RS_PLUG_LOGIN_NEZNAMY;
endif;

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