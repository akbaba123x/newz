<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "ankety" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// rozcestnik
define('RS_AKT_ROZ_VIEW_AKT','Přehled anket');
define('RS_AKT_ROZ_ADD_AKT','Přidání ankety');
define('RS_AKT_ROZ_DEL_AKT','Vymazání ankety');
define('RS_AKT_ROZ_EDIT_AKT','Úprava ankety');
define('RS_AKT_ROZ_NASTAV_AKT','Nastavení aktivní ankety');
// pomocne fce
define('RS_AKT_POM_ERR_BEZ_ANKETY','-- žádná anketa není aktivní --'); // N
// hlavni fce - sprava anket
define('RS_AKT_SA_PRIDAT_ANKETU','Přidat novou anketu');
define('RS_AKT_SA_NASTAV_ANKETU','Nastavit aktivní anketu');
define('RS_AKT_SA_DEAKTIV_ANKETU','Deaktivovat anketu');
define('RS_AKT_SA_ZPET','Zpět na hlavní stránku sekce');
define('RS_AKT_SA_ZOBRAZ_ANKETU','Zobraz ankety');
define('RS_AKT_SA_OD','od');
define('RS_AKT_SA_DO','do');
define('RS_AKT_SA_CELK_POCET','Celkem anket:'); // P
define('RS_AKT_SA_TITULEK','Titulek');
define('RS_AKT_SA_OTAZKA','Otázka');
define('RS_AKT_SA_DATUM','Datum vydání');
define('RS_AKT_SA_ZOBRAZIT','Zobrazit');
define('RS_AKT_SA_UZAMCENO','Uzamčeno');
define('RS_AKT_SA_AUTOR','Autor');
define('RS_AKT_SA_AKCE','Akce');
define('RS_AKT_SA_SMAZ','Smaž');
define('RS_AKT_SA_UPRAVIT','Edituj');
define('RS_AKT_SA_SMAZ_OZNAC','Vymaž všechny označené ankety'); // N
define('RS_AKT_SA_FORM_TITULEK','Titulek');
define('RS_AKT_SA_FORM_TITULEK_INFO','Titulek slouží pouze k administrativním účel (nutno zadat)!');
define('RS_AKT_SA_FORM_OTAZKA','Otázka');
define('RS_AKT_SA_FORM_OTAZKA_INFO','Sem vložte svou anketní otázku!');
define('RS_AKT_SA_FORM_ZOBRAZIT','Zobrazit anketu');
define('RS_AKT_SA_FORM_STAV','Stav ankety');
define('RS_AKT_SA_FORM_STAV_OPEN','Otevřena');
define('RS_AKT_SA_FORM_STAV_CLOSE','Uzamčena');
define('RS_AKT_SA_FORM_DATUM','Datum vložení');
define('RS_AKT_SA_FORM_DATUM_INFO','RRRR-MM-DD HH:MM:SS');
define('RS_AKT_SA_FORM_AUTOR','Autor');
define('RS_AKT_SA_FORM_ODPOVED_CIS','Odpověď č.');
define('RS_AKT_SA_FORM_POCET_HLA','Počet hlasů');
define('RS_AKT_SA_FORM_ULOZ_ZMENY','Ulož změny');
define('RS_AKT_SA_FORM_VYMAZ_ODPOVED','Vymaž odpověď');
define('RS_AKT_SA_FORM_VLOZ_INFO','Vložte text odpovědi');
define('RS_AKT_SA_FORM_AKTIV_AKT','Aktivní anketa');
define('RS_AKT_SA_TL_VLOZ_ODPOVED','Vlož novou odpověď');
define('RS_AKT_SA_ADD_NADPIS_ODPOVEDI','Definice odpovědí');
define('RS_AKT_SA_ADD_ODPOVEDI_INFO','(Pouze vyplněné textové pole je přijato jako platná odpověď. Prázdná pole jsou automaticky ignorována!)'); // P
define('RS_AKT_SA_EDIT_NADPIS_ODPOVEDI','Editace odpovědí');
define('RS_AKT_SA_EDIT_ODPOVEDI_INFO','(Pozor! Vždy lze upravovat pouze jednu odpověď nebo zadání ankety.)');
define('RS_AKT_SA_OK_ADD_AKT','Akce proběhla úspěšně! Anketa byla přidána.');
define('RS_AKT_SA_OK_DEL_AKT_NIC','Neoznačili jste ani jednu anketu!'); // N
define('RS_AKT_SA_OK_DEL_AKT_JEDNA','Akce proběhla úspěšně! Anketa byla vymazána.'); // N
define('RS_AKT_SA_OK_DEL_AKT_VICE','Akce proběhla úspěšně! Ankety byly vymazány.'); // N
define('RS_AKT_SA_ERR_DEL_AKT_PRAVA','Akci nelze provést, protože nemáte dostatečná přístupová práva!'); // N
define('RS_AKT_SA_ERR_DEL_AKT_VAZBA_CLA','Akci nelze provést, protože existuje alespoň jedna vazba mezi anketou a článkem!'); // N
define('RS_AKT_SA_OK_EDIT_AKT','Akce proběhla úspěšně! Anketa byla aktualizována.');
define('RS_AKT_SA_OK_ODP_EDIT_AKT','Akce proběhla úspěšně! Odpověď byla aktualizována.');
define('RS_AKT_SA_OK_ODP_DEL_AKT','Akce proběhla úspěšně! Odpověď byla vymazána.');
define('RS_AKT_SA_OK_ODP_ADD_AKT','Akce proběhla úspěšně! Byla přidána nová odpověď.');
define('RS_AKT_SA_ERR_ODP_PRAVA','Akci nelze provést, protože nemáte dostatečná přístupová práva!'); // N
define('RS_AKT_SA_OK_NASTAV_AKT','Akce proběhla úspěšně! Konfigurace byla aktualizována.');
define('RS_AKT_SA_ERR_AKTIVNI_AKT','Požadovanou akci nelez provést, jelikož tato anketa je nastavena jako aktivní.<br /><br />Deaktivujte ji a opakujte tuto akci!');
?>
