<?php

######################################################################
# phpRS Layout Engine 2.7.0 - verze: "freestyle2006"
#                           - clanek sablona: "Standard"
######################################################################

// Copyright (c) 2002-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

if (!isset($rs_typ_clanku)): $rs_typ_clanku=""; endif;

switch ($rs_typ_clanku):
  case "kratky":
// ------------------------------------- [kratky clanek] -------------------------------------
?>
<div class="ram">
    <a href="search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=<?php echo $GLOBALS["clanek"]->Ukaz("tema_id"); ?>"><img src="<?php echo $GLOBALS["clanek"]->Ukaz("tema_obr"); ?>" border="0" align="left" alt="<?php echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>" class="obrtema" /></a>
<div class="cla-cely">
    <h1 class="cla-nadpis"><?php echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<div class="cla-obsah">
<div class="cla-text">
    <?php echo $GLOBALS["clanek"]->Ukaz("uvod"); ?><br /><br />

<span class="cla-informace">
| Autor: <a href="<?php echo $GLOBALS["clanek"]->Ukaz("autor_mail"); ?>"><?php echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?></a> |
Vydáno dne <?php echo $GLOBALS["clanek"]->Ukaz("datum"); ?> | <?php echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení |
<a href="mailto:?subject=<?php echo rawurlencode(RS_CS_MAIL_PREDMET." ".$GLOBALS["wwwname"])."&amp;body=".$GLOBALS["baseadr"]."view.php?cisloclanku=".htmlspecialchars($GLOBALS["clanek"]->Ukaz("link")); ?>"><img src="image/freestyle2006/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a>
<a href="rservice.php?akce=tisk&amp;cisloclanku=<?php echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle2006/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a>
</span>

</div>
</div>
</div>
<?php
// --------------------------------- [konec - kratky clanek] ---------------------------------
  break;
  case "nahled":
// ----------------------------------- [dl. clanek nahled] -----------------------------------
?>
<div class="ram">
    <a href="search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=<?php echo $GLOBALS["clanek"]->Ukaz("tema_id"); ?>"><img src="<?php echo $GLOBALS["clanek"]->Ukaz("tema_obr"); ?>" border="0" align="left" alt="<?php echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>" class="obrtema" /></a>
<div class="cla-cely">
    <h1 class="cla-nadpis"><a href="view.php?nazevclanku=<?php echo $GLOBALS["clanek"]->Ukaz("link_seo"); ?>&amp;cisloclanku=<?php echo $GLOBALS["clanek"]->Ukaz("link"); ?>" class="clanek"><?php echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></a></h1>
<div class="cla-obsah">
    <div class="cla-text">
        <?php echo $GLOBALS["clanek"]->Ukaz("uvod"); ?>
    </div><br />

<span class="cla-informace">
| Autor: <a href="<?php echo $GLOBALS["clanek"]->Ukaz("autor_mail"); ?>"><?php echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?></a> |
Vydáno dne <?php echo $GLOBALS["clanek"]->Ukaz("datum"); ?> | <?php echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení |
<a href="mailto:?subject=<?php echo rawurlencode(RS_CS_MAIL_PREDMET." ".$GLOBALS["wwwname"])."&amp;body=".$GLOBALS["baseadr"]."view.php?cisloclanku=".htmlspecialchars($GLOBALS["clanek"]->Ukaz("link")); ?>"><img src="image/freestyle2006/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a>
<a href="rservice.php?akce=tisk&amp;cisloclanku=<?php echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle2006/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a>
</span>

</div>
</div>
</div>
<?php
// ------------------------------- [konec - dl. clanek nahled] -------------------------------
  break;
  case "cely":
// ------------------------------------ [dl. clanek cely] ------------------------------------
?>
<div class="preram">
<div class="cla-cely">
    <a href="search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=<?php echo $GLOBALS["clanek"]->Ukaz("tema_id"); ?>"><img src="<?php echo $GLOBALS["clanek"]->Ukaz("tema_obr"); ?>" border="0" align="left" alt="<?php echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>" class="obrtema" /></a>
    <h1 class="cla-nadpis"><?php echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<div class="cla-obsah">
    <div class="cla-text">
        <?php echo $GLOBALS["clanek"]->Ukaz("uvod"); ?><br /><br />
        <?php echo $GLOBALS["clanek"]->Ukaz("text"); ?>
    </div><br />
<?php
SouvisejiciAnketyCl($GLOBALS["clanek"]->Ukaz("anketa"),'view.php?cisloclanku='.$GLOBALS["clanek"]->Ukaz("link"));
SouvisejiciCl($GLOBALS["clanek"]->Ukaz("link"));
?>
<span class="cla-informace">
| Autor: <a href="<?php echo $GLOBALS["clanek"]->Ukaz("autor_mail"); ?>"><?php echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?></a> |
Vydáno dne <?php echo $GLOBALS["clanek"]->Ukaz("datum"); ?> | <?php echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení |
<a href="mailto:?subject=<?php echo rawurlencode(RS_CS_MAIL_PREDMET." ".$GLOBALS["wwwname"])."&amp;body=".$GLOBALS["baseadr"]."view.php?cisloclanku=".htmlspecialchars($GLOBALS["clanek"]->Ukaz("link")); ?>"><img src="image/freestyle2006/mail.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Informační e-mail" /></a>
<a href="rservice.php?akce=tisk&amp;cisloclanku=<?php echo $GLOBALS["clanek"]->Ukaz("link"); ?>" target="_blank"><img src="image/freestyle2006/printer.gif" height="22" width="20" border="0" hspace="0" vspace="1" align="middle" alt="Vytisknout článek" /></a>
</span>
<?php
// Pozor, jelikoz promenna "zdroj" nemusi obsahovat zadne udaje, je zde podminka, ktera zajistuje jeji (ne)zobrazeni
if ($GLOBALS["clanek"]->Ukaz("zdroj")!=''): echo '| Zdroj: '.$GLOBALS["clanek"]->Ukaz("zdroj").' '; endif;
?>
</div>
</div>
</div>
<?php
// -------------------------------- [konec - dl. clanek telo] --------------------------------
  break;
  case "zakazany":
// ------------------------------------ [zakazany clanek] ------------------------------------
?>
<div class="ram">
    <a href="search.php?rsvelikost=sab&amp;rstext=all-phpRS-all&amp;rstema=<?php echo $GLOBALS["clanek"]->Ukaz("tema_id"); ?>"><img src="<?php echo $GLOBALS["clanek"]->Ukaz("tema_obr"); ?>" border="0" align="left" alt="<?php echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>" class="obrtema" /></a>
<div class="cla-cely">
    <h1 class="cla-nadpis"><?php echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<div class="cla-obsah">
    <div class="cla-text">
        Bohužel nemáte opravnění tento článek číst. Pro získání dostatečných přístupových práv kontaktujte správce webu.
    </div><br />
<span class="cla-informace">
| Autor: <a href="<?php echo $GLOBALS["clanek"]->Ukaz("autor_mail"); ?>"><?php echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?></a> |
Vydáno dne <?php echo $GLOBALS["clanek"]->Ukaz("datum"); ?> | <?php echo $GLOBALS["clanek"]->Ukaz("visit"); ?> přečtení |
</span>

</div>
</div>
</div>
<?php
// -------------------------------- [konec - zakazany clanek] --------------------------------
  break;
endswitch;
?>