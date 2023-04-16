<?php

######################################################################
# phpRS ClassWebStat 1.5.7
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_stat_session, rs_stat_ip, rs_stat_data

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

/*
  Trida CWebStat slouzi ciste ke statistickym ucely a v ramci sve cinnosti zaznamenava do databaze pristupy lidi/pocitacu na web.
*/

class CWebStat
{
var $ctenartyp; // 1 = visit, 2 = pages
var $ctenaros; // 1 = win, 2 = linux, 3 = unix, 4 = mac, 5 = jiny
var $plses; // doba platnosti session, default = 1800 s = 0.5 h
var $aktcas,$aktdat,$akthod,$ctenarip,$ctenarostxt,$ctenarses;

 /*
   CWebStat($delka = 1800) . konstruktor
   CtenarIP() .............. vraci IP adresu ctenare
   ZjistiOS() .............. fce na zjisteni OS ctenare
   CtenarOS() .............. vraci cislo OS ctenare
   CtenarOST() ............. vraci nazev OS ctenare
   GenSes() ................ generator session
   GenNovy() ............... generator stat. SQL prikazu
   GenStary() .............. generator stat. SQL prikazu
   ZjistiPath() ............ fce na dekompilaci URL
   SendCook() .............. fce na odeslani cookies
   GenIdent() .............. pomocna identifikacni fce
   UlozStat() .............. fce pro ukladani stat.
 */

