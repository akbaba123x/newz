<?php
#####################################################################
# phpRS Admin dictionary (Admin slovnik) - modul: "ankety" - version 1.0.2
#####################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.
// preklad: Michal [zvalo] Zvalený (michal@zvaleny.sk); http://www.zvaleny.sk

// rozcestnik
define('RS_AKT_ROZ_VIEW_AKT','Prehľad ankiet');
define('RS_AKT_ROZ_ADD_AKT','Pridanie ankety');
define('RS_AKT_ROZ_DEL_AKT','Vymazanie ankety');
define('RS_AKT_ROZ_EDIT_AKT','Úprava ankety');
define('RS_AKT_ROZ_NASTAV_AKT','Nastavenie aktívnej ankety');
// pomocne fce
define('RS_AKT_POM_ERR_BEZ_ANKETY','-- žiadna anketa nie je aktívna --'); // N
// hlavni fce - sprava anket
define('RS_AKT_SA_PRIDAT_ANKETU','Pridať novú anketu');
define('RS_AKT_SA_NASTAV_ANKETU','Nastaviť aktívnu anketu');
define('RS_AKT_SA_DEAKTIV_ANKETU','Deaktivovať anketu');
define('RS_AKT_SA_ZPET','Späť na hlavnú stránku sekcie');
define('RS_AKT_SA_ZOBRAZ_ANKETU','Zobraz ankety');
define('RS_AKT_SA_OD','od');
define('RS_AKT_SA_DO','do');
define('RS_AKT_SA_CELK_POCET','Celkom ankiet:'); // P
define('RS_AKT_SA_TITULEK','Titulok');
define('RS_AKT_SA_OTAZKA','Otázka');
define('RS_AKT_SA_DATUM','Dátum vydania');
define('RS_AKT_SA_ZOBRAZIT','Zobraziť');
define('RS_AKT_SA_UZAMCENO','Uzamknuté');
define('RS_AKT_SA_AUTOR','Autor');
define('RS_AKT_SA_AKCE','Akcia');
define('RS_AKT_SA_SMAZ','Zmaž');
define('RS_AKT_SA_UPRAVIT','Edituj');
define('RS_AKT_SA_SMAZ_OZNAC','Vymaž všetky označené ankety'); // N
define('RS_AKT_SA_FORM_TITULEK','Titulok');
define('RS_AKT_SA_FORM_TITULEK_INFO','Titulok slúži len k administratívnym účelom (nutné zadať)!');
define('RS_AKT_SA_FORM_OTAZKA','Otázka');
define('RS_AKT_SA_FORM_OTAZKA_INFO','Sem vložte svoju anketnú otázku!');
define('RS_AKT_SA_FORM_ZOBRAZIT','Zobraziť anketu');
define('RS_AKT_SA_FORM_STAV','Stav ankety');
define('RS_AKT_SA_FORM_STAV_OPEN','Otvorená');
define('RS_AKT_SA_FORM_STAV_CLOSE','Uzamknutá');
define('RS_AKT_SA_FORM_DATUM','Dátum vloženia');
define('RS_AKT_SA_FORM_DATUM_INFO','RRRR-MM-DD HH:MM:SS');
define('RS_AKT_SA_FORM_AUTOR','Autor');
define('RS_AKT_SA_FORM_ODPOVED_CIS','Odpoveď č.');
define('RS_AKT_SA_FORM_POCET_HLA','Počet hlasov');
define('RS_AKT_SA_FORM_ULOZ_ZMENY','Ulož zmeny');
define('RS_AKT_SA_FORM_VYMAZ_ODPOVED','Vymaž odpoveď');
define('RS_AKT_SA_FORM_VLOZ_INFO','Vložte text odpovede');
define('RS_AKT_SA_FORM_AKTIV_AKT','Aktívna anketa');
define('RS_AKT_SA_TL_VLOZ_ODPOVED','Vlož novú odpoveď');
define('RS_AKT_SA_ADD_NADPIS_ODPOVEDI','Definícia odpovedí');
define('RS_AKT_SA_ADD_ODPOVEDI_INFO','(Len vyplnené textové pole je prijaté ako platná odpoveď. Prázdne polia sú automaticky ignorované!)'); // P
define('RS_AKT_SA_EDIT_NADPIS_ODPOVEDI','Editácia odpovedí');
define('RS_AKT_SA_EDIT_ODPOVEDI_INFO','(Pozor! Vždy možno upravovať len jednu odpoveď alebo zadanie ankety.)');
define('RS_AKT_SA_OK_ADD_AKT','Akcia prebehla úspešne! Anketa bola pridaná.');
define('RS_AKT_SA_OK_DEL_AKT_NIC','Neoznačili ste ani jednu anketu!'); // N
define('RS_AKT_SA_OK_DEL_AKT_JEDNA','Akcia prebehla úspešne! Anketa bola vymazaná.'); // N
define('RS_AKT_SA_OK_DEL_AKT_VICE','Akcia prebehla úspešne! Ankety boli vymazané.'); // N
define('RS_AKT_SA_ERR_DEL_AKT_PRAVA','Akciu nemožno vykonať, pretože nemáte dostatočné prístupové práva!'); // N
define('RS_AKT_SA_ERR_DEL_AKT_VAZBA_CLA','Akciu nemožno vykonať, pretože existuje aspoň jedna väzba medzi anketou a článkom!'); // N
define('RS_AKT_SA_OK_EDIT_AKT','Akcia prebehla úspešne! Anketa bola aktualizovaná.');
define('RS_AKT_SA_OK_ODP_EDIT_AKT','Akcia prebehla úspešne! Odpoveď bola aktualizovaná.');
define('RS_AKT_SA_OK_ODP_DEL_AKT','Akcia prebehla úspešne! Odpoveď bola vymazaná.');
define('RS_AKT_SA_OK_ODP_ADD_AKT','Akcia prebehla úspešne! Bola pridaná nová odpoveď.');
define('RS_AKT_SA_ERR_ODP_PRAVA','Akciu nemožno vykonať, pretože nemáte dostatočné prístupové práva!'); // N
define('RS_AKT_SA_OK_NASTAV_AKT','Akcia prebehla úspešne! Konfigurácia bola aktualizovaná.');
define('RS_AKT_SA_ERR_AKTIVNI_AKT','Požadovanú akciu nemožno vykonať, pretože táto anketa je nastavená ako aktívna.<br /><br />Deaktivujte ju a opakujte túto akciu!');
?>
