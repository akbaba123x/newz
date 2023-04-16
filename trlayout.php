<?php

######################################################################
# phpRS ClassLayout 2.1.3
######################################################################

// Copyright (c) 2001-2014 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// NactiPHPSablonu - sablona moze obsahovat php
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_sloupce, rs_bloky, rs_plugin, rs_levely

/*
   Definice Layout tridy v2, ktera slouzi ke kompilaci sablony v kombinaci se vsemi preddefinovanych info. sloupci a jejich naslednemu zobrazeni.

   *** Inic. cast: ***

   $text = "sablona";

   $vzhledwebu = new CLayout();
   $vzhledwebu->NactiTxtSablonu($text);
   $vzhledwebu->UlozPro("titulek","Welcome to the my home page");
   $vzhledwebu->Inic();

   *** Generujici/kompilacni faze: ***

   $vzhledwebu->Generuj();
   ... obsah hlavniho info. bloku - tento krok lze preskocit pouzitim fce UlozHlavniBlok(...) v inic. casti pro preddefinovani obsahu hl. info. bloku ...
   $vzhledwebu->Generuj();
*/

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

class CLayout
{
var $obsah_sab; // obsah sablony
var $nalez_prom; // nalezene promenne v sablone
var $poc_nalez_prom; // pocet nalezenych promennych
var $pole_prom; // pole znamych ulozenych promennych
var $sloupec; // informace o sloupcich
var $poc_sloupcu; // pocet sloupcu
var $hlavnistranka; // prepinac hlavni stranka
var $hlavniblok; // obsah hlavniho bloku
var $bloknazev,$bloktyp; // spec. promenne, ktere nesou akt. hodnoty prave nacteneho bloku; vyuziti u plug-inu
var $stopfce_stav,$stopfce_sl,$stopfce_blok,$stopfce_iddotaz,$stopfce_pocetdotaz; // // spec. promenne, ktere umoznuji zastavit generovani vzhledove tabulky
var $cela_sab; // informace o zprac. cele sablony
var $ctenar_level,$testovat_level; // akt. level ctenare; zapnuti/vypnuti testovani levelu

/*
  CLayout()
  NactiTxtSablonu($obs_sab = "")
  NactiFileSablonu($jmeno_souboru = "")
  NajdiPro()
  UlozPro($jmeno = "", $obsah = "")
  UlozHlavniBlok($vstup = "")
  NactiSloupce()
  VratIdSloupce($sloupec_cislo = 0)
  AktBlokNazev()
  AktBlokTyp()
  GenerujSloupecStopFce($idsloupce = 0)
  Preklad()
  Inic()
  Generuj()
*/

 public function __construct($hlstr = 0) // kontruktor
 {
 $this->obsah_sab='';
 $this->nalez_prom = array(); // 0 - string, 1 - typ stringu (0 - obycejny, 1 - promenna)
 $this->poc_nalez_prom=0;
 $this->pole_prom = array(); // 0 - jmeno, 1 - obsah
 $this->poc_sloupcu=0;
 $this->sloupec = array(); // 0 - id sloupce, 1 - zobrazit obsah (0 - ne / 1 - ano)
 $this->hlavnistranka=$hlstr;
 $this->hlavniblok='';
 $this->cela_sab=0;
 $this->InicZpracovaniLevelu(); // nacteni nastaveni ctenarskeho pristupoveho subsystemu
 }

 function InicZpracovaniLevelu()
 {
 if (isset($GLOBALS["prmyctenar"])):
   $this->ctenar_level=$GLOBALS["prmyctenar"]->UkazLevel(); // akt. nastaveni detekovaneho ctenare
 else:
   $this->ctenar_level=0;
 endif;
 $this->testovat_level=NactiConfigProm('hlidat_level',0); // nacteni konfigurace systemu - testovani levelu
 }

 function NactiTxtSablonu($obs_sab = "") // nacteni sablony z retezce
 {
 // ulozeni sablony
 $this->obsah_sab=$obs_sab;
 }
 
 function NactiPHPSablonu($jmeno_souboru = "") {
	if (file_exists($jmeno_souboru)==1) {
		ob_start();
		include($jmeno_souboru);
		$template_content = ob_get_contents();
		ob_end_clean();
	
		// prevod sablony do pola
		$obssoubor = explode("\n",$template_content);
		$pocetobs=count($obssoubor);
		for($pom=0;$pom<$pocetobs;$pom++) {
			$this->obsah_sab.=$obssoubor[$pom];
		}
	}
 } 
 function NactiFileSablonu($jmeno_souboru = "") // nacteni sablony ze souboru
 {
 // test na existenci souboru
 if (file_exists($jmeno_souboru)==1):
   // nacteni sablony do pole
   $obssoubor=file($jmeno_souboru);
   $pocetobs=count($obssoubor);
   for($pom=0;$pom<$pocetobs;$pom++):
     $this->obsah_sab.=$obssoubor[$pom];
   endfor;
 endif;
 }

