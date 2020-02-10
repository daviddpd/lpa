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
	--user=<STRING>  ; Reference User
	--do             ; do the changes.  Default is dry-run
";

}

$arg = new CommandLine();
$opt = $arg->parseArgs($argv);
print_r ( $opt );

if ( 
	! isset ( $opt['user'] ) 
) 
{
	_usage();
	exit;
}

$user_ldap->getUser($opt['user']);
print_r ( $user_ldap->selfObj );

$user_ldap->getUsers("objectclass=*");
$target_ldap->auth($ldapAdmin, $ldapAdminPw);

$dpd =  $user_ldap->obj['dpd']['objectclass'];
$name = "";
$keys = array_keys ( $user_ldap->obj );
sort ($keys);

foreach ( $keys as $uid ) 
{
	$v = $user_ldap->obj[$uid];
	foreach ( array ( "gecos", "cn", "givenname", "initials", "sn") as $a ) {
		if ( isset ($v[$a]) ) {
			$name = $v[$a];
			break;
		} 
	}
	
//	echo " ===========================> dpd vs $uid ====== \n";
	printf ( "%-21s %s \n", $uid,  $name );
//	$a = array_diff ( $dpd, $v['objectclass'] );
//	print_r ( $a );	
// 	foreach ( $a as $oc ) 
// 	{
// 		echo " ===>  $uid, add $oc \n";
// 		if ( isset ( $opt['do'] ) ) {
// 			$target_ldap->addObjectClass($uid, $oc );
// 		}
// 	}
}

?>
