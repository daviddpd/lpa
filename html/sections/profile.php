<style> 
	.breset {
	
		width: 50%;
	}
	
	.breset tr {
		vertical-align: middle;
	}
	.breset td.meta-inline-key { 
		width: 25%;
		height: 40px;
	}      			
	.breset td.meta-inline-value { 
		height: 40px;
	}      			

	.breset tr:hover { 
		background-color: #000;
	}

	.breset th { 
		width: 25%;
	}      			
	.metaNL {
		border-top: 1px solid #aaa; 
	
	}
	td.meta-inline-name { 
		height: 45px;
		vertical-align: middle;
		
	}  
	td.meta-inline-key { 
		border-top: 1px solid #dddddd; 
	}
	
	#userLDAPattrabute div.meta-main {
		width: 100%;
	}
	#userLDAPattrabute div.meta-main-row:hover {
		background-color: #eee;
	}

	#userLDAPattrabute div.meta-main-row {
		padding: 5px;
	}
	#userLDAPattrabute div span.meta-span-label {
		width: 20%;
		display: block;
		border: solid 0px black;
		text-align: right;
		float: left;
		height: 15px;
	}

	#userLDAPattrabute div span.meta-span {
		width: 70%;
		padding-left: 5%;
		display: block;
		
		text-align: left;
		float: left;
	}
	#userLDAPattrabute div span.meta-span-delete {
		width: 2%;
		clear: right;
	}
	#userLDAPattrabute div span.meta-span-last {
	}
	div.editable-input  input.metaInputValue {
		width: 350px;	
	}
	div.popover-content > div > form > div > div:nth-child(1) > div.editable-input > input {
		width: 350px;
	}
	.red {
		color: red;
	}
	.eighty {
		font-size: 0.8em;
	}
	.mono {
		font-family: monospace;
		font-size: 0.7em;
	}
	.bold {
		font-weight: 700;
	}
	.left {
		text-align: left;
	}
	.bottom_underline {
		border-bottom: 4px;
		border-bottom-color: #888;
		border-bottom-style: solid;
		border-bottom-width: 1px;
		margin-bottom: 2px;
	}
	.gray {
		color: #666;
	}
	.span60 {
		width: 60%;
	}
	.span40 {
		width: 40%;
	}
	.span100 {
		width: 100%;
	}
	.sshpaste {
		font-family: monospace;
		font-size: 0.8em;
		height: 160px;
		width: 500px;
	}
	#copyright {
		border-bottom-style: none;
	}
</style>
<?php 
global $user_groupLut;

function format_ldapValue($k,$v)
{
	if ( isset ($v)  ) {
		if ( $k == "sshpublickey" )
		{
//			$p = explode (" " , $v);
	//		strlen ($p[1]);
//			$s = substr($p[1], 0, 8 );
//			$e = substr($p[1], -8, 8 );
//			$p[1] = "$s ... $e";
			$sig = `echo  $v | /bin/ssh-keygen -lf - `;
			$p = explode (" " , $sig);
		} else {
			$p = array();
			$l = strlen ($v);
			if ($l > 50) {
				$p[0] = substr($v, 0, 45) . "...";
			} else {
				$p[0] = $v;
			}
		
			$p[1] = "";
			$p[2] = "";
	
		}
		return $p;
	} else {
		return array();
	}
}


