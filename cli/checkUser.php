<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

require "../SimpleLDAP.class.php";
include "../config/config.php";
require "cli.args.php";


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
";

}

function _do ($user, $srv = NULL, $attr = NULL) {
	global $user_ldap;
	$user_ldap->getUser($user);
	$r = array();
	$groups = $user_ldap->getUsersGroup("dpd", true);
	$r['user'] = $user_ldap->selfObj;
	$r['groups'] = $groups;
	if ( is_null ($attr) ) {
		print_r ($r);
	} else {
		print (" $srv $user $attr " . $r['user'][$user][$attr] . "\n");
	}
}

$arg = new CommandLine();
$opt = $arg->parseArgs($argv);
print_r ( $opt );
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

$ldapservers = array (
	'ldap1.sjc1.care2.com',
	'ldap1.iad1.care2.com',
	'ldap2.sjc1.care2.com',
	'ldap2.iad1.care2.com',
);

if ( isset ($opt['checkall']) ) {
	foreach ( $ldapservers as $srv ) {
		$user_ldap = new SimpleLDAP($srv, 389, 3);
		$user_ldap->dn = 'ou=people,dc=care2,dc=com';
		$user_ldap->gdn = 'ou=groups,dc=care2,dc=com';
		$user_ldap->sdn = 'ou=SUDOers,dc=care2,dc=com';
		_do ( $opt['user'], $srv, $opt['attr'] );
	}
	
	
} else {

	_do ( $opt['user'], NULL, $opt['attr'] );

}

?>
