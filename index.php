<?php

	include "config/config.php";
	include "config/ldapFields.php";
	$u = $_SERVER['REMOTE_USER'];
	$user_ldap->getUser($u);
	$user_groups = $user_ldap->getUsersGroup($u);
	$user_groupLut = array ();
	foreach ( $user_groups as $g ) {
		$user_groupLut{$g} = 1;
	}

	error_log ( " Index: LDAP : "  . json_encode ( $user_ldap->selfObj ) ) ;

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.js"></script>

<script src="html/js/lpa.js"></script>
<link href="html/css/default.css" rel="stylesheet" type="text/css" media="all" />
<link href="html/css/fonts.css" rel="stylesheet" type="text/css" media="all" />

<!--[if IE 6]><link href="default_ie6.css" rel="stylesheet" type="text/css" /><![endif]-->

</head>
<body>
<div id="page" class="container">
	<div id="header">
		<div id="logo">
<!--			<img src="" alt="" /> -->
			<h1><a href="#"><?= $user_ldap->selfObj{$u}{'gecos'} ?> </a></h1>
			<span><?= $user_ldap->selfObj{$u}{'title'} ?></span>
		</div>
		<div id="menu">
			<ul>
				<!-- class="current_page_item" to hight light "li" -->
				<li ><a id="top-link" href="#" accesskey="1" title="">Main</a></li>
				<li><a id="profile-link" href="#" accesskey="2" title="">Profile</a></li>
<?php if ( isset ( $user_groupLut{_LDAP_ADMIN_ACCOUNT} ) )  { ?>
				<li>

					<a  href="#" accesskey="3" title="">
						<input type="text" style="display:inline;" id="target-ldap-user" value="" size="5"></input>
						<span id="edit-user-link" >Edit</span>
					</a>
				</li>
<?php } ?>
			</ul>
		</div>
	</div>
	<div id="main">
		<div id="banner">
			<img src="<?= $brand{'logo'}{'src'} ?>" width="<?= $brand{'logo'}{'width'} ?>">
		</div>

		<div id="top" >
		</div>
<!--
		<div id="featured">
			<div class="featured">
				<h2>Maecenas lectus sapien</h2>
				<span class="byline">Integer sit amet aliquet pretium</span>
			</div>
			<ul class="style1">
				<li class="first">
					<p class="date"><a href="#">Jan<b>05</b></a></p>
					<h3>Amet sed volutpat mauris</h3>
					<p><a href="#">Consectetuer adipiscing elit. Nam pede erat, porta eu, lobortis eget, tempus et, tellus. Etiam neque. Vivamus consequat lorem at nisl. Nullam non wisi a sem semper eleifend. Etiam non felis. Donec ut ante.</a></p>
				</li>
				<li>
					<p class="date"><a href="#">Jan<b>03</b></a></p>
					<h3>Sagittis diam dolor amet</h3>
					<p><a href="#">Etiam non felis. Donec ut ante. In id eros. Suspendisse lacus turpis, cursus egestas at sem. Mauris quam enim, molestie. Donec leo, vivamus fermentum nibh in augue praesent congue rutrum.</a></p>
				</li>
				<li>
					<p class="date"><a href="#">Jan<b>01</b></a></p>
					<h3>Amet sed volutpat mauris</h3>
					<p><a href="#">Consectetuer adipiscing elit. Nam pede erat, porta eu, lobortis eget, tempus et, tellus. Etiam neque. Vivamus consequat lorem at nisl. Nullam non wisi a sem semper eleifend. Etiam non felis. Donec ut ante.</a></p>
				</li>
				<li>
					<p class="date"><a href="#">Dec<b>31</b></a></p>
					<h3>Sagittis diam dolor amet</h3>
					<p><a href="#">Etiam non felis. Donec ut ante. In id eros. Suspendisse lacus turpis, cursus egestas at sem. Mauris quam enim, molestie. Donec leo, vivamus fermentum nibh in augue praesent congue rutrum.</a></p>
				</li>
			</ul>
		</div>
	-->
		<div id="copyright">
			<span>&copy; <?= $brand{'copyright'} ?></span>
			<span>Design by <a href="http://templated.co" rel="nofollow">TEMPLATED</a>.
				<a href="https://creativecommons.org/licenses/by/3.0/" rel="nofollow"> CC BY 3.0</a>
				</span>
		</div>
	</div>
</div>
</body>
</html>
