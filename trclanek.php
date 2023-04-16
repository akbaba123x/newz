<?php

######################################################################
# phpRS ClassClanek 1.3.8
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_user, rs_topic, rs_clanky, rs_imggal_obr, rs_levely

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

class CClanek
{
var $aktivni; // stavovy ukazatel tridy; 1 = jsou nacteny clanky, 0 = trida je prazdna
var $poleautori,$poletema,$polesab; // pomocne pole: autori; temata; sablony
var $pocetautori,$pocettema,$pocetsab; // pocet prvku v pom. polich
var $stavautori,$stavtema,$stavsab; // stavove prepinace pomocnych poli
var $clanek; // assoc. pole clanek
var $ctenar_level; // akt. level ctenare
var $zakazova_sablona; // nastaveni pouziti zakazove clankove sablony
var $pocetclanku,$aktpozice,$dotazclanek; // pocet clanku ulozenych v pameti; akt. pozice v pameti; id dotazu na clanek(y)
var $testplat,$vydani,$kontroladata,$kontrolalevel; // testovat na platnost; kontrola vydani clanku; kontrola data vydani; kontrola hodnoty levelu ve vztahu ke ctenari
var $hlavni_stranka; // identifikace hlavni stranky

/*
  Dostupne funkce ve tride CClanek:

  CClanek()                            - konstruktor
  NactiAutory()                        - hromadne nacteni autoru
  ZjistiAutora($id = 0, $co = '')      - preklad autora
  AntiSpam($str = '')                  - upravuje formu e-mailove adresy
  NactiTemata()                        - hromadne nacteni temat
  ZjistiTema($id = 0, $co = '')        - preklad temata
  NactiSab()                           - hromadne nacteni clankovych sablon
  ZjistiSab($id = 0)                   - preklad sablony
  HlidatPlatnost($stav = 1)            - nastaveni hlidani platnosti cl. (defautl NE)
  HlidatVydani($stav = 1)              - nastaveni kontroly vydani cl. (default ANO)
  HlidatAktDatum($stav = 1)            - nastaveni hlidani data vydani (default ANO)
  HlidatLevel($stav = 1)               - nastaveni kontroly levelu cl. (default NE)
  NastavLevelCtenare($level = 0)       - nastaveni hodnoty levelu ctenare
  NastavZakazovouSab($stav = 1)        - nastaveni pouziti zakazove cl. sablony (default NE)
  NastavHlStr($stav = 0)               - nastaveni stavu prepinace hl. str. (default NE)
  PridatPhprsZnacku($nazev_znacky = '', $nazev_funkce = '')  - prihlaseni phpRS znacky k vyhledavani a dekodovani; $nazev_funkce = urceni externi PHP funkce pro dekodovani
  Dekoduj($text = '')                  - obecna dekodovaci fce pro phpRS znacky; vyhledava prihlasene phpRS znacky a inic. jejich preklad dle atributu
  DekodujZnackaObrazek($parametry = array())                 - dekodovani konkretni phpRS znacky "obrazek"
  DekodujZnackaReklama($parametry = array())                 - dekodovani konkretni phpRS znacky "reklama"
  NactiClanek($link = 0)               - nacteni jednoho clanku do pameti; navratova hodnota oznamuje uspesnost akce
  NactiClanky($mnozstvi = 0, $od = 0)  - nacteni X mnoz. clanku do pameti; navratova hodnota oznamuje uspesnost akce
  NactiZdrojCla($id_zdroj = 0)         - import zdroje nactenych clanku; navratova hodnota oznamuje uspesnost akce
  DalsiRadek()                         - nastaveni pozice v pameti clanku
  CelkemClanku()                       - zjisteni celkoveho poctu clanku odpovidajicich pripadne podmince
  Ukaz($co = '')                       - zobrazeni dostpnych atributu clanku
*/

