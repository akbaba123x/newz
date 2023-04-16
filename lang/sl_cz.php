<?php
#####################################################################
# phpRS Basic dictionary (Zakladni slovnik) - CZ version 2.8.1
#####################################################################

// Copyright (c) 2001-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// ************************** ctenarske rozhrani / modul ***************************
// standardni hlasky
define("RS_SAVEOK","Vše bylo korektně uloženo!");
define("RS_DBNIC","Databáze neobsahuje žádný odpovídající záznam!");
define("RS_ODESLAT","Odeslat");
define("RS_RESET","Reset");
define("RS_TLOK","OK");
define("RS_ANO","Ano");
define("RS_NE","Ne");
define("RS_TLNE","Ne");
define('RS_AKCE_ERR','Pozor chyba! Nebyla specifikována akce, kterou chcete provést!');
// ankety (ankety.php)
define('RS_AN_ERR1','Při hlasování se vyskytla neočekáváná chyba!');
define('RS_AN_ERR2','Anketní subsystém není schopen identifikovat nebo zobrazit vybranou anketu!'); // P
define('RS_AN_ERR3','Vybraná anketa je již uzavřena!');
define('RS_AN_POCET_HLA','Počet hlasů');
define('RS_AN_CELKEM_HLA','Celkem hlasovalo');
define('RS_AN_ZOBRAZ_VSE','Zobrazit všechny ankety');
define('RS_AN_HLASUJ','Hlasuj');
define('RS_AN_BLOKACE','Zobrazit výsledek');
define('RS_AN_NELZE_HLASOVAT','Anketní subsystém zjistil, že jste v této otázce již jednou hlasovali a v zájmu objektivity této ankety Vám bohužel nemůžeme povolit druhé hlasování. Děkujeme za pochopení!');
define('RS_AN_TL_HLASUJ','Hlasuj!');
define('RS_AN_NADPIS','Ankety');
// komentare (comment.php)
define('RS_KO_NIC','K tomutu článku nebyl doposud přiřazen žádný komentář!');
define('RS_KO_NEREG','neregistrovaný');
define('RS_KO_ZOBRAZ_OZN','Zobrazit jen označené');
define('RS_KO_ZOBRAZ_VSE','Zobrazit všechny komentáře');
define('RS_KO_PRIDAT','Přidat nový komentář');
define('RS_KO_ZPR_ZE_DNE','Komentář ze dne');
define('RS_KO_ZPR_REG','Reagovat');
define('RS_KO_ZPR_AUT','Autor');
define('RS_KO_ZPR_TIT','Titulek');
define('RS_KO_PRAZDNY_VYB','Váš výběr je prázdný! Zopakujte volbu ještě jednou.');
define('RS_KO_ZPR_JME','Jméno (přezdívka)');
define('RS_KO_ZPR_EMAIL','E-mail');
define('RS_KO_ZPR_KONTROLA','Odpovězte, prosím, na následující kontrolní otázku, která nám pomůže rozlišit, zda jste člověk nebo počítačový robot. Bez správné odpovědi na tuto otázku nebude váš komentář uložen. Tato kontrolní otázka se nezobrazuje registrovaným čtenářům.');
define('RS_KO_ZOBRAZ_KOM','Zobrazit komentáře');
define('RS_KO_RE_NA_KOM','Reakce na komentář');
define('RS_KO_ERR1','Při vkládání komentáře do naší databáze se vyskytly neočekávané problémy. Při opakování této chyby nás prosím kontaktujte!<br /><br />Děkujeme');
define('RS_KO_VLOZEN_OK','Váš komentář byl úspěšně vložen do naší databáze!<br /><br />Děkujeme');
define('RS_KO_ERR2','Pozor! Do systému nelze vložit prázdný komentář.');
define('RS_KO_ERR3','Není specifikováno číslo článku!');
define('RS_KO_ERR4','Zadaný článek neexistuje!');
define('RS_KO_ERR5','Pozor chyba! V průběhu vykonávání PHP skriptu se vyskytla neočekávaná chyba.');
define('RS_KO_ERR6','Vaše odpověď na kontrolní otázku je chybná! Zopakujte akci.');
define('RS_KO_NADPIS','Komentáře');
define('RS_KO_KE_CLA','ke článku');
define('RS_KO_ZE_DNE','ze dne');
define('RS_KO_AUTOR_CLA','autor článku');
define('RS_KO_ZOBRAZ_CLA','Zobrazit článek');
define('RS_KO_INFO','V rámci komentářů nelze používat HTML tagy.<br /><br />Pro vložení tučného textu, hyperlinku nebo e-mailové adresy využijte následující značky:<br />[b]tučné[/b], [url]http://www.domeny.cz[/url], [email]jmeno@domena.cz[/email]');
define('RS_KO_JEN_PRO_REGISTROVANE','Vkládat komentáře můžou jen registrovaní čtenáři.');
// download (download.php)
define('RS_DW_JMENO_SB','Název souboru');
define('RS_DW_VEL_SB','Velikost');
define('RS_DW_ZDROJ_SB','Zdroj');
define('RS_DW_DATUM_SB','Datum');
define('RS_DW_VER_SB','Verze');
define('RS_DW_KAT','Kategorie');
define('RS_DW_POCET_STAZ','Počet stažení');
define('RS_DW_KLIKNI','Klikněte pro více informací...');
define('RS_DW_STAHNI','Download...');
define('RS_DW_ZPET','Zpět na přehled souborů');
define('RS_DW_NADPIS','Download sekce');
// engine (engine.php)
define('RS_EN_TOPIC_ERR1','Nebylo nalezeno žádné téma!');
define('RS_EN_LINKS_NADPIS','Weblinks sekce');
define('RS_EN_LINKS_ERR1','V databázi se nenalézá žádný weblinks!');
define('RS_EN_LINKS_ZDROJ','Zdroj');
define('RS_EN_STAT_NADPIS','15 nejčtenějších článků');
define('RS_EN_STAT_ERR1','Nebyl nalezen žádný přečtený článek!');
// specialni funkce (specfce.php)
define('RS_SP_POCET_HLA','hl.');
define('RS_SP_CELKEM_HLA','Celkem hlasovalo');
define('RS_SP_KAL_PO','Po');
define('RS_SP_KAL_UT','Út');
define('RS_SP_KAL_ST','St');
define('RS_SP_KAL_CT','Čt');
define('RS_SP_KAL_PA','Pá');
define('RS_SP_KAL_SO','So');
define('RS_SP_KAL_NE','Ne');
define('RS_SP_KAL_M01','Leden');
define('RS_SP_KAL_M02','Únor');
define('RS_SP_KAL_M03','Březen');
define('RS_SP_KAL_M04','Duben');
define('RS_SP_KAL_M05','Květen');
define('RS_SP_KAL_M06','Červen');
define('RS_SP_KAL_M07','Červenec');
define('RS_SP_KAL_M08','Srpen');
define('RS_SP_KAL_M09','Září');
define('RS_SP_KAL_M10','Říjen');
define('RS_SP_KAL_M11','Listopad');
define('RS_SP_KAL_M12','Prosinec');
define('RS_SP_SOUVIS_CLA','Související články');
define('RS_SP_AKT_ZNAMKA','Akt. známka');
define('RS_SP_POCET_ZNAMEK','Počet hlasů');
define('RS_SP_TL_ZNAMKA','Známkování');
// ctenari (readers.php)
define('RS_CT_NADPIS','Úprava osobního nastavení');
define('RS_CT_JMENO','Jméno (přezdívka)');
define('RS_CT_HESLO','Heslo');
define('RS_CT_NAVIG_REG_NOVY','Registrace nového čtenáře');
define('RS_CT_NAVIG_ZRUSIT','Zrušit účet');
define('RS_CT_NAVIG_ZAPOMNEL','Zapomněl jsem heslo');
define('RS_CT_TL_ZAREG','Zaregistrovat');
define('RS_CT_TL_ULOZ','Uložit změny');
define('RS_CT_NOVY_LOGIN','Nové přihlášení');
define('RS_CT_ERR_1','Špatné uživatelské jméno nebo heslo!');
define('RS_CT_ERR_2','Systém nemůže identifikovat čtenáře!');
define('RS_CT_INFO_HESLO','(zadejte pouze v případě změny)'); // P
define('RS_CT_HESLO_KONTRLA','Heslo (potvrzení)');
define('RS_CT_CELE_JMENO','Jméno');
define('RS_CT_EMAIL','E-mail');
define('RS_CT_JAZYK','Jazyk prostředí');
define('RS_CT_NOVINKY','Chci, abyste mi zasílali informace o novinkách na vašem serveru na výše uvedenou e-mailovou adresu!');
define('RS_CT_VLASTNI_MENU','Vytvořte si své vlastní menu.<br />Nelze používat HTML tagy, pro vložení tučného textu, hyperlinku nebo e-mailové adresy využijte následující značky:<br />[b]tučné[/b], [url]http://www.domeny.cz[/url], [email]jmeno@domena.cz[/email]');
define('RS_CT_CHCI_ME_MENU','Chci, aby se zobrazovalo mé menu!');
define('RS_CT_INFO_POVINNA','Přezdívka a heslo patří mezi povinná pole.');
define('RS_CT_ERR_3','Některé z povinných polí je prázdné!');
define('RS_CT_ERR_4_A','Vámi vybraná přezdívka');
define('RS_CT_ERR_4_B','je již obsazená! Zvolte si jinou.');
define('RS_CT_ERR_5','V průběhu zakládání vašeho profilu došlo k neočekávané chybě. Zopakujte akci.');
define('RS_CT_ERR_6','Neočekávaná chyba!');
define('RS_CT_REG_VSE_OK','Vaše registrace byla úspěšně dokončena.');
define('RS_CT_HL_STR','Hlavní stránka');
define('RS_CT_OSOBNI_UCET','Úprava osobního účtu');
define('RS_CT_ERR_7','V průběhu úpravy vašeho profilu došlo k neočekávané chybě. Zopakujte akci.');
define('RS_CT_EDIT_VSE_OK','Vaše osobní nastavení bylo aktualizováno.');
define('RS_CT_INFO_ZRUSIT','Pokud si přejete zrušit registraci, zadejte své uživatelské jméno a heslo.');
define('RS_CT_TL_ZRUSIT','Zrušit registraci');
define('RS_CT_ERR_8','V průběhu odstraňování registrace došlo k neočekávané chybě!');
define('RS_CT_DEL_VSE_OK','Vaše registrace byla úspěšně zrušena!');
define('RS_CT_INFO_ZAPOMNEL','Zapomněli jste heslo ke svému účtu? Nevadí, můžete si nechat vygenerovat nové.<br /><br />Zadejte své registrační uživatelské jméno a e-mailovou adresu.<br />Po ověření správnosti obou údajů vám bude zasláno nově vygenerované heslo.');
define('RS_CT_DEL_EMAIL_ADR','E-mailová adresa');
define('RS_CT_TL_NOVE_HESLO','Chci nové heslo');
define('RS_CT_ERR_9','Špatné uživatelské jméno nebo e-mailová adresa!');
define('RS_CT_ERR_10','V průběhu ukládání nového hesla došlo k neočekávané chybě!');
define('RS_CT_NOVE_HESLO_VSE_OK','Nastavení nového hesla bylo úspěšně provedeno!');
define('RS_CT_ERR_11','V průběhu odesílání informačního e-mailu došlo k neočekávané chybě!');
define('RS_CT_SEND_MAIL_VSE_OK','Na vaši e-mailovou adresu byl úspěšně odeslán informační e-mail!');
define('RS_CT_ERR_12','Zadaná hesla nejsou shodná!');
define('RS_CT_ERR_13','Délka vašeho jména (přezdívky) porušuje pravidla. - Minimální povolená délka je 3 znaky; maximální hodnota je 12 znaků.');
define('RS_CT_ERR_14','Obsah vašeho jména (přezdívky) nebo celého jména porušuje pravidla. Údaje nesmí obsahovat HTML znaky'); // N
// ctenari - obsah info mailu pri generovani noveho hesla (readers.php)
define('RS_CT_GNH_PREDMET','Nové heslo k účtu');
define('RS_CT_GNH_OBS_1','Vaše nové heslo k účtu');
define('RS_CT_GNH_OBS_2','je');
// ctenarske sluzby (rservice.php)
define('RS_CS_NADPIS','Informační e-mail');
define('RS_CS_CLANEK','Článek');
define('RS_CS_PRIJEMCE','E-mail příjemce');
define('RS_CS_ODESILATEL','E-mail odesílatele');
define('RS_CS_TEXT_ZPR','Text zprávy');
define('RS_CS_ODESLAT','Odeslat e-mail');
define('RS_CS_INFO_TEXT','Prostřednictvím tohoto informačního e-mailu můžete upozornit svou kamarádku, kamaráda nebo kolegu z práce na tento zajímavý článek. Odeslání tohoto e-mailu je podmíněno vyplněním elektronické adresy příjemce a odesílatele.');
define('RS_CS_MAIL1',"Zdá se, že při čtění následujícího článku si na Vás někdo vzpomněl a zaslal Vám tento informační e-mail!\n\nČlánek:");
define('RS_CS_MAIL2','-- Zpráva od odesílatele --');
define('RS_CS_MAIL_PREDMET','Informace o zajímavém článku na serveru');
define('RS_CS_DOPIS_OK','Dopis byl úspěšně odeslán!');
define('RS_CS_ERR1','Chyba! Dopis nebyl odeslán.');
define('RS_CS_ERR2','Pozor! Chyba ve vstupních datech.');
define('RS_CS_ZOBRAZ_CLA','Zobraz celý článek');
define('RS_CS_AUTOR','Autor');
define('RS_CS_TEMA','Téma');
define('RS_CS_ZDROJ','Zdroj');
define('RS_CS_VYDANO_DNE','Vydáno dne');
define('RS_CS_POCET_CTENI','Počet přečtení');
define('RS_CS_TISK',' TISK ');
define('RS_CS_ERR_TISK','Váš WWW klient nepodporujete tuto funkci. Tiskněte z menu ...');
// vyhledavani (search.php)
define('RS_VY_NULL','Vašemu dotazu neodpovídá ani jeden článek. Prosím, opakujte vyhledávání.');
define('RS_VY_VYSLEDEK_1','Počet zobrazených článků: ');
define('RS_VY_VYSLEDEK_2','(z celkem');
define('RS_VY_VYSLEDEK_3','nalezených)');
define('RS_VY_NAZEV_CLA','Název článku');
define('RS_VY_DATUM_VYD','Datum vydání');
define('RS_VY_AUTOR','Autor');
define('RS_VY_TEMA','Téma');
define('RS_VY_NADPIS','Rozšířené vyhledávání');
define('RS_VY_BEZ_OMEZENI','-- žádné omezení --');
define('RS_VY_HLE_TEXT','Hledaný text');
define('RS_VY_HLE_AUTOR','Hledání omezit na autora');
define('RS_VY_HLE_TEMA','Hledání omezit na téma');
define('RS_VY_HLE_OMEZIT_NA','Hledání omezit na');
define('RS_VY_HLE_RAZENI','Výsledek třídit dle');
define('RS_VY_RADIT_DATUM_09','data (vzestupně)');
define('RS_VY_RADIT_DATUM_90','data (sestupně)');
define('RS_VY_RADIT_NAZEV_AZ','názvu článku (vzestupně)');
define('RS_VY_RADIT_NAZEV_ZA','názvu článku (sestupně)');
define('RS_VY_CELY_CLA','celý článek');
define('RS_VY_HLAVNI_CAST','hlavní část článku');
define('RS_VY_TITULEK','titulek');
define('RS_VY_UVOD','úvodní text');
define('RS_VY_DB_KLICU','databázi klíčových slov');
define('RS_VY_VZTAH','Vztah mezi vyhledávanými slovy');
define('RS_VY_VZT_NEBO','nebo');
define('RS_VY_VZT_A','a');
define('RS_VY_TL_HLEDAT','Vyhledat');
define('RS_VY_REDAKCE','Redakce');
// view modul (view.php, preview.php)
define('RS_VW_ERR1','Pozor chyba! Není specifikováno číslo článku, který chcete zobrazit!');
define('RS_VW_ERR2_1','Chyba! Článek číslo');
define('RS_VW_ERR2_2','neexistuje!');
// index (index.php)
define('RS_IN_IDX','index');
define('RS_IN_PRED','předchozí');
define('RS_IN_NASL','následující');
define('RS_IN_CELKEM_1','Celkem');
define('RS_IN_CELKEM_2','článků');
define('RS_IN_ERR1_1','Chyba při zobrazování článku číslo');
define('RS_IN_ERR1_2','Systém nemůže nalézt odpovídající šablonu!');
// strankovy alias (showpage.php)
define('RS_SW_ERR1','Systém nemůže identifikovat požadovanou stránku!');
define('RS_SW_ERR2','Požadovaná stránka nenalezena!');
// reklamni subsystem (direct.php)
define('RS_DI_ERR1','Pozor chyba! Systém nemůže identifikovat cílovou oblast!');
define('RS_DI_ERR2','Pozor chyba! Volaný odkaz neexistuje!');
// deblokace uctu (deblokace.php)
define('RS_DE_ERR1','Systém nemůže nalézt veškeré nezbytné vstupní proměnné!');
define('RS_DE_ERR2','Systém nemůže nalézt požadovaného uživatele!');
define('RS_DE_VSE_OK','Váš účet byl úspěšně odblokován!');
// plug-iny
define('RS_PLUG_LOGIN_VITEJ','Vítej');
define('RS_PLUG_LOGIN_LOGOUT','Odhlášení čtenáře');
define('RS_PLUG_LOGIN_NEZNAMY','Neznámý čtenář');
define('RS_PLUG_LOGIN_USERNAME','Uživatelské jméno:');
define('RS_PLUG_LOGIN_PASSWORD','Heslo:');
define('RS_PLUG_LOGIN_ODESLAT','Odeslat');
define('RS_PLUG_LOGIN_REGISTRACE','Registrace nového čtenáře!');

?>