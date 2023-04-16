<?php

######################################################################
# phpRS ClassMyReader 1.5.10
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_ctenari, rs_cte_session, rs_levely

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

/*
  Trida CMyReader slouzi k vytvareni a kontrole prihlaseni ctenaru; dale take obsluhuje vypis ctenarskeho profilu.

  Cookies "readerphprs" verze 3.0.0
   - rs3
   - id ctenare
   - session nalezici ctenari
*/

class CMyReader
{
var $ctenarstav; // stav ctenare / overeni identity
var $ctenarnovy; // prepinac detekujici existenci cisteho ctenarskeho profilu
var $delka_platnosti; // delka platnosti cookies

// data nactena z phprs reader's cookies:
var $ctenaridsess; // id session ctenare
var $ctenarsess; // string session ctenare
var $ctenardata; // pole dat o ctenarovi
var $ctenarverze; // verze phprs reader's cookies

 /*
   CMyReader() .......... konstruktor
   OverCtenare() ........ overeni platnosti session ctenare
   NactiCtenare($atr_user = '', $atr_heslo = '') ............ overeni platnosti loginu a nasledne nacteni dat + generovani ctenarske session
   GenerujCistyCtenar() . generator cisteho ctenarskeho profilu
   Nastav($co = '', $hodnota = '') .......................... (pred)nastaveni nekterych hodnot ctenarskeho profilu
   Ukaz($co = '') ....... fce pro vypis konkretnich dat o ctenarovi
   UkazLevel() .......... fce vraci aktualni level ctenare
   OdeslatCookies($atr_id_sess = 0, $atr_str_sess = '') ..... fce na odeslani cookies
   GenerujSession($atr_id_ctenar = 0, $atr_str_sess = '') ... generator session
   ZrusitSession() ...... zruseni platnosti sessions
 */

 public function __construct()
 {
 $this->ctenarstav=0; // inic. stavu ctenare; default false
 $this->ctenarnovy=0; // inic. stavu noveho ctenarskeho profilu; default false
 $this->ctenardata=array(); // inic. datoveho pole
 $this->delka_platnosti=864000; // 864000 s = 10 dni

 // test na existenci session
 if (isset($_COOKIE['readerphprs'])):
   $vstup_pole_cookies=explode('*::*',base64_decode($_COOKIE['readerphprs'])); // dekodovani a dekompilace phprs reader's cookies
   $this->ctenarverze=addslashes($vstup_pole_cookies[0]); // oznaceni aktualni verze: rs3
   $this->ctenaridsess=addslashes($vstup_pole_cookies[1]); // id session ctenare
   $this->ctenarsess=addslashes($vstup_pole_cookies[2]); // string session ctenare
   // overeni platnosti prihlaseni
   $this->OverCtenare();
 endif;
 }

