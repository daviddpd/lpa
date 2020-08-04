#!/bin/php
<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
ini_set('include_path',ini_get('include_path').':/var/www/nx/lpa:/z/home/dpd/lpa:/z/home/dpd/lpa/config:/z/home/dpd/lpa/lib:');

require "SimpleLDAP.class.php";
include "config/config.php";
require "cli/cli.args.php";

global $ouptut, $output_text, $output_nagios, $output_value, $nagios_returnCode;
$retCode = 0;

function _prompt( $prompt, $hidden=false ) {

	echo $prompt;
	if ( $hidden ) { system('stty -echo'); }
	$password = trim(fgets(STDIN));
	if ( $hidden ) { system('stty echo'); }
	echo "\n";
	return $password;
}
function _usage () {
echo "
    --admin            ; Bind as LDAP admin
    --user=<STRING>    ; username/uid required
    --attr=<STRING>    ; only print att
    --checkall         ; check all ldap servers

    --json             ; output in json, cronicle format

    --nagios           ; reutrn as nagios check
    --warning=seconds  ; number of seconds to trigger a warning (default=300)
    --critical=seconds ; number of seconds to trigger critical  (default=600)

    --server=LDAPSERVER ; connect to different ldap server than in config
    --binddn=...        ; bind as this instead.
    --password          ; prompts for bind password
    --notls             ; disbale tls/ssl
    --update            ; update/write value
    --valvue            ; value to write in --attr
";

}

function _do ($user, $srv = NULL, $attr = NULL, $json = false) {
	global $user_ldap, $output, $output_text, $output_value;
	$user_ldap->getUser($user);
	$r = array();
	$r['user'] = $user_ldap->selfObj;
	if ( is_null ($attr) ) {
		print_r ($r);
	} else {
		$output['table']['rows'][] = array ( $srv, $user, $attr,  $r['user'][$user][$attr] );
		$output_text[] = " $srv $user $attr " . $r['user'][$user][$attr];
		$output_value = json_decode ($r['user'][$user][$attr], true);
	}
}

function _update ($user, $srv = NULL, $attr = NULL, $value = NULL) {
	global $user_ldap, $output, $ldapAdmin, $ldapAdminPw;
	$user_ldap->auth($ldapAdmin, $ldapAdminPw);
	$user_ldap->getUser($user);
	$update_fields[$attr] = $value;
	$status = $user_ldap->modifyUser($user, $update_fields);
}
/*
	https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/3/en/pluginapi.html
	Nagios Return Code
	Nagios determines the status of a host or service by
	evaluating the return code from plugins. The following
	tables shows a list of valid return codes, along with their
	corresponding service or host states.

	Plugin Return Code	Service State	Host State
	0	OK	UP
	1	WARNING	UP or DOWN/UNREACHABLE*
	2	CRITICAL	DOWN/UNREACHABLE
	3	UNKNOWN	DOWN/UNREACHABLE

	Note Note: If the use_aggressive_host_checking option is
	enabled, return codes of 1 will result in a host state of
	DOWN or UNREACHABLE. Otherwise return codes of 1 will result
	in a host state of UP. The process by which Nagios
	determines whether or not a host is DOWN or UNREACHABLE is
	discussed here.

	Plugin Output Spec

	At a minimum, plugins should return at least one of text
	output. Beginning with Nagios 3, plugins can optionally
	return multiple lines of output. Plugins may also return
	optional performance data that can be processed by external
	applications. The basic format for plugin output is shown
	below:

	TEXT OUTPUT | OPTIONAL PERFDATA
	LONG TEXT LINE 1
	LONG TEXT LINE 2
	...
	LONG TEXT LINE N | PERFDATA LINE 2
	PERFDATA LINE 3
	...
	PERFDATA LINE N
*/ 
$nagios_returnCode = array (
	0	=> 'OK',
	1	=> 'WARNING',
	2	=> 'CRITICAL',
	3	=> 'UNKNOWN',
	'ok' => 0,
	'warning' => 1,
	'critical' => 2,
	'unknown' => 3, 
	'o' => 0,
	'w' => 1,
	'c' => 2,
	'u' => 3,
);

function _nagiosOutput($rc = 0, $str = NULL, $perfdata = array() )
{
	global $nagios_returnCode;
	$_rcstr = "";
	$_rc = 0;
	if ( is_string ( $rc ) ) {
		$_rc = strtolower($rc);
		$_rc = $nagios_returnCode[$_rc];
		$_rcstr = $nagios_returnCode[$_rc];		
	} else {
		echo " _rc (else) is $rc \n";
		$_rcstr = $nagios_returnCode[$rc];	
	}
	$_rcstr = $_rcstr . " - " . $str;
	if ( count ($perfdata) > 0 ) {
		$a = array ();
		foreach ( $perfdata as $k => $v ) {
			$a[] = "$k=$v";
		}
		$_rcstr	.= " | " . implode ( ", ", $a );
	}
	return $_rcstr;
}

