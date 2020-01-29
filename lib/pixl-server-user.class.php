<?php


class pixlServerUser {

	public $CACERT = NULL;
	private $host = NULL;
	private $session_id = NULL;
	
	public $newUser = NULL;
	public $data = NULL;
	public function easyCurl ($url, $data, $verb = "GET") 
	{

		$verb = strtoupper($verb);
		$qstr = "";
		$s = curl_init(); 
		if ( is_array ( $data )  ) {
			$qstr = "?" . http_build_query($data);
		} elseif ( ! is_null ( $data )  ) 
		{
			$qstr = "?" . $data;
		}
		if ( $verb == "GET" ) {
			$url = $url . $qstr;
		} elseif ( $verb == "POST" ) {
			curl_setopt($s,CURLOPT_POST,true); 
			curl_setopt($s,CURLOPT_POSTFIELDS,$data); 
		} elseif ( $verb == "PUT" ) {
			curl_setopt($s,CURLOPT_PUT,true); 
		} else {
			curl_setopt($s, CURLOPT_CUSTOMREQUEST, $verb);
		}
		curl_setopt($s,CURLOPT_URL,$url); 
		curl_setopt($s,CURLOPT_CAINFO,$this->CACERT);
		curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: text/plain'));
		curl_setopt($s,CURLOPT_RETURNTRANSFER, 1); 	
		curl_setopt($s, CURLINFO_HEADER_OUT, true);
		
		$out = curl_exec($s); 
		$hr = curl_getinfo($s); 
		$this->data = array ();
		$this->data['headers'] = $hr;
		if ( preg_match ( '/json/', $hr['content_type'] ) ) {
			$this->data['body'] = json_decode ($out, TRUE);		
		} else {
			$this->data['body'] = $out;
		}
		curl_close($s); 

		return $this->data;
	
	}

	public function __construct($host) {
		$this->host = $host;
		$this->newUser = array (
			"username" => NULL,
			"active" => "1",
			"full_name" => NULL,
			"email" => NULL,
			"password" => NULL,
			"privileges" => array (
				"admin" => 0,
				"create_events" => 1,
				"edit_events" => 1,
				"delete_events" => 1,
				"run_events" => 1,
				"abort_events" => 1,
				"state_update" => 0,
				"cat_limit" => 0
			),
			"send_email" => 0,
			"session_id" => NULL,
		);
		}

	public function adminLogin($user,$pw) 
	{
		$d = array ('username' => $user, 'password' => $pw);
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/login", json_encode( $d ), "POST" );
		$this->session_id = $this->data['body']['session_id'];
	}
	public function checkUser($user) {
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/app/check_user_exists", array('username' =>  $user, 'cachebust' => microtime(TRUE) ), "GET" );
		if ( $this->data{'body'}{'user_exists'} == 1 ) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getUser($user) 
	{
		$this->newUser['session_id'] = $this->session_id;
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/admin_get_user", json_encode( array ( 'session_id' =>$this->session_id, 'username' => $user ) ) , "POST" );
		if ( $this->data['body']['code'] == 0 ) {
			$this->newUser = array_replace ($this->newUser, $this->data['body']['user']) ;
		}
		return $this->data;
	}
	
	public function createUser($admin = false) 
	{
		$this->newUser['session_id'] = $this->session_id;

		if ( $admin == false ) {
			$this->newUser['privileges']['admin'] = 0;
		} else {
			$this->newUser['privileges']['admin'] = 1;		
		}
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/admin_create", json_encode($this->newUser) , "POST" );
		return $this->data;
	}
	public function deleteUser($user) {
		$deleteUser = array ();
		$deleteUser['session_id'] = $this->session_id;
		$deleteUser['username']		= $user;
		$deleteUser['session_id']	= $this->session_id;		
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/admin_delete", json_encode($deleteUser) , "POST" );		
	}
	public function updateUser() {
		$this->newUser['session_id'] = $this->session_id;
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/admin_update", json_encode($this->newUser) , "POST" );
		return $this->data;
	}
	public function updateUserPw($pw) {
		$this->newUser['session_id'] = $this->session_id;
		$this->newUser{'new_password'} = $pw;
		unset ($this->newUser['password']);
		unset ($this->newUser{'privileges'});
		$this->data = $this->easyCurl ( "https://" . $this->host . "/api/user/admin_update", json_encode($this->newUser) , "POST" );
		return $this->data;
	}

}

?>