 function OverCtenare()
 {
 // test na verzi 3.0.0
 if ($this->ctenarverze=='rs3'):
   $aktcas=date("Y-m-d H:i:s");
   // odmazani proslych starych session
   phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."cte_session where platnost<'".$aktcas."'",$GLOBALS["dbspojeni"]);
   // overeni platnosti session ctenare
   $dotaz="select c.*,l.hodnota as level_hodnota from ".$GLOBALS["rspredpona"]."ctenari as c,".$GLOBALS["rspredpona"]."cte_session as s,".$GLOBALS["rspredpona"]."levely as l ";
   $dotaz.="where s.ids='".$this->ctenaridsess."' and s.session='".$this->ctenarsess."' and s.platnost>'".$aktcas."' and s.id_cte=c.idc and c.level_ctenare=l.idl";
   $dotazctenar=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
   if ($dotazctenar!==false&&phprs_sql_num_rows($dotazctenar)>0):
      $this->ctenardata=phprs_sql_fetch_assoc($dotazctenar);
      $this->ctenarstav=1;
   endif;
 else:
   // odstraneni stare verze phprs reader's cookies
   setcookie('readerphprs','cistici_cookies',time()-3600); // odesilam hodinu prosle cookies
 endif;
 }

 function NactiCtenare($atr_user = '', $atr_heslo = '')
 {
 $vysl_akce=0; // false = chyba

 if (!empty($atr_user)):
   $atr_user=phprs_sql_escape_string($atr_user);
   // nacitanie hashovacich funkcii
   require('admin/hash_functions.php');
   $atr_heslo=calculate_hash($atr_heslo);
   // dotaz na ctenare
   $dotaz="select c.*,l.hodnota as level_hodnota from ".$GLOBALS["rspredpona"]."ctenari as c,".$GLOBALS["rspredpona"]."levely as l ";
   $dotaz.="where c.prezdivka='".$atr_user."' and c.password='".$atr_heslo."' and c.level_ctenare=l.idl";
   $dotazctenar=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
   if ($dotazctenar!==false&&phprs_sql_num_rows($dotazctenar)>0):
      $this->ctenardata=phprs_sql_fetch_assoc($dotazctenar);
      $this->ctenarstav=1;
      // nastaveni kladneho vysledku akce
      $vysl_akce=1;
      // automaticke vygenerovani ctenarske session
      $akt_sess_ctenare=md5(time().$this->ctenardata['prezdivka'].$this->ctenardata['password'].$this->ctenardata['jmeno'].$this->ctenardata['email']);
      $this->GenerujSession($this->ctenardata['idc'],$akt_sess_ctenare);
   endif;
 endif;

 return $vysl_akce;
 }

 function GenerujCistyCtenar()
 {
 // prednastaveni defaultnich stavu
 $this->ctenardata["idc"]=0;
 $this->ctenardata["prezdivka"]='';
 $this->ctenardata["password"]='';
 $this->ctenardata["jmeno"]='';
 $this->ctenardata["email"]='@';
 $this->ctenardata["datum"]='0000-00-00';
 $this->ctenardata["info"]=1;
 $this->ctenardata["data"]='';
 $this->ctenardata["visible"]=0;
 $this->ctenardata["jazyk"]='cz';
 $this->ctenardata["posledni_login"]='0000-00-00';
 // aktivace noveho ctenarskeho profilu
 $this->ctenarnovy=1;
 }

 function Nastav($co = '', $hodnota = '')
 {
 // bezpecnostni korekce
 //XXX $hodnota=phprs_sql_escape_string($hodnota);
 // (pred)nastavit lze pouze nektere polozky
 switch($co):
   case 'username': $this->ctenardata["prezdivka"]=$hodnota; break;
   case 'jmeno': $this->ctenardata["jmeno"]=$hodnota; break;
   case 'email': $this->ctenardata["email"]=$hodnota; break;
   case 'info': $this->ctenardata["info"]=$hodnota; break;
   case 'databox': $this->ctenardata["data"]=$hodnota; break;
   case 'zobrazitdata': $this->ctenardata["visible"]=$hodnota; break;
   case 'jazyk': $this->ctenardata["jazyk"]=$hodnota; break;
 endswitch;
 }

 function Ukaz($co = '')
 {
 if ($this->ctenarstav==1||$this->ctenarnovy==1):
   // prihlaseni ctenare je platne
   switch($co):
     case 'id': return $this->ctenardata["idc"]; break;
     case 'username': return $this->ctenardata["prezdivka"]; break;
     case 'heslo': return $this->ctenardata["password"]; break;
     case 'jmeno': return $this->ctenardata["jmeno"]; break;
     case 'email': return $this->ctenardata["email"]; break;
     case 'registrace': return $this->ctenardata["datum"]; break;
     case 'info': return $this->ctenardata["info"]; break;
     case 'databox': return $this->ctenardata["data"]; break;
     case 'zobrazitdata': return $this->ctenardata["visible"]; break;
     case 'jazyk': return $this->ctenardata["jazyk"]; break;
     case 'posledni': return $this->ctenardata["posledni_login"]; break;
     default: return ''; break;
   endswitch;
 else:
   // chyba; ctenar nema platne overeni
   return '';
 endif;
 }

 function UkazLevel()
 {
 if ($this->ctenarstav==1):
   return $this->ctenardata["level_hodnota"];
 else:
   return NactiConfigProm('default_level',0); // defaultni nastaveni levelu
 endif;
 }

 function OdeslatCookies($atr_id_sess = 0, $atr_str_sess = '')
 {
 $pom_str=base64_encode('rs3*::*'.$atr_id_sess.'*::*'.$atr_str_sess);
 setcookie('readerphprs',$pom_str,time()+$this->delka_platnosti);
 }

 function GenerujSession($atr_id_ctenar = 0, $atr_str_sess = '')
 {
 $vysl_akce = 0; // false = chyba

 if ($atr_id_ctenar>0):
   $akt_dnesni_datum=date("Y-m-d H:i:s");
   $akt_delka_platnosti=date("Y-m-d H:i:s",time()+$this->delka_platnosti);
   $atr_id_ctenar=phprs_sql_escape_string($atr_id_ctenar);
   $atr_str_sess=phprs_sql_escape_string($atr_str_sess);

   // vytvoreni nove ctenareske session
   @$dotazsession=phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."cte_session values (null,'".$atr_str_sess."','".$atr_id_ctenar."','".$akt_delka_platnosti."')",$GLOBALS["dbspojeni"]);
   // test na vysledek dotazu
   if ($dotazsession):
     // aktualizace polozky "posledni_login" u ctenare
     @phprs_sql_query("update ".$GLOBALS["rspredpona"]."ctenari set posledni_login='".$akt_dnesni_datum."' where idc='".$atr_id_ctenar."'",$GLOBALS["dbspojeni"]);
     // zjisteni id session
     $dotazidsession=phprs_sql_query("select ids from ".$GLOBALS["rspredpona"]."cte_session where id_cte='".$atr_id_ctenar."' and session='".$atr_str_sess."'",$GLOBALS["dbspojeni"]);
     if ($dotazidsession!==false&&phprs_sql_num_rows($dotazidsession)==1):
       list($akt_id_session)=phprs_sql_fetch_row($dotazidsession); // id session
       // odeslani noveho cookies
       $this->OdeslatCookies($akt_id_session,$atr_str_sess);
       // nastaveni vysledkoveho stavu
       $vysl_akce=1; // true = akce probehla v poradku
     endif;
   endif;
 endif;

 return $vysl_akce;
 }

 function ZrusitSession()
 {
 if ($this->ctenarstav==1):
   @phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."cte_session where ids='".$this->ctenaridsess."' and session='".$this->ctenarsess."'",$GLOBALS["dbspojeni"]);
 endif;
 }
}

$prmyctenar = new CMyReader();

?>