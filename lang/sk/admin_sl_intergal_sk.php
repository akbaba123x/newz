<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "intergal" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_IGA_ROZ_SHOW_GAL','Interná galéria obrázkov');
define('RS_IGA_ROZ_ADD_GAL','Pridanie novej galérie');
define('RS_IGA_ROZ_EDIT_GAL','Úprava nastavení galérie');
define('RS_IGA_ROZ_DEL_GAL','Vymazanie galérie');
define('RS_IGA_ROZ_OPEN_GAL','Prezeranie galérie');
define('RS_IGA_ROZ_ADD_OBR','Pridanie nového obrázku');
define('RS_IGA_ROZ_DEL_OBR','Vymazanie obrázku');
define('RS_IGA_ROZ_EDIT_OBR','Úprava obrázku');
define('RS_IGA_ROZ_','Pridanie nového obrázku');
// hlavni fce - sprava galerii
define('RS_IGA_SG_ZPET','Späť na hlavnú stránku sekcie');
define('RS_IGA_SG_PRIDAT_GAL','Pridať novú galériu');
define('RS_IGA_SG_NAZEV_GAL','Názov galérie');
define('RS_IGA_SG_VLASTNIK','Vlastník');
define('RS_IGA_SG_AKCE','Akcia');
define('RS_IGA_SG_SMAZ','Zmaž<br />galériu');
define('RS_IGA_SG_ZADNA_GAL','Nebola nájdená žiadna galéria!');
define('RS_IGA_SG_UPRAVIT','Edituj');
define('RS_IGA_SG_OTEVRIT','Otvor galériu');
define('RS_IGA_SG_SMAZ_OZNAC','Vymaž všetky označené galérie');
define('RS_IGA_SG_UPOZORNENI','<b>Upozornenie:</b> Galériu môže vymazaľ len majiteľ alebo administrátor systému, a to za predpokladu, že je prázdna!');
define('RS_IGA_SG_FORM_NAZEV_GAL','Názov galérie');
define('RS_IGA_SG_FORM_POPIS','Popis');
define('RS_IGA_SG_FORM_PRAVA','Prístupové práva iných užívateľov:');
define('RS_IGA_SG_FORM_PRAVA_PROHLIZENI','Prezeranie galérie');
define('RS_IGA_SG_FORM_PRAVA_ZAPIS','Zápis do galérie');
define('RS_IGA_SG_FORM_PRAVA_MAZANI','Mazanie v galérii');
define('RS_IGA_SG_OK_ADD_GAL','Akcia prebehla úspešne! Galéria bola pridaná.');
define('RS_IGA_SG_OK_EDIT_GAL','Akcia prebehla úspešne! Galéria bola aktualizovaná.');
define('RS_IGA_SG_OK_DEL_GAL_C1','Galéria');
define('RS_IGA_SG_OK_DEL_GAL_C2','bola vymazaná!');
define('RS_IGA_SG_ERR_DEL_POCET_NULA','Neoznačili ste ani jednu galériu!');
define('RS_IGA_SG_ERR_NENI_PRAZDNA_C1','Galériu');
define('RS_IGA_SG_ERR_NENI_PRAZDNA_C2','nemožno vymazať, pretože nie je prázdna!');
// hlavni fce - prace s obrazky
define('RS_IGA_PO_ZPET_PRED','Späť na predchádzajúcu stránku');
define('RS_IGA_PO_ZPET_OBR','Späť na prehľad obrázkov v galérii');
define('RS_IGA_PO_PRIDAT_OBR','Pridať nový obrázok');
define('RS_IGA_PO_ZADNY_OBR','Nebol nájdený žiadny obrázok!');
define('RS_IGA_PO_ID','ID:');
define('RS_IGA_PO_NENI_NAHLED','náhľad neexistuje');
define('RS_IGA_PO_ORIGINAL','orig.');
define('RS_IGA_PO_SIRKA_VYSKA','ŠxV:');
define('RS_IGA_PO_VELIKOST','Veľkosť:');
define('RS_IGA_PO_SMAZ','Zmazať');
define('RS_IGA_PO_UPRAVIT','Upraviť');
define('RS_IGA_PO_SMAZ_OZNAC','Vymaž všetky označené obrázky');
define('RS_IGA_PO_FORM_NAZEV_OBR','Názov obrázku');
define('RS_IGA_PO_FORM_POPIS','Popis');
define('RS_IGA_PO_FORM_OBRAZEK','Obrázok');
define('RS_IGA_PO_TL_UPLOAD','Upload nového obrázku');
define('RS_IGA_PO_ADR_ORIG_OBR','Adresa originál:');
define('RS_IGA_PO_ADR_NAHLED_OBR','Adresa náhľad:');
define('RS_IGA_PO_OK_UPLOAD_OBR','Upload obrázku bol úspešne dokončený.');
define('RS_IGA_PO_OK_ADD_OBR','Akcia prebehla úspešne! Obrázok bol uložený.');
define('RS_IGA_PO_OK_EDIT_OBR','Akcia prebehla úspešne! Obrázok bol aktualizovaný.');
define('RS_IGA_PO_OK_DEL_OBR_C1','Obrázok číslo');
define('RS_IGA_PO_OK_DEL_OBR_C2','bol vymazaný!');
define('RS_IGA_PO_ERR_NELZE_UPLOADOVAT','Súbor nemožno uploadovať!');
define('RS_IGA_PO_ERR_NULOVA_DELKA','Súbor má nulovú veľkosť!');
define('RS_IGA_PO_ERR_SECURE_ERR_NEJSOU_DATA','Secure error: Systém nemôže nájsť uploadovaný súbor alebo nemôže identifikovať formát súboru!');
define('RS_IGA_PO_ERR_SECURE_ERR_FORMAT','Secure error: Systém nepodporuje upload tohto formátu súboru!');
define('RS_IGA_PO_ERR_CHYBA_PRI_ULOZENI','V priebehu ukladania súboru na server došlo k neočakávanej chybe. Prosím opakujte akciu.');
define('RS_IGA_PO_ERR_SECURE_ERR_BEZPECNOST','Secure error: Akcia upload súboru neprešla bezpečnostnou kontrolou!');
define('RS_IGA_PO_ERR_NELZE_GENER_NAHLED','phpRS systém je schopný automaticky generovať náhľady len pre JPEG, PNG a GIF obrázky bez animácie a priehľadnosti!');
define('RS_IGA_PO_ERR_CHYBA_DEL_ORIG_OBR','V priebehu mazania orig. obrázku došlo k neočakávanej chybe!');
define('RS_IGA_PO_ERR_CHYBA_DEL_NAHLED_OBR','V priebehu mazania náhľadu došlo k neočakávanej chybe!');
define('RS_IGA_PO_ERR_NEMOHU_NAJIT','Systém nemôže identifikovať požadovaný obrázok!');
define('RS_IGA_PO_INFO_PHPRS_ZNACKY','<b>phpRS systém umožňuje vložiť do akéhokoľvek článku akýkoľvek vyššie uvedený(é) obrázok(y) prostredníctvom tzv. "phpRS značky".<br />
Obrázková "phpRS značka" má nasledujúci syntax:</b><br /><br />
<tt>&lt;obrazek id="CISLO" zarovnani="ZPUSOB_ZAROVNANI" nahled="ZOBRAZIT"&gt;</tt><br /><br />
<tt>CISLO</tt> ... na miesto tejto premennej je nutné vložiť príslušné ID požadovaného obrázku<br />
<tt>ZPUSOB_ZAROVNANI</tt> ... možné varianty sú: "nastred", "vlevo" alebo "vpravo"<br />
<tt>ZOBRAZIT</tt> ... možné varianty sú: "ano", "ne" ... prípadne možno atribút "nahled" úplne vynechať = nezobraziť');
define('RS_IGA_PO_INFO_GENER_NAHLEDU','<b>Upozornenie:</b> phpRS systém je schopný automaticky generovať náhľady len pre JPEG, PNG obrázky a pre GIF obrázky bez animácie a priehľadnosti!');
define('RS_IGA_PRESUN_DO_JINE_GAL','Presun obrázku do galérie'); // N
define('RS_IGA_PRESUN_ERR','Obrázok sa nepodarilo presunúť do cieľovej galérie.'); // N
define('RS_IGA_PRESUN_OK1','Obrázok bol úspešne presunutý do '); // N
define('RS_IGA_PRESUN_OK2','cieľovej galérie'); // N
define('RS_IGA_PRESUN_ERR_PRAVA','Chyba, presun obrázku nie je možný, pretože nemáte oprávnenia na zápis do cieľovej galérie.'); // N
// multiupload
define('RS_IGA_MULTIUPL_1','Môžete odoslať maximálne'); // N
define('RS_IGA_MULTIUPL_2','obrázkov súčasne. Ich spoločná veľkosť nesmie presiahnuť:'); // N
define('RS_IGA_MULTIUPL_3','(Mega Bajtov)'); // N
define('RS_IGA_MULTIUPL_4','Doporučujeme nahrávať fotografie s rozmermi 800 až 1200 pixelov vo formáte JPG, pre obrázky s nižším počtom farieb PNG alebo GIF.'); // N

?>