 function NajdiPro() // dekompilace sablony; zjisteni promennych
 {
 $pom_pole=explode("<*",$this->obsah_sab);
 $poc_pom_pole=count($pom_pole);

 for ($pom=0;$pom<$poc_pom_pole;$pom++):
   $end_poz=strpos($pom_pole[$pom],"*>");
   // test na existenci ukoncovaciho znaku
   if ($end_poz==0||$end_poz>20):
     $this->nalez_prom[$this->poc_nalez_prom][0]=$pom_pole[$pom];
     $this->nalez_prom[$this->poc_nalez_prom][1]=0;
     $this->poc_nalez_prom++;
   else:
     $delka=mb_strlen($pom_pole[$pom]); // delka stringu
     // promenna
     $this->nalez_prom[$this->poc_nalez_prom][0]=strtolower(mb_substr($pom_pole[$pom],0,$end_poz));
     $this->nalez_prom[$this->poc_nalez_prom][1]=1;
     $this->poc_nalez_prom++;
     // zbytek stringu
     $this->nalez_prom[$this->poc_nalez_prom][0]=mb_substr($pom_pole[$pom],$end_poz+2,$delka-$end_poz-2);
     $this->nalez_prom[$this->poc_nalez_prom][1]=0;
     $this->poc_nalez_prom++;
   endif;
 endfor;
 }

 function UlozPro($jmeno = "", $obsah = "") // ulozeni znamych promennych do pameti
 {
 $jmeno=strtolower(trim($jmeno));
 if ($jmeno!=''):
   $this->pole_prom[$jmeno]=$obsah; // ulozeni promenne do pameti
 endif;
 }

 function UlozHlavniBlok($vstup = "") // ulozeni obsahu hlavniho bloku do pameti
 {
 $this->hlavniblok=$vstup;
 }

 function NactiSloupce() // nacteni sloupcu do pameti
 {
 $dotazslo=phprs_sql_query("select ids,zobrazit from ".$GLOBALS["rspredpona"]."sloupce order by ids",$GLOBALS["dbspojeni"]);
 $this->poc_sloupcu=phprs_sql_num_rows($dotazslo);

 for ($pom=0;$pom<$this->poc_sloupcu;$pom++):
   $pole_data=phprs_sql_fetch_assoc($dotazslo);
   $this->sloupce[$pom][0]=$pole_data['ids'];
   $this->sloupce[$pom][1]=$pole_data['zobrazit'];
 endfor;
 }

 function VratIdSloupce($sloupec_cislo = 0) // prepocita logicke poradove oznaceni sloupce na platne ID
 {
 $vysl=0;

 if ($sloupec_cislo>0&&$this->poc_sloupcu>=$sloupec_cislo):
   $zaznam_cislo=$sloupec_cislo-1;
   if ($this->sloupce[$zaznam_cislo][1]==1):
     $vysl=$this->sloupce[$zaznam_cislo][0];
   endif;
 endif;

 return $vysl;
 }

 function AktBlokNazev() // vraci aktulaniho nazvu prave nacteneho bloku
 {
 return $this->bloknazev;
 }

 function AktBlokTyp() // vraci aktulaniho typu prave nacteneho bloku
 {
 return $this->bloktyp;
 }

