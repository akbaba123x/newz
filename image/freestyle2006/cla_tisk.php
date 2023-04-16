<?php

######################################################################
# phpRS Layout Engine 2.7.0 - verze: "freestyle2006"
#                           - clanek sablona: "Tisk" / standardni tiskova sablona
######################################################################

// Copyright (c) 2002-2006 by Jiri Lukas (jirilukas@supersvet.cz)
// http://www.supersvet.cz/phprs/
// This program is free software. - Toto je bezplatny a svobodny software.

// ------------------------------------ [standardni clankova tiskova sablona] ------------------------------------
?>
<div class="tisk">
<h1><?php echo $GLOBALS["clanek"]->Ukaz("titulek"); ?></h1>
<span class="cla-informace-tisk">
<?php echo RS_CS_AUTOR; ?>: <?php echo $GLOBALS["clanek"]->Ukaz("autor_jm"); ?> &lt;<?php echo $GLOBALS["clanek"]->Ukaz("autor_jen_mail"); ?>&gt;,
<?php echo RS_CS_TEMA; ?>: <?php echo $GLOBALS["clanek"]->Ukaz("tema_jm"); ?>,
<?php if ($GLOBALS["clanek"]->Ukaz("zdroj")!=''): echo RS_CS_ZDROJ.': '.$GLOBALS["clanek"]->Ukaz("zdroj").', '; endif; ?>
<?php echo RS_CS_VYDANO_DNE; ?>: <?php echo $GLOBALS["clanek"]->Ukaz("datum"); ?>
</span><br />
<hr />
<p><?php echo $GLOBALS["clanek"]->Ukaz("uvod"); ?><br /><br /><?php echo $GLOBALS["clanek"]->Ukaz("text"); ?></p>
</div>
<?php
// -------------------------------- [konec - standardni clankova tiskova sablona] --------------------------------

?>
