<?php

######################################################################
# phpRS Admin Standard Mail library 1.1.6
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  -- class --
  CPosta
*/

// ====================== CLASS

// trida CPosta
class CPosta
{
var $hlavicka; // hlavicka mailu
var $obsah; // obsah mailu
var $adresat; // adresat mailu
var $skryty_adresat; // skyty adresat
var $predmet; // predmet mailu
var $odesilatel_mail; // odesilatel mailu
var $odesilatel_txt; // odesilatel text
var $seznam_ctenaru; // seznam ctenaru
var $stav_seznam_ctenaru; // stav seznamu ctenaru
var $kodovani_dat=''; // vychozi kodovani zpracovavaneho obsahu
var $zpusob_odeslani='vse'; // zpusob rozeslani e-mailu: vse|sady
var $velikost_jedne_sady=40; // mnozstvi e-mailu zapouzdrenych do jedne e-mailove sady

 /*
   CPosta()
   Reset()
   Nastav($co = '', $hodnota = '')
   TestNaMailAdr($mail = '')
   NactiCtenare()
   NastavInfoMail();
   win1250_to_ascii($str = '')
   win1250_to_iso88592($str = '')
   Odesilac()
 */

 public function __construct()
 {
 $this->Reset();
 }

 function Reset() // reset internich promenych
 {
 $this->hlavicka='';
 $this->obsah='';
 $this->adresat='';
 $this->skryty_adresat='';
 $this->predmet='';
 $this->odesilatel_mail=$GLOBALS['redakceadr'];
 $this->odesilatel_txt=$GLOBALS['wwwname'];
 $this->seznam_ctenaru='';
 $this->stav_seznam_ctenaru=0;
 // inic. kodovani
 if (empty($GLOBALS['rsconfig']['kodovani'])):
   $this->kodovani_dat='windows-1250'; // nelze nalezt centralni nastaveni
 else:
   $this->kodovani_dat=$GLOBALS['rsconfig']['kodovani']; // pouziti centralniho nastaveni
 endif;
 }

 function Nastav($co = '', $hodnota = '') // nastaveni promennych
 {
 switch($co):
   case "hlavicka": $this->hlavicka=$hodnota; break;
   case "obsah": $this->obsah=$hodnota; break;
   case "adresat": $this->adresat=$hodnota; break;
   case "skryta_kopie": $this->skryty_adresat=$hodnota; break;
   case "predmet": $this->predmet=$hodnota; break;
   case "odesilatel_mail": $this->odesilatel_mail=$hodnota; break;
   case "odesilatel_txt": $this->odesilatel_txt=$hodnota; break;
   case "zpusob_odesilani": $this->NastavZpusobOdesilani($hodnota); break;
   case "velikost_sady": $this->velikost_jedne_sady=$hodnota; break;
 endswitch;
 }

 function NastavZpusobOdesilani($typ = '') // nastaveni zpusobu odesilani e-mailu
 {
 switch (strtolower($typ)):
   case 'vse': $this->zpusob_odeslani='vse'; break;
   case 'sady': $this->zpusob_odeslani='sady'; break;
   default: $this->zpusob_odeslani='vse'; break;
 endswitch;
 }

 function TestNaMailAdr($mail = '') // test na platnost zadaneho e-mailu
 {
 if (preg_match('|^[_a-zA-Z0-9\.\-]+@[_a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,4}$|',$mail)):
   return 1; // spravna struktura
 else:
   return 0; // chybna struktura
 endif;
 }

