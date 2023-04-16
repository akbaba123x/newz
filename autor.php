<?php

######################################################################
# phpRS authorization 1.3.9
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_guard, rs_user, rs_vazby_prava

/*
   Typy uzivatelu:
   0 - autor
   1 - redaktor
   2 - admin
*/

if (!defined('IN_CODE')):
	header("HTTP/1.1 403 Forbidden");
	die('Nepovoleny pristup! / Hacking attempt!');
endif;

$GLOBALS['jmenoovercookies']='adminco'.$GLOBALS['rspredpona']; // jmeno overovaci cookies

class RSAutor
{
var $StavSession,$IdUser,$IdSession,$UserType,$UserVydavatel;
var $SessionUser,$UserName,$Jmeno,$JazykRozhrani;
var $pole_podrizeni,$stav_podrizeni;
var $chyba;

 /*
   RSAutor()
   PrvniPrihlaseni($vstup_user = '',$vstup_pass = '') ... vystup: 0/1
   OvereniPrihlaseni($vstup_cookies = '')             ... vystup: 0/1
   Odhlaseni()
   BlokovatUcet($vstup_id_user = 0)                   ... vystup: 0/1
   DeblokovatUcet($vstup_id_user = 0)
   OvereniTypuBool($vstup_uroven = 0)                 ... vystup: 0/1
   OvereniTypu($vstup_uroven = 0)
   OvereniPravBool($ident_modulu = '')                ... vystup: 0/1
   OvereniPrav($ident_modulu = '')
   MuzeVydavat()                                      ... vystup: 0/1
   JeAdmin()                                          ... vystup: 0/1
   SeznamDostupUser()                                 ... vystup: seznam pristupnych id user
   JePodrizeny()                                      ... vystup: 0/1
   ZjistiPath()
   Ukaz($co = "")                                     ... vystup: informace o uzivateli
   ZobrazChybu($idchyba = 0)                          ... vystup: chybove hlaseni
  */

 public function __construct()
 {
 // stav autorizace
 $this->StavSession=0;
 // session
 $this->IdSession=0;
 $this->SessionUser='';
 // informace o uzivateli
 $this->IdUser=0;
 $this->UserName='';
 $this->Jmeno='';
 $this->JazykRozhrani='';
 $this->UserType=0;
 $this->UserVydavatel=0;
 // inic. pole podrizenych
 $this->pole_podrizeni=array();
 $this->stav_podrizeni=0;
 // stav chyby
 $this->chyba=0;
 }

