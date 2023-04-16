<?php

# 	Od php 5.1.2 je mozne pouzitie silnejsieho hashovacieho olgoritmu SHA512
#	pomocu funkcie hash(). Zmena algoritmu musi byt vykonana pred instalaciou. 
# 	Pozor, zmena hashovacieho algoritmu znefunckcni vsetky existujuce hesla 
# 	v systeme.  

function calculate_hash($plaintext_password = "") {
	$phprs_old_type_hash = md5($plaintext_password);
	return convert_old_type_hash($phprs_old_type_hash);
}

function convert_old_type_hash($phprs_old_type_hash = "") {
	$hash = sha1(PASSWORD_SALT.$phprs_old_type_hash);
	for ($i=0; $i < 1000; $i++) {
	    $hash = sha1($i.$hash);
	}
	return $hash;
}



/*
function calculate_hash($plaintext_password = "") {
	$phprs_old_type_hash = md5($plaintext_password);
	return convert_old_type_hash($phprs_old_type_hash);
}

function convert_old_type_hash($phprs_old_type_hash = "") {
	$hash = hash("SHA512", PASSWORD_SALT.$phprs_old_type_hash);
	for ($i=0; $i < 10000; $i++) {
		$hash = hash("SHA512", $i.$hash);
	}
	return $hash;
}
*/

?>