 function NactiCtenare() // nacteni seznamu ctenaru
 {
 // test na aktivnost seznamu ctenaru
 if ($this->stav_seznam_ctenaru==0):
   $dotazmail=phprs_sql_query("select email from ".$GLOBALS["rspredpona"]."ctenari where info='1'",$GLOBALS["dbspojeni"]);
   $pocetmail=phprs_sql_num_rows($dotazmail);

   $pom_str='';
   $pom_spojka='';

   while($pole_data = phprs_sql_fetch_assoc($dotazmail)):
      if ($this->TestNaMailAdr($pole_data['email'])): // test na korektnost adresy
        $pom_str.=$pom_spojka.$pole_data['email'];
        $pom_spojka=',';
      endif;
   endwhile;

   $this->seznam_ctenaru=$pom_str; // ulozeni vysledku
   $this->stav_seznam_ctenaru=1; // stav seznamu ctenaru = true
 endif;
 }

 function NastavInfoMail() // nastaveni defaultniho stavu redakcniho info e-mailu
 {
 $this->NactiCtenare();
 $this->skryty_adresat=$this->seznam_ctenaru; // prednastaveni skryteho adresata
 $this->adresat=$GLOBALS['infoadr']; // prednastaveni adresata
 }

 function win1250_to_ascii($str = '') // prekodovani z Win-1250 do ASCII
 {
 $diak ="ěščřžýáíéťňďúůóöüĚŠČŘŽÝÁÍÉŤŇĎÚŮÓÖÜ";
 $diak.="\x97\x96\x91\x92\x84\x93\x94\xAB\xBB";
 $ascii="escrzyaietnduuoouESCRZYAIETNDUUOOU";
 $ascii.="\x2D\x2D\x27\x27\x22\x22\x22\x22\x22";
 return StrTr($str,$diak,$ascii);
 }

 function win1250_to_iso88592($str = '') // prekodovani z Win-1250 do ISO-8859-2
 {
 // minimalni prekodovani
 // return StrTr($str,"\x8A\x8D\x8E\x9A\x9D\x9E","\xA9\xAB\xAE\xB9\xBB\xBE");
 // rozsirene prekodovani - rozsireno o prekodovani "Windows uvozovek"
 return StrTr($str,"\x8A\x8D\x8E\x9A\x9D\x9E\x91\x92\x82\x93\x94\x84","\xA9\xAB\xAE\xB9\xBB\xBE\x27\x27\x27\x22\x22\x22");
 }

 function Odesilac() // hlavni odesilaci funkce
 {
 if ($this->zpusob_odeslani=='sady'):
   return $this->Odesilac_sady(); // varianta: sady
 else:
   return $this->Odesilac_vse(); // varianta: vse
 endif;
 }

