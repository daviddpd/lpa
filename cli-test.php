<?php


require "SimpleLDAP.class.php";
include "config.php"

function _prompt( $prompt, $hidden=false ) {

	echo $prompt;
	if ( $hidden ) { system('stty -echo'); }
	$password = trim(fgets(STDIN));
	if ( $hidden ) { system('stty echo'); }
	echo "\n";
	return $password;
}

// $ldap->modifyUser('dpd', array ( 'title' => 'Director of Ops' ) );
// $ldap->modDelAttr('dpd', array ( 'title' => 'Director of Ops' ) );

$u = _prompt( 'user: ' );

$ldap->getUsers("(uid=" . $u . ")");
$a = $ldap->getAttrs($u);

print_r ( $a );
print_r ( $ldap->obj );
/*
//print_r ( $ldap->data );

echo "Changing Password  \n";
$cp = _prompt ( 'Current Password: ', true );
$ldap->check_ldap_passwd($u, $cp);
$np1 = _prompt ( 'New Password: ', true );
$np2 = _prompt ( 'New Password Again: ', true );
if ( strcmp($np1, $np2) == 0 ) {
	$ldap->change_ldap_passwd($u, $cp, $np1);
	$ldap->getUsers("(uid=" . $u . ")");
	$a = $ldap->getAttrs($u);
	print_r ( $ldap->obj );
} else {
	echo "Passwords don't match. \n";
}

*/

//$g = _prompt( 'group: ' );
//$ldap->getGroup("(cn=" . $g . ")");
// $g = _prompt( 'group: ' );
// $ldap->getGroup("objectclass=posixGroup");

// print_r ( $ldap->gobj );
// $ldap->addGroupMember('test1', "dpd");
// $ldap->delGroupMember('test1', "dpd");
//print_r ($ldap->getUsersGroup('dpd'));



?>