 function PrvniPrihlaseni($vstup_user = '',$vstup_pass = '')
 {
 if (empty($vstup_user)):
   $this->chyba=1;
   return 0; // prazdny user
 endif;

 if (empty($vstup_pass)):
   $this->chyba=2;
   return 0; // prazdne pass
 endif;

 // bezpecnostni korekce
 $vstup_user=phprs_sql_escape_string($vstup_user);
 $vstup_pass=phprs_sql_escape_string($vstup_pass);

 $dotazuser=phprs_sql_query("select idu,user,password,jmeno,admin,pravo_vydavat,blokovat,jazyk_prostredi from ".$GLOBALS["rspredpona"]."user where user='".$vstup_user."' and admin>=0",$GLOBALS["dbspojeni"]);
 if ($dotazuser!==false):
   $pocetuser=phprs_sql_num_rows($dotazuser);
 else:
   $pocetuser=0;
 endif;
 // kontrola vysledku dotazu na user
 if ($pocetuser==0):
   $this->chyba=3;
   return 0; // neodpovida zadny uzivatel
 else:
   // inic. vysledkoveho pole
   $akt_pom_login_pole=array();
   $akt_pom_login_pole=phprs_sql_fetch_assoc($dotazuser);
   // vse OK - uzivatel existuje
   if ($akt_pom_login_pole['blokovat']==1): // test na blokovani uctu
     $this->chyba=7;
     return 0; // ucet je blokovan proti prihlaseni
   else:
     // nacitanie hashovacich funkcii
     require('admin/hash_functions.php');
     if (calculate_hash($vstup_pass)!=$akt_pom_login_pole['password']): // porovnani vstupniho hesla se vzorem z DB
       if ($this->BlokovatUcet($akt_pom_login_pole['idu'])==1):
         $this->chyba=7; // blokace uctu
       else:
         $this->chyba=4; // pouze spatne heslo
       endif;
       return 0; // vstupni heslo neodpovida
     else:
       // vse OK
       $this->IdUser=$akt_pom_login_pole['idu']; // cislo uzivatele
       $this->UserType=$akt_pom_login_pole['admin']; // typ uzivatele
       $this->UserName=$akt_pom_login_pole['user']; // username uzivatele
       $this->Jmeno=$akt_pom_login_pole['jmeno']; // jmeno uzivatele
       $this->JazykRozhrani=$akt_pom_login_pole['jazyk_prostredi']; // nastaveny jazyk rozhrani
       $this->UserVydavatel=$akt_pom_login_pole['pravo_vydavat']; // pravo vydavat
       $this->SessionUser=MD5(Date("szY")."g".$vstup_user.$vstup_pass); // generovani session
       $aktualnicas=Date("Y-m-d H:i:s");

       // deblokace + vycistni uctu uzivatele
       $this->DeblokovatUcet($akt_pom_login_pole['idu']);

       // ulozeni session
       phprs_sql_query("insert into ".$GLOBALS["rspredpona"]."guard(password,kdo,cas) values('".$this->SessionUser."','".$this->IdUser."','".$aktualnicas."')",$GLOBALS["dbspojeni"]);

       // dotaz na session
       $dotazsess=phprs_sql_query("select idg from ".$GLOBALS["rspredpona"]."guard where password='".$this->SessionUser."' and kdo='".$this->IdUser."' and cas='".$aktualnicas."'",$GLOBALS["dbspojeni"]);
       if ($dotazsess!==false&&phprs_sql_num_rows($dotazsess)>0):
         list($this->IdSession)=phprs_sql_fetch_row($dotazsess); // id session

         // vlozeni do cookies a odeslani
         $adminco=base64_encode("phpRS:".$this->IdSession.":".$this->SessionUser.":".$this->IdUser);
         // test na nastaveni citlivosti cookies
         if ($GLOBALS['rsconfig']['cookies_s_domenou']==1):
			// cookies - jmeno_cookies , obsah , platnost , path , domena
			if (isset($GLOBALS['rsconfig']['ssl']) && $GLOBALS['rsconfig']['ssl']===true) {
				setcookie($GLOBALS['jmenoovercookies'],$adminco,time()+$GLOBALS['rsconfig']['platnost_auth'],$this->ZjistiPath(),$_SERVER["HTTP_HOST"], true, true); // secure, HTTP only
			} else {
				setcookie($GLOBALS['jmenoovercookies'],$adminco,time()+$GLOBALS['rsconfig']['platnost_auth'],$this->ZjistiPath(),$_SERVER["HTTP_HOST"], false, true); // not secure, HTTP only
			}
         else:
           // cookies - jmeno_cookies , obsah , platnost
           setcookie($GLOBALS['jmenoovercookies'],$adminco,time()+$GLOBALS['rsconfig']['platnost_auth']);
         endif;

         $this->StavSession=1; // aktivni stav prihlaseni
         return 1;
       else:
         $this->chyba=8;
         return 0; // chyba pri ulozeni session; nelze pridat novy zaznam do tab. rs_guard
       endif;
     endif;
   endif;
 endif;
 }

