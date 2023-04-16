<?php

######################################################################
# phpRS konverze DB funkci: phpRS_SQL to MySQL (MySQL do v4.1) 1.0.1
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
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
phprs_sql_escape_string --  Upraví řetězec pro bezpečné použití v mysql_query.
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
phprs_sql_real_escape_string --  Upraví řetězec pro bezpečné použití v mysql_query.
phprs_sql_result -- Načte obsah jednoho sloupce tabulky
phprs_sql_select_db -- Nastaví MySQL databázi
phprs_sql_stat -- Vrací aktuální stav systému
*/

function phprs_sql_affected_rows($spojeni = false)
{
if ($spojeni === false):
  return mysql_affected_rows();
else:
  return mysql_affected_rows($spojeni);
endif;
}

function phprs_sql_client_encoding($spojeni = false)
{
if ($spojeni === false):
  return mysql_client_encoding();
else:
  return mysql_client_encoding($spojeni);
endif;
}

function phprs_sql_close($spojeni = false)
{
if ($spojeni === false):
  return mysql_close();
else:
  return mysql_close($spojeni);
endif;
}

function phprs_sql_data_seek($vysledek,$cislo_zaznamu)
{
return mysql_data_seek($vysledek,$cislo_zaznamu);
}

function phprs_sql_dbcon()
{
@$spojeni=mysql_connect($GLOBALS["dbserver"].':'.$GLOBALS["dbport"],$GLOBALS["dbuser"],$GLOBALS["dbpass"]);
if (!$spojeni):
  die('Spojeni se serverem nelze vytvorit! / Could not connect to database server!');
endif;
mysql_select_db($GLOBALS["dbname"],$spojeni);
return $spojeni;
}

function phprs_sql_errno($spojeni = false)
{
if ($spojeni === false):
  return mysql_errno();
else:
  return mysql_errno($spojeni);
endif;
}

function phprs_sql_error($spojeni = false)
{
if ($spojeni === false):
  return mysql_error();
else:
  return mysql_error($spojeni);
endif;
}

function phprs_sql_escape_string($neupraveny_retezec)
{
return mysql_escape_string($neupraveny_retezec);
}

function phprs_sql_fetch_array($vysledek,$typ_vysledku = false)
{
if ($typ_vysledku === false):
  return mysql_fetch_array($vysledek);
else:
  return mysql_fetch_array($vysledek,$typ_vysledku);
endif;
}

function phprs_sql_fetch_assoc($vysledek)
{
return mysql_fetch_assoc($vysledek);
}

function phprs_sql_fetch_field($vysledek,$poradi_sloupce = false)
{
if ($poradi_sloupce === false):
  return mysql_fetch_field($vysledek);
else:
  return mysql_fetch_field($vysledek,$poradi_sloupce);
endif;
}

function phprs_sql_fetch_lengths($vysledek)
{
return mysql_fetch_lengths($vysledek);
}

function phprs_sql_fetch_object($vysledek)
{
return mysql_fetch_object($vysledek);
}

function phprs_sql_fetch_row($vysledek)
{
return mysql_fetch_row($vysledek);
}

function phprs_sql_field_seek($vysledek,$cislo_sloupce)
{
return mysql_field_seek($vysledek,$cislo_sloupce);
}

function phprs_sql_free_result($vysledek)
{
return mysql_free_result($vysledek);
}

function phprs_sql_info($spojeni = false)
{
if ($spojeni === false):
  return mysql_info();
else:
  return mysql_info($spojeni);
endif;
}

function phprs_sql_insert_id($spojeni = false)
{
if ($spojeni === false):
  return mysql_insert_id();
else:
  return mysql_insert_id($spojeni);
endif;
}

function phprs_sql_num_fields($vysledek)
{
return mysql_num_fields($vysledek);
}

function phprs_sql_num_rows($vysledek)
{
return mysql_num_rows($vysledek);
}

function phprs_sql_ping($spojeni = false)
{
if ($spojeni === false):
  return mysql_ping();
else:
  return mysql_ping($spojeni);
endif;
}

function phprs_sql_query($dotaz,$spojeni = false)
{
if ($spojeni === false):
  @$vysledek=mysql_query($dotaz);
  if ($vysledek==0):
    echo mysql_error();
  endif;
  return $vysledek;
else:
  @$vysledek=mysql_query($dotaz,$spojeni);
  if ($vysledek==0):
    echo mysql_error($spojeni);
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
  @$vysledek=mysql_query($dotaz);
  if ($vysledek==0):
    echo mysql_error();
  endif;
  return $vysledek;
else:
  @$vysledek=mysql_query($dotaz,$spojeni);
  if ($vysledek==0):
    echo mysql_error($spojeni);
  endif;
  return $vysledek;
endif;
}

function phprs_sql_real_escape_string($neupraveny_retezec,$spojeni = false)
{
if ($spojeni === false):
  return mysql_real_escape_string($neupraveny_retezec);
else:
  return mysql_real_escape_string($neupraveny_retezec,$spojeni);
endif;
}

function phprs_sql_result($vysledek,$zaznam,$pole = false)
{
if ($pole === false):
  return mysql_result($vysledek,$zaznam);
else:
  return mysql_result($vysledek,$zaznam,$pole);
endif;
}

function phprs_sql_select_db($jmeno_databaze,$spojeni = false)
{
if ($spojeni === false):
  return mysql_select_db($jmeno_databaze);
else:
  return mysql_select_db($jmeno_databaze,$spojeni);
endif;
}

function phprs_sql_stat($spojeni = false)
{
if ($spojeni === false):
  return mysql_stat();
else:
  return mysql_stat($spojeni);
endif;
}
?>