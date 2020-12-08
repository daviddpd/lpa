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
	--json			; output in json
	--sleep		; add jitter
";

}

function _do ($user, $srv = NULL, $attr = NULL, $json = false) {
	global $user_ldap, $output;
	$user_ldap->getUser($user);
	$r = array();
	$r['user'] = $user_ldap->selfObj;
	if ( is_null ($attr) ) {
		print_r ($r);
	} else {
		if ( $json ) {
			$output['table']['rows'][] = array ( $srv, $user, $attr,  $r['user'][$user][$attr], "preupdate" );
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
if (isset ($opt['sleep'])) {
	if (is_file ('/usr/local/etc/ansible-env.sh') ) {
		$sleep =`. /usr/local/etc/ansible-env.sh;  echo \${_RAND60}`;
		sleep((int)$sleep);
	}
} 

$c = count ($ldapservers);
$t = time();
$today = date("Y-m-d H:i:s T", $t);
$s = $t % (4*60);
$minutes = intdiv( $t , 60) ;
$m = $minutes % $c;
// printf ( "%16d %6d %6d \n", $t, $s, $m ); 

$output['unixtime'] = $t;
$output['date'] = $today;
$output['user'] = $opt['user'];
$output['srv'] = $ldapservers[$m];

$update_fields[$opt['attr']] = json_encode ( $output );

$output['table']['title'] = "LDAP Update Test";
$output['table']['header'] = array ( "server", "user", "attr", "new-value" );
$user_ldap = new SimpleLDAP($output['srv'], 389, 3);
$user_ldap->dn  = $ldap_dn;
$user_ldap->gdn = $ldap_gdn;
$user_ldap->sdn = $ldap_sdn;
$user_ldap->auth($ldapAdmin, $ldapAdminPw);


$status = $user_ldap->modifyUser(
	$opt['user'],
	$update_fields
);
$output['table']['rows'][] = array ( $output['srv'], $opt['user'], $opt['attr'], $update_fields[$opt['attr']] );
echo json_encode ( $output ) . "\n";

?>
