<?php

require_once "SimpleLDAP.class.php";

$tld = "com";
$domain = "example";

$ldap = new SimpleLDAP('ldap.corp.$domain.$tld', 389, 3);
$ldap->dn = "ou=people,dc=$domain,dc=$tld";
$ldap->gdn = "ou=groups,dc=$domain,dc=$tld";
$ldap->sdn = "ou=SUDOers,dc=$domain,dc=$tld";

$ldap->adn = "cn=Manager,dc=$domain,dc=$tld";
$ldap->apass = "";

$brand = array();

$brand{'copyright'} = "David P. Discher";
$brand{'logo'}{'src'} = "./html/images/dpd-logo-only-2019.png";
$brand{'logo'}{'width'} = "200px";



?>
