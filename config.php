<?php

require "SimpleLDAP.class.php";

$tld = "com";
$domain = "example"

$ldap = new SimpleLDAP('util1.$domain.$tld', 389, 3);
$ldap->dn = 'ou=people,dc=$domain,dc=$tld';
$ldap->gdn = 'ou=groups,dc=$domain,dc=$tld';
$ldap->sdn = 'ou=SUDOers,dc=$domain,dc=$tld';

$ldap->adn = "cn=Manager,dc=$domain,dc=$tld";
$ldap->apass = "";

?>