 public function __construct()
 {
 $this->aktivni=0;
 // inic. pomocnych promennych
 $this->stavautori=0;
 $this->stavtema=0;
 $this->stavsab=0;
 $this->testplat=0;
 $this->vydani=1;
 $this->hlavni_stranka=0;
 $this->kontroladata=1;
 $this->ctenar_level=0;
 $this->kontrolalevel=0;
 $this->zakazova_sablona=0;
 $this->clanek=array();
 // inic. defaultnich phpRS znacek
 $this->pole_phprs_znacky['vsechny_znacky'][]='obrazek';
 $this->pole_phprs_znacky['obrazek']['funkce_typ']='metoda';
 $this->pole_phprs_znacky['obrazek']['funkce_nazev']='DekodujZnackaObrazek';
 $this->pole_phprs_znacky['vsechny_znacky'][]='reklama';
 $this->pole_phprs_znacky['reklama']['funkce_typ']='metoda';
 $this->pole_phprs_znacky['reklama']['funkce_nazev']='DekodujZnackaReklama';
 }

 function NactiAutory()
 {
 // nacteni seznamu uzivatelu(autoru) do pole "poleautori"
 if ($this->stavautori==0):
   $dotazaut=phprs_sql_query("select idu,jmeno,email,im_ident from ".$GLOBALS["rspredpona"]."user order by idu",$GLOBALS["dbspojeni"]);
   $this->pocetautori=phprs_sql_num_rows($dotazaut);
   if ($this->pocetautori>0):
     $this->stavautori=1; // nastaveni aktivniho stavu
     while($data_aut = phprs_sql_fetch_assoc($dotazaut)):
       $this->poleautori[$data_aut['idu']][0]=$data_aut['jmeno'];
       $this->poleautori[$data_aut['idu']][1]=$this->AntiSpam($data_aut['email']);
       $this->poleautori[$data_aut['idu']][2]=$data_aut['im_ident'];
     endwhile;
   endif;
 endif;
 }

 function ZjistiAutora($id = 0, $co = '')
 {
 $vysl_jm='';
 $vysl_mail='';
 $vysl_im='';

 if ($id!=0):
   if ($this->stavautori==1):
     // existuje aktivni pole s autory ve tride
     if (isset($this->poleautori[$id])): // autor nalezen
       $vysl_jm=$this->poleautori[$id][0]; // jmeno
       $vysl_mail=$this->poleautori[$id][1]; // e-mail
       $vysl_im=$this->poleautori[$id][2]; // instant messaging
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazaut=phprs_sql_query("select jmeno,email,im_ident from ".$GLOBALS["rspredpona"]."user where idu='".$id."'",$GLOBALS["dbspojeni"]);
     if (phprs_sql_num_rows($dotazaut)==1):
       $pole_data=phprs_sql_fetch_assoc($dotazaut);
       $vysl_jm=$pole_data['jmeno'];
       $vysl_mail=$this->AntiSpam($pole_data['email']);
       $vysl_im=$pole_data['im_ident'];
     endif;
   endif;

   // vypis vysledku
   switch($co):
     case 'jm': return $vysl_jm; break;
     case 'mail': return $vysl_mail; break;
     case 'im': return $vysl_im; break;
     default: return ''; break;
   endswitch;
 else:
   return ''; // chyba
 endif;
 }

 function AntiSpam($str = '')
 {
 	return(str_replace(array("@", "."), array("&#064;", "&#046;") , $str));
 }

 function NactiTemata()
 {
 if ($this->stavtema==0):
   // nacteni seznamu temat do pole "rubriky"
   $dotazrub=phprs_sql_query("select idt,nazev,obrazek from ".$GLOBALS["rspredpona"]."topic order by idt",$GLOBALS["dbspojeni"]);
   $this->pocettema=phprs_sql_num_rows($dotazrub);
   if ($this->pocettema>0):
     $this->stavtema=1; // nastaveni aktivniho stavu
     while ($data_tem = phprs_sql_fetch_assoc($dotazrub)):
       $this->poletema[$data_tem['idt']][1]=$data_tem['nazev'];
       $this->poletema[$data_tem['idt']][2]=$data_tem['obrazek'];
     endwhile;
   endif;
 endif;
 }

