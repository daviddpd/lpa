<?php

	
	include "config/config.php";
	include "config/ldapFields.php";

	$r = array ();	
	$u = $_SERVER['REMOTE_USER'];
	$ldap->getUsers("(uid=" . $u . ")");
	$a = $ldap->getAttrs($u);
	$groups = $ldap->getUsersGroup($u);		
	$groupLut = array ();
	foreach ( $groups as $g ) {
		$groupLut{$g} = 1;
	}
	
if ($_SERVER['REQUEST_METHOD'] == "GET"  ) 
{

	$r['method'] = $_SERVER['REQUEST_METHOD'];

	if ( $_GET['action'] = "query" ) {

		if ( $_GET['value'] == "shells" ) { $r = $shells; }

	} else {
		$r['errno'] = -1;	
		$r['result'] = 'Not implemented';
	}

	header('Content-type: application/json');
	echo json_encode ( $r );

} elseif ($_SERVER['REQUEST_METHOD'] == "POST"  ) 
{

	$r['method'] = $_SERVER['REQUEST_METHOD'];
	
	$r['errno'] = -1;	
	$r['result'] = 'Not implemented';
	header('Content-type: application/json');
	echo json_encode ( $r );


} else {
	$r['method'] = $_SERVER['REQUEST_METHOD'];
	$r['errno'] = -1;	
	$r['result'] = 'Not implement';
	header('Content-type: application/json');
	echo json_encode ( $r );
}
	
?>