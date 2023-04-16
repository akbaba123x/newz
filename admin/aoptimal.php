<?php

######################################################################
# phpRS Administration Engine - Optimalization's section 1.0.7
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// vyuzivane tabulky: rs_stat_arch, rs_stat_data, rs_guard

function DBOptStat()
{
$optcas=(time()-3600);
$optdatum=Date("Y-m-d",$optcas);
$opthodina=Date("H",$optcas);

// presun stare statistky do archivni tabulky
@$error=phprs_sql_query("INSERT INTO ".$GLOBALS["rspredpona"]."stat_arch SELECT null, datum, hodina, visit, pages, os_win, os_linux, os_unix, os_mac, os_dalsi FROM ".$GLOBALS["rspredpona"]."stat_data WHERE datum<'".$optdatum."' OR (datum='".$optdatum."' and hodina<='".$opthodina."')",$GLOBALS["dbspojeni"]);
if ($error): // vse OK
  // vymazani presunutych dat
  @phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."stat_data where datum<'".$optdatum."' or (datum='".$optdatum."' and hodina<='".$opthodina."')",$GLOBALS["dbspojeni"]);
endif;
}

function DBOptGuard()
{
// zjisteni platneho intervalu
if (isset($GLOBALS['rsconfig']['platnost_auth'])):
  $pocet_sekund=time()-$GLOBALS['rsconfig']['platnost_auth']-1000;
else:
  $pocet_sekund=time()-864000;
endif;
$konecny_cas=Date("Y-m-d H:i:s",$pocet_sekund);

// vymazani starych session
@phprs_sql_query("delete from ".$GLOBALS["rspredpona"]."guard where cas<='".$konecny_cas."'",$GLOBALS["dbspojeni"]);
}

DBOptStat();
DBOptGuard();

?>