 function ZjistiTema($id = 0, $co = '')
 {
 $vysl_jm='';
 $vysl_obr='';

 if ($id!=0):
   if ($this->stavtema==1):
     // existuje aktivni pole s tematy ve tride
     if (isset($this->poletema[$id])): // tema nalezeno
       $vysl_jm=$this->poletema[$id][1]; // nazev tema
       $vysl_obr=$this->poletema[$id][2]; // obrazek
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazrub=phprs_sql_query("select nazev,obrazek from ".$GLOBALS["rspredpona"]."topic where idt='".$id."'",$GLOBALS["dbspojeni"]);
     if (phprs_sql_num_rows($dotazrub)==1):
       $data_tem=phprs_sql_fetch_assoc($dotazrub);
       $vysl_jm=$data_tem["nazev"];
       $vysl_obr=$data_tem["obrazek"];
     endif;
   endif;

   // vypis vysledku
   switch($co):
     case 'jm': return $vysl_jm; break;
     case 'obr': return $vysl_obr; break;
     default: return ''; break;
   endswitch;
 else:
   return ''; // chyba
 endif;
 }

 function NactiSab()
 {
 if ($this->stavsab==0):
   // nacteni seznamu temat do pole "rubriky"
   $dotazsab=phprs_sql_query("select ids,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab order by ids",$GLOBALS["dbspojeni"]);
   $this->pocetsab=phprs_sql_num_rows($dotazsab);
   while ($pole_data = phprs_sql_fetch_assoc($dotazsab)):
     $this->polesab[$pole_data["ids"]]=$pole_data["soubor_cla_sab"];
   endwhile;
   if ($this->pocetsab>0): $this->stavsab=1; endif; // aktivni stav
 endif;
 }

 function ZjistiSab($id = 0)
 {
 $vysl_sab='';

 if ($id>0):
   if ($this->stavsab==1):
     // existuje nactene pole sablon
     if (isset($this->polesab[$id])): // sablona nalezena
       $vysl_sab=$this->polesab[$id];
     endif;
   else:
     // musi se zjisti primo v DB
     $dotazsab=phprs_sql_query("select ids,soubor_cla_sab from ".$GLOBALS["rspredpona"]."cla_sab where ids='".$id."'",$GLOBALS["dbspojeni"]);
     if (phprs_sql_num_rows($dotazsab)==1):
       $pole_data=phprs_sql_fetch_assoc($dotazsab);
       $vysl_sab=$pole_data['soubor_cla_sab'];
     endif;
   endif;
 endif;

 return $vysl_sab;
 }

 function HlidatPlatnost($stav = 1)
 {
 if ($stav==1):
   $this->testplat=1; // bude se testovat platnost cl.
 else:
   $this->testplat=0; // nebude se testovat
 endif;
 }

 function HlidatVydani($stav = 1)
 {
 if ($stav==0):
   $this->vydani=0; // na stavu clanku nezalezi
 else:
   $this->vydani=1; // clanek musi byt vydan
 endif;
 }

 function HlidatAktDatum($stav = 1)
 {
 if ($stav==0):
   $this->kontroladata=0; // nebude se testovat stari clanku
 else:
   $this->kontroladata=1; // datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 endif;
 }

 function HlidatLevel($stav = 1)
 {
 if ($stav==0):
   $this->kontrolalevel=0; // nebude se testovat level clanku a ctenare
 else:
   $this->kontrolalevel=1; // level ctenare musi byt vyssi nebo roven levelu clanku
 endif;
 }

 function NastavLevelCtenare($level = 0)
 {
 $this->ctenar_level=phprs_sql_escape_string($level); // nastaveni hodnoty levelu ctenare
 }

 function NastavZakazovouSab($stav = 1)
 {
 if ($stav==0):
   $this->zakazova_sablona=0; // nebude se pouzivat zakazova sablona
 else:
   $this->zakazova_sablona=1; // bude se pouzivat zakazova sablona
 endif;
 }

 function NastavHlStr($stav = 0)
 {
 if ($stav==0):
   $this->hlavni_stranka=0; // nejsem na hlavni str.
 else:
   $this->hlavni_stranka=1; // jsme na hlavni str.
 endif;
 }

 function PridatPhprsZnacku($nazev_znacky = '', $nazev_funkce = '')
 {
 // test na korektni vstup
 if (!empty($nazev_znacky)&&!empty($nazev_funkce)):
   // pridani phpRS znacky do pole
   $this->pole_phprs_znacky['vsechny_znacky'][]=$nazev_znacky;
   $this->pole_phprs_znacky[$nazev_znacky]['funkce_typ']='funkce';
   $this->pole_phprs_znacky[$nazev_znacky]['funkce_nazev']=$nazev_funkce;
 endif;
 }