 function OvereniPrihlaseni($vstup_cookies = '')
 {
 if (empty($vstup_cookies)):
   $this->chyba=5;
   return 0; // chyba; neexistuje session
 endif;

 $dekoduj_vstup=base64_decode($vstup_cookies);
 $dekoduj_vstup=explode(":",$dekoduj_vstup);

 // dekompilace + bezpecnostni korekce
 $this->IdSession=phprs_sql_escape_string($dekoduj_vstup[1]);   // id session
 $this->SessionUser=phprs_sql_escape_string($dekoduj_vstup[2]); // session
 $this->IdUser=phprs_sql_escape_string($dekoduj_vstup[3]);      // cislo uzivatele

 // kontrola - porovnani exist. session s cookies
 $dotazkontrsess=phprs_sql_query("select password from ".$GLOBALS["rspredpona"]."guard where idg='".$this->IdSession."' and password='".$this->SessionUser."' and kdo='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
 if ($dotazkontrsess!==false):
   $pocetkontrsess=phprs_sql_num_rows($dotazkontrsess);
 else:
   $pocetkontrsess=0;
 endif;

 if ($pocetkontrsess==0):
   // chyba; neplatna session
   $this->chyba=6;
   return 0;
 else:
   // vse OK
   $dotazprava=phprs_sql_query("select user,jmeno,admin,pravo_vydavat,jazyk_prostredi from ".$GLOBALS["rspredpona"]."user where idu='".$this->IdUser."'",$GLOBALS["dbspojeni"]);

   // inic. vysledkoveho pole
   $akt_pom_login_pole=array();
   $akt_pom_login_pole=phprs_sql_fetch_assoc($dotazprava);

   $this->UserName=$akt_pom_login_pole['user']; // username uzivatele
   $this->Jmeno=$akt_pom_login_pole['jmeno']; // jmeno uzivatele
   $this->JazykRozhrani=$akt_pom_login_pole['jazyk_prostredi']; // nastaveny jazyk rozhrani
   $this->UserType=$akt_pom_login_pole['admin']; // typ uzivatele
   $this->UserVydavatel=$akt_pom_login_pole['pravo_vydavat']; // pravo vydavat

   define("RSAUT_PRAVA",$this->UserType);
   define("RSAUT_IDUSER",$this->IdUser);
   define("RSAUT_VYDAVATEL",$this->UserVydavatel);

   $this->StavSession=1; // aktivni stav prihlaseni
   return 1;
 endif;
 }

 function Odhlaseni()
 {
 $this->StavSession=0;
 // odmazani session
 phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."guard where idg='".$this->IdSession."'",$GLOBALS["dbspojeni"]);
 // vytvoreni mazaciho cookies a odeslani
 /*
 $adminco=base64_encode("phpRS:::");
 setcookie($GLOBALS['jmenoovercookies'],$adminco,time()-3600); // jmeno_cookies , obsah , platnost
 */
 }

 function BlokovatUcet($vstup_id_user = 0)
 {
 $vysl=0; // false - ucet neni blokovan

 $dotazucet=phprs_sql_query("select user,password,email,pocet_chyb from ".$GLOBALS["rspredpona"]."user where idu='".$vstup_id_user."'",$GLOBALS["dbspojeni"]);
 if (phprs_sql_num_rows($dotazucet)==1):
   $akt_pole_data=phprs_sql_fetch_assoc($dotazucet);
   // test na pocet chyb - chyby se pocitaji od 0, proto musi byt ">="
   if ($akt_pole_data['pocet_chyb']>=$GLOBALS['rsconfig']['auth_max_pocet_chyb']):
     // kdyz je pocet chyb vetsi nez mezni hodnota -> zablokovani uctu
     $akt_blokacni_str=md5('blokace'.date("Y").$akt_pole_data['password']);
     phprs_sql_query("update ".$GLOBALS["rspredpona"]."user set blokovat=1, pocet_chyb=0, pom_str='".$akt_blokacni_str."' where idu='".$vstup_id_user."'",$GLOBALS["dbspojeni"]);
     $vysl=1; // true - ucet je blokovan
     // odeslani deblokovaciho e-mailu
     include('admin/astdlib_mail.php');
     $postovni_centrum = new CPosta();
     $postovni_centrum->Nastav('adresat',$akt_pole_data['email']);
     $postovni_centrum->Nastav('predmet','Deblokacni e-mail');
     $postovni_centrum->Nastav('obsah','Vas ucet na webu "'.$GLOBALS['wwwname'].'" byl zablokovan! Pro odblokovani pouzijte nasledujici link: '.$GLOBALS['baseadr'].'deblokace.php?blok_user='.$akt_pole_data['user'].'&blok_string='.$akt_blokacni_str);
     $postovni_centrum->Odesilac();
   else:
     // pricteni chyby
     phprs_sql_query("update ".$GLOBALS["rspredpona"]."user set pocet_chyb=(pocet_chyb+1) where idu='".$vstup_id_user."'",$GLOBALS["dbspojeni"]);
   endif;
 endif;

 return $vysl;
 }

