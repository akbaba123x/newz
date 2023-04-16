<?php
################################################################################
#                                                                              #
#   Copyright (c) 2011 Patrik JAN, http://www.kvalitne.sk/                     #
#                                                                              #
#   Tento program je volné programové vybavení; můžete jej šířit               #
#   a modifikovat podle ustanovení Obecné veřejné licence GNU, vydávané Free   #
#   Software Foundation; a to buď verze 2 této licence anebo (podle vašeho     #
#   uvážení) kterékoli pozdější verze.                                         #
#                                                                              #
################################################################################

	// Subor sluzi na vygenerovanie noveho hashu pre zabudnute heslo.
	// Ucet v phpRS je mozne obnovit pomocou vygenerovania noveho haslu a jeho 
	// vlozenie do prisusnej databazovej tabulky - napr. pomocou phpMyAdmin-u 
	// Z dovodu vyssej bezpecnosti tento subor pouzite na ostrom webe 
	// len docasne - po vygenerovani hashu znovu odkomentujte 19. riadok 
	
	die('Pristup blokovany - pre spravnu funkciu zakomentujte 19. riadok');

################################################################################

	define("IN_CODE", TRUE);
	require("config.php");
	// nacitanie hashovacich funkcii
	require('admin/hash_functions.php');
	
################################################################################

	// ak je vyzadovane SSL, presmerujem na https
	if (
		isset($GLOBALS['rsconfig']['ssl'])
		&&
		$GLOBALS['rsconfig']['ssl'] === true
		&&
		!isset($_SERVER['HTTPS'])
	) {
		header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		die();
	}

################################################################################

	// spracovanie poziadavky
	if (!empty($_POST['plaintextpass'])) {
		header(
			'Location: '
				.(!isset($_SERVER['HTTPS'])? 'http://' : 'https://')
				.$_SERVER["HTTP_HOST"]
				.$_SERVER['SCRIPT_NAME']
				.'?hash='.base64_encode(calculate_hash($_POST['plaintextpass']))
			,
			301
		);
		die;
	}

################################################################################
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>phpRS hash generator</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['rsconfig']['kodovani']; ?>">
	<meta name="author" content="Patrik Ján, http://www.kvalitne.sk/">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta name="robots" content="noindex,follow">
	<meta name="googlebot" content="nosnippet,noarchive">
	<style type="text/css">
	<!--
	body {
		font-family: verdana, geneva, arial, helvetica, sans-serif;
		font-size: 16px;
		color: #444;
		background: #eee url('image/key.png') no-repeat;
		padding-top:210px;
	}
	table {
		font-family: verdana, geneva, arial, helvetica, sans-serif;
		font-size: 16px;
		color: #000;
	}
	.textpole {
		border: 1px solid #000;
		color: #f30;
		font-family: verdana, geneva, arial, helvetica, sans-serif;
		font-size: 16px;
		padding:2px;
	}
	.tl {
		font-family: arial, sans-serif;
		font-size: 14px;
		font-weight: bold;
		text-align: center;
		background:#ccc url('image/button-grey.png') repeat-x;
		color:#111;
	}
	#key {
		float:left;
	}
	small {
		font-size:50%;
		color:#999;
	}
	-->
	</style>
</head>
<body>

	<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" style="padding:1em; background:#ddd;">
		<table cellspacing="8" align="center" style="border:1px dotted #ccc;">
		<tr>
			<td>
				PASSWORD:
			<td>
				<input
					type="password"
					name="plaintextpass"
					size="50"
					maxlength="100"
					class="textpole">
		<tr>
			<td>
				HASH:
			<td>
				<input
					type="text"
					size="50"
					class="textpole"
					value="<?php echo (!empty($_GET['hash'])? htmlspecialchars(base64_decode($_GET['hash']), ENT_QUOTES) : ''); ?>"
					<?php echo (empty($_GET['hash'])? 'disabled="disabled"' : ''); ?>>
		<tr>
			<td>
			<td><input type="submit" value="  GENERATE HASH  " class="tl">

		</table>
	</form>
	
	<p align="right">
		<small>phpRS hash generator &copy; 2011 Patrik JAN, www.kvalitne.sk</small>

</body>
</html>