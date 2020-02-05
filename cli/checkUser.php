#!/bin/php
<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
ini_set('include_path',ini_get('include_path').':/var/www/nx/lpa:/z/home/dpd/lpa:/z/home/dpd/lpa/config:/z/home/dpd/lpa/lib:');

require "SimpleLDAP.class.php";
include "config/config.php";
require "cli/cli.args.php";

global $ouptut;
function _prompt( $prompt, $hidden=false ) {

	echo $prompt;
	if ( $hidden ) { system('stty -echo'); }
	$password = trim(fgets(STDIN));
	if ( $hidden ) { system('stty echo'); }
	echo "\n";
	return $password;
}
function _usage () {
echo "
	--user=<STRING>  ; username/uid required
	--attr=<STRING>  ; only print att
	--checkall		; check all ldap servers
	--json			; output in json
";

}

function _do ($user, $srv = NULL, $attr = NULL, $json = false) {
	global $user_ldap, $output;
	$user_ldap->getUser($user);
	$r = array();
	$groups = $user_ldap->getUsersGroup("dpd", true);
	$r['user'] = $user_ldap->selfObj;
	$r['groups'] = $groups;
	if ( is_null ($attr) ) {
		print_r ($r);
	} else {
		if ( $json ) {
			$output['table']['rows'][] = array ( $srv, $user, $attr,  $r['user'][$user][$attr] );
		} else {	
			print (" $srv $user $attr " . $r['user'][$user][$attr] . "\n");
		}
	}
}

$arg = new CommandLine();
$opt = $arg->parseArgs($argv);
// print_r ( $opt );
if ( 
	! isset ( $opt['attr'] )
)
{
	$opt['attr'] = NULL;	
}

if ( 
	! isset ( $opt['user'] )
) 
{
	_usage();
	exit;
}

if (isset ($opt['json'])) 
{
	$json = true;
} else {
	$json = false;
}

if ( isset ($opt['checkall']) ) {
	foreach ( $ldapservers as $srv ) {
		$user_ldap = new SimpleLDAP($srv, 389, 3);
		$user_ldap->dn  = $ldap_dn;
		$user_ldap->gdn = $ldap_gdn;
		$user_ldap->sdn = $ldap_sdn;
		_do ( $opt['user'], $srv, $opt['attr'], $json );
	}
	
	
} else {

	_do ( $opt['user'], NULL, $opt['attr'], $json );

}

if ( $json ) {

	$output['table']['title'] = "LDAP Replication Monitoring";
	$output['table']['header'] = array ( "server", "user", "attr", "value" );
	echo json_encode($output);
	echo "\n";

	$output1['complete'] = 1;
	$output1['code'] = 0;
	echo json_encode($output1);
	echo "\n";
	
}


?>