 function Dekoduj($text = '')
 {
 if (!empty($text)&&!empty($this->pole_phprs_znacky['vsechny_znacky'])):
   // sestaveni prehledu testovanych znacek
   $seznam_test_znacek=implode('|',$this->pole_phprs_znacky['vsechny_znacky']);
   // sestaveni regularniho vyrazu
   $regularni_vyraz="/<(".$seznam_test_znacek.")([\"\w\s='\"]*)[\/]?".">/is";
   // test na existenci phpRS znacek v retezci
   if (preg_match_all($regularni_vyraz,$text,$vysl_pole_znacek)):
     $pocet_znacek=count($vysl_pole_znacek[0]);
     // print_r($vysl_pole_znacek); // ** LADENI **
     for ($znacka=0;$znacka<$pocet_znacek;$znacka++):
       // inic. vysledku
       $znacka_pole_atr=array();
       $znacka_nazev=$vysl_pole_znacek[1][$znacka]; // nazev znacky
       $vysl_preklad_znacky='';
       // test na existenci atributu znacky
       if (!empty($vysl_pole_znacek[2][$znacka])):
         // zpracovani atributu
         $atributy=strtolower(trim($vysl_pole_znacek[2][$znacka])); // atributy znacky
         $atributy=str_replace(array('"',"'"),array('',''),$atributy);
         // sestaveni zakladniho pole s atributy
         $pole_atr=explode(' ',$atributy);
         $pocet_atr=count($pole_atr);
         // sestaveni vysledkoveho pole
         for ($pom=0;$pom<$pocet_atr;$pom++):
           $casti_atr=explode('=',$pole_atr[$pom]);
           if (empty($casti_atr[1])): // hodnota atr. je prazdna nebo neexistuje
             $znacka_pole_atr[$pom]['atr']=$casti_atr[0];
             $znacka_pole_atr[$pom]['hodnota']='';
           else: // hodnota atr. existuje
             $znacka_pole_atr[$pom]['atr']=$casti_atr[0];
             $znacka_pole_atr[$pom]['hodnota']=$casti_atr[1];
           endif;
         endfor;
       endif;
       // volani prekladace phpRS znacky
       if ($this->pole_phprs_znacky[$znacka_nazev]['funkce_typ']=='funkce'):
         // volani PHP funkce
         $vysl_preklad_znacky=call_user_func($this->pole_phprs_znacky[$znacka_nazev]['funkce_nazev'],$znacka_pole_atr);
       elseif ($this->pole_phprs_znacky[$znacka_nazev]['funkce_typ']=='metoda'):
         // volani metody teto tridy
         $vysl_preklad_znacky=call_user_func(array('CClanek',$this->pole_phprs_znacky[$znacka_nazev]['funkce_nazev']),$znacka_pole_atr);
       else:
         // nelze urcit zpusob volani prekladace
         $vysl_preklad_znacky='<!-- znacku nelze prelozit -->';
       endif;
       // preklad znacky; vstupem je cela znacka, nahrazujici retezec a cely retezec
       $text=str_replace($vysl_pole_znacek[0][$znacka],$vysl_preklad_znacky,$text);
     endfor;
   endif;
 endif;

 return $text;
 }

