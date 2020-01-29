<?php

define ('_LDAP_ADMIN_ACCOUNT', "admin");

$booleans = array (
	'YES',
	'NO'
);
$shells = array( 
		'/bin/sh',
		'/bin/bash',
		'/usr/bin/sh',
		'/usr/bin/bash',
		'/usr/bin/tmux',
		'/bin/tcsh',
		'/bin/csh',
		'/bin/zsh'
	);

$editObj = array (
	'ldapou' => 'user',
	'editable' => true,
	'deletable' => false,
	'by' => _LDAP_ADMIN_ACCOUNT,
	'values' => NULL,
	'relabel' => NULL,
	'display' => TRUE,
	'type' => "text",

);
global $ldapFields;
$ldapFields = array ();
$ldapFields{'edittable'} = array ();
$ldapFields{'edittable'}{'user'} = array ();
$ldapFields{'edittable'}{'user'}{'uid'} = $editObj; 
$ldapFields{'edittable'}{'user'}{'uid'}{'relabel'} = "username";
$ldapFields{'edittable'}{'user'}{'uid'}{'editable'} = FALSE;

$ldapFields{'edittable'}{'user'}{'uidnumber'} = $editObj;
$ldapFields{'edittable'}{'user'}{'gidnumber'} = $editObj;

$ldapFields{'edittable'}{'user'}{'sn'} = $editObj;
$ldapFields{'edittable'}{'user'}{'sn'}{'relabel'} = "Last Name";
$ldapFields{'edittable'}{'user'}{'sn'}{'by'} = "_self";

$ldapFields{'edittable'}{'user'}{'givenname'} = $editObj;
$ldapFields{'edittable'}{'user'}{'givenname'}{'relabel'} = "First Name";
$ldapFields{'edittable'}{'user'}{'givenname'}{'by'}  = "_self";

$ldapFields{'edittable'}{'user'}{'cn'} = $editObj;
$ldapFields{'edittable'}{'user'}{'cn'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'gecos'} = $editObj;
$ldapFields{'edittable'}{'user'}{'gecos'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'objectclass'} = $editObj;
$ldapFields{'edittable'}{'user'}{'objectclass'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'loginshell'} = $editObj;
$ldapFields{'edittable'}{'user'}{'loginshell'}{'values'} = $shells;
$ldapFields{'edittable'}{'user'}{'loginshell'}{'by'}  = "_self";
$ldapFields{'edittable'}{'user'}{'loginshell'}{'type'} = "select";
$ldapFields{'edittable'}{'user'}{'loginshell'}{'src'} = "api.php?action=query&value=shells";

$ldapFields{'edittable'}{'user'}{'userpassword'} = $editObj;
$ldapFields{'edittable'}{'user'}{'userpassword'}{'by'}  = "_self";
$ldapFields{'edittable'}{'user'}{'userpassword'}{'type'}  = "password";
$ldapFields{'edittable'}{'user'}{'userpassword'}{'display'}  = FALSE;

$ldapFields{'edittable'}{'user'}{'title'} = $editObj;
$ldapFields{'edittable'}{'user'}{'title'}{'by'}  = "_self";
$ldapFields{'edittable'}{'user'}{'title'}{'deletable'} = TRUE;

$ldapFields{'edittable'}{'user'}{'sshpublickey'} = $editObj;
$ldapFields{'edittable'}{'user'}{'sshpublickey'}{'by'}  = "_self";
$ldapFields{'edittable'}{'user'}{'sshpublickey'}{'deletable'} = TRUE;
$ldapFields{'edittable'}{'user'}{'sshpublickey'}{'editable'} = FALSE;

$ldapFields{'edittable'}{'user'}{'initials'} = $editObj;
$ldapFields{'edittable'}{'user'}{'initials'}{'relabel'} = "Middle Name/Inital";
$ldapFields{'edittable'}{'user'}{'initials'}{'by'}  = "_self";
$ldapFields{'edittable'}{'user'}{'initials'}{'deletable'} = TRUE;

$ldapFields{'edittable'}{'user'}{'homedirectory'} = $editObj;

$ldapFields{'edittable'}{'user'}{'mail'} = $editObj;
$ldapFields{'edittable'}{'user'}{'mail'}{'relabel'} = "Email";
$ldapFields{'edittable'}{'user'}{'mail'}{'by'}  = "_self";


$ldapFields{'edittable'}{'groups'}{'accounting'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'admin'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'antontsv'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'apache'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'bastion'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'ecards'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'campaigns'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'contractor'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'cron'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'cronadmins'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'cronManager'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'CS'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'database'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'design'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'devs'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'ecards'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'eng1'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'eng2'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'executives'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'mollyb'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'ngms'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'ops'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'pwmadmin'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'QA'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'roberth'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'rundeck'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'TempLogin'} = $editObj;
#$ldapFields{'edittable'}{'groups'}{'test1'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'vpn'} = $editObj;
$ldapFields{'edittable'}{'groups'}{'wheel'} = $editObj;

foreach ( array_keys ($ldapFields{'edittable'}{'groups'}) as $g ) {
	$ldapFields{'edittable'}{'groups'}{$g}{'ldapou'} = 'groups';
	$ldapFields{'edittable'}{'groups'}{$g}{'deletable'} = TRUE;
	$ldapFields{'edittable'}{'groups'}{$g}{'values'} = $booleans;
	$ldapFields{'edittable'}{'groups'}{$g}{'by'} = "admin";
	$ldapFields{'edittable'}{'groups'}{$g}{'type'} = "select";
	$ldapFields{'edittable'}{'groups'}{$g}{'src'} = "api.php?action=query&value=booleans";

}



?>