 function Odesilac_vse() // odesilac e-mailu - varianta "vse v jednom e-mailu"
 {
 $chyba=0; // inic. chyba
 $konec_radku_hlavicka="\n"; // zakonceni radku v hlavicce

 // uprava definice kodovani
 $this->kodovani_dat=strtolower(trim($this->kodovani_dat));

 // obsah
 switch ($this->kodovani_dat): // test na kodovani
   case 'windows-1250': $probsah=chunk_split(base64_encode($this->win1250_to_iso88592($this->obsah))); break;
   case 'iso-8859-2': $probsah=chunk_split(base64_encode($this->obsah)); break;
   default: $probsah=chunk_split(base64_encode($this->obsah)); break;
 endswitch;

 // predmet
 $prpredmet='';
 if ($this->predmet==''):
   // chyba: predmet je prazdny
   $chyba=1;
 else:
   // zpracovani predmetu
   switch ($this->kodovani_dat): // test na kodovani
     case 'windows-1250': $prpredmet .='=?ISO-8859-2?B?'.base64_encode($this->win1250_to_iso88592($this->predmet)).'?='; break;
     case 'iso-8859-2': $prpredmet .='=?ISO-8859-2?B?'.base64_encode($this->predmet).'?='; break;
     default: $prpredmet .='=?'.$this->kodovani_dat.'?B?'.base64_encode($this->predmet).'?='; break;
   endswitch;
 endif;

 // hlavicka
 $prhlavicka='';
 // hlavicka - mail odesilatele
 if (!empty($this->odesilatel_mail)):
   $prhlavicka .='From: ';
   if (!empty($this->odesilatel_txt)): // textovy popis odesilatele
     switch ($this->kodovani_dat): // test na kodovani
       case 'windows-1250': $prhlavicka .='=?ISO-8859-2?B?'.base64_encode($this->win1250_to_iso88592($this->odesilatel_txt)).'?= '; break;
       case 'iso-8859-2': $prhlavicka .='=?ISO-8859-2?B?'.base64_encode($this->odesilatel_txt).'?= '; break;
       default: $prhlavicka .='=?'.$this->kodovani_dat.'?B?'.base64_encode($this->odesilatel_txt).'?= '; break;
     endswitch;
   endif;
   $prhlavicka .='<'.$this->odesilatel_mail.'>'.$konec_radku_hlavicka; // mail odesilatele
 endif;
 // hlavicka - skryta adresa
 if (!empty($this->skryty_adresat)):
   $prhlavicka .='Bcc: '.$this->skryty_adresat.$konec_radku_hlavicka;
 endif;
 // hlavicka - MIME format
 $prhlavicka .='MIME-Version: 1.0'.$konec_radku_hlavicka;
 // hlavicka - urceni znakove sady
 switch ($this->kodovani_dat): // test na kodovani
   case 'windows-1250': $prhlavicka .='Content-Type: text/plain; charset="iso-8859-2"'.$konec_radku_hlavicka; break;
   case 'iso-8859-2': $prhlavicka .='Content-Type: text/plain; charset="iso-8859-2"'.$konec_radku_hlavicka; break;
   default: $prhlavicka .='Content-Type: text/plain; charset="'.$this->kodovani_dat.'"'.$konec_radku_hlavicka; break;
 endswitch;
 // hlavicka - urceni kodovani
 $prhlavicka .='Content-Transfer-Encoding: base64'.$konec_radku_hlavicka;

 // adresat
 if ($this->adresat==''):
   // chyba: adresat je prazdny
   $chyba=1;
 endif;

 // odeslani e-mailu + vysledek
 if ($chyba==0):
   if (Mail($this->adresat,$prpredmet,$probsah,$prhlavicka)):
     return 1; // vse OK
   else:
     return 0; // chyba
   endif;
 else:
   return 0; // chyba
 endif;
 }