 function DekodujZnackaObrazek($parametry = array())
 {
 $vysl=''; // inic. vysledku dekodovani znacky

 if (empty($parametry)):
   $vysl='<!-- nelze identifikovat obrazek -->';
 else:
   if (!is_array($parametry)):
     $vysl='<!-- chybi parametry obrazku -->';
   else:
     // inic.
     $pocet_parametru=count($parametry);
     $idobrazku=0;  // prednastaveni atr. id obrazku
     $zaobrazku='center'; // prednastaveni atr. zarovnani
     $nahled='ne';  // prednastaveni atr. nahled
     $externi='ne'; // prednastaveni atr. externi
     // zpracovani pole atributu
     for ($pom=0;$pom<$pocet_parametru;$pom++):
       switch($parametry[$pom]['atr']):
         case 'id': $idobrazku=phprs_sql_escape_string($parametry[$pom]['hodnota']); break;
         case 'zarovnani':
           switch($parametry[$pom]['hodnota']):
             case 'nastred': $zaobrazku='center'; break;
             case 'vlevo': $zaobrazku='left'; break;
             case 'vpravo': $zaobrazku='right'; break;
           endswitch;
           break;
         case 'externi':
           switch($parametry[$pom]['hodnota']):
             case 'ano': $externi='ano'; break;
             case 'ne': $externi='ne'; break;
           endswitch;
           break;
         case 'nahled': $nahled=$parametry[$pom]['hodnota']; break;
       endswitch;
     endfor;
     // dotaz na obrazek
     if($externi=='ano'):
       // data ziskana z externi galerie
       $dotazobr=phprs_sql_query("select media_id,media_caption as nazev,media_file as obr_poloha,media_width as obr_width,media_height as obr_height,media_thumbnail as nahl_poloha,media_thumbnail_width as nahl_width,media_thumbnail_height as nahl_height from ".$GLOBALS["rspredpona"]."media where media_id='".$idobrazku."'",$GLOBALS["dbspojeni"]);
       $pocetobr=phprs_sql_num_rows($dotazobr);
       if($dotazobr!==false&&$pocetobr>0):
         $pole_obrazek=phprs_sql_fetch_assoc($dotazobr);
         $odkaz_obrazek='gallery.php?akce=obrazek_ukaz&amp;media_id='.$pole_obrazek["media_id"];
       endif;
     else:
       // data ziskana z interni galerie
       $dotazobr=phprs_sql_query("select nazev,obr_poloha,obr_width,obr_height,nahl_poloha,nahl_width,nahl_height from ".$GLOBALS["rspredpona"]."imggal_obr where ido='".$idobrazku."'",$GLOBALS["dbspojeni"]);
       $pocetobr=phprs_sql_num_rows($dotazobr);
       if($dotazobr!==false&&$pocetobr>0):
         $pole_obrazek=phprs_sql_fetch_assoc($dotazobr);
         $odkaz_obrazek=$pole_obrazek["obr_poloha"];
       endif;
     endif;
     // zpracovani obrazku
     if ($pocetobr==1):
       if ($nahled=='ano'):
         // nahled
         if ($zaobrazku=='center'):
           $sestaveny_obr="<div align=\"center\"><a href=\"".$odkaz_obrazek."\" target=\"_blank\"><img src=\"".$pole_obrazek["nahl_poloha"]."\" width=\"".$pole_obrazek["nahl_width"]."\" height=\"".$pole_obrazek["nahl_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\"></a></div>";
         else:
           $sestaveny_obr="<a href=\"".$odkaz_obrazek."\" target=\"_blank\"><img src=\"".$pole_obrazek["nahl_poloha"]."\" align=\"".$zaobrazku."\" width=\"".$pole_obrazek["nahl_width"]."\" height=\"".$pole_obrazek["nahl_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\"></a>";
         endif;
       else:
         // bez nahledu
         if ($zaobrazku=='center'):
           $sestaveny_obr="<div align=\"center\"><img src=\"".$pole_obrazek["obr_poloha"]."\" width=\"".$pole_obrazek["obr_width"]."\" height=\"".$pole_obrazek["obr_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\"></div>";
         else:
           $sestaveny_obr="<img src=\"".$pole_obrazek["obr_poloha"]."\" align=\"".$zaobrazku."\" width=\"".$pole_obrazek["obr_width"]."\" height=\"".$pole_obrazek["obr_height"]."\" alt=\"".$pole_obrazek["nazev"]."\" title=\"".$pole_obrazek["nazev"]."\">";
         endif;
       endif;
       // vysledny HTML kod
       $vysl=$sestaveny_obr;
     else:
       // chyba; obrazek nelze urcit nebo nebyl nalezen
       $vysl='<!-- obrazek id '.$idobrazku.' nenalezen -->';
     endif;
   endif;
 endif;

 return $vysl;
 }

 function DekodujZnackaReklama($parametry = array())
 {
 $vysl=''; // inic. vysledku dekodovani znacky

 if (empty($parametry)):
   $vysl='<!-- nelze identifikovat reklamu -->';
 else:
   if (!is_array($parametry)):
     $vysl='<!-- chybi parametry reklamy -->';
   else:
     // inic.
     $pocet_parametru=count($parametry);
     $idreklama=0;  // prednastaveni atr. id reklama
     $typreklama=''; // prednastaveni atr. typ reklama
     for ($pom=0;$pom<$pocet_parametru;$pom++):
       switch($parametry[$pom]['atr']):
         case 'id': $idreklama=phprs_sql_escape_string($parametry[$pom]['hodnota']); break;
         case 'typ': $typreklama=phprs_sql_escape_string($parametry[$pom]['hodnota']); break;
       endswitch;
     endfor;
     // inic. vysledku
     $vysl='<!-- reklamni kod nenalezen -->';
     // kontrola platnosti ID
     if (!empty($idreklama)):
       // urceni typu reklamniho prvku
       switch ($typreklama):
         case 'banner': $vysl=Banners_prvek($idreklama); break;
         case 'kampan': $vysl=Banners_kampan($idreklama); break;
       endswitch;
     endif;
   endif;
 endif;

 return $vysl;
 }

 function NactiClanek($link = 0)
 {
 // test na platnost vstupu
 if (!empty($link)): // kdyz existuje link
   // bezpecnostni kontrola
   $link=phprs_sql_escape_string($link);

   // inic.
   $dotaz_where_pole=array();
   $dnesni_datum=date("Y-m-d H:i:s");

   // kontrola vydani (publikovani) clanku; test na visible
   if ($this->vydani==1):
     $dotaz_where_pole[]="c.visible=1";
   endif;
   // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
   if ($this->kontroladata==1):
     $dotaz_where_pole[]="c.datum<='".$dnesni_datum."'";
   endif;
   // level ctenare musi byt vyssi nebo roven levelu clanku; zaroven musi byt vypnute pouziti zakazove sablony
   if ($this->kontrolalevel==1&&$this->zakazova_sablona==0):
     $dotaz_where_pole[]="l.hodnota<='".$this->ctenar_level."'";
   endif;
   // aplikace omezeni zobrazeni dle data
   if ($this->testplat==1):
     $dotaz_where_pole[]="c.datum_pl>'".$dnesni_datum."'";
   endif;
   // test na hlavni stranku; u clanku lze povolit/zakazat zobrazeni na hl.str.
   if ($this->hlavni_stranka==1):
     $dotaz_where_pole[]="c.zobr_na_indexu=1";
   endif;
   // propojeni mezi tabulkami "rs_clanky" a "rs_levely"
   $dotaz_where_pole[]="c.level_clanku=l.idl";

   // zpracovani omezeni
   if (empty($dotaz_where_pole)):
     $dotaz_where='';
   else:
     $dotaz_where=' and '.implode(' and ',$dotaz_where_pole);
   endif;

   // dotaz
   $dotaz="select c.idc,c.link,c.seo_link,c.titulek,c.uvod,c.text,c.tema,date_format(c.datum,'%d. %m. %Y') as vyslden,c.autor,c.kom,c.visit,c.t_slova,";
   $dotaz.="c.visible,c.zdroj,c.skupina_cl,c.znacky,c.typ_clanku,c.sablona,c.level_clanku,c.anketa_cl,l.hodnota as level_hodnota ";
   $dotaz.="from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l ";
   $dotaz.="where c.link='".$link."'".$dotaz_where;

   $this->dotazclanek=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
   $this->pocetclanku=phprs_sql_num_rows($this->dotazclanek);
   if ($this->pocetclanku==1):
     // vse OK
     $this->clanek=phprs_sql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;

 return $this->aktivni;
 }

 function NactiClanky($mnozstvi = 0, $od = 0)
 {
 // test na mnozstvi polozek
 if ($mnozstvi>0): // kdyz je mnozstvi je vetsi nez 0
   // bezpecnostni kontrola
   $mnozstvi=phprs_sql_escape_string($mnozstvi);
   $od=phprs_sql_escape_string($od);

   // prednacteni pomocnych poli
   $this->NactiAutory();
   $this->NactiTemata();
   $this->NactiSab();
   // inic.
   $dotaz_where_pole=array();
   $dnesni_datum=Date("Y-m-d H:i:s");

   // kontrola vydani (publikovani) clanku; test na visible
   if ($this->vydani==1):
     $dotaz_where_pole[]="c.visible=1";
   endif;
   // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
   if ($this->kontroladata==1):
     $dotaz_where_pole[]="c.datum<='".$dnesni_datum."'";
   endif;
   // level ctenare musi byt vyssi nebo roven levelu clanku; zaroven musi byt vypnute pouziti zakazove sablony
   if ($this->kontrolalevel==1&&$this->zakazova_sablona==0):
     $dotaz_where_pole[]="l.hodnota<='".$this->ctenar_level."'";
   endif;
   // aplikace omezeni zobrazeni dle data
   if ($this->testplat==1):
     $dotaz_where_pole[]="c.datum_pl>'".$dnesni_datum."'";
   endif;
   // test na hlavni stranku; u clanku lze povolit/zakazat zobrazeni na hl.str.
   if ($this->hlavni_stranka==1):
     $dotaz_where_pole[]="c.zobr_na_indexu=1";
   endif;
   // propojeni mezi tabulkami "rs_clanky" a "rs_levely"
   $dotaz_where_pole[]="c.level_clanku=l.idl";

   // zpracovani omezeni
   if (empty($dotaz_where_pole)):
     $dotaz_where='';
   else:
     $dotaz_where=' where '.implode(' and ',$dotaz_where_pole);
   endif;

   // dotaz
   $dotaz="select c.idc,c.link,c.seo_link,c.titulek,c.uvod,c.text,c.tema,date_format(c.datum,'%d. %m. %Y') as vyslden,c.autor,c.kom,c.visit,c.t_slova,";
   $dotaz.="c.visible,c.zdroj,c.skupina_cl,c.znacky,c.typ_clanku,c.sablona,c.level_clanku,c.anketa_cl,l.hodnota as level_hodnota ";
   $dotaz.="from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l ";
   $dotaz.=$dotaz_where." order by c.priority desc,c.datum desc limit ".$od.",".$mnozstvi;

   $this->dotazclanek=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
   $this->pocetclanku=phprs_sql_num_rows($this->dotazclanek);
   if ($this->pocetclanku>0):
     // vse OK
     $this->clanek=phprs_sql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;

 return $this->aktivni;
 }

 function NactiZdrojCla($id_zdroj = 0)
 {
 if (is_resource($id_zdroj)):
   // prednacteni pomocnych poli
   $this->NactiAutory();
   $this->NactiTemata();
   $this->NactiSab();

   $this->dotazclanek=$id_zdroj;
   $this->pocetclanku=phprs_sql_num_rows($this->dotazclanek);
   if ($this->pocetclanku>0):
     // vse OK
     $this->clanek=phprs_sql_fetch_assoc($this->dotazclanek);
     $this->aktpozice=0;
     $this->aktivni=1; // trida je aktivni
   else:
     // chyba
     $this->aktivni=0; // trida je v neaktivnim stavu
   endif;
 endif;
 }

 function DalsiRadek()
 {
 if ($this->aktivni==1&&($this->aktpozice+1)<$this->pocetclanku): // pokud je trida aktivni a je fyzicky mozne prejit na dalsi existujici radek
   $this->aktpozice++;
   if (phprs_sql_data_seek($this->dotazclanek,$this->aktpozice)):
     // nacteni dalsiho radku dat
     $this->clanek=phprs_sql_fetch_assoc($this->dotazclanek);
   else:
     // chyba - zpet na puvodni hodnotu
     $this->aktpozice--;
   endif;
 endif;
 }

 function CelkemClanku()
 {
 // inic.
 $dotaz_where_pole=array();
 $dnesni_datum=Date("Y-m-d H:i:s");

 // kontrola vydani (publikovani) clanku; test na visible
 if ($this->vydani==1):
   $dotaz_where_pole[]="c.visible=1";
 endif;
 // test na stari clanku; datum publikovani clanku musi byt starsi nebo shodne s aktualnim datem
 if ($this->kontroladata==1):
   $dotaz_where_pole[]="c.datum<='".$dnesni_datum."'";
 endif;
 // level ctenare musi byt vyssi nebo roven levelu clanku; zaroven musi byt vypnute pouziti zakazove sablony
 if ($this->kontrolalevel==1&&$this->zakazova_sablona==0):
   $dotaz_where_pole[]="l.hodnota<='".$this->ctenar_level."'";
 endif;
 // aplikace omezeni zobrazeni dle data
 if ($this->testplat==1):
   $dotaz_where_pole[]="c.datum_pl>'".$dnesni_datum."'";
 endif;
 // test na hlavni stranku; u clanku lze povolit/zakazat zobrazeni na hl.str.
 if ($this->hlavni_stranka==1):
   $dotaz_where_pole[]="c.zobr_na_indexu=1";
 endif;
 // propojeni mezi tabulkami "rs_clanky" a "rs_levely"
 $dotaz_where_pole[]="c.level_clanku=l.idl";

 // zpracovani omezeni
 if (empty($dotaz_where_pole)):
   $dotaz_where='';
 else:
   $dotaz_where=' where '.implode(' and ',$dotaz_where_pole);
 endif;

 // dotaz
 $dotaz="select count(c.idc) as pocet from ".$GLOBALS["rspredpona"]."clanky as c, ".$GLOBALS["rspredpona"]."levely as l".$dotaz_where;
 $dotazcelkem=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
 if ($dotazcelkem!==false&&phprs_sql_num_rows($dotazcelkem)==1):
   // vse OK
   $pole_data=phprs_sql_fetch_assoc($dotazcelkem);
   return $pole_data['pocet'];
 else:
   // chyba
   return 0;
 endif;
 }

 function Ukaz($co = '')
 {
 // promenne "uvod" a "text" mohou pri nekterych nastaveni MySQL databaze vyzadovat jeste korekci funkci - stripslashes
 if ($this->aktivni==1): // kdyz je trida aktivni
  switch($co):
    case "pozice": return $this->aktpozice; break;
    case "pocetclanku": return $this->pocetclanku; break;
    case "idc": return $this->clanek["idc"]; break;
    case "link": return $this->clanek["link"]; break;
    case "link_seo": return $this->clanek["seo_link"]; break;
    case "titulek": return $this->clanek["titulek"]; break;
    case "uvod":
         if ($this->clanek["znacky"]==1): // kdyz jsou povoleny phpRS znacky
           return $this->Dekoduj($this->clanek["uvod"]);
         else:
           return $this->clanek["uvod"];
         endif;
         break;
    case "text":
         if ($this->clanek["znacky"]==1): // kdyz jsou povoleny phpRS znacky
           return $this->Dekoduj($this->clanek["text"]);
         else:
           return $this->clanek["text"];
         endif;
         break;
    case "tema_id": return $this->clanek["tema"]; break;
    case "tema_jm": return $this->ZjistiTema($this->clanek["tema"],"jm"); break;
    case "tema_obr": return $this->ZjistiTema($this->clanek["tema"],"obr"); break;
    case "datum": return $this->clanek["vyslden"]; break;
    case "autor_id": return $this->clanek["autor"]; break;
    case "autor_jm": return $this->ZjistiAutora($this->clanek["autor"],"jm"); break;
    case "autor_mail": return "mailto:".$this->ZjistiAutora($this->clanek["autor"],"mail"); break;
    case "autor_jen_mail": return $this->ZjistiAutora($this->clanek["autor"],"mail"); break;
    case "autor_im": return $this->ZjistiAutora($this->clanek["autor"],"im"); break;
    case "pocet_kom": return $this->clanek["kom"]; break;
    case "visit": return $this->clanek["visit"]; break;
    case "visit_plus": return ($this->clanek["visit"]+1); break;
    case "slovni_popis": return $this->clanek["t_slova"]; break;
    case "visible": return $this->clanek["visible"]; break;
    case "zdroj": return $this->clanek["zdroj"]; break;
    case "skupina": return $this->clanek["skupina_cl"]; break;
    case "znacky": return $this->clanek["znacky"]; break;
    case "typ_clanku": return $this->clanek["typ_clanku"]; break; // 1 - standardni, 2 - kratky
    case "sablona": return $this->ZjistiSab($this->clanek["sablona"]); break;
    case "zakazova_sab":
         if ($this->kontrolalevel==1&&$this->zakazova_sablona==1): // kdyz je povolena zakazova sablona
           if ($this->clanek["level_hodnota"]<=$this->ctenar_level): return 0; else: return 1; endif; // test na platnost clanku
         else:
           return 0;
         endif;
         break;
    case "level": return $this->clanek["level_hodnota"]; break;
    case "anketa": return $this->clanek["anketa_cl"]; break;
    default: return ''; // neznamy dotaz
  endswitch;
 endif;
 }
}

?>