 function DeblokovatUcet($vstup_id_user = 0)
 {
 // odblokovani + vycisteni uctu
 phprs_sql_query("update ".$GLOBALS["rspredpona"]."user set blokovat=0, pocet_chyb=0, pom_str='' where idu='".$vstup_id_user."'",$GLOBALS["dbspojeni"]);
 }

 function OvereniTypuBool($vstup_uroven = 0)
 {
 // uzivatele: 0 = autor, 1 = redaktor, 2 = admin
 if ($vstup_uroven>$this->UserType): // kdyz je pozadovana uroven vetsi, nez aktualni, tak uzivatel nesplnuje podminku
   return 0;
 else:
   return 1;
 endif;
 }

 function OvereniTypu($vstup_uroven = 0)
 {
 // uzivatele: 0 = autor, 1 = redaktor, 2 = admin
 if ($vstup_uroven>$this->UserType): // kdyz je pozadovana uroven vetsi, nez aktualni, tak uzivatel nesplnuje podminku
   echo "<p align=\"center\">Nemáte potřebná práva pro vstup do této sekce!</p></body></html>";
   exit;
 endif;
 }

 function OvereniPravBool($ident_modulu = '')
 {
 // test na platnost vstupu a aktivni session
 if (empty($ident_modulu)||$this->StavSession==0):
   // prazdny vstup
   return 0; // NE
 else:
   // test na typ uzivatele: 2 = admin
   if ($this->UserType==2):
     return 1; // OK
   else:
     $ident_modulu=addslashes($ident_modulu);
     // vyhledani prislusne sekce
     $dotazsek=phprs_sql_query("select fks_prava_users,all_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$ident_modulu."'",$GLOBALS["dbspojeni"]);
     $pocetsek=phprs_sql_num_rows($dotazsek);
     if ($pocetsek>0):
       // zaznam existuje
       $akt_pole_nalez_prava=phprs_sql_fetch_assoc($dotazsek); // nacteni ziskanych dat
       // test na globalni pristupnost modulu
       if ($akt_pole_nalez_prava['all_prava_users']==1):
         return 1; // OK
       else:
         $uzivatele=explode(":",$akt_pole_nalez_prava['fks_prava_users']); // prevod seznamu povolenych uzivatelu do pole
         $pocetuzivatelu=count($uzivatele);

         $aktivni=0; // defaultne pristup zamitnut

         for ($pom=0;$pom<$pocetuzivatelu;$pom++):
           if ($uzivatele[$pom]==$this->IdUser): $aktivni=1; break; endif; // pristupove pravo potvrzeno
         endfor;

         return $aktivni; // vysledek po vyhledavni shody
       endif;
     else:
       // zaznam neexistuje
       return 0; // NE
     endif;
   endif;
 endif;
 }

 function OvereniPrav($ident_modulu = '')
 {
 // test na platnost vstupu a aktivni session
 if (empty($ident_modulu)||$this->StavSession==0):
   // prazdny vstup
   echo "<p align=\"center\">Nelze ověřit přístupová práva!</p></body></html>"; // NE
   exit();
 else:
   // test na typ uzivatele: 0 = autor, 1 = redaktor, 2 = admin
   if ($this->UserType==0||$this->UserType==1):
     $ident_modulu=phprs_sql_escape_string($ident_modulu);
     // vyhledani prislusne sekce
     $dotazsek=phprs_sql_query("select fks_prava_users,all_prava_users from ".$GLOBALS["rspredpona"]."moduly_prava where ident_modulu='".$ident_modulu."'",$GLOBALS["dbspojeni"]);
     $pocetsek=phprs_sql_num_rows($dotazsek);
     if ($pocetsek>0):
       // zaznam existuje
       $akt_pole_nalez_prava=phprs_sql_fetch_assoc($dotazsek); // nacteni ziskanych dat
       // test na globalni pristupnost modulu: 0 = musi byt testovani kazdy uzivatel zvlast, 1 = globalni pristupny
       if ($akt_pole_nalez_prava['all_prava_users']==0):
         $uzivatele=explode(":",$akt_pole_nalez_prava['fks_prava_users']); // prevod seznamu povolenych uzivatelu do pole
         $pocetuzivatelu=count($uzivatele);

         $aktivni=0; // defaultne pristup zamitnut

         for ($pom=0;$pom<$pocetuzivatelu;$pom++):
           if ($uzivatele[$pom]==$this->IdUser): $aktivni=1; break; endif; // pristupove pravo potvrzeno
         endfor;

         // redakce na vysledek vyhledavni shody
         if ($aktivni==0):
           echo "<p align=\"center\">Nemáte potřebná práva pro vstup do této sekce!</p></body></html>";
           exit;
         endif;
       endif;
     else:
       // zaznam neexistuje
       echo "<p align=\"center\">Nelze ověřit přístupová práva!</p></body></html>"; // NE
       exit();
     endif;
   endif;
 endif;
 }

 function MuzeVydavat()
 {
 // test na aktivni stav prihlaseni
 if ($this->StavSession==1):
   if ($this->UserType==2): // admin
     return 1; // admin vzdy muze
   else:
     return $this->UserVydavatel; // aktualni nastaveni
   endif;
 else:
   return 0; // neexistuje session
 endif;
 }

 function JeAdmin()
 {
 // test na aktivni stav prihlaseni (1 = true) a typ uzivatele (2 = admin)
 if ($this->StavSession==1&&$this->UserType==2):
   return 1; // vysledek true
 else:
   return 0; // vysledek false
 endif;
 }

 function SeznamDostupUser()
 {
 $seznam='';

 if ($this->StavSession==1):
   $seznam.=$this->IdUser; // seznam obsahuje i ID nadrizeneho uzivatele
   $dotazpod=phprs_sql_query("select fk_id_podrizeny from ".$GLOBALS["rspredpona"]."vazby_prava where fk_id_nadrizeny='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
   while($pole_data = phprs_sql_fetch_assoc($dotazpod)):
     $seznam.=','.$pole_data['fk_id_podrizeny'];
   endwhile;
 else:
   $seznam='0'; // nic
 endif;

 return $seznam;
 }

 function JePodrizeny($id_test = 0)
 {
 if ($this->stav_podrizeni==0):
   // nutno nacist data
   if ($this->StavSession==1):
     $this->pole_podrizeni[]=$this->IdUser; // pole obsahuje i ID samotneho nadrizeneho uzivatele
     $dotazpod=phprs_sql_query("select fk_id_podrizeny from ".$GLOBALS["rspredpona"]."vazby_prava where fk_id_nadrizeny='".$this->IdUser."'",$GLOBALS["dbspojeni"]);
     while($pole_data = phprs_sql_fetch_assoc($dotazpod)):
       $this->pole_podrizeni[]=$pole_data['fk_id_podrizeny'];
     endwhile;
     $this->stav_podrizeni=1;
   endif;
 endif;

 // test na podrizenost id user
 if (in_array($id_test,$this->pole_podrizeni)):
   return 1; // je podrizeny
 else:
   return 0; // neni podrizeny
 endif;
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

 function Ukaz($co = '')
 {
 if ($this->StavSession==1):
   // prihlaseni uzivatele je platne
   switch($co):
     case 'id': return $this->IdUser; break;
     case 'username': return $this->UserName; break;
     case 'jmeno': return $this->Jmeno; break;
     case 'jazyk': return $this->JazykRozhrani; break;
     case 'idtyp': return $this->UserType; break;
     default: return '';
   endswitch;
 else:
   // chyba; uzivatel nema platnou autorizaci
   return "";
 endif;
 }

 function ZobrazChybu($idchyba = 0)
 {
 switch ($idchyba):
   case 0: $popis='Neznama chyba!'; break;
   case 1: $popis='Nezadali jste zadneho uzivatele.'; break;
   case 2: $popis='Nezadali jste zadne heslo.'; break;
   case 3: $popis='Neznamy uzivatel nebo uzivatel s nedostatecnymi pristupovymi pravy.'; break;
   case 4: $popis='Spatne heslo.'; break;
   case 5: $popis='Prihlasovaci session neexistuje.'; break;
   case 6: $popis='Neplatna prihlasovaci session.'; break;
   case 7: $popis='Ucet je blokovan.'; break;
   case 8: $popis='Nelze ulozit prihlasovaci session.'; break;
   default: $popis='Neznama chyba cislo '.$idchyba.'.'; break;
 endswitch;

 return $popis;
 }
}

$Uzivatel = new  RSAutor();

if (isset($GLOBALS['rsnewuser'])&&isset($GLOBALS['rsnewpass'])):
  // autorizace uzivatele
  if ($Uzivatel->PrvniPrihlaseni($GLOBALS['rsnewuser'],$GLOBALS['rsnewpass'])!=1):
    showLoginForm();
    exit;
  else:
    // presmerovani na vysledkovou stranku - ochrana proti reloadu loginu
    if (isset($_SERVER["HTTPS"])) {
    	// SSL
    	header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF']);
    	exit;
    } else {
    	header('Location: http://'.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF']);
    	exit;
    } 
  endif;
else:
  if (isset($_COOKIE[$GLOBALS['jmenoovercookies']])):
    // overeni jiz existujiciho pristupu
    if ($Uzivatel->OvereniPrihlaseni($_COOKIE[$GLOBALS['jmenoovercookies']])!=1):
      //echo "<html><body><div align=\"center\">".$Uzivatel->ZobrazChybu($Uzivatel->chyba)."</div></body></html>";
      showLoginForm();  
      exit;
    endif;
  else:
    // k dispozici nejsou jak session, tak ani prihlasovaci promenne
    showLoginForm(); 
    exit;
  endif;
endif;

################################################################################

function showLoginForm() {
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title><?php echo $GLOBALS['wwwname']; ?> - phpRS editorial system</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['rsconfig']['kodovani']; ?>">
  <meta name="author" content="Jiří Lukáš, phpRS community">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta name="robots" content="noindex,follow">
  <meta name="googlebot" content="nosnippet,noarchive">
  <style type="text/css">
  <!--
    body {  
        font-family: verdana, geneva, arial, helvetica, sans-serif;
        font-size: 16px; 
        color: #444; 
        background: #eee url('image/key.png') no-repeat;
        padding-top:210px;   
    }
    table { 
        font-family: verdana, geneva, arial, helvetica, sans-serif;
        font-size: 16px; 
        color: #000; 
    }
    .textpole { 
        border: 1px solid #000; 
        color: #444; 
        font-family: verdana, geneva, arial, helvetica, sans-serif;
        font-size: 16px;
        padding:2px; 
    }
    .tl { 
        font-family: arial, sans-serif;
        font-size: 14px; 
        font-weight: bold; 
        text-align: center; 
        background:#ccc url('image/button-grey.png') repeat-x;
        color:#111; 
    }
    #key {
        float:left;
    }
  -->
  </style>
</head>
<body>

    <form action="" method="post" style="padding:1em; background:#ddd;">
    <table cellspacing="8" align="center" style="border:1px dotted #ccc;">
    <tr>
        <td>User:
        <td><input type="text" name="rsnewuser" size="15" maxlength="100" class="textpole">
    <tr>
        <td>Password:
        <td><input type="password" name="rsnewpass" size="15" maxlength="100" class="textpole" style="color:#f30;">
    <tr>
    	<td>
        <td><input type="submit" value="  LOGIN  " class="tl">

    </table>
    </form>

</body>
</html>

<?php
}

?>