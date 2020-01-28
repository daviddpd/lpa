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
foreach ( $user_ldap->obj as $uid => $v ) 
{
	if (isset ($v{'uidnumber'}) ) {
		echo " $uid " . $v{'uidnumber'} . "\n";
		array_push ( $unixuids, $v{'uidnumber'});
	}
	
}
sort  ($unixuids);
print_r ( $unixuids );

?>