 function Odesilac_sady() // odesilac e-mailu - varianta "rozdeleni do e-mailovych sad"
 {
 $chyba=0; // inic. chyba
 $chyba_mail=0; // inic. globalni chby
 $konec_radku_hlavicka="\n"; // zakonceni radku v hlavicce

 // dekompilace skrytych adres
 if (empty($this->skryty_adresat)):
   $pocet_skrytych_adres=0;
 else:
   $pole_skrytych_adres=explode(',',$this->skryty_adresat);
   $pocet_skrytych_adres=count($pole_skrytych_adres);
 endif;

 if ($pocet_skrytych_adres<=$this->velikost_jedne_sady): // start - test na mnozstvi skrytych adres

   // s ohledem na male mnozstvi skrytych adres (velikosti odpovida pouze jedne sade), bude pro odeslani pouzita varianta "vse v jednom e-mailu"
   return $this->Odesilac_vse(); // stavove hlaseni se kompletne prebira z pouzite funkce

 else: // stred - test na mnozstvi skrytych adres

   // vypocet mnozstvi sad
   $pocet_emailovych_sad=ceil($pocet_skrytych_adres/$this->velikost_jedne_sady);

   // vytovreni sad
   $pole_emailove_sady=array();
   for ($pom=0;$pom<$pocet_emailovych_sad;$pom++):
     $akt_pozice=($pom*$this->velikost_jedne_sady);
     $pole_emailove_sady[$pom]=array_slice($pole_skrytych_adres,$akt_pozice,$this->velikost_jedne_sady); // vykopirovani casti pole skrytych adres do pole e-mailove sady
   endfor;

   // uprava definice kodovani
   $this->kodovani_dat=strtolower(trim($this->kodovani_dat));

   // obsah
   switch ($this->kodovani_dat): // test na kodovani
     case 'windows-1250': $probsah=chunk_split(base64_encode($this->win1250_to_iso88592($this->obsah))); break;
     case 'iso-8859-2': $probsah=chunk_split(base64_encode($this->obsah)); break;
     default: $probsah=chunk_split(base64_encode($this->obsah)); break;
   endswitch;

   // predmet
   $prpredmet='';
   if ($this->predmet==''):
     // chyba: predmet je prazdny
     $chyba=1;
   else:
     // zpracovani predmetu
     switch ($this->kodovani_dat): // test na kodovani
       case 'windows-1250': $prpredmet .='=?ISO-8859-2?B?'.base64_encode($this->win1250_to_iso88592($this->predmet)).'?='; break;
       case 'iso-8859-2': $prpredmet .='=?ISO-8859-2?B?'.base64_encode($this->predmet).'?='; break;
       default: $prpredmet .='=?'.$this->kodovani_dat.'?B?'.base64_encode($this->predmet).'?='; break;
     endswitch;
   endif;

   // tvorba e-mailovych sad
   for ($pom=0;$pom<$pocet_emailovych_sad;$pom++): // start - e-mailova sada

     // hlavicka
     $prhlavicka='';
     // hlavicka - mail odesilatele
     if (!empty($this->odesilatel_mail)):
       $prhlavicka .='From: ';
       if (!empty($this->odesilatel_txt)): // textovy popis odesilatele
         switch ($this->kodovani_dat): // test na kodovani
           case 'windows-1250': $prhlavicka .='=?ISO-8859-2?B?'.base64_encode($this->win1250_to_iso88592($this->odesilatel_txt)).'?= '; break;
           case 'iso-8859-2': $prhlavicka .='=?ISO-8859-2?B?'.base64_encode($this->odesilatel_txt).'?= '; break;
           default: $prhlavicka .='=?'.$this->kodovani_dat.'?B?'.base64_encode($this->odesilatel_txt).'?= '; break;
         endswitch;
       endif;
       $prhlavicka .='<'.$this->odesilatel_mail.'>'.$konec_radku_hlavicka; // mail odesilatele
     endif;
     // hlavicka - skryta adresa
     if (!empty($pole_emailove_sady[$pom])):
       $prhlavicka .='Bcc: '.implode(',',$pole_emailove_sady[$pom]).$konec_radku_hlavicka;
     endif;
     // hlavicka - MIME format
     $prhlavicka .='MIME-Version: 1.0'.$konec_radku_hlavicka;
     // hlavicka - urceni znakove sady
     switch ($this->kodovani_dat): // test na kodovani
       case 'windows-1250': $prhlavicka .='Content-Type: text/plain; charset="iso-8859-2"'.$konec_radku_hlavicka; break;
       case 'iso-8859-2': $prhlavicka .='Content-Type: text/plain; charset="iso-8859-2"'.$konec_radku_hlavicka; break;
       default: $prhlavicka .='Content-Type: text/plain; charset="'.$this->kodovani_dat.'"'.$konec_radku_hlavicka; break;
     endswitch;
     // hlavicka - urceni kodovani
     $prhlavicka .='Content-Transfer-Encoding: base64'.$konec_radku_hlavicka;

     // adresat
     if ($this->adresat==''):
       // chyba: adresat je prazdny
       $chyba=1;
     endif;

     // odeslani e-mailu
     if ($chyba==0):
       if (Mail($this->adresat,$prpredmet,$probsah,$prhlavicka)==0):
         $chyba_mail=1; // chyba
       endif;
     else:
       $chyba_mail=1; // chyba
     endif;

   endfor; // konec - e-mailova sada

   // finalni vysledek
   if ($chyba==0&&$chyba_mail==0):
     return 1; // vse OK
   else:
     return 0; // chyba
   endif;

 endif; // konec - test na mnozstvi skrytych adres
 }
}

?>