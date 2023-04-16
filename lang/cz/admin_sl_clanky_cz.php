<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "clanky" - version 1.0.3
#####################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_CLA_ROZ_SHOW_CLA','Výpis článků');
define('RS_CLA_ROZ_ADD_CLA','Vložení nového článku');
define('RS_CLA_ROZ_DEL_CLA','Vymazávání článku');
define('RS_CLA_ROZ_EDIT_CLA','Editace článku');
define('RS_CLA_ROZ_ADD_SKUP','Vložení nové skupiny článků');
define('RS_CLA_ROZ_SHOW_SKUP','Přehled všech skupin souvisejících článků');
define('RS_CLA_ROZ_EDIT_SKUP','Úprava skupiny souvisejících článků');
define('RS_CLA_ROZ_DEL_SKUP','Vymazání skupiny souvisejících článků');
define('RS_CLA_ROZ_POSTA_CLA','Poštovní centrum');
// pomocne fce
define('RS_CLA_POM_ERR_ZADNA_SKUPINA','Prozatím nebyla definována žádná skupina!');
define('RS_CLA_POM_ERR_ZADNA_SAB','Prozatím nebyla definována žádná článková šablona!');
define('RS_CLA_POM_ERR_ZADNY_LEVEL','Prozatím nebyl definován žádný level!'); // N
define('RS_CLA_POM_ERR_ZADNA_ANKETA','Prozatím nebyla definována žádná anketa!'); // N
define('RS_CLA_POM_ERR_ZADNA_RUBRIKA','Prozatím nebyla definován žádná rubrika!'); // N
define('RS_CLA_POM_ERR_NEEXIST_SAB','Systém nemůže identifikovat přiřazenou šablonu!');
define('RS_CLA_POM_ERR_BEZ_ZARAZENI','-- bez zařazení --'); // N
define('RS_CLA_POM_ZVOLTE_RUBRIKU','-- zvolte téma --'); // N
// navigacni box
define('RS_CLA_NB_TL_ZOBRAZ','Zobraz články');
define('RS_CLA_NB_OD','od');
define('RS_CLA_NB_DO','do');
define('RS_CLA_NB_TRIDIT','třídit dle');
define('RS_CLA_NB_TRIDIT_DATUM','data vydání');
define('RS_CLA_NB_TRIDIT_NAZVU_CLA','názvu článku');
define('RS_CLA_NB_TRIDIT_LINK','linku');
define('RS_CLA_NB_TRIDIT_AUTOR','autora');
define('RS_CLA_NB_CELKEM_CLA','Celkem článků:');
define('RS_CLA_NB_HLEDAT_TEXT','vyhledat text');
define('RS_CLA_NB_V','v');
define('RS_CLA_NB_HLEDAT_V_NAZVU_CLA','názvu článku');
define('RS_CLA_NB_HLEDAT_V_ANOTACI','anotaci');
define('RS_CLA_NB_HLEDAT_V_HLA_TEXT','hlavním textu');
define('RS_CLA_NB_HLEDAT_V_LINKACH','linkách');
define('RS_CLA_NB_OMEZIT_NA_RUBRIKU','omezit na téma'); // N
define('RS_CLA_NB_JEN_ME_CLA','Zobrazit pouze mé články');
// hlavni fce - clanky
define('RS_CLA_CL_SPR_SOUVIS_CLA','Správa souvisejících článků');
define('RS_CLA_CL_SPR_ANKET','Správa anket'); // N
define('RS_CLA_CL_PRIDAT_CLA','Přidat nový článek');
define('RS_CLA_CL_PRIDAT_SOUVIS_SKUP','Přidat novou skupinu');
define('RS_CLA_CL_ZPET','Zpět na hlavní stránku sekce');
define('RS_CLA_CL_ZPET_PRED','Zpět na předchozí stránku');
define('RS_CLA_CL_ZPET_VYPIS_CLA','Zpět na výpis článků');
define('RS_CLA_CL_ZPET_EDIT_CLA','Vrátit se zpět k editaci článku');
define('RS_CLA_CL_LINK','Link');
define('RS_CLA_CL_TITULEK','Titulek');
define('RS_CLA_CL_DATUM_VYDANI','Datum vydání / Vydáno');
define('RS_CLA_CL_AUTOR','Autor článku');
define('RS_CLA_CL_LEVEL_CLA','Přístupový level'); // N
define('RS_CLA_CL_AKCE','Akce');
define('RS_CLA_CL_SMAZ','Smaž čl.');
define('RS_CLA_CL_UPRAVIT','Edituj');
define('RS_CLA_CL_PREVIEW','Preview');
define('RS_CLA_CL_SMAZ_OZNACENE','Vymaž všechny označené články');
define('RS_CLA_CL_FORM_LINK_CLA','Volací link článku');
define('RS_CLA_CL_FORM_LINK_CLA_INFO','bude automaticky doplněn');
define('RS_CLA_CL_FORM_TITULEK','Titulek článku');
define('RS_CLA_CL_FORM_UVOD','Úvod');
define('RS_CLA_CL_FORM_UVOD_INFO','Sem vložte příslušný text včetně HTML syntaxe nebo v případě nevyužití této části vše vymažte - tedy včetně tohoto textu!');
define('RS_CLA_CL_FORM_HLA_TEXT','Hlavní text');
define('RS_CLA_CL_FORM_HLA_TEXT_INFO','Sem vložte příslušný text včetně HTML syntaxe nebo v případě nevyužití této části vše vymažte - tedy včetně tohoto textu!');
define('RS_CLA_CL_FORM_ZNACKY','phpRS značky');
define('RS_CLA_CL_FORM_TEMA','Téma');
define('RS_CLA_CL_FORM_TYP_CLA','Typ článku');
define('RS_CLA_CL_FORM_TYP_CLA_DLOUHY','Dlouhý (standardní)');
define('RS_CLA_CL_FORM_TYP_CLA_KRATKY','Krátký');
define('RS_CLA_CL_FORM_SABLONA','Šablona');
define('RS_CLA_CL_FORM_ZDROJ','Zdroj článku');
define('RS_CLA_CL_FORM_DATUM_VYDANI','Datum vydání');
define('RS_CLA_CL_FORM_DATUM_INFO','RRRR-MM-DD HH:MM:SS');
define('RS_CLA_CL_FORM_DATUM_STAZ','Datum stažení');
define('RS_CLA_CL_FORM_AUTOR','Autor');
define('RS_CLA_CL_FORM_POC_CTENI','Počet přečtení');
define('RS_CLA_CL_FORM_KLIC_SLOVA','Slovní spojení odpovídající danému článku');
define('RS_CLA_CL_FORM_KLIC_SLOVA_INFO','Sem vložte slova nebo slovní spojení, která vystihují vkládaný článek. V případě nevyužití této části vše vymažte!');
define('RS_CLA_CL_FORM_LEVEL_CLA','Level článku'); // N
define('RS_CLA_CL_FORM_DAT_NA_INDEX','Zobrazit článek<br />na hlavní stránce'); // N
define('RS_CLA_CL_FORM_VYDAT_CLA','Vydat článek');
define('RS_CLA_CL_FORM_PRIORITA','Priorita článku');
define('RS_CLA_CL_FORM_HODNOCENI','Hodnocení článku');
define('RS_CLA_CL_FORM_POC_HLAS','počet hlasování:');
define('RS_CLA_CL_FORM_SOUVIS_ANKETA','Související anketa'); // N
define('RS_CLA_CL_FORM_SOUVIS_CLA','Související články');
define('RS_CLA_CL_VOLACI_LINK','Volací link článku:');
define('RS_CLA_CL_POSLI_MAIL','Odeslání informačního e-mailu');
define('RS_CLA_CL_OK_ADD_CLA','Akce proběhla úspěšně! Článek byl přidán.');
define('RS_CLA_CL_OK_EDIT_CLA','Akce proběhla úspěšně! Článek byl aktualizován.');
define('RS_CLA_CL_OK_DEL_CLA','Akce proběhla úspěšně! Článek byl vymazán.');
define('RS_CLA_CL_OK_DEL_VICE_CLA','Akce proběhla úspěšně! Články byly vymazány.');
define('RS_CLA_CL_ERR_NEMATE_PRAVA','Nemáte potřebná práva!');
define('RS_CLA_CL_ERR_ZADNY_OZNAC_CLA','Neoznačili jste ani jeden článek!');
define('RS_CLA_CL_UPOZORNENI','<b><sup>1)</sup></b> ... Tento atribut článku lze využít pouze v případě, že máte v konfiguraci phpRS aktivovanou službu "Hlídání platnosti článků na hlavní stránce"! Pokud tuto službu nemáte aktivní, ponechte prosím automaticky vloženou hodnotu bez změny.');
// hlavni fce - skupina souvisejicich clanku
define('RS_CLA_SS_ZPET_PREHLED','Přehled skupin souvisejících článků');
define('RS_CLA_SS_PRIDAT_SKUP','Přidat novou skupinu');
define('RS_CLA_SS_NAZEV_SKUP','Označení skupiny');
define('RS_CLA_SS_AKCE','Akce');
define('RS_CLA_SS_UPRAVIT','Edituj');
define('RS_CLA_SS_SMAZ','Smaž');
define('RS_CLA_SS_ZADNY_ZAZNAM','Databáze neobsahuje žádný záznam.');
define('RS_CLA_SS_FORM_NAZEV_SKUP','Označení skupiny');
define('RS_CLA_SS_OK_ADD_SKUP','Akce proběhla úspěšně! Byla přidána nová skupina souvisejících článků.');
define('RS_CLA_SS_OK_EDIT_SKUP','Akce proběhla úspěšně! Skupina souvisejících článků byla aktualizována.');
define('RS_CLA_SS_OK_DEL_SKUP','Akce proběhla úspěšně! Skupina souvisejících článků byla vymazána.');
define('RS_CLA_SS_ERR_SKUP_JE_AKTIVNI','Vybranou skupinu souvisejících článků nelze vymazat, protože se na ni odkazuje minimálně jeden článek.');
// pomocna fce - postovni centrum
define('RS_CLA_PC_FROM_PREDMET','Předmět e-mailu');
define('RS_CLA_PC_FORM_OBSAH','Obsah e-mailu');
define('RS_CLA_PC_TL_ODELAT_MAIL','Odeslat e-mail');
define('RS_CLA_PC_ERR_NEEXISTUJE_CLA','Neočekávaná chyba! Ověřte si prosím platnost článku.');
// casti informacniho mailu
define('RS_CLA_PC_PREDMET_MAIL','info ze dne');
define('RS_CLA_PC_OBS_MAIL_1','Nové články:');
define('RS_CLA_PC_OBS_MAIL_2','S pozdravem');
define('RS_CLA_PC_OBS_MAIL_3','redakce');
?>
