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
	
	.mono {
		font-family: monospace;
		font-size: 0.8em;
	}
	
	
</style>
<?php 
global $groupLut;

function format_ldapValue($k,$v)
{
	if ( $k == "sshpublickey" ) 
	{
		$p = explode (" " , $v);
//		strlen ($p[1]);
		$s = substr($p[1], 0, 8 );
		$e = substr($p[1], -8, 8 );
		$p[1] = "$s ... $e";
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
}


function editableRow($fo, $k, $v, $v2) {
	global $groupLut;
	$editClasses="";
	$editClassesDelete="";
	$faHidden="fa-hidden";
	$mono = "";
	$ldap_full_data = "ldapdata=\"$v\"";
//	$editClassesDelete="";
	$eg = $fo{'by'}; // Getting group required to to edit this field.
	
	if ( $fo{'editable'}  && isset ($groupLut{$eg}) ) {
		$editClasses="meta-inline meta-inline-value editable editable-click";
	}
	if ( $fo{'editable'} && $fo{'deletable'} && isset ($groupLut{$eg}) ) {
		$editClassesDelete="delete_dpdMeta";
		$faHidden="";
	}
	if ( $k == "sshpublickey" )  {
		$mono = "mono";	
	}
	$type = $fo{'type'};
	if ($type == "select") {
		 $data_source = 'data-source="' . $fo{'src'} . '"'; 
	} else {
		 $data_source = '';
	}
	
print "
	<span class=\"meta-span\">
	 <span class=\"$editClasses $mono\" 
		id=\"value\" data-inputclass=\"metaInputValue\" 
		data-type=\"$type\" data-pk=\"" . $k . "\" 
		data-url=\"api.php?field=" . $k . "&action=update" .  "\"
		data-title=\"enter value\" 
		$data_source >" . $v2 . " 
	</span>
	</span>
	<span class=\"meta-span-delete meta-span-last\">
		<a class=\"$editClassesDelete btn btn-default btn-xs $faHidden\" 
			ldap-field=\"" . $k .  "\" 
			$ldap_full_data 
			data-container=\"body\" title=\"\" data-original-title=\"Delete Meta Data\">
			<i class=\"fa fa-gray fa-times $faHidden\"></i>
		</a>
	</span>
\n";
								
}
									
function row($k,$v,$fo){
global $groupLut;
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
global $groupLut;
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
		<div id="userLDAPattrabute" >
<?php
	include "../../config/config.php";
	include "../../config/ldapFields.php";
	$u = $_SERVER['REMOTE_USER'];
	$ldap->getUsers("(uid=" . $u . ")");
	$a = $ldap->getAttrs($u);
	$groups = $ldap->getUsersGroup($u);	
	$groupLut = array ();
	foreach ( $groups as $g ) {
		$groupLut{$g} = 1;
	}

	foreach ($ldap->obj as $k => $v) {
		$k = strtolower($k);
		$fo = $ldapFields{'edittable'}{'user'}{$k}; // fieldObject
		if (!$fo{'display'}) { continue; }
		if ( is_array ($v)  ) {
			row_multi($k,$v,$fo);
		} else {
			row($k,$v,$fo);
		}
	}
?>
		</div>
		</div>
<script>
	userLDADInit();
</script>
</div>

