<?php

######################################################################
# phpRS Admin Standard Comment library 1.0.7
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- function --
  VycistiKoment($txt = '')
  PrelozKomZnacky($txt = '')
  KorekceVstupu($txt = '')
  KorekceVelikosti($txt = '')
*/

// ====================== FUNCTION

function VycistiKoment($txt = '')
{
	return strip_tags($txt, '<a><span><b><u><i><pre>');
}

function PrelozKomZnacky($txt = '')
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
        "<a href=\"$1\" rel=\"nofollow\">$1</a>",
        "<a href=\"$1\" rel=\"nofollow\">$1</a>",
        "<a href=\"$1\" rel=\"nofollow\">$3</a>",
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

function KorekceVstupu($txt = '')
{
if ($txt!=''):
  // pole nepripustnych znaku
  $hledam = array ("'&(?!#)'i",
                 "'<'i",
                 "'>'i",
                 "'\"'i");
  // pole alternativ
  $nahrazuji = array ("&amp;",
                 "&lt;",
                 "&gt;",
                 "&quot;");
  $txt=preg_replace($hledam,$nahrazuji,$txt);
endif;

return $txt;
}

function KorekceVelikosti($txt = '')
{
if ($txt!=''):
  // max. delka celeho komentare
  if (empty($GLOBALS['rsconfig']['max_delka_komentare'])): $GLOBALS['rsconfig']['max_delka_komentare']=1000; endif;
  if (mb_strlen($txt)>$GLOBALS['rsconfig']['max_delka_komentare']):
    $txt=mb_substr($txt,0,$GLOBALS['rsconfig']['max_delka_komentare']);
  endif;
  // max. delka slova
  if (empty($GLOBALS['rsconfig']['max_delka_slova'])): $GLOBALS['rsconfig']['max_delka_slova']=50; endif;
  //$txt=wordwrap($txt,$GLOBALS['rsconfig']['max_delka_slova']," ",1); // chybne rozdelovanie URL
  $txt=breakLongWords($txt,$GLOBALS['rsconfig']['max_delka_slova'],"&shy;");
endif;

return $txt;
}

// nahrada za wordwrap, ktory rozsekava dlhe URL, cim znefunkcni odkazy
function breakLongWords($str, $maxLength, $char) {
    $wordEndChars = array(" ", "\n", "\r", "\f", "\v", "\0");
    $count = 0;
    $newStr = "";
    $openTag = false;
    for ($i=0; $i<mb_strlen($str); $i++) {
        $curLtr = mb_substr($str, $i, 1);
        $newStr .= $curLtr;
        if ($curLtr == "<") {
            $openTag = true;
            continue;
        }
        if (($openTag) && ($curLtr == ">")) {
            $openTag = false;
            continue;
        }
        if (!$openTag) {
            if (!in_array($curLtr, $wordEndChars)) {
                $count++;
                if ($count==$maxLength){
                    $newStr .= $char;
                    $count = 0;
                }
            } else {
                $count = 0;
            }
        }
    }//End for
    return $newStr;
}


?>