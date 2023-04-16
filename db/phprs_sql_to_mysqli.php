<?php

######################################################################
# phpRS konverze DB funkci: phpRS_SQL to MySQLi (MySQL v4.1 a vyssi) 1.0.6
######################################################################

// Copyright (c) 2001-2019 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!defined('IN_CODE')): die('Nepovoleny pristup! / Hacking attempt!'); endif;

/*
phprs_sql_affected_rows -- Vrátí počet ovlivněných (změněných) záznamů v MySQL po posledním dotazu
phprs_sql_client_encoding -- Vrátí název znakové sady
phprs_sql_close -- Ukončí (zavře) MySQL spojení
phprs_sql_data_seek -- Přesune ukazatel na aktuální záznam
phprs_sql_dbcon -- Vytvoří spojení s MySQL serverem
phprs_sql_errno --  Vrátí číslenou hodnotu chybové hlášky předchozího MySQL příkazu.
phprs_sql_error --  Vrátí text chybové zprávy předchozího MySQL příkazu.
phprs_sql_escape_string --  Upraví řetězec pro bezpečné použití v mysqli_query.
phprs_sql_fetch_array --  Načte výsledný řádek do asociativního, čísleného pole nebo obojího.
phprs_sql_fetch_assoc --  Načte výsledný řádek do asociativního pole
phprs_sql_fetch_field --  Načte informace o sloupci z výsledku do proměnné objektu
phprs_sql_fetch_lengths --  Zjistí délku všech položek aktuálního výstupu
phprs_sql_fetch_object --  Načte výsledný záznam do proměnné objektu
phprs_sql_fetch_row -- Načte výsledný záznam do pole
phprs_sql_field_seek --  Nastaví ukazatel na zadaný sloupec
phprs_sql_free_result -- Uvolní výsledek z paměti
phprs_sql_info --  Vrací informace o posledním dotazu
phprs_sql_insert_id --  Vrací generovanou hodnotu id posledního příkazu INSERT
phprs_sql_num_fields -- Vrací počet sloupců ve výsledku
phprs_sql_num_rows -- Vrací počet záznamů ve výsledku
phprs_sql_ping -- Ověří spojení se serverem, případně, není-li spojení dostupné, pokusí se připojit znovu.
phprs_sql_query -- Pošle MySQL dotaz
phprs_sql_query_add_limit -- Pošle MySQL dotaz; před odesláním se provedene spojení části "dotaz" a "limit" (lze pouzit pouze pro jednodussi SQL dotazy)
phprs_sql_real_escape_string --  Upraví řetězec pro bezpečné použití v mysqli_query.
phprs_sql_result -- Načte obsah jednoho sloupce tabulky
phprs_sql_select_db -- Nastaví MySQL databázi
phprs_sql_stat -- Vrací aktuální stav systému

UPOZORNENI:
Pro fungovani funkce "phprs_sql_escape_string" musi byt identifikator spojeni ulozen v promenne $GLOBALS['dbspojeni']
*/

function phprs_sql_affected_rows($spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_affected_rows($spojeni);
endif;
}

function phprs_sql_client_encoding($spojeni = false)
{
if ($spojeni === false):
  return NULL; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_client_encoding($spojeni);
endif;
}

function phprs_sql_close($spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_close($spojeni);
endif;
}

function phprs_sql_data_seek($vysledek,$cislo_zaznamu)
{
return mysqli_data_seek($vysledek,$cislo_zaznamu);
}

function phprs_sql_dbcon()
{
@$spojeni=mysqli_connect($GLOBALS["dbserver"], $GLOBALS["dbuser"], $GLOBALS["dbpass"], $GLOBALS["dbname"], $GLOBALS["dbport"]);

if (!$spojeni):
  die('Spojeni se serverem nelze vytvorit! / Could not connect to database server!');
endif;
//mysqli_select_db($spojeni,$GLOBALS["dbname"]);
return $spojeni;
}

function phprs_sql_errno($spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_errno($spojeni);
endif;
}

function phprs_sql_error($spojeni = false)
{
if ($spojeni === false):
  return NULL; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_error($spojeni);
endif;
}

