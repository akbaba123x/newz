<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "clanky" - version 1.0.3
#####################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_CLA_ROZ_SHOW_CLA','Výpis článkov');
define('RS_CLA_ROZ_ADD_CLA','Vloženie nového článku');
define('RS_CLA_ROZ_DEL_CLA','Vymazanie článku');
define('RS_CLA_ROZ_EDIT_CLA','Editácia článku');
define('RS_CLA_ROZ_ADD_SKUP','Vloženie novej skupiny článkov');
define('RS_CLA_ROZ_SHOW_SKUP','Prehľad všetkých skupín súvisiacich článkov');
define('RS_CLA_ROZ_EDIT_SKUP','Úprava skupiny súvisiacich článkov');
define('RS_CLA_ROZ_DEL_SKUP','Vymazanie skupiny súvisiacich článkov');
define('RS_CLA_ROZ_POSTA_CLA','Poštové centrum');
// pomocne fce
define('RS_CLA_POM_ERR_ZADNA_SKUPINA','Zatiaľ nebola definovaná žiadna skupina!');
define('RS_CLA_POM_ERR_ZADNA_SAB','Zatiaľ nebola definovaná žiadna článková šablóna!');
define('RS_CLA_POM_ERR_ZADNY_LEVEL','Zatiaľ nebol definovaný žiadny level!'); // N
define('RS_CLA_POM_ERR_ZADNA_ANKETA','Zatiaľ nebola definovaná žiadna anketa!'); // N
define('RS_CLA_POM_ERR_ZADNA_RUBRIKA','Zatiaľ nebola nadefinovaná žiadna rubrika!'); // N
define('RS_CLA_POM_ERR_NEEXIST_SAB','Systém nemôže identifikovať priradenú šablónu!');
define('RS_CLA_POM_ERR_BEZ_ZARAZENI','-- bez zaradenia --'); // N
define('RS_CLA_POM_ZVOLTE_RUBRIKU','-- zvoľte tému --'); // N
// navigacni box
define('RS_CLA_NB_TL_ZOBRAZ','Zobraz články');
define('RS_CLA_NB_OD','od');
define('RS_CLA_NB_DO','do');
define('RS_CLA_NB_TRIDIT','triediť podľa');
define('RS_CLA_NB_TRIDIT_DATUM','dátumu vydania');
define('RS_CLA_NB_TRIDIT_NAZVU_CLA','názvu článku');
define('RS_CLA_NB_TRIDIT_LINK','linku');
define('RS_CLA_NB_TRIDIT_AUTOR','autora');
define('RS_CLA_NB_CELKEM_CLA','Celkom článkov:');
define('RS_CLA_NB_HLEDAT_TEXT','vyhľadať text');
define('RS_CLA_NB_V','v');
define('RS_CLA_NB_HLEDAT_V_NAZVU_CLA','názve článku');
define('RS_CLA_NB_HLEDAT_V_ANOTACI','anotácii');
define('RS_CLA_NB_HLEDAT_V_HLA_TEXT','hlavnom texte');
define('RS_CLA_NB_HLEDAT_V_LINKACH','linkách');
define('RS_CLA_NB_OMEZIT_NA_RUBRIKU','omezit na temu'); // N
define('RS_CLA_NB_JEN_ME_CLA','Zobraziť len moje články');
// hlavni fce - clanky
define('RS_CLA_CL_SPR_SOUVIS_CLA','Správa súvisiacich článkov');
define('RS_CLA_CL_SPR_ANKET','Správa anket'); // N
define('RS_CLA_CL_PRIDAT_CLA','Pridať nový článok');
define('RS_CLA_CL_PRIDAT_SOUVIS_SKUP','Pridať novú skupinu');
define('RS_CLA_CL_ZPET','Späť na hlavnú stránku sekcie');
define('RS_CLA_CL_ZPET_PRED','Späť na predchádzajúcu stránku');
define('RS_CLA_CL_ZPET_VYPIS_CLA','Späť na výpis článkov');
define('RS_CLA_CL_ZPET_EDIT_CLA','Vrátiť sa späť k editácii článkov');
define('RS_CLA_CL_LINK','Link');
define('RS_CLA_CL_TITULEK','Titulok');
define('RS_CLA_CL_DATUM_VYDANI','Dátum vydania / Vydané');
define('RS_CLA_CL_AUTOR','Autor článku');
define('RS_CLA_CL_LEVEL_CLA','Přístupový level'); // N
define('RS_CLA_CL_AKCE','Akcia');
define('RS_CLA_CL_SMAZ','Zmaž čl.');
define('RS_CLA_CL_UPRAVIT','Edituj');
define('RS_CLA_CL_PREVIEW','Náhľad');
define('RS_CLA_CL_SMAZ_OZNACENE','Vymaž všetky označené články');
define('RS_CLA_CL_FORM_LINK_CLA','Volací link článku');
define('RS_CLA_CL_FORM_LINK_CLA_INFO','bude automaticky doplnený');
define('RS_CLA_CL_FORM_TITULEK','Titulok článku');
define('RS_CLA_CL_FORM_UVOD','Úvod');
define('RS_CLA_CL_FORM_UVOD_INFO','Sem vložte príslušný text vrátane HTML syntaxu alebo v prípade nevyužitia tejto časti všetko vymažte - teda vrátane tohoto textu!');
define('RS_CLA_CL_FORM_HLA_TEXT','Hlavný text');
define('RS_CLA_CL_FORM_HLA_TEXT_INFO','Sem vložte príslušný text vrátane HTML syntaxu alebo v prípade nevyužitia tejto časti všetko vymažte - teda vrátane tohoto textu!');
define('RS_CLA_CL_FORM_ZNACKY','phpRS značky');
define('RS_CLA_CL_FORM_TEMA','Téma');
define('RS_CLA_CL_FORM_TYP_CLA','Typ článku');
define('RS_CLA_CL_FORM_TYP_CLA_DLOUHY','Dlhý (štandardný)');
define('RS_CLA_CL_FORM_TYP_CLA_KRATKY','Krátky');
define('RS_CLA_CL_FORM_SABLONA','Šablóna');
define('RS_CLA_CL_FORM_ZDROJ','Zdroj článku');
define('RS_CLA_CL_FORM_DATUM_VYDANI','Dátum vydania');
define('RS_CLA_CL_FORM_DATUM_INFO','RRRR-MM-DD HH:MM:SS');
define('RS_CLA_CL_FORM_DATUM_STAZ','Dátum stiahnutia');
define('RS_CLA_CL_FORM_AUTOR','Autor');
define('RS_CLA_CL_FORM_POC_CTENI','Počet prečítaní');
define('RS_CLA_CL_FORM_KLIC_SLOVA','Slovné spojenie odpovedajúce danému článku');
define('RS_CLA_CL_FORM_KLIC_SLOVA_INFO','Sem vložte slové alebo slovné spojenia, ktorá vystihujú vkladaný článok. V prípade nevyužitia tejto časti všetko vymažte!');
define('RS_CLA_CL_FORM_LEVEL_CLA','Level článku'); // N
define('RS_CLA_CL_FORM_DAT_NA_INDEX','Zobraziť článok<br />na hlavnej stránke'); // N
define('RS_CLA_CL_FORM_VYDAT_CLA','Vydať článok');
define('RS_CLA_CL_FORM_PRIORITA','Priorita článku');
define('RS_CLA_CL_FORM_HODNOCENI','Hodnotenie článku');
define('RS_CLA_CL_FORM_POC_HLAS','počet hlasovaní:');
define('RS_CLA_CL_FORM_SOUVIS_ANKETA','Súvisiaca anketa'); // N
define('RS_CLA_CL_FORM_SOUVIS_CLA','Súvisiace články');
define('RS_CLA_CL_VOLACI_LINK','Volací link článku:');
define('RS_CLA_CL_POSLI_MAIL','Odoslanie informačného e-mailu');
define('RS_CLA_CL_OK_ADD_CLA','Akcia prebehla úspešne! Článok bol pridaný.');
define('RS_CLA_CL_OK_EDIT_CLA','Akcia prebehla úspešne! Článok bol aktualizovaný.');
define('RS_CLA_CL_OK_DEL_CLA','Akcia prebehla úspešne! Článok bol vymazaný.');
define('RS_CLA_CL_OK_DEL_VICE_CLA','Akcia prebehla úspešne! Články boli vymazané.');
define('RS_CLA_CL_ERR_NEMATE_PRAVA','Nemáte potrebné práva!');
define('RS_CLA_CL_ERR_ZADNY_OZNAC_CLA','Neoznačili ste ani jeden článok!');
define('RS_CLA_CL_UPOZORNENI','<b><sup>1)</sup></b> ... Tento atribút článku možno využiť len v prípade, že máte v konfigurácii phpRS aktivovanú službu "Stráženie platnosti článkov na hlavnej stránke"! Pokiaľ túto službu nemáte aktívnu, ponechajte prosím automaticky vloženú hodnotu bez zmeny.');
// hlavni fce - skupina souvisejicich clanku
define('RS_CLA_SS_ZPET_PREHLED','Prehľad skupín súvisiacich článkov');
define('RS_CLA_SS_PRIDAT_SKUP','Pridať novú skupinu');
define('RS_CLA_SS_NAZEV_SKUP','Označenie skupiny');
define('RS_CLA_SS_AKCE','Akcia');
define('RS_CLA_SS_UPRAVIT','Edituj');
define('RS_CLA_SS_SMAZ','Zmaž');
define('RS_CLA_SS_ZADNY_ZAZNAM','Databáza neobsahuje žiadny záznam.');
define('RS_CLA_SS_FORM_NAZEV_SKUP','Označenie skupiny');
define('RS_CLA_SS_OK_ADD_SKUP','Akcia prebehla úspešne! Bola pridaná nová skupina súvisiacich článkov.');
define('RS_CLA_SS_OK_EDIT_SKUP','Akcia prebehla úspešne! Skupina súvisiacich článkov bola aktualizovaná.');
define('RS_CLA_SS_OK_DEL_SKUP','Akcia prebehla úspešne! Skupina súvisiacich článkov bola vymazaná.');
define('RS_CLA_SS_ERR_SKUP_JE_AKTIVNI','Vybranú skupinu súvisiacich článkov nemožno vymazať, pretože sa na ňu odkazuje minimálne jeden článok.');
// pomocna fce - postovni centrum
define('RS_CLA_PC_FROM_PREDMET','Predmet e-mailu');
define('RS_CLA_PC_FORM_OBSAH','Obsah e-mailu');
define('RS_CLA_PC_TL_ODELAT_MAIL','Odoslať e-mail');
define('RS_CLA_PC_ERR_NEEXISTUJE_CLA','Neočakávaná chyba! Overte si prosím platnosť článku.');
// casti informacniho mailu
define('RS_CLA_PC_PREDMET_MAIL','info zo dna');
define('RS_CLA_PC_OBS_MAIL_1','Nové články:');
define('RS_CLA_PC_OBS_MAIL_2','S pozdravom');
define('RS_CLA_PC_OBS_MAIL_3','redakcia');
?>
