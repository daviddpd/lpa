<?php

require_once "SimpleLDAP.class.php";

$tld = "com";
$domain = "example";


$user_ldap = new SimpleLDAP('ldap.corp.$domain.$tld', 389, 3);
$user_ldap->dn = 'ou=people,dc=care2,dc=com';
$user_ldap->gdn = 'ou=groups,dc=care2,dc=com';
$user_ldap->sdn = 'ou=SUDOers,dc=care2,dc=com';

$user_ldap->adn = "cn=Manager,dc=care2,dc=com";
$user_ldap->apass = "";

$target_ldap = SimpleLDAP('ldap.corp.$domain.$tld', 389, 3);
$target_ldap->dn = 'ou=people,dc=care2,dc=com';
$target_ldap->gdn = 'ou=groups,dc=care2,dc=com';
$target_ldap->sdn = 'ou=SUDOers,dc=care2,dc=com';

$target_ldap->adn = "cn=Manager,dc=care2,dc=com";
$target_ldap->apass = "";

$brand = array();

$brand{'copyright'} = "David P. Discher";
$brand{'logo'}{'src'} = "./html/images/dpd-logo-only-2019.png";
$brand{'logo'}{'width'} = "200px";



?>