 function GenerujSloupecStopFce($idsloupce = 0) // generator jednotlivych sloupcu
 {
 if ($this->stopfce_stav==0): // tato podminka zajisti pouze jedno nacteni; pokud je aktivni fce stop, tak se jiz znovu nenacita
   // sestaveni podminky zobrazeni (zobrazit_kde: 0 = vsude, 1 = jen hl. str., 2 = vsude mimo hl. str.)
   if ($this->hlavnistranka): // hl. str. = true
     $dotaz_where=" and b.zobrazit_kde!=2"; // zobrazit: vsude + jen na hl. str.
   else: // hl. str. = false
     $dotaz_where=" and b.zobrazit_kde!=1"; // zobrazit: vsude + vsude mimo hl. str.
   endif;
   // test na podminku kontroly platnosti levelu
   if ($this->testovat_level==1):
     // testuje se s ciselnou hodnotou ulozenou v tabulce "rs_levely"
     $dotaz_where.=" and l.hodnota<='".$this->ctenar_level."'"; // level ctenare musi byt vyssi nebo roven levelu bloku
   endif;

   $this->stopfce_blok=0; // vynulovani hlidace pozice

   // nacteni bloku odpovidajicich podmince
   $dotaz="select b.nazev,b.obsah,b.typ,b.data_sys,b.sys_funkce from ".$GLOBALS["rspredpona"]."bloky as b,".$GLOBALS["rspredpona"]."levely as l ";
   $dotaz.="where b.id_sloupec='".$idsloupce."' and b.zobrazit=1 and b.level_blok=l.idl".$dotaz_where." order by b.hodnost desc,b.idb";
   $this->stopfce_iddotaz=phprs_sql_query($dotaz,$GLOBALS["dbspojeni"]);
   $this->stopfce_pocetdotaz=phprs_sql_num_rows($this->stopfce_iddotaz);
 else:
   $this->stopfce_stav=0; // vrati stav na false, aby dalsi prubeh touto funkci mohl byt kompletni
 endif;

 if($this->stopfce_pocetdotaz>0):
   for ($pom=$this->stopfce_blok;$pom<$this->stopfce_pocetdotaz;$pom++):
     list($blonazev,$bloobsah,$blotyp,$blodatasys,$blosysfunkce)=phprs_sql_fetch_row($this->stopfce_iddotaz);
     if ($blodatasys==0):
       // datove bloky
       switch($blotyp):
           case 1: Blok1($blonazev,$bloobsah);  break;
           case 2: Blok2($blonazev,$bloobsah);  break;
           case 3: Blok3($blonazev,$bloobsah);  break;
           case 4: Blok4($blonazev,$bloobsah);  break;
           case 5: Blok5($blonazev,$bloobsah);  break;
       endswitch;
     else:
       // ulozeni nazvu a typu bloku do pameti
       $this->bloknazev=$blonazev;
       $this->bloktyp=$blotyp;
       // systemove bloky
       switch($blosysfunkce): // ank, nov, rub, kal, hlb
           case 'ank': Anketa(); break;
           case 'nov': HotNews(); break;
           case 'rub': GenHlavMenu(); break;
           case 'kal': Kalendar(); break;
           case 'hlb': if ($this->hlavniblok==''):
                         $this->stopfce_stav=1; // aktivace stop fce
                         $this->stopfce_blok=($pom+1); // ulozeni navratove pozice v MySQL dotazu
                       else:
                         echo $this->hlavniblok;
                       endif;
                       break;
           default:
             $dotazplug=phprs_sql_query("select inclsb_blok,funkce_blok from ".$GLOBALS["rspredpona"]."plugin where zkratka_blok='".$blosysfunkce."' and sys_blok='1'",$GLOBALS["dbspojeni"]);
             if ($dotazplug!==false&&phprs_sql_num_rows($dotazplug)>0): // nasel se odpovidajici plug-in
               $pole_akt_plug=phprs_sql_fetch_assoc($dotazplug);
               include_once($pole_akt_plug['inclsb_blok']);
               call_user_func($pole_akt_plug['funkce_blok']);
             endif;
             break;
       endswitch;
     endif;

     if ($this->stopfce_stav==1): break; endif; // kdyz je aktivovana stop fce, tak ukonci beh for cyklu
   endfor; // konec $pocteslo

   $this->bloknazev='';
   $this->bloktyp='';
 endif; // konec $pocetslo
 }

 function Preklad() // preklad sablony do finalni podoby
 {
 for ($pom=$this->stopfce_preklad;$pom<$this->poc_nalez_prom;$pom++):
   if ($this->nalez_prom[$pom][1]==0):
     // obycejny string
     echo $this->nalez_prom[$pom][0];
   else:
     // promenna
     if (mb_substr_count($this->nalez_prom[$pom][0],"syssl")==1): // test na "systemovou promennou - sloupec"
       $sys_sloupec=explode(":",$this->nalez_prom[$pom][0]); // 0 - identifikace (syssl), 1 - cislo sloupce
       $this->GenerujSloupecStopFce($this->VratIdSloupce($sys_sloupec[1])); // poradove cislo sloupce musi byt skrze fci VratIdSloupce() prelozeno na platne ID
       // test na stop stav
       if ($this->stopfce_stav==1):
         $this->stopfce_preklad=$pom;
         break;
       endif;
     else:
       // test na odpovidajici "beznou promennou"
       if (isset($this->pole_prom[$this->nalez_prom[$pom][0]])):
         echo $this->pole_prom[$this->nalez_prom[$pom][0]];
       endif;
     endif;
   endif;
 endfor;
 // test na probehnuti cele sablony
 if ($pom>=$this->poc_nalez_prom):
   $this->cela_sab=1;
 endif;
 }

 function Inic()
 {
 // prednacteni a zprac. potrebnych dat
 $this->NajdiPro();
 $this->NactiSloupce();
 // inic. inter. promennych
 $this->bloknazev="";
 $this->bloktyp="";
 // inic. stop fce
 $this->stopfce_stav=0; // stav stop fce
 $this->stopfce_sl=0; // akt. sloupec
 $this->stopfce_blok=0; // akt. blok cekajici na zpracovani
 $this->stopfce_iddotaz=0; // id MySQL dotazu
 $this->stopfce_pocetdotaz=0; // pocet radku ziskanych behem MySQL dotazu
 $this->stopfce_preklad=0; // akt. pozice v prekladu sablony
 }

 function Generuj()
 {
 // test na uplnost dokonceni prekladu sablony; zabranuje vice-nasobnemu volani
 if ($this->cela_sab==0):
   $this->Preklad();
 endif;
 }
}

?>