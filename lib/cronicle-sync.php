<?php

require_once  "lib/pixl-server-user.class.php";

class cronicleSync {

	public $ldapentry = NULL;
	public $groups = NULL;
	public $server = NULL;
	private $password = NULL;
	private $u;
	private $p;
	
	public function __construct($u, $p, $server) 
	{
		$this->server = $server;
		$this->u = $u;
		$this->p = $p;
	}
	public function sync($ldapentry, $groups, $password) 
	{

		global $CACERT;
		
		// _CRON_GROUP = "cron";
		// _CRON_ADMIN_GROUP = "cronadmins";
		
		if ( ! isset ( $groups[_CRON_GROUP] )  )
		{			
			return false;		
		}
		$c = new pixlServerUser($this->server);		
		$c->CACERT = $CACERT;
		$r = $c->adminLogin($this->u, $this->p);
		
		$isuser = $c->checkUser($ldapentry['uid']);
		
		if ( $isuser ) {
			$c->getUser($ldapentry['uid']);
			$c->updateUserPw($password);
		}

		$c->newUser['username'] = $ldapentry['uid'];
		$c->newUser['password'] = $password;

		if ( isset ($ldapentry['gecos'])  ) {
			$c->newUser['full_name'] = $ldapentry['gecos'];
		}
		if (isset ($ldapentry['mail'])) {
			$c->newUser['email'] = $ldapentry['mail'];
		} else {
			$c->newUser['email'] = "noreply@care2team.com";
		}
		if ( ! $isuser ) {
			$r = $c->createUser( isset($groups[_CRON_ADMIN_GROUP]) );
		}		
		
		if ( $isuser ) {
			$c->updateUser();		
		}
		return true;
	}
	
}



?>