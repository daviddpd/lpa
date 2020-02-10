<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

include "../lib/pixl-server-user.class.php";
require "cli.args.php";
include "../config/config.php";

function _prompt( $prompt, $hidden=false ) {

	echo $prompt;
	if ( $hidden ) { system('stty -echo'); }
	$password = trim(fgets(STDIN));
	if ( $hidden ) { 
		system('stty echo'); 
		echo "\n";
	}
	return $password;
}

function _usage () {
echo "
	--user=<STRING>  ; username/uid (required) 
	--delete		 ; boolean, delete user
	--add			 ; boolean, add USER
	--password		 ; change password.
	--update		 ; update user
	--admin			 ; toggle admin flag for user
	
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
$u = $cronicle_user;
$p = $cronicle_password;

$c = new pixlServerUser($cronicle_server);
$c->CACERT =$CACERT;
$r = $c->adminLogin($u, $p);
$r = $c->checkUser($opt['user']);
print_r ($r);
if ( $r{'body'}{'user_exists'} == 1 ) 
{
	print ( " User: " . $opt['user']  . " ; user_exists = " . $r{'body'}{'user_exists'} . "\n");
	if  ( isset($opt['add']) ) {
		exit;
	}
}

if  ( isset($opt['add']) ) 
{

	$c->newUser['username'] =  $opt['user'];
	$c->newUser['password'] = _prompt("Password:", true );
	$c->newUser['full_name'] = _prompt("full_name:", false );
	$c->newUser['email'] = _prompt("email:", false );
	
	if ( isset($opt['admin']) ) 
	{
		$r = $c->createUser(true);
	} else {
		$r = $c->createUser(false);	
	}
	print_r ( $r );
}
if  ( isset($opt['password']) ) {
	$c->getUser($opt['user']);
	$pw = _prompt("Password:", true );
	$r = $c->updateUserPw($pw);
	print_r ( $c );	
}

?>
