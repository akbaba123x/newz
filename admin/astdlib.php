<?php

######################################################################
# phpRS Admin Standard library 1.0.11
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- function --
  KorekceHTML($text = '')
  KorekceNadpisu($str = '')
  JeToMSIE()
  OverDatum($str)
  MyDnesniDatum()
  MyDatetimeToDate($mysql_datum)
  MyDateToDate($datum = "")
  MyDateTimeToDateTime($udaj = "")
  Me($velikost = 0,$sirkaintervalu = 1)
  GenerujSeznam($pocatecnihodnota = 0)
  GenerujSeznamSCestou($pocatecnihodnota = 0)
  TestNaAdresu($mail = "")
  TestNaNic($str = "")
  TestAnoNe($vstup)
  OptAutori($hledam = "")

  -- class --
  SezAutori()
*/

// ====================== FUNCTION

function KorekceHTML($text = '')
{
// tento radek umoznuje spravne zobrazit v editacnim poli vsechny zvlastni znaky zapsane jako &X;
//return str_replace('&','&amp;',$text);
return htmlspecialchars($text, ENT_QUOTES);
}

function KorekceNadpisu($str = '')
{
// tento radek nahrazuje uvozovky za - &quot;
//return str_replace('"','&quot;',$str);
return htmlspecialchars(strip_tags($str), ENT_QUOTES);
}

function JeToMSIE()
{
// test na typ prohlizece
$teststr="test".$_ENV["HTTP_USER_AGENT"];
return strpos($teststr,"MSIE"); // 1 = je to MSIE, 0 = neni to MSIE
}

function OverDatum($str)
{
// overeni platnosti data; vstup MySQL format
list($datum,$hodiny)=explode(" ",trim($str)); // dekompilace datumu
list($rok,$mes,$den)=explode("-",$datum);
list($hod,$min,$sek)=explode(":",$hodiny);
// sestaveni overeneho datumu
return date("Y-m-d H:i:s",mktime($hod,$min,$sek,$mes,$den,$rok));
}

function MyDnesniDatum()
{
// generuji dnesni datum i s casem v MySQL formatu
return date("Y-m-d H:i:s");
}

function MyDatetimeToDate($mysql_datum)
{
// preved MySQL datetime typ do formy bezneho teckou oddeleneho datumu
$rozlozenedatum=explode(" ",trim($mysql_datum)); // [0] - datum, [1] - cas
$vysledek=explode("-",$rozlozenedatum[0]);
return $vysledek[2].".".$vysledek[1].".".$vysledek[0]; // dd.mm.rrrr
}

function MyDateToDate($datum = "")
{
$rozloz=explode("-",$datum);
return $rozloz[2].".".$rozloz[1].".".$rozloz[0]; // vysledny format DD.MM.RRRR
}

function MyDateTimeToDateTime($udaj = "")
{
list($datum,$cas)=explode(" ",$udaj);
$rozloz=explode("-",$datum);
return $rozloz[2].".".$rozloz[1].".".$rozloz[0]." ".$cas; // vysledny format DD.MM.RRRR HH:MM:SS
}

function Me($velikost = 0,$sirkaintervalu = 1)
{
// generator pevne mezery
$vysledek='';
if ($velikost>0&&$sirkaintervalu>0):
  $mezera=str_repeat("&nbsp;",$sirkaintervalu);
  $vysledek=str_repeat($mezera,$velikost);
endif;
return $vysledek;
}

function GenerujSeznam($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik
$dotazsez=phprs_sql_query("select idt,nazev,id_predka,hodnost from ".$GLOBALS["rspredpona"]."topic order by level,hodnost desc,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=phprs_sql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++):
  $pole_data=phprs_sql_fetch_assoc($dotazsez);
  // pole informaci
  $vstdata[$pom][0]=$pole_data["idt"];       // id
  $vstdata[$pom][1]=$pole_data["nazev"];     // nazev polozky
  $vstdata[$pom][2]=$pole_data["id_predka"]; // id rodice
  $vstdata[$pom][3]=0;                       // prepinace pouzito pole
  $vstdata[$pom][4]=$pole_data["hodnost"];   // priorita
endfor;

if ($pocetsez>0): $trideni=1; else: $trideni=0; endif;

$polehist[0]=$pocatecnihodnota; // historie prohledavani
$polex=0; // poloha v poly historie prohledavani

$vysledekcislo=0; // akt. volna posledni pozice ve vysledkovem poli

while ($trideni==1):
  $nasel=0; // 0 = prvek nenalezen, 1 = prvek nalezen

  for ($pom=0;$pom<$pocetsez;$pom++):
    if ($vstdata[$pom][3]==0): // kdyz nebylo akt. radek jeste pouzit
      if ($vstdata[$pom][2]==$polehist[$polex]): // kdyz nalezi hledanemu predku
            // ulozeni vysledku
            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0]; // id prvku
            $vysledek[$vysledekcislo][1]=$vstdata[$pom][1]; // nazev prvku
            $vysledek[$vysledekcislo][2]=$polex; // uroven vnoreni prvku
            $vysledek[$vysledekcislo][4]=$vstdata[$pom][4]; // uroven vnoreni prvku
            // nastaveni dalsich promennych
            $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
            $vstdata[$pom][3]=1; // nastaveni prepinace na pouzito
            $polex++; // prechod na vyssi uroven v historii
            $polehist[$polex]=$vstdata[$pom][0];
            $nasel=1;
            break;
      endif;
    endif;
  endfor;

  if ($nasel==0): // kdyz nebyl v celem poli nalezen zadny odpovidajici prvek
    if ($polehist[$polex]==$pocatecnihodnota):
      // vysledek hledani na zakladni urovni, ktera byla stanovena na zacatku, je prazdny -> neexistuje zadna dalsi vetev
      $trideni=0;
    else:
      $polex--; // prechod na nizsi uroven v historii
    endif;
  endif;
endwhile;

/*
   $vysledek[X][0] - id prkvu
               [1] - nazev prvku
               [2] - cislo urovne
*/
if ($pocetsez>0):
  return $vysledek;
else:
  return 0;
endif;
}