function editableRow($fo, $k, $v, $v2) {
	global $user_groupLut;
	$rowClasses="";
	$editClasses="";
	$editClassesDelete="";
	$faHidden="fa-hidden";
	$mono = "";
	$user_ldap_full_data = "ldapdata=\"$v\"";
//	$editClassesDelete="";
	$eg = $fo{'by'}; // Getting group required to to edit this field.
	$qs	 = ""; // Query String Additions
	
	if ( $fo{'editable'}  && isset ($user_groupLut{$eg}) ) {
		$editClasses="meta-inline meta-inline-value editable editable-click";
	}
	if ( $fo{'ldapou'} == "user" ) {
		 $qs = "&ou=user";
	}
	if ( $fo{'ldapou'} == "SUDOers" ) {
		 $qs = "&ou=SUDOers";
	}

	if ( $fo{'ldapou'} == "groups" ) {
		 $qs = "&ou=groups";
		 if ( $v == "YES" ) {
			 $rowClasses="bold";
		}
		 if ( $v == "NO" ) {
			 $rowClasses="gray";
		 }
	}

/*
	# Deleteing not yet implemented, so remove the button.

	if ( $fo{'editable'} && $fo{'deletable'} && isset ($user_groupLut{$eg}) ) {
		$editClassesDelete="delete_dpdMeta";
		$faHidden="";
	}
*/
	if ( $k == "sshpublickey" )  {
		$mono = "mono";	
	}
	$type = $fo{'type'};
	if ($type == "select") {
		 $data_source = 'data-source="' . $fo{'src'} . '"'; 
	}elseif ($type == "password") {
		$type = "text";
//		 $data_source = 'data-mode="popup"';
	} else {
		 $data_source = '';
	}
	
print "
	<span class=\"meta-span $rowClasses\">
	 <span class=\"$editClasses $mono\" 
		id=\"" . $k . "\" data-inputclass=\"metaInputValue\"
		data-type=\"$type\" data-pk=\"" . $k . "\" 
		data-url=\"api.php?field=" . $k . "&action=update" . $qs .  "\"
		data-title=\"enter value\" 
		$data_source >" . $v2 . " 
	</span>
	</span>
	<span class=\"meta-span-delete meta-span-last $faHidden\">
		<a class=\"$editClassesDelete btn btn-default btn-xs $faHidden\" 
			ldap-field=\"" . $k .  "\" 
			$user_ldap_full_data
			data-container=\"body\" title=\"\" data-original-title=\"Delete Meta Data\">
			<i class=\"fa fa-gray fa-times $faHidden\"></i>
		</a>
	</span>
\n";
								
}
									
function row($k,$v,$fo){
global $user_groupLut;
?>
<div class="meta-main-row">
	<span class="meta-span-label">
<?php
	 if  ( !is_null ($fo{'relabel'}) )
	 {
		echo $fo{'relabel'} . "\n";
	 } else {
		echo $k . "\n";
	 }
 ?>
	 </span>
<?php
	$eg = $fo{'by'}; // Getting group required to to edit this field.
	$s = format_ldapValue($k,$v);
	$v2 = join (" ", $s);

	editableRow($fo, $k, $v, $v2);

?>
</div>

<?php
}

function row_multi($k,$v,$fo){
global $user_groupLut;
	foreach ($v as $n => $i) 
	{
 ?>
	<div class="meta-main-row">
		<span class="meta-span-label">
<?php	 

			if ( $n == 0 ) {
				 if  ( !is_null ($fo{'relabel'}) )
				 {
					echo $fo{'relabel'} . "\n";
				 } else {
					echo $k . "\n";
				 }
			} else {
				echo "&nbsp;";
			}
		
 ?>
		 </span>
<?php 						
#			$x = print_r ( $v, 1 );
#			echo "<!-- $x -->";							

				$s = format_ldapValue($k,$i);
				$v2 = join (" ", $s);	
				editableRow($fo, $k, $i, $v2);
?>
	</div>
<?php
}
}
?>

<div class="title">
		<h2 class="alt">Profile</h2>
		<p>Profile user settings</p>
</div>
<div>
<div>
	<div>
		<button id="changePassword" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">Change Password</button>
		<button id="sshKeyUploadButton" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-trigger="manual" data-target="#myModalSSH">Upload SSH Public Key</button>
	</div>
	<div>
		&nbsp;
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Changing/Syncing Password(s)</h4>
        <h5 class="modal-title" id="pwdChgStatus">&nbsp;</h5>
      </div>
      <div class="modal-body">
      <table>
      <tr>
		<td>Old Password:</td>
		<td><input type="password" id="oldpassword1" size="20" /></td>
	</tr>
	<tr>
		<td>New Password:</td>
		<td><input type="password" id="password1" size="20" /></td>
	</tr>
		<td>Confirm New Password:</td>
		<td><input type="password" id="password2" size="20" /></td>
	</tr>
	</table>
      </div>
      <div class="modal-footer">
        <button id="closePassword" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="savePassword" type="submit" class="btn btn-primary" >Save changes</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal - SSH KEYS -->