$arg = new CommandLine();
$opt = $arg->parseArgs($argv);

if ( 
	! isset ( $opt['attr'] )
)
{
	$opt['attr'] = NULL;	
}

if ( 
	! isset ( $opt['user'] )
) 
{
	_usage();
	exit;
}

if (isset ($opt['json'])) 
{
	$json = true;
} else {
	$json = false;
}

/*
    --nagios           ; reutrn as nagios check
    --warning=seconds  ; number of seconds to trigger a warning (default=300)
    --critical=seconds ; number of seconds to trigger critical  (default=600)
*/

if ( isset ($opt['nagios']) ) {
	$nagios = True;
} else {
	$nagios = False;
}
if ( isset ($opt['warning']) ) {
	$warning = $opt['warning'];
} else {
	$warning = 300;
}

if ( isset ($opt['critical']) ) {
	$critical = $opt['critical'];
} else {
	$critical = 600;
}




/*
	--server=LDAPSERVER ; connect to different ldap server than in config
	--binddn=...	; bind as this instead.
	--password		; prompts for bind password
*/
if (isset ($opt['server']))  {
	$user_ldap->close();
	if ( isset ($opt['notls']) ) {
		$tls = false;
	} else {
		$tls = true;
	}
	echo "Connecting to " . $opt['server'] . "\n";
	$user_ldap = new SimpleLDAP($opt['server'], 389, 3, $tls);
	
}
if (isset ($opt['binddn']))  {
	$ldapAdmin = $opt['binddn'];
}
#echo "Bind as DN = " . $ldapAdmin . "\n";
if (isset ($opt['password']))  {
	$ldapAdminPw = _prompt("Password: ", true);
}

if (isset ($opt['admin']) ||  ( isset ($opt['binddn'])  && isset ($opt['password']) )  )  {
	echo " Binding ... \n";
	$user_ldap->auth($ldapAdmin, $ldapAdminPw);
}


if ( isset ($opt['checkall']) ) {
	foreach ( $ldapservers as $srv ) {
		$user_ldap = new SimpleLDAP($srv, 389, 3);
		$user_ldap->dn  = $ldap_dn;
		$user_ldap->gdn = $ldap_gdn;
		$user_ldap->sdn = $ldap_sdn;
		_do ( $opt['user'], $srv, $opt['attr'], $json );
	}
} else {

	$srv = $user_ldap->getHostname();
	if ( isset($opt['update']) ) { 
		_update ( $opt['user'], $srv, $opt['attr'], $opt['value'] );		
	} else {
		_do ( $opt['user'], $srv, $opt['attr'], $json );
	}

}


	$output['table']['title'] = "LDAP Replication Monitoring";
	$output['table']['header'] = array ( "server", "user", "attr", "value" );

	$output1['complete'] = 1;
	$output1['code'] = 0;
	
// print_r  ($output);
// print_r  ($output_value);



if ( $nagios ) 
{
	$str = '';
	$_timekey = 'unixtime';  # this could be an option later
	if ( is_array($output_value) ) 
	{
		if ( isset ( $output_value[$_timekey] ) )
		{
			$d = time() - $output_value[$_timekey];
			if ( $d >= $critical ){ 
				$str = _nagiosOutput("c", "timekey difference is $d seconds." , array ('time' => $d) );
				$retCode = 2;
			} elseif ( $d >= $warning ){ 
				$str = _nagiosOutput("w", "timekey difference is $d seconds." , array ('time' => $d) );
				$retCode = 1;
			} else {
				$str = _nagiosOutput("ok", "timekey difference is $d seconds." , array ('time' => $d) );
				$retCode = 0;				
			}
		} else {
			$str = _nagiosOutput("u", "key " . $_timekey . " was not found in the json in the attr " . $opt['attr'] );
			$retCode = 3;
		}
	} else {
		$str = _nagiosOutput("u", "not json values where in the attr" . $opt['attr'] );
		$retCode = 3;
	}
	echo $str . "\n";
}
	
if ( $json ) {
	# cronicle format
	echo json_encode($output);
	echo "\n";	
	echo json_encode($output1);
	echo "\n";
}

exit($retCode);
?>