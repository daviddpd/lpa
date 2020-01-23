<?php
$shells = array( 
		'/bin/sh',
		'/bin/bash',
		'/usr/bin/sh',
		'/usr/bin/bash',
		'/usr/bin/tmux',
		'/bin/tcsh',
		'/bin/csh',
		'/bin/zsh	',
	);

$editObj = array (
	'editable' => true,
	'deletable' => false,
	'by' => 'ldapadmin',
	'values' => NULL,
	'relabel' => NULL,
	'display' => TRUE,
	'type' => "text",
);

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
$ldapFields{'edittable'}{'user'}{'sn'}{'by'} = "bastion";

$ldapFields{'edittable'}{'user'}{'givenname'} = $editObj;
$ldapFields{'edittable'}{'user'}{'givenname'}{'relabel'} = "First Name";
$ldapFields{'edittable'}{'user'}{'givenname'}{'by'} = "bastion";

$ldapFields{'edittable'}{'user'}{'cn'} = $editObj;
$ldapFields{'edittable'}{'user'}{'cn'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'gecos'} = $editObj;
$ldapFields{'edittable'}{'user'}{'gecos'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'objectclass'} = $editObj;
$ldapFields{'edittable'}{'user'}{'objectclass'}{'display'} = FALSE;

$ldapFields{'edittable'}{'user'}{'loginshell'} = $editObj;
$ldapFields{'edittable'}{'user'}{'loginshell'}{'values'} = $shells;
$ldapFields{'edittable'}{'user'}{'loginshell'}{'by'} = "bastion";
$ldapFields{'edittable'}{'user'}{'loginshell'}{'type'} = "select";
$ldapFields{'edittable'}{'user'}{'loginshell'}{'src'} = "api.php?action=query&value=shells";

$ldapFields{'edittable'}{'user'}{'userpassword'} = $editObj;
$ldapFields{'edittable'}{'user'}{'userpassword'}{'by'} = "bastion";

$ldapFields{'edittable'}{'user'}{'title'} = $editObj;
$ldapFields{'edittable'}{'user'}{'title'}{'by'} = "bastion";
$ldapFields{'edittable'}{'user'}{'title'}{'deletable'} = TRUE;

$ldapFields{'edittable'}{'user'}{'sshpublickey'} = $editObj;
$ldapFields{'edittable'}{'user'}{'sshpublickey'}{'by'} = "bastion";
$ldapFields{'edittable'}{'user'}{'sshpublickey'}{'deletable'} = TRUE;

$ldapFields{'edittable'}{'user'}{'initials'} = $editObj;
$ldapFields{'edittable'}{'user'}{'initials'}{'relabel'} = "Middle Name/Inital";
$ldapFields{'edittable'}{'user'}{'initials'}{'by'} = "bastion";
$ldapFields{'edittable'}{'user'}{'initials'}{'deletable'} = TRUE;

$ldapFields{'edittable'}{'user'}{'homedirectory'} = $editObj;

$ldapFields{'edittable'}{'user'}{'mail'} = $editObj;
$ldapFields{'edittable'}{'user'}{'mail'}{'relabel'} = "Email";
$ldapFields{'edittable'}{'user'}{'mail'}{'by'} = "bastion";


?>