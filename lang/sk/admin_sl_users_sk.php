<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "users" - version 1.0.1
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_USR_ROZ_SPRAVA_USER','Správa užívateľov (autorov)');
define('RS_USR_ROZ_ADD_USER','Pridanie užívateľa (autora)');
define('RS_USR_ROZ_DEL_USER','Vymazanie užívateľa (autora)');
define('RS_USR_ROZ_EDIT_USER','Úprava užívateľa (autora)');
define('RS_USR_ROZ_KONFIG_USER','Konfigurácia užívateľa (autora)');
// pomocne fce
define('RS_USR_POM_ZADNY_USER','Neexistuje žiadny odpovedajúci užívateľ!');
// hlavni fce - sprava uzivatelu
define('RS_USR_SU_PRIDAT_USER','Pridať nového užívateľa (autora)');
define('RS_USR_SU_ZPET','Späť na hlavnú stránku sekcie');
define('RS_USR_SU_SMAZ_OZNAC','Vymaž všetkých označených užívateľov');
define('RS_USR_SU_UZIVATEL','Užívateľ<br />(User)');
define('RS_USR_SU_JMENO','Meno');
define('RS_USR_SU_TYP','Typ užívateľa');
define('RS_USR_SU_JAZYK','Jazyk prostredia');
define('RS_USR_SU_STAV','Stav účtu');
define('RS_USR_SU_AKCE','Akcia');
define('RS_USR_SU_SMAZ','Zmaž');
define('RS_USR_SU_ZADNY_USER','Nebol nájdený žiadny užívateľ!');
define('RS_USR_SU_AUTOR','autor');
define('RS_USR_SU_REDAKTOR','redaktor');
define('RS_USR_SU_ADMIN','admin');
define('RS_USR_SU_BLOKOVAT','blokovaný');
define('RS_USR_SU_AKTIVNI','aktívny');
define('RS_USR_SU_UPRAVIT','Edituj');
define('RS_USR_SU_NAST_PRAVA','Nastav práva');
define('RS_USR_SU_NAST_VAZBY','Nastav väzby');
define('RS_USR_SU_FORM_USER','Užívateľ (User)');
define('RS_USR_SU_FORM_HESLO','Heslo');
define('RS_USR_SU_FORM_HESLO_POTV','Heslo (potvrdenie)');
define('RS_USR_SU_FORM_JMENO','Meno');
define('RS_USR_SU_FORM_EMAIL','E-mail');
define('RS_USR_SU_FORM_URL','URL domovskej stránky');
define('RS_USR_SU_FORM_IM','IM klient (ICQ, atd.)');
define('RS_USR_SU_FORM_BLOKOVAT','Blokovať účet');
define('RS_USR_SU_FORM_TYP','Typ užívateľa');
define('RS_USR_SU_FORM_INFO_HESLO','Tento formulár použite len v prípade, že chcete zmeniť heslo!');
define('RS_USR_SU_FORM_NEW_HESLO','Nové heslo');
define('RS_USR_SU_FORM_NEW_HESLO_POTV','Nové heslo (potvrdenie)');
define('RS_USR_SU_OK_ADD_USER','Akcia prebehla úspešne! Užívateľ (autor) bol pridaný.');
define('RS_USR_SU_OK_DEL_USER','Akcia prebehla úspešne! Užívateľ bol vymazaný.');
define('RS_USR_SU_OK_EDIT_USER','Akcia prebehla úspešne! Užívateľ (autor) bol aktualizovaný.');
define('RS_USR_SU_ERR_DLOUHE_USERNAME','Požadovanú akciu nemožno vykonať, pretože užívateľské meno je príliš dlhé! Maximálna dĺžka je 20 znakov.');
define('RS_USR_SU_ERR_KRATKE_USERNAME','Požadovanú akciu nemožno vykonať, pretože užívateľské meno je príliš krátke! Minimálna dĺžka je 2 znaky.');
define('RS_USR_SU_ERR_DUPLICITNI_USERNAME','Požadovanú akciu nemožno vykonať, pretože vami zadaný užívateľ už existuje. Zvoľte si prosím iné označenie.');
define('RS_USR_SU_ERR_DLOUHE_HESLO','Požadovanú akciu nemožno vykonať, pretože užívateľské heslo je príliš dlhé! Maximálna dĺžka je 100 znakov.');
define('RS_USR_SU_ERR_SLABE_HESLO','Požadovanoú akciu nemožno vykonať, pretože užívateľské heslo je príliš jednoduché! Heslo musí mať dĺžku minimálne 8 znakov, musí obsahovat číslo, velké aj malé písmená.');
define('RS_USR_SU_ERR_RUZNA_HESLO','Požadovanú akciu nemožno vykonať, pretože sa zadané heslá nezhodujú.');
define('RS_USR_SU_ERR_USER_ZAVAZKY','Akciu nemožno vykonať, pretože vybraný užívateľ je spojený s jedným alebo viac článkami.');
// hlavni fce - nastaveni prav
define('RS_USR_NP_AKTUAL_USER','Aktuálny užívateľ');
define('RS_USR_NP_USER_PR','Užívateľské práva');
define('RS_USR_NP_NASTAV_PR','Nastavenie práv');
define('RS_USR_NP_PR_VYDAVAT','Právo vydávať');
define('RS_USR_NP_PR_POVOLENO','povolené');
define('RS_USR_NP_PR_ZAKAZANO','zakázané');
define('RS_USR_NP_NAZEV_MODUL','Názov modulu');
define('RS_USR_NP_PRISTUP_MODUL','Prístup k modulu');
define('RS_USR_NP_NIC_MODUL','Nebol nájdený žiadny modul!');
define('RS_USR_NP_M_POVOLEN','povolené');
define('RS_USR_NP_M_ZAKAZAN','zakázané');
define('RS_USR_NP_ERR_NASTAV_PR','Požadovanú akciu nemožno vykonať, pretože systém nie je schopný zistiť nastavenie prístupových práv.');
// hlavni fce - nastaveni vazeb
define('RS_USR_NV_PODRIZENI_USER','Podriadení užívatelia');
define('RS_USR_NV_SMAZ','Zmaž');
define('RS_USR_NV_SMAZ_OZNAC','Vymaž všetkých označených užívateľov');
define('RS_USR_NV_NIC_PODRIZENI','Nebol nájdený žiadny podriadený užívateľ!');
define('RS_USR_NV_ADD_PODRIZENI','Pridať podriadeného užívateľa');
define('RS_USR_NV_OK_ADD_PODRIZENI','Akcia prebehla úspešne! Bola pridaná nová väzba.');
define('RS_USR_NV_OK_DEL_PODRIZENI','Akcia prebehla úspešne! Označený užívatelia boli vymazaný.');
?>
