<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "users" - version 1.0.1
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_USR_ROZ_SPRAVA_USER','Správa uživatelů (autorů)');
define('RS_USR_ROZ_ADD_USER','Přidání uživatele (autora)');
define('RS_USR_ROZ_DEL_USER','Vymazání uživatele (autora)');
define('RS_USR_ROZ_EDIT_USER','Úprava uživatele (autora)');
define('RS_USR_ROZ_KONFIG_USER','Konfigurace uživatele (autora)');
// pomocne fce
define('RS_USR_POM_ZADNY_USER','Neexistuje žádný odpovídající uživatel!');
// hlavni fce - sprava uzivatelu
define('RS_USR_SU_PRIDAT_USER','Přidat nového uživatele (autora)');
define('RS_USR_SU_ZPET','Zpět na hlavní stránku sekce');
define('RS_USR_SU_SMAZ_OZNAC','Vymaž všechny označené uživatele');
define('RS_USR_SU_UZIVATEL','Uživatel<br />(User)');
define('RS_USR_SU_JMENO','Jméno');
define('RS_USR_SU_TYP','Typ uživatele');
define('RS_USR_SU_JAZYK','Jazyk prostředí');
define('RS_USR_SU_STAV','Stav účtu');
define('RS_USR_SU_AKCE','Akce');
define('RS_USR_SU_SMAZ','Smaž');
define('RS_USR_SU_ZADNY_USER','Nebyl nalezen žádný uživatel!');
define('RS_USR_SU_AUTOR','autor');
define('RS_USR_SU_REDAKTOR','redaktor');
define('RS_USR_SU_ADMIN','admin');
define('RS_USR_SU_BLOKOVAT','blokován');
define('RS_USR_SU_AKTIVNI','aktivní');
define('RS_USR_SU_UPRAVIT','Edituj');
define('RS_USR_SU_NAST_PRAVA','Nastav práva');
define('RS_USR_SU_NAST_VAZBY','Nastav vazby');
define('RS_USR_SU_FORM_USER','Uživatel (User)');
define('RS_USR_SU_FORM_HESLO','Heslo');
define('RS_USR_SU_FORM_HESLO_POTV','Heslo (potvrzení)');
define('RS_USR_SU_FORM_JMENO','Jméno');
define('RS_USR_SU_FORM_EMAIL','E-mail');
define('RS_USR_SU_FORM_URL','URL domovské stránky');
define('RS_USR_SU_FORM_IM','IM klient (ICQ, atd.)');
define('RS_USR_SU_FORM_BLOKOVAT','Blokovat účet');
define('RS_USR_SU_FORM_TYP','Typ uživatele');
define('RS_USR_SU_FORM_INFO_HESLO','Tento formulář použijte pouze v případě, že chcete změnit heslo!');
define('RS_USR_SU_FORM_NEW_HESLO','Nové heslo');
define('RS_USR_SU_FORM_NEW_HESLO_POTV','Nové heslo (potvrzení)');
define('RS_USR_SU_OK_ADD_USER','Akce proběhla úspěšně! Uživatel (autor) byl přidán.');
define('RS_USR_SU_OK_DEL_USER','Akce proběhla úspěšně! Uživatel byl vymazán.');
define('RS_USR_SU_OK_EDIT_USER','Akce proběhla úspěšně! Uživatel (autor) byl aktualizován.');
define('RS_USR_SU_ERR_DLOUHE_USERNAME','Požadovanou akci nelze provést, jelikož uživatelské jméno je příliš dlouhé! Maximální velikost je 20 znaků.');
define('RS_USR_SU_ERR_KRATKE_USERNAME','Požadovanou akci nelze provést, jelikož uživatelské jméno je příliš krátké! Minimální velikost je 2 znaky.');
define('RS_USR_SU_ERR_DUPLICITNI_USERNAME','Požadovanou akci nelze provést, jelikož vámi zadaný uživatel již existuje. Zvolte si prosím jiné označení.');
define('RS_USR_SU_ERR_DLOUHE_HESLO','Požadovanou akci nelze provést, jelikož uživatelské heslo je příliš dlouhé! Maximální velikost je 100 znaků.');
define('RS_USR_SU_ERR_SLABE_HESLO','Požadovanou akci nelze provést, jelikož uživatelské heslo je příliš jednoduché! Heslo musí mít velikost minimálne 8 znaků, musí obsahovat číslo, velká i malá písmena.');
define('RS_USR_SU_ERR_RUZNA_HESLO','Požadovanou akci nelze provést, jelikož se zadaná hesla neshodují.');
define('RS_USR_SU_ERR_USER_ZAVAZKY','Akci nelze provést, jelikož vybraný uživatel je spojen s jedním nebo více články.');
// hlavni fce - nastaveni prav
define('RS_USR_NP_AKTUAL_USER','Aktuální uživatel');
define('RS_USR_NP_USER_PR','Uživatelská práva');
define('RS_USR_NP_NASTAV_PR','Nastavení práv');
define('RS_USR_NP_PR_VYDAVAT','Právo vydávat');
define('RS_USR_NP_PR_POVOLENO','povoleno');
define('RS_USR_NP_PR_ZAKAZANO','zakázáno');
define('RS_USR_NP_NAZEV_MODUL','Název modulu');
define('RS_USR_NP_PRISTUP_MODUL','Přístup k modulu');
define('RS_USR_NP_NIC_MODUL','Nebyl nalezena žádný modul!');
define('RS_USR_NP_M_POVOLEN','povolen');
define('RS_USR_NP_M_ZAKAZAN','zakázán');
define('RS_USR_NP_ERR_NASTAV_PR','Požadovanou akci nelze provést, jelikož systém není schopen zjistit nastavení přístupových práv.');
// hlavni fce - nastaveni vazeb
define('RS_USR_NV_PODRIZENI_USER','Podřízení uživatelé');
define('RS_USR_NV_SMAZ','Smaž');
define('RS_USR_NV_SMAZ_OZNAC','Vymaž všechny označené uživatele');
define('RS_USR_NV_NIC_PODRIZENI','Nebyl nalezen žádný podřízený uživatel!');
define('RS_USR_NV_ADD_PODRIZENI','Přidat podřízeného uživatele');
define('RS_USR_NV_OK_ADD_PODRIZENI','Akce proběhla úspěšně! Byla přidána nová vazba.');
define('RS_USR_NV_OK_DEL_PODRIZENI','Akce proběhla úspěšně! Označení uživatelé byli vymazáni.');
?>
