<?php
######################################################################
# phpRS Instalační skript verze 0.4
# Copyright (c) 2011 by Kristian Vrhel (kristian.vrhel@seznam.cz) 
# & .....
# & phpRS community
# This program is free software. - Toto je bezplatny a svobodny software.
# soubor bude funkcni 1 hodinu po nahrani na server
#
######################################################################

$start=time();
define('CONFIGPHPRS','config.php');
define('THISFILE', $_SERVER['PHP_SELF']);
define('CONFIGVZOR', 'config.ins.php');
define('NASTAVENI', 'nastaveni.ins');
define('CASINSTALACE', 60);
define('NOVA', 'NOVA');
define('UPGRADE', 'UPGRADE');
define('NOVA_DB', 'nova_db.sql');
define('UPGRADE_DB', 'upgrade_db.sql');
define('HASH', 'admin/hash_functions.php');
$mysql_verze='?';

$konec_instalace=strtotime('+' . CASINSTALACE . ' min', getlastmod());
if (time() > $konec_instalace) {
  echo "ERROR - vyprsel cas<br>\r\n";
  exit;   // bez infozpravy... jeste nevim.
  }

if (isset($_GET['konec']) or isset($_POST['konec'])) {
  header('Location: ' . THISFILE . '?stranka=298');
  exit;
  }

// seznam tabulek phprs, vcetne jiz neexistujicich, ktere se ale mohou vyskytnou pri upgrade
// kde se dela jejich DROP napr. rs_user_prava
$tabulky=array('alias', 'ankety', 'bloky', 'captcha_test_otazky', 'clanky', 'cla_sab', 'config', 'ctenari', 'cte_session', 'download', 'download_sekce', 'global_sab', 'guard', 'imggal_obr', 'imggal_sekce', 'klik_ban', 'klik_kampan', 'klik_rekl', 'komentare', 'kontrola_ip', 'levely', 'links', 'links_sekce', 'moduly_prava', 'news', 'odpovedi', 'plugin', 'skup_cl', 'sloupce', 'stat_arch', 'stat_data', 'stat_ip', 'stat_session', 'topic', 'user', 'user_prava', 'vazby_prava');

/* seznam nastavovanych promennych s jejich vychozi hodnotou a jejich krátkým popisem
   podrobny popis se nacita ze vzoroveho configu

typ 1 promenna (napr. $dbuser='blablavla')
    2 promenna v $GLOBALS['rsconfig'] napr. $GLOBALS['rsconfig']['ssl']
    3 konstanta pomoci define

cislo 0 je pro texty
      1 je pro cela cisla - integer
      2 je pro true/false - boolean
*/
$promenne['dbtyp']=array('typ' => 1,
                       'cislo' => 0,
                     'hodnota' => 'mysqli',
                    'moznosti' => array('mysql', 'mysqli'),
                       'popis' => 'Typ databáze');

$promenne['dbserver']=array('typ' => 1,
                          'cislo' => 0,
                        'hodnota' => '',
                       'moznosti' => array(),
                          'popis' => 'Adresa serveru s databází');

$promenne['dbport']=array('typ' => 1,
                          'cislo' => 1,
                        'hodnota' => '',
                       'moznosti' => array(),
                          'popis' => 'Číslo portu databázového serveru');

$promenne['dbuser']=array('typ' => 1,
                        'cislo' => 0,
                      'hodnota' => '',
                     'moznosti' => array(),
                        'popis' => 'Uživatel databáze');

$promenne['dbpass']=array('typ' => 1,
                        'cislo' => 0,
                      'hodnota' => '',
                     'moznosti' => array(),
                        'popis' => 'Heslo k databázi');

$promenne['dbname']=array('typ' => 1,
                        'cislo' => 0,
                      'hodnota' => '',
                     'moznosti' => array(),
                        'popis' => 'Jméno databáze');

$promenne['rspredpona']=array('typ' => 1,
                            'cislo' => 0,
                          'hodnota' => 'rs_',
                         'moznosti' => array(),
                            'popis' => 'Rozliš. db předpona');

$promenne['wwwname']=array('typ' => 1,
                         'cislo' => 0,
                       'hodnota' => 'Název webu',
                      'moznosti' => array(),
                         'popis' => 'Jméno serveru');

$promenne['wwwdescription']=array('typ' => 1,
                                'cislo' => 0,
                              'hodnota' => 'Popis webového projektu',
                             'moznosti' => array(),
                                'popis' => 'Popis webového projektu');

$promenne['baseadr']=array('typ' => 1,
                         'cislo' => 0,
                       'hodnota' => '',
                      'moznosti' => array(),
                         'popis' => 'Base adresa');

$promenne['redakceadr']=array('typ' => 1,
                            'cislo' => 0,
                          'hodnota' => '',
                         'moznosti' => array(),
                            'popis' => 'email redakce');

$promenne['infoadr']=array('typ' => 1,
                         'cislo' => 0,
                       'hodnota' => '',
                      'moznosti' => array(),
                         'popis' => 'info email');

$promenne['anketa_cil_str']=array('typ' => 2,
                                'cislo' => 0,
                              'hodnota' => 'index',
                             'moznosti' => array('index', 'vysledek', 'ref', 'url'),
                                'popis' => 'Cíl ankety: index - hlavní stránka, vysledek - výsledková stránka ankety, ref - stránka ze které bylo hlasování odesláno (využití zejména při hlasování z bloku), url - vlastní URL');

$promenne['anketa_max_pocet_opak']=array('typ' => 2,
                                       'cislo' => 1,
                                     'hodnota' => 6,
                                    'moznosti' => array(),
                                       'popis' => 'Kolikrát za čas. limit');

$promenne['anketa_delka_omezeni']=array('typ' => 2,
                                      'cislo' => 1,
                                    'hodnota' => 3600,
                                   'moznosti' => array(),
                                      'popis' => 'Časový limit');

$promenne['max_delka_komentare']=array('typ' => 2,
                                     'cislo' => 1,
                                   'hodnota' => 1000,
                                  'moznosti' => array(),
                                     'popis' => 'Délka komentáře');

$promenne['max_delka_slova']=array('typ' => 2,
                                 'cislo' => 1,
                               'hodnota' => 50,
                              'moznosti' => array(),
                                 'popis' => 'Délka slova');

$promenne['img_adresar']=array('typ' => 2,
                             'cislo' => 0,
                           'hodnota' => 'storage/',
                          'moznosti' => array(),
                             'popis' => 'Obrázky, adresář');

$promenne['img_nahled_vyska']=array('typ' => 2,
                                  'cislo' => 1,
                                'hodnota' => 96,
                               'moznosti' => array(),
                                  'popis' => 'Náhledy, výška');

$promenne['img_nahled_sirka']=array('typ' => 2,
                                  'cislo' => 1,
                                'hodnota' => 120,
                               'moznosti' => array(),
                                  'popis' => 'Náhledy, šířka');

$promenne['file_adresar']=array('typ' => 2,
                              'cislo' => 0,
                            'hodnota' => 'storage/',
                           'moznosti' => array(),
                              'popis' => 'Soubory, adresář');

$promenne['kodovani']=array('typ' => 2,
                          'cislo' => 0,
                        'hodnota' => 'utf-8',
                       'moznosti' => array('utf-8', 'windows-1250', 'iso-8859-2'),
                          'popis' => 'Kódování');

$promenne['cla_delka_omezeni']=array('typ' => 2,
                                   'cislo' => 1,
                                 'hodnota' => 3600,
                                'moznosti' => array(),
                                   'popis' => 'Přečtení za limit');

$promenne['cla_max_pocet_opak']=array('typ' => 2,
                                    'cislo' => 1,
                                  'hodnota' => 6,
                                 'moznosti' => array(),
                                    'popis' => 'Časový limit');

$promenne['ssl']=array('typ' => 2,
                     'cislo' => 2,
                   'hodnota' => 'false',
                  'moznosti' => array('false', 'true'),
                     'popis' => 'Možnost přístupu https');

$promenne['platnost_auth']=array('typ' => 2,
                               'cislo' => 1,
                             'hodnota' => 7200,
                            'moznosti' => array(),
                               'popis' => 'Limit připojení');

$promenne['auth_max_pocet_chyb']=array('typ' => 2,
                                     'cislo' => 1,
                                   'hodnota' => 3,
                                  'moznosti' => array(),
                                     'popis' => 'Tolerance omylů');

$promenne['PASSWORD_SALT']=array('typ' => 3,
                               'cislo' => 0,
                             'hodnota' => '',
                            'moznosti' => array(),
                               'popis' => 'SALT');