<div class="modal fade" id="myModalSSH" tabindex="-1" role="dialog" aria-labelledby="myModalLabelSSH">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabelSSH">Upload/Change SSH Authorized keys</h4>
        <h5 class="modal-title" id="sshUploadStatus">&nbsp;</h5>
        <p class="eighty" >Copy/Paste any number of SSH Public Keys, delimitated by Carriage Return <br>( same as the authorized_keys file format ).<br>This action will replace all currently installed keys.</p>
      </div>
      <div class="modal-body">
		<textarea class="sshpaste" id="sshKeyUpload" ></textarea>

      </div>
      <div class="modal-footer">
        <button id="closeSSH" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button id="saveSSH" type="submit" class="btn btn-primary" >Save</button>
      </div>
    </div>
  </div>
</div>

<div class="meta-main-row">&nbsp;</div>
<div class="meta-main-row bold left bottom_underline">User Information</div>
<div id="userLDAPattrabute" >
<?php

	include "../../config/config.php";
	include "../../config/ldapFields.php";
	$u = $_SERVER['REMOTE_USER'];

	$user_ldap->getUsers("(uid=" . $u . ")");
	$user_groups = $user_ldap->getUsersGroup($u);
//	$user_groupLut = array ();
	foreach ( $user_groups as $g ) {
		$user_groupLut{$g} = 1;
	}

	if ( isset ($user_groupLut{_LDAP_ADMIN_ACCOUNT}) ) 
	{
		if ( isset ($_GET['user'] ) ) {
			$u = $_GET['user'];
		}
	}

	$target_ldap->getUsers("(uid=" . $u . ")");
	$target_groups = $target_ldap->getUsersGroup($u);
//	$target_groupLut = array ();
	foreach ( $target_groups as $g ) {
		$target_groupLut{$g} = 1;
	}

	foreach (array_keys ($ldapFields{'edittable'}{'user'}) as $k ) {
//	foreach ($target_ldap->obj as $k => $v) {
		$k = strtolower($k);
		if ( !isset ( $target_ldap->selfObj{$u}{$k} ) ) {
			$v = "";
		} else {
			$v = $target_ldap->selfObj{$u}{$k};
		}
		$fo = $ldapFields{'edittable'}{'user'}{$k}; // fieldObject
		if (!$fo{'display'}) { continue; }
		if ( is_array ($v)  ) {
			row_multi($k,$v,$fo);
		} else {
			row($k,$v,$fo);
		}
	}

?>
<div class="meta-main-row">&nbsp;</div>
<div class="meta-main-row bold left bottom_underline">Group Membership</div>
<?php
	foreach (array_keys ($ldapFields{'edittable'}{'groups'}) as $k ) {
		$k = strtolower($k);
//		if (!isset ($ldapFields{'edittable'}{'groups'}{$k})) { continue; }
		if ( isset ( $ldapFields{'edittable'}{'groups'}{$k} ) ) 
		{
			$fo = $ldapFields{'edittable'}{'groups'}{$k};	
		} else {
		 continue;
		}
		if (!$fo{'display'}) { continue; }
		
		if ( isset ($target_groupLut{$k}) ) {
			row($k,'YES',$fo);
		} else {
			row($k,'NO',$fo);
		
		}
	}
?>
<div class="meta-main-row">&nbsp;</div>
<div class="meta-main-row bold left bottom_underline">Link Authentications</div>

</div>
</div>
<script>
	var _targetUser =  $('#uid').html().trim();
	userLDADInit();
</script>
</div>

