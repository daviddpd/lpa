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
	--admin           ; Bind as LDAP admin
	--user=<STRING>  ; username/uid required
	--attr=<STRING>  ; only print att
	--checkall		; check all ldap servers
	--json			; output in json
	--server=LDAPSERVER ; connect to different ldap server than in config
	--binddn=...	; bind as this instead.
	--password		; prompts for bind password
	--notls			; disbale tls/ssl
	--update		; update/write value
	--valvue		; value to write in --attr
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

function _update ($user, $srv = NULL, $attr = NULL, $value = NULL) {
	global $user_ldap, $output, $ldapAdmin, $ldapAdminPw;
	$user_ldap->auth($ldapAdmin, $ldapAdminPw);
	$user_ldap->getUser($user);
	$update_fields[$attr] = $value;
	$status = $user_ldap->modifyUser($user, $update_fields);
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

/*
	--server=LDAPSERVER ; connect to different ldap server than in config
	--binddn=...	; bind as this instead.
	--password		; prompts for bind password
*/
if (isset ($opt['server']))  {
	$user_ldap->close();
	if ( isset ($opt['notls']) ) {
		$tls = false;
	} else {
		$tls = true;
	}
#	echo "Connecting to " . $opt['server'] . "\n";
	$user_ldap = new SimpleLDAP($opt['server'], 389, 3, $tls);
	
}
if (isset ($opt['binddn']))  {
	$ldapAdmin = $opt['binddn'];
}
#echo "Bind as DN = " . $ldapAdmin . "\n";
if (isset ($opt['password']))  {
	$ldapAdminPw = _prompt("Password: ", true);
}

if (isset ($opt['admin']) ||  ( isset ($opt['binddn'])  && isset ($opt['password']) )  )  {
#	echo " Binding ... \n";
	$user_ldap->auth($ldapAdmin, $ldapAdminPw);
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

	if ( isset($opt['update']) ) { 
		_update ( $opt['user'], NULL, $opt['attr'], $opt['value'] );		
	} else {
		_do ( $opt['user'], NULL, $opt['attr'], $json );
	}

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