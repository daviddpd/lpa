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


$arg = new CommandLine();
$opt = $arg->parseArgs($argv);
print_r ( $opt );

$user_ldap->getUsers("objectclass=*");
$unixuids =  array ();
$group_qed  = null;
foreach ( $user_ldap->obj as $uid => $v ) 
{
	if ( is_null ( $group_qed ) ) {
		$group_qed = $user_ldap->getUsersGroup($uid);
	}	
	if (isset ($v{'uidnumber'}) ) {
		echo " $uid " . $v{'uidnumber'} . "\n";
		array_push ( $unixuids, $v{'uidnumber'});
	}
	
}
sort  ($unixuids);
print_r ( $unixuids );
echo " ==============" ;

$groups = array ();
foreach ( $user_ldap->gdata as $g ) {

	$groups[$g['cn'][0]] = $g['gidnumber'][0];

}

print_r ($groups);
sort($groups);
print_r ($groups);


?>
