<?php

######################################################################
# phpRS Admin Standard File library 1.0.2
######################################################################

// Copyright (c) 2001-2011 by Jiri Lukas (jirilukas@supersvet.cz) & phpRS community
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

/*
  JedinecneCislo()
  CisteJmenoSouboru($jmeno_sb = '')
  StdUploadSoubor($vs_jm_souboru = '', $vs_cilovy_adr = '', $vs_pripona = 'sb')
*/

function JedinecneCislo()
{
if (!isset($GLOBALS['rsconfig']['img_pom_citac'])):
  $GLOBALS['rsconfig']['img_pom_citac']=100; // inic.
else:
  $GLOBALS['rsconfig']['img_pom_citac']--; // ponizeni o 1
endif;

$vysl=time()-$GLOBALS['rsconfig']['img_pom_citac'];

return $vysl;
}

function CisteJmenoSouboru($jmeno_sb = '')
{
  // nejprve prelozit znaky, ktere iconv//TRANSLIT divne preklada
  $preklad['co'][]='€'; $preklad['kam'][]='-euro-';
  $preklad['co'][]='&'; $preklad['kam'][]='-and-';
  $preklad['co'][]='™'; $preklad['kam'][]='-tm-';
  $preklad['co'][]='§'; $preklad['kam'][]='';
  $preklad['co'][]='©'; $preklad['kam'][]='';
  $preklad['co'][]='®'; $preklad['kam'][]='';
  $preklad['co'][]='£'; $preklad['kam'][]='';
  $preklad['co'][]='²'; $preklad['kam'][]='';
  $preklad['co'][]='³'; $preklad['kam'][]='';
  $preklad['co'][]='¢'; $preklad['kam'][]='';
  $preklad['co'][]='°'; $preklad['kam'][]='';
  $preklad['co'][]='‰'; $preklad['kam'][]='';
  $preklad['co'][]='Ş'; $preklad['kam'][]='';
  $preklad['co'][]='¬'; $preklad['kam'][]='';
  $preklad['co'][]='±'; $preklad['kam'][]='';
  $preklad['co'][]='ß'; $preklad['kam'][]='';
  $preklad['co'][]='•'; $preklad['kam'][]='';
  $preklad['co'][]='µ'; $preklad['kam'][]='';
  $jmeno_sb=str_replace($preklad['co'], $preklad['kam'], $jmeno_sb);
  
  // prekodovat pomoci iconv jen na zakladni ASCII a prevest na mala pismenka
  iconv_set_encoding('input_encoding', 'UTF-8');
  iconv_set_encoding('internal_encoding', 'UTF-8');
  $jmeno_sb=strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $jmeno_sb));
  
  // v poli array() jsou znaky, ktere budou fungovat jako oddelovace a jsou nahrazeny pomlckou
  $jmeno_sb=str_replace(array(' ', '-', '/', ':', '=', '\\', '_', '|', '~', '–', '—', ' ', '¦', '­', '¯', '‒', '―'), '-', $jmeno_sb);
  $jmeno_sb=preg_replace('/[^a-z0-9\-]/', '', $jmeno_sb); // vymazat vsechny znaky krome malych pismen, cislic a pomlcky
  $jmeno_sb=preg_replace('/-+/', '-', $jmeno_sb); // vic pomlcek za sebou nahradit jen jednou
  return trim($jmeno_sb, '-'); // odstranit pomlcky na zacatku a konci retezce
}

