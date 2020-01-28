<?php

	
	include "config/config.php";
	include "config/ldapFields.php";

/*
	Verify that the signed in User can modify the field being edited.
*/
function verifyPermissions($ou, $field, $user, $targetUser) {

	global $target_ldap;
	global $ldapFields;
	global $editMode;
	$groups = $target_ldap->getUsersGroup($user, true);
	$whoCan = $ldapFields{'edittable'}{$ou}{$field}{'by'};
	error_log ( "verifyPermissions: whoCan: " . $whoCan . " ; adminAccount: " . _LDAP_ADMIN_ACCOUNT . " groups: " . json_encode ($groups) );

	if ( $whoCan == "_self" && ( $user == $targetUser ) )
	{
		$editMode =  'user';
		return true;
	} elseif (
		isset ( $groups{_LDAP_ADMIN_ACCOUNT} )
	) {
		$editMode = 'admin';
		return true;
	}
	return false;
}

/*
	make a return/exit function, as this was getting
	redunant.
*/
	
function ret( $a = array(), $raw = false )
{
	global $editMode;
	$r = array ();
	if ( $raw == FALSE ) {
		$r['method'] = $_SERVER['REQUEST_METHOD'];
		$r['POST'] = $_POST;
		$r['GET'] = $_GET;
		$r['EDITMODE'] = $editMode;
		$r = array_merge($r, $a);
	} else {
		$r = $a;
	}
	header('Content-type: application/json');
	echo json_encode ( $r );
	exit;
}


if ($_SERVER['REQUEST_METHOD'] == "GET"  ) 
{

	$r['method'] = $_SERVER['REQUEST_METHOD'];

	if ( $_GET['action'] = "query" ) {

		if ( $_GET['value'] == "shells" ) { ret ( $shells, true ); }
		if ( $_GET['value'] == "booleans" ) { ret ( $booleans, true ); }

	} else {
		ret ( array ('errno' => -1, 'result' => 'Not Found.') );
	}

	ret ( array ('errno' => -1, 'result' => 'Not implemented') );

} elseif ($_SERVER['REQUEST_METHOD'] == "POST"  ) 
{

	$r['method'] = $_SERVER['REQUEST_METHOD'];
	
	if ( isset ($_POST['action']) && $_POST['action'] == 'passwordChange' )
	{

		if ( $_POST['password1'] != $_POST['password2'] ) {
			ret ( array ('errno' => -1, 'result' => 'New Passwords did not match.') );
		} else {

			if ( ! verifyPermissions($_GET['ou'], $_GET['field'], $_SERVER['REMOTE_USER'], $_POST['user'] ) )
			{
				ret ( array ('errno' => 1, 'result' => 'User Not Allowed to Modify this value.') );
			}
			$pwcheck = $target_ldap->check_ldap_passwd( $_POST['user'], $_POST['oldpassword1'] );
			error_log ("pw check: " . json_encode ($pwcheck) );
			$pwchg = $target_ldap->change_ldap_passwd( $_POST['user'], $_POST['oldpassword1'], $_POST['password1'] );
			error_log ("pw chg: " . json_encode($pwchg) );

			if ($pwchg == NULL ) {
				ret ( array ('errno' => 1, 'result' => 'Changing Password Faild.') );
			} else {
				ret ( array ('errno' => 0, 'result' => 'Password Changed') );
			}
		}

	} elseif ( isset ($_POST['action']) && $_POST['action'] == 'sshKeyUpload' ) {


		if ( ! verifyPermissions($_GET['ou'], $_GET['field'], $_SERVER['REMOTE_USER'], $_POST['user'] ) )
		{
			ret ( array ('errno' => -1, 'result' => 'User .(' . $_POST['user'] . ') Not Allowed to Modify ' . $_GET['field'] . ' value.') );
		}
		$target_ldap->auth($ldapAdmin, $ldapAdminPw);
		$target_ldap->getUser($_POST['user']);

		if ( isset ($target_ldap->obj['sshpublickey'])  )
		{
			if ( !is_array ( $target_ldap->obj['sshpublickey'] ) ) {
				$keys = array ( $target_ldap->obj['sshpublickey'] );
			} else {
				$keys = $target_ldap->obj['sshpublickey'];
			}

			foreach ($keys as $key ) {
				$target_ldap->modDelUserAttr($_POST['user'], array ( 'sshpublickey' => $key ) );
			}
		}

		$keys = array ();
		foreach ( explode ( "\n", $_POST['sshkey'] ) as $key ) {
			$key = trim($key);
			array_push ($keys, $key);
		}
		$target_ldap->modifyUser(
			$_POST['user'],
			array ( 'sshPublicKey' => $keys )
		);
		ret ( array ('errno' => 0, 'result' => 'SSH Keys Updated.') );


	} elseif (
			(isset($_POST['action']) && $_POST['action'] == 'update')
			|| (isset($_GET['action']) && $_GET['action'] == 'update')
		) {

		$user = NULL;
		if ( isset ($_POST['name'])  ) {
			$user = $_POST['name'];
		} elseif ( isset ($_POST['user'])  ) {
			$user = $_POST['user'];
		} else {
			ret ( array ('errno' => -1, 'result' => 'Action "Update": User/Name was not sent in the request.') );
		}
		if ( $user != NULL ) {

			if ( ! verifyPermissions($_GET['ou'], $_GET['field'], $_SERVER['REMOTE_USER'], $_POST['name'] ) )
			{
				ret ( array ('errno' => 1, 'result' => 'User Not Allowed to Modify this value.') );
			}

			$target_ldap->auth($ldapAdmin, $ldapAdminPw);
			$target_ldap->getUser($user);


			if (  isset ( $_GET['ou'] ) && $_GET['ou'] == "groups" )
			{
				if ( $_POST['value'] == "YES" ) {
					$status = $target_ldap->addGroupMember($_POST['pk'], $user );
				} elseif ( $_POST['value'] == "NO" ) {
					$status = $target_ldap->delGroupMember($_POST['pk'], $user );
				} else {
					$status = false;
				}

			} else {
				$status = $target_ldap->modifyUser(
					$user,
					array ( $_POST['pk'] => $_POST['value'] )
				);
			}
			if ( $status == TRUE )
			{
				ret ( array ('errno' => 0, 'result' => 'Action "Update": Succesfull') );
			} else {
				ret ( array ('errno' => -1, 'result' => 'Action "Update": Error') );
			}
		}

	} else {
		ret ( array ('errno' => -1, 'result' => 'Not implemented') );
	}

	ret ( array ('errno' => -2, 'result' => 'Undefined') );

} else {
	ret ( array ('errno' => -2, 'result' => 'Not implemented') );
}

?>