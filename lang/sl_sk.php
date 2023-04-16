<?php
#####################################################################
# phpRS Basic dictionary (Zakladni slovnik) - SK version 2.8.2
#####################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// ************************** ctenarske rozhrani / modul ***************************
// standardni hlasky
define("RS_SAVEOK","Všetko bolo korektne uložené!");
define("RS_DBNIC","Databáza neobsahuje žiadny odpovedajúci záznam!");
define("RS_ODESLAT","Odoslať");
define("RS_RESET","Reset");
define("RS_TLOK","OK");
define("RS_ANO","Áno");
define("RS_NE","Nie");
define("RS_TLNE","Nie");
define('RS_AKCE_ERR','Pozor chyba! Nebola špecifikovaná akcia, ktorú chcete vykonať!');
// ankety (ankety.php)
define('RS_AN_ERR1','Pri hlasovaní sa vyskytla neočakávaná chyba!');
define('RS_AN_ERR2','Anketný subsystém nie je schopný identifikovať alebo zobraziť vybranú anketu!'); // P
define('RS_AN_ERR3','Vybraná anketa je už uzavretá!');
define('RS_AN_POCET_HLA','Počet hlasov');
define('RS_AN_CELKEM_HLA','Celkem hlasovalo');
define('RS_AN_ZOBRAZ_VSE','Zobraziť všetky ankety');
define('RS_AN_HLASUJ','Hlasuj');
define('RS_AN_BLOKACE','Zobraziť výsledok');
define('RS_AN_NELZE_HLASOVAT','Anketný subsystém zistil, že ste v tejo otázke už raz hlasovali a v záujme objektivity tejto ankety Vám bohužial nemôžeme povoliť druhé hlasovanie. Ďakujeme za pochopenie!');
define('RS_AN_TL_HLASUJ','Hlasuj!');
define('RS_AN_NADPIS','Ankety');
// komentare (comment.php)
define('RS_KO_NIC','K tomuto článku nebol doposiaľ priradený žiadny komentár!');
define('RS_KO_NEREG','neregistrovaný');
define('RS_KO_ZOBRAZ_OZN','Zobraziť len označené');
define('RS_KO_ZOBRAZ_VSE','Zobraziť všetky komentáre');
define('RS_KO_PRIDAT','Pridať nový komentár');
define('RS_KO_ZPR_ZE_DNE','Komentár zo dňa');
define('RS_KO_ZPR_REG','Reagovať');
define('RS_KO_ZPR_AUT','Autor');
define('RS_KO_ZPR_TIT','Titulok');
define('RS_KO_PRAZDNY_VYB','Váš výber je prázdny! Zopakujte voľbu ešte raz.');
define('RS_KO_ZPR_JME','Meno (prezývka)');
define('RS_KO_ZPR_EMAIL','E-mail');
define('RS_KO_ZPR_KONTROLA','Odpovězte, prosím, na následující kontrolní otázku, která nám pomůže rozlišit, zda jste člověk nebo počítačový robot. Bez správné odpovědi na tuto otázku nebude váš komentář uložen. Tato kontrolní otázka se nezobrazuje registrovaným čtenářům.');
define('RS_KO_ZOBRAZ_KOM','Zobraziť komentáre');
define('RS_KO_RE_NA_KOM','Reakcia na komentár');
define('RS_KO_ERR1','Pri vkladaní komentára do našej databázy sa vyskytli neočakávané problémy. Pri opakovaní tejto chyby nás prosím kontaktujte!<br /><br />Ďakujeme');
define('RS_KO_VLOZEN_OK','Váš komentár bol úspešne vložený do našej databázy!<br /><br />Ďakujeme');
define('RS_KO_ERR2','Pozor! Do systému nemožno vložiť prázdny komentár.');
define('RS_KO_ERR3','Nie je špecifikované číslo článku!');
define('RS_KO_ERR4','Zadaný článok neexistuje!');
define('RS_KO_ERR5','Pozor chyba! V priebehu vykonávania PHP skriptu sa vyskytla neočakávaná chyba.');
define('RS_KO_ERR6','Vaše odpověď na kontrolní otázku je chybná! Zopakujte akci.');
define('RS_KO_NADPIS','Komentáre');
define('RS_KO_KE_CLA','k článku');
define('RS_KO_ZE_DNE','zo dňa');
define('RS_KO_AUTOR_CLA','autor článku');
define('RS_KO_ZOBRAZ_CLA','Zobraziť článok');
define('RS_KO_INFO','V rámci komentárov nemožno používať HTML tagy.<br /><br />Pre vloženie tučného textu, hyperlinku alebo e-mailovej adresy využite nasledujúce značky:<br />[b]tučné[/b], [url]http://www.domeny.cz[/url], [email]meno@domena.sk[/email]');
define('RS_KO_JEN_PRO_REGISTROVANE','Vkladať komentáre môžu len registrovaní čitatelia.');