/**
 *
 * ulozeni dat z formulare a presmerovani na dalsi stranku
 *
 */
if (isset($_POST['ulozit']) and isset($_POST['stranka'])) {
  $_POST['stranka']=(int)$_POST['stranka'];
  $dalsi=$_POST['stranka']+1;
  $ulozit=false;
  switch ($_POST['stranka']) {
    case 1:
      break;
    case 2:
      $instalace=((strtoupper($_POST['f_instalace']) == NOVA)?NOVA:UPGRADE);
      if (! file_exists(HASH)) {
        $dalsi=211;
        break;
        }
      if ($instalace == UPGRADE) {
          if (! file_exists(CONFIGPHPRS)) {
            $dalsi=207;
            break;
            }
          if (! file_exists(UPGRADE_DB)) {
            $dalsi=208;
            break;
            }
          }
        else {
          if (! file_exists(NOVA_DB)) {
            $dalsi=206;
            break;
            }
          }

      // do $nastaveni ukladam aktualní konfiguraci
      $nastaveni=$promenne;
      $cas_limit=ini_get('max_execution_time');
      @set_time_limit($cas_limit+10);
      if (($cas_limit+10) == ini_get('max_execution_time')) {
          $cas_limit=true;
          }
        else {
          $cas_limit=false;
          }


      $config_file=@file(CONFIGVZOR, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if ($config_file === false) {
        $dalsi=202;
        break;
        }
      // ulozeni hodnot z defaultniho configu do $nastaveni
      $chyba_moznosti=false;
      array_walk($config_file, 'nacti_promenne', array(true, $config_file));
      if ($instalace == UPGRADE) {
          // upgrade
          $config_old=file(CONFIGPHPRS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
          // prepsani defaultnich hodnot z puvodniho configu do $nastaveni
          $chyba_moznosti=false;
          array_walk($config_old, 'nacti_promenne', array(false, false));
          if ($chyba_moznosti) {
            $dalsi='213&navrat=' . ($_POST['stranka']+1);
            $ulozit=true;
            }
          unset($config_old);
          }
        else {
          // novainstalce
          $nastaveni['baseadr']['hodnota']=base_url();
          }
      $nastaveni['PASSWORD_SALT']['hodnota']=GeneratorSaltu();
      if ($_SERVER['SERVER_PORT'] == 443) {
        $nastaveni['ssl']['hodnota']='true';
        }
      break;
    case 3:
    case 4:
    case 5:
    case 6:
    case 7:
    case 8:
      if (nacti_nastaveni()) {
          if (get_magic_quotes_gpc()) {
            array_walk($_POST, 'oprav_gpc');
            }
          foreach ($_POST as $key => $value) {
            if (isset($nastaveni[$key])) {
              $nastaveni[$key]['hodnota']=$value;
              }
            }
          }
        else {
          $dalsi=1;
          }
      break;
    case 9:
      if (nacti_nastaveni()) {
          $config_file=@file(CONFIGVZOR, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
          array_walk($nastaveni, 'nastaveni_do_configu');
          $pom=0;
          while (file_exists('config.' . str_pad($pom, 3, '0', STR_PAD_LEFT) . '.php')) {
            ++$pom;
            }
          rename(CONFIGPHPRS, 'config.' . str_pad($pom, 3, '0', STR_PAD_LEFT) . '.php');
          uloz_config();
          }
        else {
          $dalsi=1;
          }
      break;
    case 10:
      if (nacti_nastaveni()) {
          if ($instalace == NOVA) {
              // smazani starych tabulek a vytvoreni novych
              if (nova_db()) {
                // doslo k chybe
                loguj('nova_db.log', "Vytvoreni db skoncilo s chybou\r\n");
                $dalsi=209;
                }
              }
            else {
              // upgrade predchozi verze phprs, lze od verze 2.3.5
              // starsi se musi nejdrive rucne upgradovat alespon na 2.3.5 a pak instalator pustit znovu
              if (upgrades_db($dalsi)) {
                // doslo k chybe
                loguj('upgrade_db.log', "Upgrade db skoncilo s chybou\r\n");
                $dalsi=210;
                }
              }
          }
        else {
          $dalsi=1;
          }
      break;
    case 11:
      $dalsi=299;
      nacti_nastaveni();
      if (trim($_POST['f_heslo1']) != trim($_POST['f_heslo2'])) {
          $dalsi='212&navrat=11';
          }
        else {
          include HASH;
          define('PASSWORD_SALT', $nastaveni['PASSWORD_SALT']['hodnota']);
          if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
              $dblink=mysqli_connect($nastaveni['dbserver']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota'], $nastaveni['dbport']['hodnota']);
              @mysqli_select_db($dblink, $nastaveni['dbname']['hodnota']);
              @mysqli_query($dblink, "SET NAMES 'utf8'");
              @mysqli_query($dblink, "UPDATE " . $nastaveni['rspredpona']['hodnota'] . "user SET password = '" . mysqli_real_escape_string($dblink, calculate_hash(trim($_POST['f_heslo1']))) . "' WHERE idu = 1");
              @mysqli_close($dblink);
              }
            else {
              $dblink=mysql_connect($nastaveni['dbserver']['hodnota'].':'.$nastaveni['dbport']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota']);
              @mysql_select_db($nastaveni['dbname']['hodnota'], $dblink);
              @mysql_query("SET NAMES 'utf8'", $dblink);
              @mysql_query("UPDATE " . $nastaveni['rspredpona']['hodnota'] . "user SET password = '" . mysql_real_escape_string(calculate_hash(trim($_POST['f_heslo1'])), $dblink) . "' WHERE idu = 1", $dblink);
              @mysql_close($dblink);
              }

          }
      break;
    case 100:
      nacti_nastaveni();
      if (vytvor_db($nastaveni)) {
          $dalsi=(int)$_POST['vporadku'];
          }
        else {
          $dalsi='201&navrat=201';
          }
      break;
    default:
      $dalsi=1;
    }

  // test na pripojeni k databazi, pokud to nefunguje, tak neni co instalovat
  if (isset($_POST['dbtyp'])) {
    if (strtolower($_POST['dbtyp']) == 'mysqli') {
        $dblink=@mysqli_connect($_POST['dbserver'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['dbport']);
        if ($dblink) {
            list($mysql_verze)=mysqli_fetch_row(mysqli_query($dblink, "SELECT version() AS verze"));
            if (@mysqli_select_db($dblink, $_POST['dbname'])) {
                if ($instalace == UPGRADE and ! je_tabulka($_POST['rspredpona'] . 'cte_session')) {
                  // nelze delat upgrade moc stara verze phpRS
                  $dalsi='205';
                  }
                if (je_sloupecek($_POST['rspredpona'] . 'ctenari', 'hash')) {
                  @mysqli_query($dblink, "ALTER TABLE `{$_POST['rspredpona']}ctenari` DROP `hash`");
                  }
                if (je_sloupecek($_POST['rspredpona'] . 'user', 'hash')) {
                  @mysqli_query($dblink, "ALTER TABLE `{$_POST['rspredpona']}user` DROP `hash`");
                  }
                }
              else {
                $dalsi='100&vporadku=' . ($_POST['stranka']+1);
                }
            @mysqli_close($dblink);
            }
          else {
            $dalsi='200&navrat=' . $_POST['stranka'];
            }
        }
      elseif (strtolower($_POST['dbtyp']) == 'mysql') {
        $dblink=@mysql_connect($_POST['dbserver'].':'.$nastaveni['dbport']['hodnota'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbname']);
        if ($dblink) {
            list($mysql_verze)=mysql_fetch_row(mysql_query("SELECT version() AS verze", $dblink));
            if (@mysql_select_db($_POST['dbname'], $dblink)) {
                if ($instalace == UPGRADE and ! je_tabulka($_POST['rspredpona'] . 'cte_session')) {
                  // nelze delat upgrade moc stara verze phpRS
                  $dalsi='205';
                  }
                if (je_sloupecek($_POST['rspredpona'] . 'ctenari', 'hash')) {
                  @mysql_query("ALTER TABLE `{$_POST['rspredpona']}ctenari` DROP `hash`", $dblink);
                  }
                if (je_sloupecek($_POST['rspredpona'] . 'user', 'hash')) {
                  @mysql_query("ALTER TABLE `{$_POST['rspredpona']}user` DROP `hash`", $dblink);
                  }
                }
              else {
                $dalsi='100&vporadku=' . ($_POST['stranka']+1);
                }
            @mysql_close($dblink);
            }
          else {
            $dalsi='200&navrat=' . $_POST['stranka'];
            }
        }
      else {
        // vybrany neexistujici typ databaze
        $dalsi='200&navrat=' . $_POST['stranka'];
        }
    }

  if (($_POST['stranka'] > 1) and ($_POST['stranka'] < 100) and (($dalsi < 100) or $ulozit) and isset($instalace) and isset($nastaveni) and is_array($nastaveni)) {
    // ulozit nastaveni
    $inst_nastaveni=@fopen(NASTAVENI, 'w');
    fwrite($inst_nastaveni, serialize(array($instalace, $mysql_verze, $cas_limit, $nastaveni)));
    fclose($inst_nastaveni);
    }

  header('Location: ' . THISFILE . '?stranka=' . $dalsi);
  exit;
  }
/**
 *
 * konec zpracovani $_POST dat
 *
 */


// napisy na tlacitkach
define('PROVED', 'OK? Tak ulož data a pokračuj');
define('PROVED_HELP', 'Zapíše změněná data a přejde na další stránku');
define('DALE', 'Beru na vědomí a jdu do toho');
define('DALE_HELP', 'Pokračuje v instalačním skriptu');
define('KONEC', 'Konec instalace');
define('KONEC_HELP', 'Ukončení instalace a smazání dočasných souborů, vytvářených instalací');
define('RESETD', 'Reset');
define('RESETD_HELP', 'Nastaví původní data při spuštění skriptu');
define('PROVEDDB', 'OK? Tak vygeneruj databázi a pokračuj');
define('PROVEDDB_HELP', 'Vytvoří databázi podle Vašeho zadání a přejde na další stránku');
define('PROVEDDELL', 'Vymazat tento skript a začít používat phpRS');
define('PROVEDDELL_HELP', 'Odstraní z phpRS tento zakládací skript');

// popisy jednotlivych stranek, posunul jsem index na 1, aby nebyl bordel v cislech stranky a jejim indexu v poli
// texty musí být napsány tak, aby se správně zobazily v html kodu
// jsou tam pouzity html tagy, takze nejsou osetrovany pomoci htmlspecialchars()
$vocogo=array(
     1 => array('<span class="nadpis">Vítejte v instalaci phpRS 283</span>', 'Toto je instalační skript, který Vás provede instalací phpRS verze 283 step-by-step.<br><br><a href="https://' . $_SERVER['SERVER_NAME'] . THISFILE . '"><span class="zdurazneni">Pokud vám funguje přístup pře https, je bezpečnější konfiguraci provádět tímto šifrovaným spojením.<br>Takže kliknutím na tento odkaz přejděte na https.</span></a><br><br>Věnujte, prosím, pozornost správnému vyplnění jednotlivých položek a přečtěte si tyto instrukce pečlivě <span style="font-weight: bold;">předtím nežli začnete s instalací</span>!!!<br /><br /><span  class="zdurazneni">DŮLEŽITÉ: Pokud provádíte UPGRADE, nezapomeňte si databázi <span style="font-weight: bold;">JEŠTĚ PŘEDTÍM</span> velmi pečlivě ZAZÁLOHOVAT !!!</span><br /><br />V průběhu skriptu se lze vrátit zpět a některé položky opravit. Pokud se vrátíte ke kroku 1 nebo 2, tak budete zase zadávat vše znovu, neboť v kroku 2 se načítají defaultní hodnoty, případně při upgrade hodnoty z vašeho původního configu. Je to ale <span style="font-weight: bold;">velmi nebezpečné v okamžiku kdy již necháte vygenerovat databázi a proběhnout její úpravu</span>. Tento upgrade phpRS se zaměřuje zejména na opravu bezpečnostních chyb a v rámci zabezpečení zde dochází ke změně šifrování (hashování) přihlašovacích údajů. Skript by měl zabezpečit hladký upgrade za podmínek, že nebude přerušen a bude proveden <span style="font-weight: bold;">jen jednou. </span><br /><br />Pokud tento proces proběhne dvakrát (nebo vícekrát) tak si znemožníte přihlašování a budete muset veškerá hesla znovu nastavit. I na to je připraven v phpRS nástroj, ale přidělá Vám to zbytečně práci. Pokud to je možné, postupujte krok za krokem a nevracejte se.<br><br>Z bezpečnostních důvodů je časově omezena funkčnost tohoto skriptu na ' . CASINSTALACE . ' minut od nakopírování tohoto instalačního souboru na server.', array()),
          array('Výběr typu instalace - nová nebo upgrade', array(NOVA => 'Nová instalace phpRS, neboť nebyl nalezen starý konfigurační soubor.<input type="hidden" name="f_instalace" value="' . NOVA . '"><br><br>', UPGRADE => '<label for="'. UPGRADE . '">Upgrade stávající verze</label><input type="radio" name="f_instalace" value="' . UPGRADE . '" checked="checked" id="'. UPGRADE . '"><br><label for="' . NOVA . '">Nová instalace</label><input type="radio" name="f_instalace" value="' . NOVA . '" id="' . NOVA . '"><br><br>'), array()),
          array('Nastavení databázového serveru','Následující údaje se týkají databázového serveru MySQL (uživatelské jméno, heslo, název a adresa databázového serveru atd) a získáte je nejčastěji od svého poskytovatele hostingu. Typ databáze mysql je funkční pouze pro starší verze php (<7), pro verze php 7 a novější je nutné použít <b>mysqli</b>. Rozlišující databázová předpona má hlavní význam pokud provozujete (nebo chcete provozovat) více serverů v jedné jediné databázi. Pokud máte, či plánujete mít jen jeden server, ponechte výchozí hodnotu. Případnou další nápovědu hledejte v dokumentaci k phpRS.<input type="hidden" name="f_databaze" value="test"><br><br>', array('dbtyp','dbserver','dbport','dbuser','dbpass','dbname','rspredpona')),
          array('Nastavení HTTP serveru','Zde nastavujete název Vašeho serveru, adresu kořenového adresáře, základní informační emaily. Nezapomeňte, prosím, ve druhém řádku "base adresu" ukončit lomítkem - bez toho se Váš server nerozběhne!! Emailové adresy ve tvaru redakce@mujserver.neco a info@mujserver.neco se standardně očekávají, tak je vyplňte. V nouzi mohou být i obě dvě stejné, ale měl by je někdo číst...<br><br>', array('wwwname', 'wwwdescription', 'baseadr', 'redakceadr', 'infoadr')),
          array('Nastavení anket a komentářů','Zde můžete specifikovat, kam bude uživatel přesměrován, když vyplní anketu, a jak často smí být hlasováno z jedné IP adresy. Dále můžete omezit maximální délku komentářů a také délku slov v nich (roboti nebo vtipálci někdy vkládají velmi dlouhá slova a takto můžete předejít rozbití designu stránky). Pokud si nejste jisti, co máte vložit, ponechte přednastavené hodnoty a změnit to můžete ručně v budoucnu.<br><br>', array('anketa_cil_str', 'anketa_max_pocet_opak', 'anketa_delka_omezeni', 'max_delka_komentare', 'max_delka_slova')),
          array('Obrázky a soubory','Volby, které máte zde, se týkají obrázků (velikosti náhledů a umístění souborů), dále umístění souborů "ke stažení". Ani zde neuděláte žádnou chybu, pokud ponecháte výchozí hodnoty. Lze je také v budoucnu změnit ale nelze  bez dalších nástrojů již změnit například velikost vytvořených náhledů. Pokud tedy máte rozmyšleno jiné rozlišění, nastavte si ho hned. Hodnoty neznamenají výslednou velikost obrázku ale velikost prostoru, do kterého se náhled obrázku po zmenšení vejde. Původní poměr stran bude pochopitelně zachován. Adresy umístění obrázků a souborů musí být ukončeny lomítkem!<br><br>', array('img_adresar', 'img_nahled_vyska', 'img_nahled_sirka', 'file_adresar')),
          array('Kódování a statistika','Vaše stránky mohou mít výstup v různém kódování: windows-1250, iso-8859-2 nebo UTF-8 - které doporučujeme - ale je to na Vás. Systém též obsahuje jednoduchou orientační statistiku přístupů a čtenosti článků. Její přesnost můžete ovlivnit nastavením zde. Vzhledem k tomu že se jedná jen o orientační hodnoty, můžete i zde klidně ponechat výchozí nastavení.<br><br>', array('kodovani', 'cla_delka_omezeni', 'cla_max_pocet_opak')),
          array('Administrace a zabezpečení','Můžete zde nastavit zabezpečené připojení k administraci, pokud je Váš server umožňuje tak ponechte na true. Dále zde nastavíte dobu jednoho přihlášení do administrace a počet povolených omylů při přihlašování. Poslední co zde nastavujete je SALT - je to řetezec, který se přidá ke každému heslu při přihlašování. Instalační skript každému vygeneruje náhodný SALT. Můžete ho zde klidně změnit, ale pozor: Po vygenerování databáze a hlavně po její případné úpravě už to neměňte. Váš server jako takový to neohrozí, ale pravděpodobně elegantně zlikvidujete všechna předtím uložená hesla. Což znamená je všechny po jednum ručně vytvořit a v databázi znovu nastavit. Takže buďte opatrní, může s tím být potom docela dost zbytečné práce.<br><br>', array('ssl', 'platnost_auth', 'auth_max_pocet_chyb', 'PASSWORD_SALT')),
          array('Vytvoření config.php','Nyní dojde k vytvoření souboru config.php, pokud se jedná o upgrade, je původní soubor config.php přejmenován na config.xxx.php, kde xxx je nejnižší volné číslo od 000.<br><br>', array()),
          array('Instalace databáze', array(NOVA => 'Nová instalace phpRS - všechny tabulky phpRS budou znovu vytvořeny a pokud nějaké existovaly, tak budou smazány - máte je pro jistotu zazálohovaný?<br><br>', UPGRADE => 'Tabulky vašeho původního phpRS budou aktualizovány na verzi 2.8.3, máte je pro jistotu zazálohovaný?<br><br><span class="zdurazneni">Po tomto kroku se již nevracejte zpět, neboť dojde ke značným změnám v databázi, které nesmí proběhnout víckrát. Jediný možný návrat je obnova původní databáze a spuštění nové instalace.<br><br>Pozor!!! - pokud máte hodně uživatelů či čtenářů, může tento proces trvat i několik minut - takže proces nechte běžet, dokud se vám neukáže další stránka.</span><br><br>'), array()),
          array('Zadání nového administrátorského hesla', 'Zadejde nové heslo administrátora<br>Poprvé : <input type="password" name="f_heslo1"><br>Podruhé: <input type="password" name="f_heslo2"> pro kontrolu raději dvakrát<br><br>', array()),
          );
$pocetstranek=count($vocogo);
// chybove stranky, nemaji se zapocitat do $pocetstranek, aby nebylo chybne strankovani
$vocogo[100]=array('Chyba při instalaci - NEEXISTUJE ZADANÁ DATABÁZE', 'Databáze bude automaticky vytvořena.', array());
$vocogo[101]=array('Chyba při instalaci - krátký časový limit', 'Stránka se používá na opakované přesměrování a postupný update hesel, když je mnoho uživatelů a nestíhá se to v max_execution_time a nejde měnit pomocí set_time_limit() a stránka se nezobrazuje.', array());
$vocogo[200]=array('Chyba při instalaci - NEJDE SE PŘIPOJIT K DATABÁZOVÉMU SERVERU', 'Nelze pokračovat v instalaci, dokud nezadáte správné přihlašovací údaje pro připojení k databázi. Po kliknutí na "<b>' . DALE . '</b>" budete vráceni na zadání správných údajů k databázi.', array());
$vocogo[201]=array('Chyba při instalaci - DATABÁZE NEJDE VYTVOŘIT', 'Nelze pokračovat v instalaci. Je třeba zjistit důvod, proč nejde databáze vytvořit, případně jí vytvořit jiným způsobem (např. administrátorské rozhraní vašeho webhostingu) a při instalaci zadat její správné jméno, aby skript mohl pokračovat dál.', array());
$vocogo[202]=array('Chyba při instalaci - chybí vzorový config.ins.php', 'Je třeba na web nakopírovat celou novou verzi phpRS včetně všech vzorových souborů.', array());
$vocogo[203]=array('Chyba při instalaci - nelze zapisovat do adresáře s phpRS', 'Je třeba nejspíš upravit práva k adresáři phpRS.', array());
$vocogo[204]=array('Chyba při instalaci - nelze smazat soubor z adresáře phpRS', 'Je třeba nejspíš upravit práva k adresáři phpRS.', array());
$vocogo[205]=array('Chyba při instalaci - MOC STARÁ VERZE phpRS - NELZE DĚLAT UPGRADE', 'Upgrade lze dělat alespoň z verze 2.3.5, pokud máte starší, tak buď musíte udělat ruční upgrade na verzi 2.3.5 pomocí SQL skriptů v distribuci nebo zvolte novou instalaci, pak ale přijdete o vše, co máte v této vaší staré verzi phpRS.<br><br>Také je zde možnost, že při instalaci omylem došlo ke změně rozlišovací předpony tabulek v databázi a proto požadovaná tabulka nebyla nalezena.', array());
$vocogo[206]=array('Chyba při instalaci - chybí vzorový soubor s novou databází', 'Je třeba na web nakopírovat celou novou verzi phpRS včetně všech vzorových souborů.', array());
$vocogo[207]=array('Chyba při instalaci - chybí původní config.php', 'Není z čeho převzít nastavení pro upgrade systému.', array());
$vocogo[208]=array('Chyba při instalaci - chybí vzorový soubor s upgradem databáze', 'Je třeba na web nakopírovat celou novou verzi phpRS včetně všech vzorových souborů.', array());
$vocogo[209]=array('Chyba při instalaci databáze', 'Je třeba zkontrolovat log vytváření databáze v souboru nova_db.log.', array());
$vocogo[210]=array('Chyba při upgrade databáze', 'Je třeba zkontrolovat log upgrade databáze v souboru upgrade_db.log.', array());
$vocogo[211]=array('Chyba při instalaci - chybí soubor s hashovací funkcí', 'Je třeba na web nakopírovat celou novou verzi phpRS, kde je tento soubor obsažen.', array());
$vocogo[212]=array('Chyba v zadání hesla', 'Zadaná hesla nejsou stejná, zadání zopakujte', array());
$vocogo[213]=array('Chyba při načítání starého configu', 'Některá hodnota ze starého configu neodpovídá povoleným možnostem, zkontrolujte si důkladně nastavení v dalších krocích, neboť tato nepovolená hodnota byla nahrazena defaultní hodnotou.', array());
$vocogo[298]=array('PŘEDČASNĚ JSTE UKONČILI INSTALACI', '<a href="' . THISFILE . '">Instalaci můžete spustit znovu od začátku</a>', array());
$vocogo[299]=array('ÚSPĚŠNÝ KONEC INSTALACE<br><a href="' . base_url() . '" target="_blank">Vašeho phpRS</a>', array(NOVA => 'Nyní byste měli mít plně funkční phpRS po nové instalaci.', UPGRADE => 'Nyní byste měli mít plně funkční phpRS po upgrade své staré instalace.'), array());

$str=1;
if (isset($_GET['stranka'])) {
  $str=(int)$_GET['stranka'];
  if (! isset($vocogo[$str])) {
    $str=1;
    }
  }

if ($str == 1) {
    // prvni spusteni, testy funkcnosti
    if (! file_exists(CONFIGVZOR)) {
      // chybi config.ins.php
      header('Location: ' . THISFILE . '?stranka=202');
      exit;
      }
    $inst_nastaveni=@fopen(NASTAVENI, 'w');
    if ($inst_nastaveni == false) {
      // nelze zapsat soubor s nastavenim
      header('Location: ' . THISFILE . '?stranka=203');
      exit;
      }
    fclose($inst_nastaveni);
    if (@unlink(NASTAVENI) === false) {
      // nastaveni nejde smazat
      header('Location: ' . THISFILE . '?stranka=204');
      exit;
      }
    }
  elseif ($str == 2) {
    // zjištění, jestli je stary config.php a muze se jednat o upgrade
    $instalace=((file_exists(CONFIGPHPRS))?UPGRADE:NOVA);
    }
  elseif ($str == 101) {
    // redirekt na updatovani hesel novym hashem
    nacti_nastaveni();
    if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
        $dblink=@mysqli_connect($nastaveni['dbserver']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota'], $nastaveni['dbport']['hodnota']);
        @mysqli_select_db($dblink, $nastaveni['dbname']['hodnota']);
        @mysqli_query($dblink, "SET NAMES 'utf8'");
        }
      else {
        $dblink=@mysql_connect($nastaveni['dbserver']['hodnota'].':'.$nastaveni['dbport']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota']);
        @mysql_select_db($nastaveni['dbname']['hodnota'], $dblink);
        @mysql_query("SET NAMES 'utf8'", $dblink);
        }
    update_password((int)$_GET['navrat']);
    header('Location: ' . THISFILE . '?stranka=' . (int)$_GET['navrat']);
    exit;
    }
  elseif ($str == 298) {
    // predcasny konec instalace, smazani nastaveni
    @unlink(NASTAVENI);
    }
  elseif ($str == 299) {
    // konec instalace, smazani nastaveni, provadeneho instalaci
    nacti_nastaveni();
    @unlink(NASTAVENI);
    }
  elseif (! nacti_nastaveni() and $str < 200) {
    // pokud se nepodari nacist nastaveni, vrati znovu na zacatek instalace
    header('Location: ' . THISFILE . '?stranka=1');
    exit;
    }

?><html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="author" content="Jiří Lukáš & phpRS community">
  <style>
    h1 { text-align: center;
         font-size: 25px;
      }

    table {
      border-collapse: collapse;
      border-width: 1px;
      border-style: solid;
      border-color: #000;
      width: 680px;
      min-width: 680px;

    }
    .lasttable {
      height: 250px;

    }
    table tr td{
      padding: 5px 10px 2px 10px;
    }
    .infovtabulce {
      color: #003B88;
      font-size: 12px;
      font-style: normal;
    }
    .infovtabulce td {
      color: #003B88;
      padding: 0px 10px 10px 10px;
    }
    .tucne {
      font-weight: bold;
    }
    .suda{
      background:#CAFFFF;
    }
    .licha{
      background:#FFFFD2;
    }
    .info{
      background:#CBDBFF;
    }
    .tableinfonadpis {
      color: #006600;
      font-size: 18px;
      font-weight: bold;
      text-align: center;
    }
    .tableinfotext {
      color: #003B88;
      font-size: 14px;
      font-style: normal;
    }

    .tlacitko {
      background:#FFFF68;
      font-weight: bold;
      border-width: 1px;
      border-style: solid;
      border-color: #000;
    }
    .tlacitkoreset {
      background:#ACCECF;
      font-weight: bold;
      border-width: 1px;
      border-style: solid;
      border-color: #000;
    }
    .doprava {
      float: right;
    }
    .ram {
     width:100%; border:1px solid #c6c6c6;
    }
    .intstran {
    text-align:center;
    font-weight: bold;
    padding-bottom:2px;
    }
    .intstranpoz {
    background:#CDE0FF;
    }
    .intstranakt {
    color:#FF0000;
    background:#FFFF00;
    }
    .nadpis {
    font-weight: bold;
    color: #000094;
    }
    .zdurazneni {
    color: #C20000;
    font-weight: bold;
    background-color: #FBFF91;
    }
  </style>
</head>
<body>
<h1>Instalace phpRS 283 - community version</h1>
<?php

  echo InteligentniStrankovac($pocetstranek, $str);
  stranka($str);
  echo "</body>\r\n</html>\r\n";

/**
 *
 * zde konci program a nasleduji jen definice pouzitych funkci
 *
 */

function oprav_gpc(&$hodnota, $key) {
  $hodnota=stripslashes($hodnota);
  }

function nacti_promenne(&$radek, $key, $parametry) {
  global $nastaveni, $chyba_moznosti;

  list($pozice, $config_file)=$parametry;

  // pozor, zalezi na poradi techto regularnich vyrazu !!!
  static $regv=array('^define\(\s*[\\\'"](.*)[\\\'"]\s*,\s*(.*)\s*\);',
                     '^\$GLOBALS\[[\\\'"]rsconfig[\\\'"]\]\[[\\\'"](.*)[\\\'"]\]\s*=\s*(.*);',
                     '^\$([^\[]*)\s*=\s*(.*);');
  $vysl=false;
  foreach($regv as $regvyr) {
    if ($vysl=preg_match('~' . $regvyr . '~sUu', trim($radek), $vysledek)) {
      break;
      }
    }
  if ($vysl and isset($nastaveni[$vysledek[1]])) {
    if ($pozice)  {
      // do nastaveni si ulozim cislo radku v nactenem configu, abych vedel, kde hodnotu prepsat
      // kdyz nacitam vzorovy config
      // pokud nacitam stary config, tak u toho me to nezajima, ten nebudu ukladat
      $nastaveni[$vysledek[1]]['radek']=$key;
      // a taky detailni popis k dane promenne
      $pom=1;
      $nastaveni[$vysledek[1]]['podrobne']='';
      while (preg_match('~^\s*\/\/ [^\-]~u', $config_file[$key-$pom])) {
        $nastaveni[$vysledek[1]]['podrobne']=trim($config_file[$key-$pom], ' /') . "\n" . $nastaveni[$vysledek[1]]['podrobne'];
        ++$pom;
        }
      }
    if ($nastaveni[$vysledek[1]]['cislo'] == 1) {
        $nastaveni[$vysledek[1]]['hodnota']=(int)$vysledek[2];
        }
      elseif ($nastaveni[$vysledek[1]]['cislo'] == 2) {
        if (count($nastaveni[$vysledek[1]]['moznosti'])) {
            // jsou definovany povolene hodnoty
            if (in_array(trim($vysledek[2], '\'" '), $nastaveni[$vysledek[1]]['moznosti'])) {
                // je to povolena hodnota, tak ji ulozime
                $nastaveni[$vysledek[1]]['hodnota']=$vysledek[2];
                }
              else {
                // neni to povolena hodnota, tak ulozime prvni z pole moznosti jako defaultni
                $nastaveni[$vysledek[1]]['hodnota']=$nastaveni[$vysledek[1]]['moznosti'][0];
                $chyba_moznosti=true;
                }
            }
          else {
            $nastaveni[$vysledek[1]]['hodnota']=$vysledek[2];
            }
        }
      else {
        if (count($nastaveni[$vysledek[1]]['moznosti'])) {
            // jsou definovany povolene hodnoty
            if (in_array(trim($vysledek[2], '\'" '), $nastaveni[$vysledek[1]]['moznosti'])) {
                // je to povolena hodnota, tak ji ulozime
                // eval je z duvodu spravne interpretace escapovaciho znaku \ pred znaky "'\
                // protoze neni jiste, jestli text byl uvozen apostofy nebo uvozovkami
                eval("\$nastaveni[\$vysledek[1]]['hodnota']={$vysledek[2]};");
                }
              else {
                // neni to povolena hodnota, tak ulozime prvni z pole moznosti jako defaultni
                $nastaveni[$vysledek[1]]['hodnota']=$nastaveni[$vysledek[1]]['moznosti'][0];
                $chyba_moznosti=true;
                }
            }
          else {
            // eval je z duvodu spravne interpretace escapovaciho znaku \ pred znaky "'\
            // protoze neni jiste, jestli text byl uvozen apostofy nebo uvozovkami
            eval("\$nastaveni[\$vysledek[1]]['hodnota']={$vysledek[2]};");
            }
        }
    }
  }

function GeneratorSaltu($kolik=40) {
  $novahodnota='';
  $vychozistring='0WXYZ12DE90F890J67890RK567L3ABCM34TU345567Q1234NOPV12GHI125S678948';
  for($pom=0; $pom < $kolik; $pom++) {
    $novahodnota.=$vychozistring[mt_rand(0, 65)];
    }
  return $novahodnota;
  }

function InteligentniStrankovac($celkem=1, $stranka=1) {
  global $vocogo;

  if ($stranka >= 100) {
    return '';
    }
  $vysledek='<div class="intstran">';
  for($pom=1; $pom <= $celkem; $pom++){
    $vysledek.='<span title="' . strip_tags($vocogo[$pom][0]) . '" ';
    if ($pom == $stranka) {
        $vysledek.='class="intstranakt">&nbsp;' . $pom . '&nbsp;</span> •• ';
        }
      elseif ($pom < $stranka) {
        $vysledek.='class="intstranpoz">&nbsp;<a href="' . THISFILE . '?stranka=' . $pom . '">' . $pom . '</a>&nbsp;</span> •• ';
        }
      else {
        $vysledek.='class="intstranpoz">&nbsp;' . $pom . '&nbsp;</span> •• ';
        }
    }
  $vysledek=mb_substr($vysledek, 0, -4) . "</div>\r\n";
  return $vysledek;
  }

function stranka($stranka) {
  global $vocogo, $instalace, $mysql_verze, $nastaveni, $konec_instalace;

  echo "<center>\r\n", '<form action="'.THISFILE.'" method="' . (($stranka < 200)?'post':'get') . '">', "\r\n";
  echo '<input type="hidden" name="stranka" value="', ((isset($_GET['navrat']))?(int)$_GET['navrat']:$stranka), '">', "\r\n";
  if (isset($_GET['vporadku'])) {
    echo '<input type="hidden" name="vporadku" value="', (int)$_GET['vporadku'], '">', "\r\n";
    }
  echo '<table>', "\r\n";
  // info
  if (isset($vocogo[$stranka])) {
    echo '<tr class="info">', "\r\n";
    echo '<td colspan = "2" class="tableinfonadpis">', $vocogo[$stranka][0];
    echo "</td>\r\n</tr>\r\n";
    echo '<tr class="info">', "\r\n";
    echo '<td colspan="2" class="tableinfotext">';
    if (is_array($vocogo[$stranka][1])) {
        if (isset($instalace)) {
          echo $vocogo[$stranka][1][$instalace];
          }
        }
      else {
        echo $vocogo[$stranka][1];
        }
    echo "</td>\r\n</tr>\r\n";
  }

  // jsou-li nejake promenne k vyplneni, tak je zobrazi v tabulce
  if (count($vocogo[$stranka][2])) {
    $jaka='licha';
    foreach($vocogo[$stranka][2] as $promenna) {
      echo '<tr class="' . $jaka . '">', "\r\n";
      echo '<td class="tucne">', htmlspecialchars($nastaveni[$promenna]['popis'], ENT_QUOTES), ': </td>', "\r\n";
      echo '<td>';
      if (count($nastaveni[$promenna]['moznosti'])) {
          // SELECT box na vybrane hodnoty
          echo '<select name="', $promenna, '">', "\r\n";
          foreach($nastaveni[$promenna]['moznosti'] as $volba) {
            echo '<option value="', $volba, '"', (($volba == $nastaveni[$promenna]['hodnota'])?' selected="selected"':''), '>', $volba, "</option>\r\n";
            }
          echo "</select>\r\n";
          }
        else {
          // INPUT na zadani textu nebo cisla
          echo '<input type="text" size="70" name="', $promenna, '"  value="', (($nastaveni[$promenna]['cislo'] == 2)?(($nastaveni[$promenna]['hodnota'])?'true':'false'):htmlspecialchars($nastaveni[$promenna]['hodnota'], ENT_QUOTES)), '">';
          }
      echo "</td>\r\n</tr>\r\n";
      echo '<tr class="infovtabulce">';
      echo '<td colspan="2" class="', $jaka, '">&bull; ', nl2br(htmlspecialchars($nastaveni[$promenna]['podrobne']));
      echo "</td>\r\n</tr>\r\n";
      $jaka=(($jaka == 'suda')?'licha':'suda');
      }
    }

  // radek s odesilacim tlacitkem
  echo "<tr>\r\n";
  echo '<td colspan="2"><span class="doprava">';
  if (($stranka == 1) or ($stranka >= 100)) {
      echo '<input type="submit" name="konec" class="tlacitko" value="', KONEC, '" title="', KONEC_HELP, '">&nbsp;&nbsp;&nbsp;';
      echo '<input type="submit" name="ulozit" class="tlacitko" value="', DALE, '" title="', DALE_HELP, '">';
      }
    else {
      echo '<input type="reset"  class="tlacitkoreset" value="', RESETD, '" title="', RESETD_HELP, '">&nbsp;&nbsp;&nbsp;';
      echo '<input type="submit" name="konec" class="tlacitko" value="', KONEC, '" title="', KONEC_HELP, '">&nbsp;&nbsp;&nbsp;';
      echo '<input type="submit" name="ulozit" class="tlacitko" value="', PROVED, '" title="', PROVED_HELP, '">';
      }
  echo "&nbsp;&nbsp;&nbsp;</span></td>\r\n</tr>\r\n";
  echo '<tr><td colspan="2" align="center">';
  if (isset($_SERVER['SERVER_SOFTWARE'])) {
      echo "Web server: <b>", $_SERVER['SERVER_SOFTWARE'], '</b> •• ';
      }
    elseif (function_exists('apache_get_version')) {
      echo "Web server: <b>", apache_get_version(), '</b> •• ';
      }
  echo 'PHP verze: <b>', PHP_VERSION, '</b> ••  MySQL server: <b>', $mysql_verze, '</b>';
  if (isset($nastaveni['dbtyp']['hodnota'])) {
    echo '<br>Používá se databázová PHP knihovna: <b>', $nastaveni['dbtyp']['hodnota'], '</b>';
    }
  if (function_exists('mysql_get_client_info')) {
    echo '<br><span style="font-size: 12px;">MySQL klient: ', mysql_get_client_info(), '</span>';
    }
  if (function_exists('mysqli_get_client_info')) {
    echo '<br><span style="font-size: 12px;">MySQLi klient: ', mysqli_get_client_info(), '</span>';
    }
  echo "</td>\r\n</tr>\r\n";
  echo "</table>\r\n</form>\r\n</center>\r\n";
  echo '<p align="center">Konec instalace : <b>', date('j.n.Y G:i', $konec_instalace);
  echo "</b></p>\r\n";
  }

function vytvor_db($nastaveni)  {
  $vysledek=false;
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      $dblink=@mysqli_connect($nastaveni['dbserver']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota'], $nastaveni['dbport']['hodnota']);
      if ($dblink) {
        $sql="CREATE DATABASE `" . $nastaveni['dbname']['hodnota'] . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci";
        if (@mysqli_query($dblink, $sql)) {
          if (@mysqli_select_db($dblink, $nastaveni['dbname']['hodnota'])) {
            $vysledek=true;
            }
          }
        @mysqli_close($dblink);
        }
      }
    else {
      $dblink=@mysql_connect($nastaveni['dbserver']['hodnota'].':'.$nastaveni['dbport']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota']);
      if ($dblink) {
        $sql="CREATE DATABASE `" . $nastaveni['dbname']['hodnota'] . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci";
        if (@mysql_query($sql, $dblink)) {
          if (@mysql_select_db($nastaveni['dbname']['hodnota'], $dblink)) {
            $vysledek=true;
            }
          }
        @mysql_close($dblink);
        }
      }
  return $vysledek;
  }

function uprav_hodnotu($hodnota, $typ) {
  switch ($typ) {
    case 0:
      // text
      // je v uvozovkach, tak oescapovat potrebne znaky, staci ' a \
      return "'" . addcslashes($hodnota, "'\\") . "'";
      break;
    case 1:
      // cislo
      return (int)$hodnota;
      break;
    case 2:
      // boolean
      $hodnota=strtolower($hodnota);
      if ($hodnota == 'true' or $hodnota == 'false') {
          return $hodnota;
          }
        else {
          return 'false';
          }
      break;
    }
  }

function uloz_config() {
  global $config_file;

  $f1=@fopen(CONFIGPHPRS, 'w');
  foreach ($config_file as $radek) {
    fwrite($f1, trim($radek) . "\r\n");
    }
  fclose($f1);
  }

function nacti_nastaveni() {
  global $instalace, $mysql_verze, $cas_limit, $nastaveni;

  if ($inst_nastaveni=@fopen(NASTAVENI, 'r')) {
      $nastaveni='';
      while (! feof($inst_nastaveni)) {
        $nastaveni.=fread($inst_nastaveni, 4096);
        }
      list($instalace, $mysql_verze, $cas_limit, $nastaveni)=unserialize($nastaveni);
      fclose($inst_nastaveni);
      return true;
      }
    else {
      return false;
      }
  }

function nastaveni_do_configu(&$value, $key) {
  global $config_file;

  if (isset($value['radek'])) {
    switch ($value['typ']) {
      case 1:
        $config_file[$value['radek']]='$' . "$key=" . uprav_hodnotu($value['hodnota'], $value['cislo']) . ";";
        break;
      case 2:
        $config_file[$value['radek']]="\$GLOBALS['rsconfig']['$key']=" . uprav_hodnotu($value['hodnota'], $value['cislo']) . ";";
        break;
      case 3:
        $config_file[$value['radek']]="define('$key', " . uprav_hodnotu($value['hodnota'], $value['cislo']) . ");";
        break;
      }
    }
  }

function base_url() {
  // prevod \ u windows serveru na / v url ceste pro "baseadr"
  $pom=strtr(dirname($_SERVER['PHP_SELF']), '\\', '/');
  if ($pom != '/') {
    $pom="$pom/";
    }
  return 'http://' . $_SERVER['SERVER_NAME'] . $pom;
  }

function je_tabulka($tabulka) {
  global $nastaveni, $dblink;

  $tabulka=strtolower($tabulka);
  $vysledek=false;
  $sql="SHOW TABLES";
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, $sql)) {
        while ($line=@mysqli_fetch_row($result)) {
          if ($tabulka == strtolower($line[0])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
    else {
      if ($result=@mysql_query($sql, $dblink)) {
        while ($line=@mysql_fetch_row($result)) {
          if ($tabulka == strtolower($line[0])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
  return $vysledek;
  }

function je_sloupecek($tabulka, $sloupecek) {
  global $dblink, $nastaveni;

  $tabulka=strtolower($tabulka);
  $sloupecek=strtolower($sloupecek);
  $vysledek=false;
  $sql="SHOW COLUMNS FROM $tabulka";
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, $sql)) {
        while ($line=@mysqli_fetch_row($result)) {
          if ($sloupecek == strtolower($line[0])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
    else {
      if ($result=@mysql_query($sql, $dblink)) {
        while ($line=@mysql_fetch_row($result)) {
          if ($sloupecek == strtolower($line[0])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
  return $vysledek;
  }

function je_hodnota($tabulka, $podminka) {
  global $dblink, $nastaveni;

  $vysledek=false;
  $sql="SELECT COUNT(*) as pocet FROM $tabulka WHERE $podminka";
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, $sql)) {
        if ($line=@mysqli_fetch_row($result)) {
          $vysledek=(($line[0] == 0)?false:true);
          }
        }
      }
    else {
      if ($result=@mysql_query($sql, $dblink)) {
        if ($line=@mysql_fetch_row($result)) {
          $vysledek=(($line[0] == 0)?false:true);
          }
        }
      }
  return $vysledek;
  }

function je_index($tabulka, $index) {
  global $dblink, $nastaveni;

  $vysledek=false;
  $index=strtolower($index);
  $sql="SHOW INDEXES FROM $tabulka";
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, $sql)) {
        while ($line=@mysqli_fetch_assoc($result)) {
          if ($index == strtolower($line['Key_name'])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
    else {
      if ($result=@mysql_query($sql, $dblink)) {
        while ($line=@mysql_fetch_assoc($result)) {
          if ($index == strtolower($line['Key_name'])) {
            $vysledek=true;
            break;
            }
          }
        }
      }
  return $vysledek;
  }

function nova_db() {
  global $nastaveni, $tabulky, $mysql_verze;

  $vzor_sql=@fopen(NOVA_DB, 'r');
  $nacteno='';
  while (! feof($vzor_sql)) {
    $nacteno.=fread($vzor_sql, 4096);
    }
  $sqls=unserialize($nacteno);
  fclose($vzor_sql);

  // takova opicarna, aby to nebyl dlouhy kod a vznikly promenny s nazvy tabulek
  // ktere budou obsahovat skutecne nazvy dle nastavene predpony
  // napr bude-li predpona "blog_" tak to vytvori $rs_alias='blog_alias' viz napoveda k $$
  foreach ($tabulky as $tabulka) {
    $pom="rs_$tabulka";
    $$pom=$nastaveni['rspredpona']['hodnota'] . $tabulka;
    }

  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      $dblink=@mysqli_connect($nastaveni['dbserver']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota'], $nastaveni['dbport']['hodnota']);
      @mysqli_select_db($dblink, $nastaveni['dbname']['hodnota']);
      @mysqli_query($dblink, "SET NAMES 'utf8'");
      }
    else {
      $dblink=@mysql_connect($nastaveni['dbserver']['hodnota'].':'.$nastaveni['dbport']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota']);
      @mysql_select_db($nastaveni['dbname']['hodnota'], $dblink);
      @mysql_query("SET NAMES 'utf8'", $dblink);
      }

  $chyba=false;
  $pom="- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\nStart ";
  $pom.=date('Y-m-d H.i.s') . "\r\n";
  $pom.="MySQL server: $mysql_verze\r\n";
  if (isset($_SERVER['SERVER_SOFTWARE'])) {
      $pom.="Web server: " . $_SERVER['SERVER_SOFTWARE'] . "\r\n";
      }
    elseif (function_exists('apache_get_version')) {
      $pom.="Web server: " . apache_get_version() . "\r\n";
      }
  $pom.='PHP verze: ' . PHP_VERSION . "\r\n";
  if (function_exists('mysql_get_client_info')) {
    $pom.='MySQL klient: ' . mysql_get_client_info() . "\r\n";
    }
  if (function_exists('mysqli_get_client_info')) {
    $pom.='MySQLi klient: ' . mysqli_get_client_info() . "\r\n";
    }
  $pom.='Používá se databázová PHP knihovna: ' . $nastaveni['dbtyp']['hodnota'];
  $pom.="\r\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\n";
  loguj('nova_db.log', $pom);


  foreach ($sqls as $sql) {
    // osetreni sql dotazu pro vytvoreni prirazovaciho prikazu do promenne $sql1
    // nazvy tabulek se doplni o $ na zacatku, takze se z nich stanou promenne
    // ktere jsou vyse spravne naplnene s definovanou predponou
    // a oescapuji se znaky, ktere je treba v retezci uvozenem ""
    eval('$sql1="' . strtr($sql, array('\\' => '\\\\', '$' => '\\$', '"' => '\\"', ' rs_' => ' $rs_')) . '";');
    if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
        if (! mysqli_query($dblink, $sql1)) {
          $chyba=true;
          loguj('nova_db.log', mysqli_error($dblink) . "\r\n\r\n$sql\r\n\r\n$sql1\r\n");
          }
        }
      else {
        if (! @mysql_query($sql1, $dblink)) {
          $chyba=true;
          loguj('nova_db.log', mysql_error($dblink) . "\r\n\r\n$sql\r\n\r\n$sql1\r\n");
          }
        }
    }

  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      mysqli_close($dblink);
      }
    else {
      mysql_close($dblink);
      }

  loguj('nova_db.log', "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\nKonec " . date('Y-m-d H.i.s') . "\r\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\n");
  return $chyba;
  }

function upgrades_db($dalsi) {
  global $nastaveni, $tabulky, $mysql_verze, $dblink;

  $vzor_sql=@fopen(UPGRADE_DB, 'r');
  $nacteno='';
  while (! feof($vzor_sql)) {
    $nacteno.=fread($vzor_sql, 4096);
    }
  $updates=unserialize($nacteno);
  fclose($vzor_sql);

  // takova opicarna, aby to nebyl dlouhy kod a vznikly promenny s nazvy tabulek
  // ktere budou obsahovat skutecne nazvy dle nastavene predpony
  // napr bude-li predpona "blog_" tak to vytvori $rs_alias='blog_alias' viz napoveda k $$
  foreach ($tabulky as $tabulka) {
    $pom="rs_$tabulka";
    $$pom=$nastaveni['rspredpona']['hodnota'] . $tabulka;
    }

  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      $dblink=@mysqli_connect($nastaveni['dbserver']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota'], $nastaveni['dbport']['hodnota']);
      @mysqli_select_db($dblink, $nastaveni['dbname']['hodnota']);
      @mysqli_query($dblink, "SET NAMES 'utf8'");
      @mysqli_query($dblink, "ALTER TABLE `rs_ctenari` ADD `hash` TINYINT UNSIGNED NOT NULL DEFAULT '0'");
      @mysqli_query($dblink, "ALTER TABLE `rs_user` ADD `hash` TINYINT UNSIGNED NOT NULL DEFAULT '0'");

      }
    else {
      $dblink=@mysql_connect($nastaveni['dbserver']['hodnota'].':'.$nastaveni['dbport']['hodnota'], $nastaveni['dbuser']['hodnota'], $nastaveni['dbpass']['hodnota'], $nastaveni['dbname']['hodnota']);
      @mysql_select_db($nastaveni['dbname']['hodnota'], $dblink);
      @mysql_query("SET NAMES 'utf8'", $dblink);
      @mysql_query("ALTER TABLE `rs_ctenari` ADD `hash` TINYINT UNSIGNED NOT NULL DEFAULT '0'", $dblink);
      @mysql_query("ALTER TABLE `rs_user` ADD `hash` TINYINT UNSIGNED NOT NULL DEFAULT '0'", $dblink);
      }

  $chyba=false;
  $pom="- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\nStart ";
  $pom.=date('Y-m-d H.i.s') . "\r\n";
  $pom.="MySQL server: $mysql_verze\r\n";
  if (isset($_SERVER['SERVER_SOFTWARE'])) {
      $pom.="Web server: " . $_SERVER['SERVER_SOFTWARE'] . "\r\n";
      }
    elseif (function_exists('apache_get_version')) {
      $pom.="Web server: " . apache_get_version() . "\r\n";
      }
  $pom.='PHP verze: ' . PHP_VERSION . "\r\n";
  if (function_exists('mysql_get_client_info')) {
    $pom.='MySQL klient: ' . mysql_get_client_info() . "\r\n";
    }
  if (function_exists('mysqli_get_client_info')) {
    $pom.='MySQLi klient: ' . mysqli_get_client_info() . "\r\n";
    }
  $pom.='Používá se databázová PHP knihovna: ' . $nastaveni['dbtyp']['hodnota'];
  $pom.="\r\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\n";
  loguj('upgrade_db.log', $pom);

  foreach ($updates as $update) {
    $vysl=eval('$update1=' . strtr($update, array(' rs_' => ' $rs_', '"rs_' => '"$rs_')));
    if ($vysl === false) {
      loguj('upgrade_db.log', "Chyba EVAL : " . htmlspecialchars($update) . "\r\n");
      $chyba=true;
      continue;
      }
    if ($update1['typ'] == 'index') {
        if ( ! je_index($update1['tabulka'], $update1['sloupec'])) {
          if (sql_dotaz($update1['sql'], 'upgrade_db.log')) {
            $chyba=true;
            }
          }
        }
      elseif ($update1['podminka'] > '') {
        if (je_tabulka($update1['tabulka'])) {
          if (strtolower($update1['typ']) == 'neni' xor je_hodnota($update1['tabulka'], $update1['podminka'])) {
            if (is_array($update1['sql'])) {
                foreach ($update1['sql'] as $sql) {
                  if (sql_dotaz($sql, 'upgrade_db.log')) {
                    $chyba=true;
                    }
                  }
                }
              else {
                if (sql_dotaz($update1['sql'], 'upgrade_db.log')) {
                  $chyba=true;
                  }
                }
            } else { loguj('upgrade_db.log', "Preskakuji : " . $update1['verze'] . " - " . $update1['typ'] . " - " . $update1['tabulka'] . " - " . htmlspecialchars($update1['podminka']) . "\r\n"); }
          }
        }
      elseif ($update1['sloupec'] > '') {
        if (je_tabulka($update1['tabulka'])) {
          if (strtolower($update1['typ']) == 'neni' xor je_sloupecek($update1['tabulka'], $update1['sloupec'])) {
            if (is_array($update1['sql'])) {
                foreach ($update1['sql'] as $sql) {
                  if (sql_dotaz($sql, 'upgrade_db.log')) {
                    $chyba=true;
                    }
                  }
                }
              else {
                if (sql_dotaz($update1['sql'], 'upgrade_db.log')) {
                  $chyba=true;
                  }
                }
            } else { loguj('upgrade_db.log', "Preskakuji : " . $update1['verze'] . " - " . $update1['typ'] . " - " . $update1['tabulka'] . " - " . $update1['sloupec'] . "\r\n"); }
          }
        }
      else {
        if (strtolower($update1['typ']) == 'neni' xor je_tabulka($update1['tabulka'])) {
          if (is_array($update1['sql'])) {
              foreach ($update1['sql'] as $sql) {
                if (sql_dotaz($sql, 'upgrade_db.log')) {
                  $chyba=true;
                  }
                }
              }
            else {
              if (sql_dotaz($update1['sql'], 'upgrade_db.log')) {
                $chyba=true;
                }
              }
          } else { loguj('upgrade_db.log', "Preskakuji : " . $update1['verze'] . " - " . $update1['typ'] . " - " . $update1['tabulka'] . "\r\n"); }
        }
    }

  update_password($dalsi);

  loguj('upgrade_db.log', "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\nKonec " . date('Y-m-d H.i.s') . "\r\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\r\n");
  return $chyba;
  }

function update_password($dalsi) {
  global $dblink, $cas_limit, $start, $nastaveni;

  $max_execution_time=ini_get('max_execution_time')-2;

  define('PASSWORD_SALT', $nastaveni['PASSWORD_SALT']['hodnota']);
  include HASH;
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, "SELECT idu, password FROM {$nastaveni['rspredpona']['hodnota']}user WHERE hash = 0")) {
        while ($line=@mysqli_fetch_assoc($result)) {
          @mysqli_query($dblink, "UPDATE {$nastaveni['rspredpona']['hodnota']}user SET hash = 1, password = '" . mysqli_real_escape_string($dblink, convert_old_type_hash($line['password'])) . "' WHERE idu = {$line['idu']}");
          if ( (time()-$start) >= ($max_execution_time) ) {
            if ($cas_limit) {
                set_time_limit($max_execution_time+2);
                $start=time();
                }
              else {
                // redirekt na pokracovani, tam kde se ted skoncilo
                header('Location: ' . THISFILE . "?navrat=$dalsi&stranka=101");
                exit;
                }
            }
          }
        }
      if ($result=@mysqli_query($dblink, "SELECT idc, password FROM {$nastaveni['rspredpona']['hodnota']}ctenari WHERE hash = 0")) {
        while ($line=@mysqli_fetch_assoc($result)) {
          @mysqli_query($dblink, "UPDATE {$nastaveni['rspredpona']['hodnota']}ctenari SET hash = 1, password = '" . mysqli_real_escape_string($dblink, convert_old_type_hash($line['password'])) . "' WHERE idc = {$line['idc']}");
          if ( (time()-$start) >= ($max_execution_time) ) {
            if ($cas_limit) {
                set_time_limit($max_execution_time+2);
                $start=time();
                }
              else {
                // redirekt na pokracovani, tam kde se ted skoncilo
                header('Location: ' . THISFILE . "?navrat=$dalsi&stranka=101");
                exit;
                }
            }
          }
        }
      @mysqli_query($dblink, "ALTER TABLE `rs_ctenari` DROP `hash`");
      @mysqli_query($dblink, "ALTER TABLE `rs_user` DROP `hash`");
      @mysqli_close($dblink);
      }
    else {
      if ($result=@mysql_query("SELECT idu, password FROM {$nastaveni['rspredpona']['hodnota']}user WHERE hash = 0", $dblink)) {
        while ($line=@mysql_fetch_assoc($result)) {
          @mysql_query("UPDATE {$nastaveni['rspredpona']['hodnota']}user SET hash = 1, password = '" . mysql_real_escape_string(convert_old_type_hash($line['password']), $dblink) . "' WHERE idu = {$line['idu']}", $dblink);
          if ( (time()-$start) >= ($max_execution_time) ) {
            if ($cas_limit) {
                set_time_limit($max_execution_time+2);
                $start=time();
                }
              else {
                // redirekt na pokracovani, tam kde se ted skoncilo
                header('Location: ' . THISFILE . "?navrat=$dalsi&stranka=101");
                exit;
                }
            }
          }
        }
      if ($result=@mysql_query("SELECT idc, password FROM {$nastaveni['rspredpona']['hodnota']}ctenari WHERE hash = 0", $dblink)) {
        while ($line=@mysql_fetch_assoc($result)) {
          @mysql_query("UPDATE {$nastaveni['rspredpona']['hodnota']}ctenari SET hash = 1, password = '" . mysql_real_escape_string(convert_old_type_hash($line['password']), $dblink) . "' WHERE idc = {$line['idc']}", $dblink);
          if ( (time()-$start) >= ($max_execution_time) ) {
            if ($cas_limit) {
                set_time_limit($max_execution_time+2);
                $start=time();
                }
              else {
                // redirekt na pokracovani, tam kde se ted skoncilo
                header('Location: ' . THISFILE . "?navrat=$dalsi&stranka=101");
                exit;
                }
            }
          }
        }
      @mysql_query("ALTER TABLE `rs_ctenari` DROP `hash`", $dblink);
      @mysql_query("ALTER TABLE `rs_user` DROP `hash`", $dblink);
      @mysql_close($dblink);
      }
  }

function sql_dotaz($sql, $log_soubor) {
  global $dblink, $nastaveni;

  $chyba=false;
  loguj($log_soubor, "- - - - - - - - - - - - - - - - - - - -\r\n$sql\r\n");
  if (strtolower($nastaveni['dbtyp']['hodnota']) == 'mysqli') {
      if ($result=@mysqli_query($dblink, $sql)) {
          loguj($log_soubor, "Ovlivňeno řádků: " . mysqli_affected_rows($dblink) . "\r\n");
          }
        else {
          loguj($log_soubor, "Chyba: " . mysqli_error($dblink) . "\r\n");
          $chyba=true;
          }
      }
    else {
      if ($result=@mysql_query($sql, $dblink)) {
          loguj($log_soubor, "Ovlivňeno řádků: " . mysql_affected_rows($dblink) . "\r\n");
          }
        else {
          loguj($log_soubor, "Chyba: " . mysql_error($dblink) . "\r\n");
          $chyba=true;
          }
      }
  return $chyba;
  }

function loguj($log_soubor, $hlaska) {
  if ($log=fopen($log_soubor, 'a')) {
    fwrite($log, $hlaska . "\r\n");
    fclose($log);
    }
  }
?>