function StdUploadSoubor($vs_jm_souboru = '', $vs_cilovy_adr = '', $vs_pripona = 'sb')
{
$max_velikost=31457280; // 30 MB

$chyba_vse=0; // jakokliv chyba
$chyba_txt=''; // textovy popis chyby
$chyba_fatal=0; // zasadni chyba vznikla pri zpracovani souboru
$chyba_format=0; // chyba formatu
$nove_umisteni=''; // nova adresa souboru

// test na chybu + bezpecnostni korekce
if (empty($vs_jm_souboru)): $chyba_vse=1; endif;
if (empty($vs_pripona)): $chyba_vse=1; endif;

if ($chyba_vse==0): // kdyz je vse OK
  // upload obrazku
  if ($_FILES[$vs_jm_souboru]["size"]>0&&$_FILES[$vs_jm_souboru]["size"]<=$max_velikost): // soubor musi byt vetsi nez 0 B a zaroven mensi(roven) nez X B
    // soubor existuje; neni prazdny
    list($prtypjmeno,$prtyppripona)=explode(".",trim($_FILES[$vs_jm_souboru]["name"])); // dekompilace celeho jmena souboru
    $prtyppripona=strtolower($prtyppripona); // prevedeni na male pismo
    $prtypjmeno=CisteJmenoSouboru($prtypjmeno); // totalni ocisteni jmena souboru
    $jedinecny_ident=JedinecneCislo(); // ziskani jedinecneho oznaceni

	// definice povolenych formatu
    $pole_povolenych_formatu=array('rar', 'zip', 'arj', '7z', 'pdf', 'doc', 'xls', 'ppt', 'docx', 'xlsx', 'odt', 'ods', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'bmp');
    // test na format souboru
    if (!in_array($prtyppripona, $pole_povolenych_formatu)):
      // chyba: nepovoleny format souboru
      $chyba_txt .= "<p align=\"center\" class=\"txt\">".RS_ADM_ASL_ERR_FORMAT_SB."</p>\n";
      $chyba_vse=1;
      $chyba_fatal=1;
      $chyba_format=1;
    else:
      // vse OK
      $nove_umisteni = $vs_cilovy_adr.$jedinecny_ident.'_'.$vs_pripona.'_'.$prtypjmeno.'.'.$prtyppripona;
    endif;
    // upload obrazku
    if ($chyba_format==0):
      if (is_uploaded_file($_FILES[$vs_jm_souboru]["tmp_name"])):
        if (move_uploaded_file($_FILES[$vs_jm_souboru]["tmp_name"],$nove_umisteni)):
          chmod ($nove_umisteni,0664);  // oktal; spravna hodnota modu
          //echo "<p align=\"center\" class=\"txt\">Do systemu byl uspesne vlozen novy soubor.</p>\n"; // vse OK
        else:
          // chyba pri prenosu souboru
          $chyba_txt.="<p align=\"center\" class=\"txt\">".RS_ADM_ASL_ERR_CHYBA_PRENOS_SB."</p>\n";
          $chyba_vse=1;
          $chyba_fatal=1;
        endif;
      else:
        // chyba pri uploadu souboru
        $chyba_txt.="<p align=\"center\" class=\"txt\">".RS_ADM_ASL_ERR_CHYBA_UPLOAD_SB."</p>\n";
        $chyba_vse=1;
        $chyba_fatal=1;
      endif;
    endif;
  else: // stred - velikost souboru
    if ($_FILES[$vs_jm_souboru]["size"]>$max_velikost):
      // chyba: max. velikost souboru
      $chyba_txt.="<p align=\"center\" class=\"txt\">".RS_ADM_ASL_ERR_CHYBA_VELIKOST_SB." ".($max_velikost/1024)." kB.</p>\n";
      $chyba_fatal=1;
    endif;
    $chyba_vse=1;
  endif; // konec - velikost souboru
endif; // konec - $chyba_vse

if ($chyba_vse==1):
  // vyskystla se chyba
  return array('stav' => 0, 'jmeno_sb' => $_FILES[$vs_jm_souboru]["name"], 'cesta_sb' => $nove_umisteni, 'chyba' => $chyba_fatal, 'chyba_popis' => $chyba_txt, 'chyba_format' => $chyba_format);
else:
  // vse OK
  return array('stav' => 1, 'jmeno_sb' => $_FILES[$vs_jm_souboru]["name"], 'cesta_sb' => $nove_umisteni, 'chyba' => $chyba_fatal, 'chyba_popis' => $chyba_txt, 'chyba_format' => $chyba_format);
endif;
}
?>
