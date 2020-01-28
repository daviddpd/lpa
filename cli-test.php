<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);

require "SimpleLDAP.class.php";
include "config/config.php";

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

// $u = _prompt( 'user: ' );
// $p = _prompt( 'pass: ', true );

echo " ===============================" ;

// $data = $user_ldap->auth($u, $p);
// $user_ldap->getUsers($u);
//$user_ldap->getUsers("(uid=$u)");
$user_ldap->getUsers("objectclass=*");

//$a = $user_ldap->getAttrs($u);

// print_r ( $user_ldap->data );
//print_r ( $user_ldap->selfObj );
print_r ( $user_ldap->obj );

$dpd =  $user_ldap->obj['dpd']['objectclass'];
foreach ( $user_ldap->obj as $uid => $v ) 
{
	echo " ===========================> dpd vs $uid ====== \n";
	$a = array_diff ( $dpd, $v['objectclass'] );
	print_r ( $a );
	echo "        ---------------------                     \n";
	$b = array_diff ( $v['objectclass'],  $dpd );
	print_r ( $b );

}


/*
echo " ===============================" ;
$g = $user_ldap->getUsersGroup($u, true);
print_r ( $g );
echo " ===============================" ;
$g = $user_ldap->getUsersGroup($u);
print_r ( $g );
echo " ===============================" ;


echo "\n";
#$g = _prompt( 'group: ' );
#$user_ldap->getGroup("(cn=" . $g . ")");
#print_r ( $user_ldap->gdata );
#print_r ( $user_ldap->gobj );

$user_ldap->getUser();
print_r ( $user_ldap->data );
print_r ( $user_ldap->obj );
print_r ( $user_ldap->selfObj );
*/

/*
echo "Changing Password  \n";
$cp = _prompt ( 'Current Password: ', true );
$ldap->check_ldap_passwd($u, $cp);
$np1 = _prompt ( 'New Password: ', true );
$np2 = _prompt ( 'New Password Again: ', true );
if ( strcmp($np1, $np2) == 0 ) {
	$ldap->change_ldap_passwd($u, $cp, $np1);
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

// $ldap->addGroupMember('test1', "dpd");
// $ldap->delGroupMember('test1', "dpd");
//print_r ($user_ldap->getUsersGroup('dpd'));
//print_r ( $user_ldap->gdata );



?>
