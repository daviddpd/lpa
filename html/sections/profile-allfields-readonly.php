<?php

	function format_ssh_key($key)
	{
		$p = explode (" " , $key);
		
		count_chars ($p[1]);
		$s = substr($p[1], 0, 8 );
		$e = substr($p[1], -8, 8 );
		$p[1] = "$s ... $e";
	
		return $p;
	}

?>
<div class="title">
		<h2 class="alt">Profile</h2>
		<p>Profile user settings</p>
</div>
<div>
		<table>
<?php
	include "../../config/config.php";
	$u = $_SERVER['REMOTE_USER'];
	$ldap->getUsers("(uid=" . $u . ")");
	$a = $ldap->getAttrs($u);
	$groups = $ldap->getUsersGroup($u);		

	foreach ($ldap->obj as $k => $v) {
?>
			<tr>
				<td><?= $k ?></td>
				<td>
					<?php 
						if ( is_array ($v)  ) {
							$x = print_r ( $v, 1 );
							echo "<!-- $x -->";
							echo "<select>";
							
							foreach ($v as $i) 
							{
								if ( $k == "sshpublickey" ) 
								{
									$sk = format_ssh_key($i);
									$s = $sk[0] . " " . $sk[1] . " " . $sk[2];
									echo "<option>$s</option>";							
								} else {
									echo "<option>$i</option>";
								}
							}
							echo "</select>";
						} else {
							echo $v;
						}
				
					?>
				</td>
			</tr>

<?php
}
?>
		</table>
		</div>
</div>