function GenerujSeznamSCestou($pocatecnihodnota = 0)
{
// generuje a tridi pole hierarchicky na sobe zavislych rubrik; vystup obsahuje uplnou cestu k jednotlivym rubrikam
$dotazsez=phprs_sql_query("select idt,nazev,id_predka from ".$GLOBALS["rspredpona"]."topic order by level,hodnost desc,nazev",$GLOBALS["dbspojeni"]);
$pocetsez=phprs_sql_num_rows($dotazsez);

for ($pom=0;$pom<$pocetsez;$pom++):
  $pole_data=phprs_sql_fetch_assoc($dotazsez);
  // pole informaci
  $vstdata[$pom][0]=$pole_data["idt"];       // id
  $vstdata[$pom][1]=$pole_data["nazev"];     // nazev polozky
  $vstdata[$pom][2]=$pole_data["id_predka"]; // id rodice
  $vstdata[$pom][3]=0;                       // prepinace pouzito pole
endfor;

if ($pocetsez>0): $trideni=1; else: $trideni=0; endif;

$polehist[0]=$pocatecnihodnota; // historie prohledavani
$polecesta[0]="";
$polex=0; // poloha v poly historie prohledavani

$vysledekcislo=0; // akt. volna posledni pozice ve vysledkovem poli

while ($trideni==1):
  $nasel=0; // 0 = prvek nenalezen, 1 = prvek nalezen

  for ($pom=0;$pom<$pocetsez;$pom++):
    if ($vstdata[$pom][3]==0): // kdyz nebylo akt. radek jeste pouzit
      if ($vstdata[$pom][2]==$polehist[$polex]): // kdyz nalezi hledanemu predku
            // ulozeni vysledku
            $vysledek[$vysledekcislo][0]=$vstdata[$pom][0]; // id prvku
            $vysledek[$vysledekcislo][1]=$polecesta[$polex].$vstdata[$pom][1]; // nazev prvku
            $vysledek[$vysledekcislo][2]=$polex; // uroven vnoreni prvku
            // nastaveni dalsich promennych
            $vysledekcislo++; // prechod na dalsi radek ve vysledkovem poli
            $vstdata[$pom][3]=1; // nastaveni prepinace na pouzito
            $polex++; // prechod na vyssi uroven v historii
            $polehist[$polex]=$vstdata[$pom][0];
            $polecesta[$polex]=$polecesta[$polex-1].$vstdata[$pom][1]." - ";
            $nasel=1;
            break;
      endif;
    endif;
  endfor;

  if ($nasel==0): // kdyz nebyl v celem poli nalezen zadny odpovidajici prvek
    if ($polehist[$polex]==$pocatecnihodnota):
      // vysledek hledani na zakladni urovni, ktera byla stanovena na zacatku, je prazdny -> neexistuje zadna dalsi vetev
      $trideni=0;
    else:
      $polex--; // prechod na nizsi uroven v historii
    endif;
  endif;
endwhile;

/*
   $vysledek[X][0] - id prkvu
               [1] - nazev prvku
               [2] - cislo urovne
*/
if ($pocetsez>0):
  return $vysledek;
else:
  return 0;
endif;
}

function TestNaAdresu($mail = "")
{
// tato funkce testuje platnost zadaneho e-mailu
if (preg_match('|^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$|',$mail)):
  return 1; // spravna struktura
else:
  return 0; // chybna struktura
endif;
}

function TestNaNic($str = "")
{
// v pripade, ze je vstupem prazdna promenna, tak je na vystupu vracena tvrda mezera
if ($str==""):
  return "&nbsp;";
else:
  return $str;
endif;
}

function TestAnoNe($vstup)
{
// tato fce prevadi logicky stav vstupni promenne na retezcove vyjadreni
switch ($vstup):
  case "0": return RS_TL_NE;  // "Ne";
  case "1": return RS_TL_ANO; //"Ano";
  default: return "chyba / error";
endswitch;
}