// download (download.php)
define('RS_DW_JMENO_SB','Názov súboru');
define('RS_DW_VEL_SB','Veľkosť');
define('RS_DW_ZDROJ_SB','Zdroj');
define('RS_DW_DATUM_SB','Dátum');
define('RS_DW_VER_SB','Verzia');
define('RS_DW_KAT','Kategória');
define('RS_DW_POCET_STAZ','Počet stiahnutí');
define('RS_DW_KLIKNI','Kliknite pre viac informácií...');
define('RS_DW_STAHNI','Download...');
define('RS_DW_ZPET','Späť na prehľad súborov');
define('RS_DW_NADPIS','Download sekcia');
// engine (engine.php)
define('RS_EN_TOPIC_ERR1','Nebola nájdená žiadna téma!');
define('RS_EN_LINKS_NADPIS','Weblinks sekcia');
define('RS_EN_LINKS_ERR1','V databáze sa nenachádza žiadny weblink!');
define('RS_EN_LINKS_ZDROJ','Zdroj');
define('RS_EN_STAT_NADPIS','15 najčítanejších článkov');
define('RS_EN_STAT_ERR1','Nebol nájdený žiadny prečítaný článok!');
// specialni funkce (specfce.php)
define('RS_SP_POCET_HLA','hl.');
define('RS_SP_CELKEM_HLA','Celkom hlasovalo');
define('RS_SP_KAL_PO','Po');
define('RS_SP_KAL_UT','Ut');
define('RS_SP_KAL_ST','St');
define('RS_SP_KAL_CT','Št');
define('RS_SP_KAL_PA','Pi');
define('RS_SP_KAL_SO','So');
define('RS_SP_KAL_NE','Ne');
define('RS_SP_KAL_M01','Január');
define('RS_SP_KAL_M02','Február');
define('RS_SP_KAL_M03','Marec');
define('RS_SP_KAL_M04','Apríl');
define('RS_SP_KAL_M05','Máj');
define('RS_SP_KAL_M06','Jún');
define('RS_SP_KAL_M07','Júl');
define('RS_SP_KAL_M08','August');
define('RS_SP_KAL_M09','September');
define('RS_SP_KAL_M10','Október');
define('RS_SP_KAL_M11','November');
define('RS_SP_KAL_M12','December');
define('RS_SP_SOUVIS_CLA','Súvisiace články');
define('RS_SP_AKT_ZNAMKA','Akt. známka');
define('RS_SP_POCET_ZNAMEK','Počet hlasov');
define('RS_SP_TL_ZNAMKA','Známkovanie');
// ctenari (readers.php)
define('RS_CT_NADPIS','Úprava osobného nastavenia');
define('RS_CT_JMENO','Meno (prezývka)');
define('RS_CT_HESLO','Heslo');
define('RS_CT_NAVIG_REG_NOVY','Registrácia nového čitateľa');
define('RS_CT_NAVIG_ZRUSIT','Zrušiť účet');
define('RS_CT_NAVIG_ZAPOMNEL','Zabudol som heslo');
define('RS_CT_TL_ZAREG','Zaregistrovať');
define('RS_CT_TL_ULOZ','Uložiť zmeny');
define('RS_CT_NOVY_LOGIN','Nové prihlásenie');
define('RS_CT_ERR_1','Zlé užívateľské meno alebo heslo!');
define('RS_CT_ERR_2','Systém nemôže identifikovať čitateľa!');
define('RS_CT_INFO_HESLO','(zadajte len v prípade zmeny)'); // P
define('RS_CT_HESLO_KONTRLA','Heslo (potvrdenie)');
define('RS_CT_CELE_JMENO','Meno');
define('RS_CT_EMAIL','E-mail');
define('RS_CT_JAZYK','Jazyk prostredia');
define('RS_CT_NOVINKY','Chcem, aby ste mi zasielali informácie o novinkách na vašom serveri na vyššie uvedenú e-mailovú adresu!');
define('RS_CT_VLASTNI_MENU','Vytvorte si svoje vlastné menu.<br />HTML tagy sú zakázane, pre vloženie tučného textu, hyperlinku alebo e-mailovej adresy využite nasledujúce značky:<br />[b]tučné[/b], [url]http://www.domeny.cz[/url], [email]meno@domena.sk[/email]');
define('RS_CT_CHCI_ME_MENU','Chcem, aby sa zobrazovalo moje menu');
define('RS_CT_INFO_POVINNA','Prezývka a heslo patria medzi povinné polia.');
define('RS_CT_ERR_3','Niektoré z povinných polí je prázdne!');
define('RS_CT_ERR_4_A','Vami vybraná prezývka');
define('RS_CT_ERR_4_B','je už obsadená! Zvoľte si inú.');
define('RS_CT_ERR_5','V priebehu zakladania vášho profilu došlo k neočakávanej chybe. Zopakujte akciu.');
define('RS_CT_ERR_6','Neočakávaná chyba!');
define('RS_CT_REG_VSE_OK','Vaša registrácia bola úspešne dokončená.');
define('RS_CT_HL_STR','Hlavná stránka');
define('RS_CT_OSOBNI_UCET','Úprava osobného účtu');
define('RS_CT_ERR_7','V priebehu úpravy vášho profilu došlo k neočakávanej chybe. Zopakujte akciu.');
define('RS_CT_EDIT_VSE_OK','Vaše osobné nastavenie bolo aktualizované.');
define('RS_CT_INFO_ZRUSIT','Pokiaľ si prajete zrušiť registráciu, zadajte svoje užívateľské meno a heslo.');
define('RS_CT_TL_ZRUSIT','Zrušiť registráciu');
define('RS_CT_ERR_8','V priebehu odstraňovania registrácie došlo k neočakávanej chybe!');
define('RS_CT_DEL_VSE_OK','Vaša registrácia bola úspešne zrušená!');
define('RS_CT_INFO_ZAPOMNEL','Zabudli ste heslo k svojmu účtu? Nevadí, môžete si nechať vygenerovať nové.<br /><br />Zadajte svoje registračné užívateľské meno a e-mailovú adresu.<br />Po overení správnosti oboch údajov vám bude zaslané nanovo vygenerované heslo.');
define('RS_CT_DEL_EMAIL_ADR','E-mailová adresa');
define('RS_CT_TL_NOVE_HESLO','Chcem nové heslo');
define('RS_CT_ERR_9','Zlé užívateľské meno alebo e-mailová adresa!');
define('RS_CT_ERR_10','V priebehu ukladania nového hesla došlo k neočakávanej chybe!');
define('RS_CT_NOVE_HESLO_VSE_OK','Nastavenie nového hesla bolo úspešne vykonané!');
define('RS_CT_ERR_11','V priebehu odosielania informačného e-mailu došlo k neočakávanej chybe!');
define('RS_CT_SEND_MAIL_VSE_OK','Na vašu e-mailovú adresu bol úspešne odoslaný informačný e-mail!');
define('RS_CT_ERR_12','Zadané heslá nie sú zhodné!');
define('RS_CT_ERR_13','Dĺžka vášho mena (prezývky) porušuje pravidlá. - Minimálna povolená dĺžka je 3 znaky; maximálna hodnota je 12 znakov.');
define('RS_CT_ERR_14','Obsah vášho mena (prezývky) alebo celého mena porušuje pravidlá. Údaje nemôžu obsahovať HTML znaky'); // N
// ctenari - obsah info mailu pri generovani noveho hesla (readers.php)
define('RS_CT_GNH_PREDMET','Nové heslo k účtu');
define('RS_CT_GNH_OBS_1','Vaše nové heslo k účtu');
define('RS_CT_GNH_OBS_2','je');
// ctenarske sluzby (rservice.php)
define('RS_CS_NADPIS','Informačný e-mail');
define('RS_CS_CLANEK','Článok');
define('RS_CS_PRIJEMCE','E-mail príjemcu');
define('RS_CS_ODESILATEL','E-mail odosielateľa');
define('RS_CS_TEXT_ZPR','Text správy');
define('RS_CS_ODESLAT','Odoslať e-mail');
define('RS_CS_INFO_TEXT','Prostredníctvom tohoto informačného e-mailu môžete upozorniť svoju kamarátku, kamaráta alebo kolegu z práce na tento zaujímavý článok. Odoslanie tohoto e-mailu je podmienené vyplnením elektronickej adresy príjemcu a odosielateľa.');
define('RS_CS_MAIL1',"Zdá sa, že pri čítaní nasledujúceho článku si na Vás niekto spomenul a zaslal Vám tento informačný e-mail!\n\nČlánok:");
define('RS_CS_MAIL2','-- Správa od odosielateľa --');
define('RS_CS_MAIL_PREDMET','Informácia o zaujímavom článku na serveri');
define('RS_CS_DOPIS_OK','E-mail bol úspešne odoslaný!');
define('RS_CS_ERR1','Chyba! E-mail nebol odoslaný.');
define('RS_CS_ERR2','Pozor! Chyba vo vstupných dátach.');
define('RS_CS_ZOBRAZ_CLA','Zobraz celý článok');
define('RS_CS_AUTOR','Autor');
define('RS_CS_TEMA','Téma');
define('RS_CS_ZDROJ','Zdroj');
define('RS_CS_VYDANO_DNE','Vydané dňa');
define('RS_CS_POCET_CTENI','Počet prečítaní');
define('RS_CS_TISK',' TLAČ ');
define('RS_CS_ERR_TISK','Váš WWW klient nepodporuje túto funkciu. Tlačte z menu ...');
// vyhledavani (search.php)
define('RS_VY_NULL','Vašej požiadavke nezodpovedá ani jeden článok. Prosím, opakujte vyhľadávanie.');
define('RS_VY_VYSLEDEK_1','Počet zobrazených článkov: ');
define('RS_VY_VYSLEDEK_2','(z celkom');
define('RS_VY_VYSLEDEK_3','nájdených)');
define('RS_VY_NAZEV_CLA','Názov článku');
define('RS_VY_DATUM_VYD','Dátum vydania');
define('RS_VY_AUTOR','Autor');
define('RS_VY_TEMA','Téma');
define('RS_VY_NADPIS','Rozšírené vyhľadávanie');
define('RS_VY_BEZ_OMEZENI','-- žiadne obmedzenie --');
define('RS_VY_HLE_TEXT','Hľadaný text');
define('RS_VY_HLE_AUTOR','Hľadanie obmedziť na autora');
define('RS_VY_HLE_TEMA','Hľadanie obmedziť na tému');
define('RS_VY_HLE_OMEZIT_NA','Hľadanie obmedziť na');
define('RS_VY_HLE_RAZENI','Výsledok triediť podľa');
define('RS_VY_RADIT_DATUM_09','dátumu (vzostupne)');
define('RS_VY_RADIT_DATUM_90','dátumu (zostupne)');
define('RS_VY_RADIT_NAZEV_AZ','názvu článku (vzostupne)');
define('RS_VY_RADIT_NAZEV_ZA','názvu článku (zostupne)');
define('RS_VY_CELY_CLA','celý článok');
define('RS_VY_HLAVNI_CAST','hlavnú časť článku');
define('RS_VY_TITULEK','titulok');
define('RS_VY_UVOD','úvodný text');
define('RS_VY_DB_KLICU','databázu kľúčových slov');
define('RS_VY_VZTAH','Vzťah medzi vyhľadávanými slovami');
define('RS_VY_VZT_NEBO','alebo');
define('RS_VY_VZT_A','a');
define('RS_VY_TL_HLEDAT','Vyhľadať');
define('RS_VY_REDAKCE','Redakcia');
// view modul (view.php, preview.php)
define('RS_VW_ERR1','Pozor chyba! Nie je špecifikované číslo článku, ktorý chcete zobraziť!');
define('RS_VW_ERR2_1','Chyba! Článok číslo');
define('RS_VW_ERR2_2','neexistuje!');
// index (index.php)
define('RS_IN_IDX','index');
define('RS_IN_PRED','predchádzajúci');
define('RS_IN_NASL','nasledujúci');
define('RS_IN_CELKEM_1','Celkom');
define('RS_IN_CELKEM_2','článkov');
define('RS_IN_ERR1_1','Chyba pri zobrazovaní článku číslo');
define('RS_IN_ERR1_2','Systém nemôže nájsť odpovedajúcu šablónu!');
// strankovy alias (showpage.php)
define('RS_SW_ERR1','Systém nemôže identifikovať požadovanú stránku!');
define('RS_SW_ERR2','Požadovaná stránka nebola nájdená!');
// reklamni subsystem (direct.php)
define('RS_DI_ERR1','Pozor chyba! Systém nemôže identifikovať cieľovú oblasť!');
define('RS_DI_ERR2','Pozor chyba! Volaný odkaz neexistuje!');
// deblokace uctu (deblokace.php)
define('RS_DE_ERR1','Systém nemôže nájsť všetky potrebné vstupné premenné!');
define('RS_DE_ERR2','Systém nemôže nájsť požadovaného užívateľa!');
define('RS_DE_VSE_OK','Váš účet bol úspešne odblokovaný!');
// plug-iny
define('RS_PLUG_LOGIN_VITEJ','Vitaj');
define('RS_PLUG_LOGIN_LOGOUT','Odhlásenie čiteteľa');
define('RS_PLUG_LOGIN_NEZNAMY','Neznámy čitateľ');
define('RS_PLUG_LOGIN_USERNAME','Užívateľské meno:');
define('RS_PLUG_LOGIN_PASSWORD','Heslo:');
define('RS_PLUG_LOGIN_ODESLAT','Odoslať');
define('RS_PLUG_LOGIN_REGISTRACE','Registrácia nového čitateľa!');

?>