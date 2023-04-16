<?php

######################################################################
# phpRS Pictures 1.0.7
######################################################################

// Copyright (c) 2001-2005 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny software.

Header("Pragma: no-cache");
Header("Cache-Control: no-cache");
Header("Expires: ".GMDate("D, d M Y H:i:s")." GMT");
Header("Content-type: image/jpeg");

/*
vstup: $rvel - realna velikost obr.
       $barva - urcuje barevne spektrum
*/

if (strtoupper($_SERVER['REQUEST_METHOD'])=='GET'):
  // get metoda
  if (!isset($_GET['rvel'])): $delka_obrazku=0; else: $delka_obrazku=$_GET['rvel']; endif;
  if (!isset($_GET['barva'])): $id_barva=1; else: $id_barva=$_GET['barva']; endif;
else:
  // post metoda
  if (!isset($_POST['rvel'])): $delka_obrazku=0; else: $delka_obrazku=$_POST['rvel']; endif;
  if (!isset($_POST['barva'])): $id_barva=1; else: $id_barva=$_POST['barva']; endif;
endif;

$delka_obrazku++; // automaticke navyseni delky obrazku o 1px, aby nevzniklaly prazde obrazky

$pic=imagecreate($delka_obrazku,8); // id obrazku (vytvoreni kostry)

// volba barvy
switch($id_barva):
  case 1: // modra
    $b1 = ImageColorAllocate($pic,14,115,173);
    $b2 = ImageColorAllocate($pic,52,155,215);
    $b3 = ImageColorAllocate($pic,113,185,228);
    break;
  case 2: // cervena
    $b1 = ImageColorAllocate($pic,208,51,40);
    $b2 = ImageColorAllocate($pic,234,103,95);
    $b3 = ImageColorAllocate($pic,250,147,140);
    break;
  case 3: // hneda
    $b1 = ImageColorAllocate($pic,169,106,55);
    $b2 = ImageColorAllocate($pic,199,144,99);
    $b3 = ImageColorAllocate($pic,207,162,125);
    break;
  case 4: // oranzova
    $b1 = ImageColorAllocate($pic,250,113,24);
    $b2 = ImageColorAllocate($pic,249,150,74);
    $b3 = ImageColorAllocate($pic,250,176,125);
    break;
  case 5: // zluta
    $b1 = ImageColorAllocate($pic,180,178,15);
    $b2 = ImageColorAllocate($pic,223,225,42);
    $b3 = ImageColorAllocate($pic,255,255,63);
    break;
  case 6: // zelena
    $b1 = ImageColorAllocate($pic,15,178,15);
    $b2 = ImageColorAllocate($pic,42,225,42);
    $b3 = ImageColorAllocate($pic,63,255,63);
    break;
  case 7: // svetle modra
    $b1 = ImageColorAllocate($pic,15,178,180);
    $b2 = ImageColorAllocate($pic,42,225,223);
    $b3 = ImageColorAllocate($pic,63,255,255);
    break;
  case 8: // bila
    $b1 = ImageColorAllocate($pic,233,233,233);
    $b2 = ImageColorAllocate($pic,243,243,243);
    $b3 = ImageColorAllocate($pic,255,255,255);
    break;
  default: // defaultne bila barva
    $b1 = ImageColorAllocate($pic,233,233,233);
    $b2 = ImageColorAllocate($pic,243,243,243);
    $b3 = ImageColorAllocate($pic,255,255,255);
    break;
endswitch;

imagefilledrectangle($pic,0,0,$delka_obrazku,1,$b1);
imagefilledrectangle($pic,0,2,$delka_obrazku,2,$b2);
imagefilledrectangle($pic,0,3,$delka_obrazku,4,$b3);
imagefilledrectangle($pic,0,5,$delka_obrazku,5,$b2);
imagefilledrectangle($pic,0,6,$delka_obrazku,7,$b1);

// vykresleni id obrazku
ImageJPEG($pic);
?>