 // konstruktor
 public function __construct($delka = 1800)
 {
 $this->aktcas=Date("Y-m-d H:i:s"); // aktualni cas
 $this->aktdat=Date("Y-m-d");
 $this->akthod=Date("H");
 $this->ctenarip=$_SERVER["REMOTE_ADDR"]; // ip ctenare
 $this->ZjistiOS(); // inic. OS
 $this->plses=$delka;

 // zakladni rozpoznavaci test
 if (isset($_COOKIE['phprswebstat'])):
   // vymazani vsech neaktualnich session
   @phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."stat_session where platnost_do<'".$this->aktcas."'",$GLOBALS["dbspojeni"]);
   // kdyz existuje session -> cookeis se session retezcem
   $ziskanases=base64_decode($_COOKIE['phprswebstat']);
   $dotazses=phprs_sql_query("select count(ids) as pocet from ".$GLOBALS["rspredpona"]."stat_session where session='".$ziskanases."' and platnost_do>'".$this->aktcas."'",$GLOBALS["dbspojeni"]);
   list($akt_pocet_ses)=phprs_sql_fetch_row($dotazses);
   if ($akt_pocet_ses>0):
     $this->ctenartyp=2; // existuje zaznam o testovanem ctenari
   else:
     $this->ctenartyp=1; // novy ctenar
     $this->GenSes();
   endif;
 else:
   // vymazani vsech neaktualnich IP
   @phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."stat_ip where platnost_do<'".$this->aktcas."'",$GLOBALS["dbspojeni"]);
   // neexistuje session; testuje se IP adresa
   $dotazip=phprs_sql_query("select count(idi) as pocet from ".$GLOBALS["rspredpona"]."stat_ip where ip='".$this->ctenarip."' and platnost_do>'".$this->aktcas."'",$GLOBALS["dbspojeni"]);
   list($akt_pocet_ip)=phprs_sql_fetch_row($dotazip);
   if ($akt_pocet_ip>0):
     $this->ctenartyp=2; // existuje zaznam o testovanem ctenari
   else:
     $this->ctenartyp=1; // novy ctenar
     $this->GenSes();
   endif;
 endif;
 }

 function CtenarIP()
 {
 return $this->ctenarip; // IP ctenare
 }

 function ZjistiOS()
 {
 $ctenar=$_SERVER['HTTP_USER_AGENT'];

  // 1 = win, 2 = linux, 3 = unix, 4 = mac, 5 = jiny
  if (strstr($ctenar,"Win")) {
    $this->ctenaros=1; $this->ctenarostxt="Windows";
  } else if (strstr($ctenar,"Linux")) {
    $this->ctenaros=2; $this->ctenarostxt="Linux";
  } else if (strstr($ctenar,"Unix")) {
    $this->ctenaros=3; $this->ctenarostxt="Unix";
  } else if (strstr($ctenar,"Mac")) {
    $this->ctenaros=4; $this->ctenarostxt="Mac";
  } else {
    $this->ctenaros=5; $this->ctenarostxt="Jiny OS";
  }
 }

 function CtenarOS()
 {
 return $this->ctenaros; // OS ctenare - cislo
 }

 function CtenarOST()
 {
 return $this->ctenarostxt; // OS ctenare - text
 }

 function GenSes()
 {
 $this->ctenarses=md5($this->aktcas.$this->ctenarip.$this->ctenarostxt.rand(10,60));
 }

 function GenNovy()
 {
 // sestaveni SQL prikazu
 $sqlprikaz="insert into ".$GLOBALS["rspredpona"]."stat_data values (null,'".$this->aktdat."','".$this->akthod."',"; // 1. cast
 switch ($this->ctenartyp): // 2. cast
   case 1: $sqlprikaz .="'1','0',"; break;
   case 2: $sqlprikaz .="'0','1',"; break;
   default: $sqlprikaz .="'1','0',"; break;
 endswitch;
 if ($this->ctenartyp==1): // OS se pocita pouze u visit
  switch ($this->ctenaros): // 3. cast
    case 1: $sqlprikaz .="'1','0','0','0','0')"; break;
    case 2: $sqlprikaz .="'0','1','0','0','0')"; break;
    case 3: $sqlprikaz .="'0','0','1','0','0')"; break;
    case 4: $sqlprikaz .="'0','0','0','1','0')"; break;
    case 5: $sqlprikaz .="'0','0','0','0','1')"; break;
    default: $sqlprikaz .="'1','0','0','0','0')"; break;
  endswitch;
 else:
  $sqlprikaz .="'0','0','0','0','0')";
 endif;
 return $sqlprikaz; // vysledek
 }

 function GenStary()
 {
 // sestaveni SQL prikazu
 $sqlprikaz="update ".$GLOBALS["rspredpona"]."stat_data set "; // 1. cast
 switch ($this->ctenartyp): // 2. cast
   case 1: $sqlprikaz .="visit=(visit+1)"; break;
   case 2: $sqlprikaz .="pages=(pages+1)"; break;
   default: $sqlprikaz .="visit=(visit+1)"; break;
 endswitch;
 if ($this->ctenartyp==1): // OS se pocita pouze u visit
  switch ($this->ctenaros): // 3. cast
    case 1: $sqlprikaz .=", os_win=(os_win+1)"; break;
    case 2: $sqlprikaz .=", os_linux=(os_linux+1)"; break;
    case 3: $sqlprikaz .=", os_unix=(os_unix+1)"; break;
    case 4: $sqlprikaz .=", os_mac=(os_mac+1)"; break;
    case 5: $sqlprikaz .=", os_dalsi=(os_dalsi+1)"; break;
    default: $sqlprikaz .=", os_dalsi=(os_dalsi+1)"; break;
  endswitch;
 endif;
 $sqlprikaz .=" where datum='".$this->aktdat."' and hodina='".$this->akthod."'"; // 4. cast
 return $sqlprikaz; // vysledek
 }

 function ZjistiPath()
 {
 $skript=$_SERVER["PHP_SELF"];
 $casti=explode("/",$skript); // vzdy min. rozlozen na dve casti
 $pocetcasti=count($casti);

 if ($pocetcasti>2):
   $pocetcasti--; // musime odecist posledni, ktera obsahuje samotny skript
   $cesta="/";
   for ($pom=1;$pom<$pocetcasti;$pom++): // zaciname od 1, protoze 0 cast obsahuje prazdne pole, ktere vznikne separaci koren. adr.
     $cesta.=$casti[$pom]."/";
   endfor;
 else:
   // skript je v korenovem adresari nebo ma prazdnou cestu
   $cesta="/";
 endif;

 return $cesta;
 }

 function SendCook()
 {
 // test na nastaveni citlivosti cookies
 if ($GLOBALS['rsconfig']['cookies_s_domenou']==1):
   // cookies - jmeno_cookies , obsah , platnost , path , domena
   setcookie("phprswebstat",base64_encode($this->ctenarses),time()+$this->plses,$this->ZjistiPath(),$_SERVER["HTTP_HOST"]);
 else:
   // cookies - jmeno_cookies , obsah , platnost
   setcookie("phprswebstat",base64_encode($this->ctenarses),time()+$this->plses);
 endif;
 }

 function GenIdent()
 {
 $platnost=Date("Y-m-d H:i:s",time()+$this->plses); // vypocitani omezeni platnosti session
 @phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."stat_session values (null,'".$this->ctenarses."','".$platnost."')",$GLOBALS["dbspojeni"]);
 $this->SendCook();
 @phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."stat_ip values (null,'".$this->ctenarip."','".$platnost."')",$GLOBALS["dbspojeni"]);
 }

 function UlozStat()
 {
 // vyhodnoceni statistiky
 $dotazexist=phprs_sql_query("select count(idd) as pocet from ".$GLOBALS["rspredpona"]."stat_data where datum='".$this->aktdat."' and hodina='".$this->akthod."'",$GLOBALS["dbspojeni"]);
 list($akt_pocet_dat)=phprs_sql_fetch_row($dotazexist);
 if ($akt_pocet_dat==0):
   $statprikaz=$this->GenNovy(); // zaznam jeste neexistuje
 else:
   $statprikaz=$this->GenStary(); // zaznam existuje
 endif;
 @phprs_sql_query($statprikaz,$GLOBALS["dbspojeni"]); // vykonani SQL prikazu

 // priprava budouci statistiky; kdyz byl identifikovan novy ctenar, tak se ulozi jeho identita do docasne tabulky
 if ($this->ctenartyp==1): $this->GenIdent(); endif;
 }
}

// aktivace statistiky
$prwebovast = new CWebStat();
$prwebovast->UlozStat();

?>