function phprs_sql_escape_string($neupraveny_retezec)
{
if (empty($GLOBALS['dbspojeni'])): // chyba; MySQLi vyzaduje znalost "spojeni"
  die("DB ERR");
else:
  return mysqli_real_escape_string($GLOBALS['dbspojeni'],$neupraveny_retezec);
endif;
}

function phprs_sql_fetch_array($vysledek,$typ_vysledku = false)
{
if ($typ_vysledku === false):
  return mysqli_fetch_array($vysledek);
else:
  return mysqli_fetch_array($vysledek,$typ_vysledku);
endif;
}

function phprs_sql_fetch_assoc($vysledek)
{
return mysqli_fetch_assoc($vysledek);
}

function phprs_sql_fetch_field($vysledek,$poradi_sloupce = false)
{
if ($poradi_sloupce === false):
  return mysqli_fetch_field($vysledek);
else:
  return mysqli_fetch_field($vysledek,$poradi_sloupce);
endif;
}

function phprs_sql_fetch_lengths($vysledek)
{
return mysqli_fetch_lengths($vysledek);
}

function phprs_sql_fetch_object($vysledek)
{
return mysqli_fetch_object($vysledek);
}

function phprs_sql_fetch_row($vysledek)
{
return mysqli_fetch_row($vysledek);
}

function phprs_sql_field_seek($vysledek,$cislo_sloupce)
{
return mysqli_field_seek($vysledek,$cislo_sloupce);
}

function phprs_sql_free_result($vysledek)
{
return mysqli_free_result($vysledek);
}

function phprs_sql_info($spojeni = false)
{
if ($spojeni === false):
  return NULL; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_info($spojeni);
endif;
}

function phprs_sql_insert_id($spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_insert_id($spojeni);
endif;
}

function phprs_sql_num_fields($vysledek)
{
//return mysqli_num_fields($vysledek);
return $vysledek->field_count;
}

function phprs_sql_num_rows($vysledek)
{
//return mysqli_num_rows($vysledek);
return $vysledek->num_rows;
}

function phprs_sql_ping($spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_ping($spojeni);
endif;
}

function phprs_sql_query($dotaz,$spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  @$vysledek=mysqli_query($spojeni,$dotaz);
  if ($vysledek === false):
    echo mysqli_error($spojeni);
  endif;
  return $vysledek;
endif;
}

function phprs_sql_query_add_limit($dotaz,$dotaz_limit_mnozstvi = NULL,$dotaz_limit_od = NULL,$spojeni = false)
{
if (!is_null($dotaz_limit_mnozstvi)):
  if (!is_null($dotaz_limit_od)):
    $dotaz.=' limit '.$dotaz_limit_od.','.$dotaz_limit_mnozstvi;
  else:
    $dotaz.=' limit '.$dotaz_limit_mnozstvi;
  endif;
endif;

if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  @$vysledek=mysqli_query($spojeni,$dotaz);
  if ($vysledek === false):
    echo mysqli_error($spojeni);
  endif;
  return $vysledek;
endif;
}

function phprs_sql_real_escape_string($neupraveny_retezec, $spojeni = false)
{
if (empty($GLOBALS['dbspojeni'])): // chyba; MySQLi vyzaduje znalost "spojeni"
  die("DB ERR");
else:
  if ($spojeni === false) {
    $spojeni = $GLOBALS['dbspojeni'];
  }
  return mysqli_real_escape_string($spojeni, $neupraveny_retezec);
endif;
}

function phprs_sql_result($vysledek,$zaznam,$pole = false)
{
	die("DB ERR - SQL_RESULT neni soucasti MySQLi"); // neni soucasti MySQLi

}

function phprs_sql_select_db($jmeno_databaze,$spojeni = false)
{
if ($spojeni === false):
  return false; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_select_db($spojeni,$jmeno_databaze);
endif;
}

function phprs_sql_stat($spojeni = false)
{
if ($spojeni === false):
  return NULL; // chyba; MySQLi vyzaduje "spojeni"
else:
  return mysqli_stat($spojeni);
endif;
}
?>