function OptAutori($hledam = '', $omezujici_list = '')
{
$str='';
$nalezl=0;

if (empty($omezujici_list)):
  // vsichni uziatele
  $dotaz="select idu,user from ".$GLOBALS["rspredpona"]."user order by user";
else:
  // uzivatele omezeni seznamem id
  $dotaz="select idu,user from ".$GLOBALS["rspredpona"]."user where idu in (".$omezujici_list.") order by user";
endif;

$dotazusr=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
$pocetusr=phprs_sql_num_rows($dotazusr);

if ($pocetusr==0):
  // nebyl definovan zadny autor
  $str.="<option value=\"0\">".RS_ADM_ASL_ERR_DEF_AUTOR."</option>";
else:
  while ($pole_data = phprs_sql_fetch_assoc($dotazusr)):
    $str.="<option value=\"".$pole_data["idu"]."\"";
    if ($hledam==$pole_data["idu"]): $str.=" selected"; $nalezl=1; endif;
    $str.=">".$pole_data["user"]."</option>\n";;
  endwhile;
  // test na vysledek
  if ($nalezl==0&&$hledam!=""):
    $str.="<option value=\"".$hledam."\" selected>".RS_ADM_ASL_ERR_IDENT_AUTOR."</option>"; // chyba: nelze idetifikovat autora
  endif;
endif;

return $str;
}

// ====================== CLASS

class SezAutori
{
var $pole_autori;

 public function __construct()
 {
 $this->NactiAut();
 }

 function NactiAut()
 {
 $dotazusr=phprs_sql_query("select idu,user,jmeno from ".$GLOBALS["rspredpona"]."user order by user",$GLOBALS["dbspojeni"]);
 $pocetusr=phprs_sql_num_rows($dotazusr);

 while ($pole_data = phprs_sql_fetch_assoc($dotazusr)):
   $this->pole_autori[$pole_data["idu"]][0]=$pole_data["user"];
   $this->pole_autori[$pole_data["idu"]][1]=$pole_data["jmeno"];
 endwhile;
 }

 function UkazUser($id = 0)
 {
 if (isset($this->pole_autori[$id])):
   return $this->pole_autori[$id][0];
 else:
   return RS_ADM_ASL_ERR_IDENT_AUTOR; // chyba: nelze idetifikovat autora
 endif;
 }

 function UkazJmeno($id = 0)
 {
 if (isset($this->pole_autori[$id])):
   return $this->pole_autori[$id][1];
 else:
   return RS_ADM_ASL_ERR_IDENT_AUTOR; // chyba: nelze idetifikovat autora
 endif;
 }
}


function VratSEOLink($retezec = '')
{
// nejprve prelozit znaky, ktere iconv//TRANSLIT divne preklada
$preklad['co'][]='€'; $preklad['kam'][]='-euro-';
$preklad['co'][]='&'; $preklad['kam'][]='-and-';
$preklad['co'][]='™'; $preklad['kam'][]='-tm-';
$preklad['co'][]='§'; $preklad['kam'][]='';
$preklad['co'][]='©'; $preklad['kam'][]='';
$preklad['co'][]='®'; $preklad['kam'][]='';
$preklad['co'][]='£'; $preklad['kam'][]='';
$preklad['co'][]='²'; $preklad['kam'][]='';
$preklad['co'][]='³'; $preklad['kam'][]='';
$preklad['co'][]='¢'; $preklad['kam'][]='';
$preklad['co'][]='°'; $preklad['kam'][]='';
$preklad['co'][]='‰'; $preklad['kam'][]='';
$preklad['co'][]='Ş'; $preklad['kam'][]='';
$preklad['co'][]='¬'; $preklad['kam'][]='';
$preklad['co'][]='±'; $preklad['kam'][]='';
$preklad['co'][]='ß'; $preklad['kam'][]='';
$preklad['co'][]='•'; $preklad['kam'][]='';
$preklad['co'][]='µ'; $preklad['kam'][]='';
$retezec=str_replace($preklad['co'], $preklad['kam'], $retezec);

// prekodovat pomoci iconv jen na zakladni ASCII a prevest na mala pismenka
iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('internal_encoding', 'UTF-8');
setlocale(LC_ALL, 'cs_CZ.utf8');
$retezec=strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $retezec));

// v poli array() jsou znaky, ktere budou fungovat jako oddelovace a jsou nahrazeny pomlckou
$retezec=str_replace(array(' ', '-', '/', ':', '=', '\\', '_', '|', '~', '–', '—', ' ', '¦', '­', '¯', '‒', '―'), '-', $retezec);
$retezec=preg_replace('/[^a-z0-9\-]/', '', $retezec); // vymazat vsechny znaky krome malych pismen, cislic a pomlcky
$retezec=preg_replace('/-+/', '-', $retezec); // vic pomlcek za sebou nahradit jen jednou
return trim($retezec, '-'); // odstranit pomlcky na zacatku a konci retezce
}
?>