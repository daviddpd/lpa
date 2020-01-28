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
	--oc=<STRING>    ; object class being added/removed
	--remove		 ; boolean, remove instead of add.
";

}

$arg = new CommandLine();
$opt = $arg->parseArgs($argv);
print_r ( $opt );

if ( 
	! isset ( $opt['user'] )  ||
	! isset ( $opt['oc'] ) 
) 
{
	_usage();
	exit;
}

$user_ldap->getUser($opt['user']);
print_r ( $user_ldap->selfObj );

$target_ldap->auth($ldapAdmin, $ldapAdminPw);
if ( isset ( $opt['remove'] ) ) {
	$target_ldap->removeObjectClass($opt['user'], $opt['oc'] );
} else {
	$target_ldap->addObjectClass($opt['user'], $opt['oc'] );
}

$user_ldap->getUser($opt['user']);
print_r ( $user_ldap->selfObj );


?>
