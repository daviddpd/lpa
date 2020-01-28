<?php
/**
 * SimpleLDAP
 *
 * An abstraction layer for LDAP server communication using PHP
 *
 * @author Klaus Silveira <contact@klaussilveira.com>
 * @package simpleldap
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version 0.1
 * @link http://github.com/klaussilveira/SimpleLDAP
 

$lil = base64_encode($entry[$i]["jpegphoto"][0]);
echo '<img src="data:image/jpeg;base64,'.$lil.'"/>';
 
 */
class SimpleLDAP {
	
	/**
	 * Holds the LDAP server connection
	 *
	 * @var resource
	 * @access private
	 */
	private $ldap = NULL;

	private $bind = NULL;

	private $hostname = NULL;
	private $port = NULL;
	private $protocol = null;
	private $tls = TRUE;

	private $user = NULL;
	private $passwod  = NULL;

	/**
	  * Holds the default Distinguished Name.
		dn  => ou=people,dc=example,dc=com
		gdn => ou=groups,dc=example,dc=com
		sdn => ou=SUDOers,dc=example,dc=com
	 *
	 * @var string
	 * @access public
	 */
	public $dn;
	public $gdn;
	public $sdn;


	/**
	 * holds ldap return data.
	 *
	 * @var array/hash/obj
	 * @access public
	 */
	public $data = null;
	public $gdata = null;
	public $sdata = null;

	public $selfObj = null;
	public $obj = null;
	public $gobj = null;
	public $sobj = null;


	/**
	 * LDAP server connection
	 *
	 * In the constructor we initiate a connection with the specified LDAP server 
	 * and optionally allow the setup of LDAP protocol version
	 *
	 * @access public
	 * @param string $hostname Hostname of your LDAP server
	 * @param int $port Port of your LDAP server
	 * @param int $protocol (optional) Protocol version of your LDAP server
	 */
	public function __construct($hostname, $port, $protocol = null, $tls = true) {
		$this->hostname = $hostname;
		$this->port = $port;
		$this->protocol = $protocol;
		$this->tls = $tls;

		$this->ldap = ldap_connect($hostname, $port);
		if ( $tls ) {
			ldap_start_tls($this->ldap);
		}

		if($protocol != null) {
			ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, $protocol);
		}
		$this->bind = ldap_bind($this->ldap);

	}
	

	/**
	 * Authenticate an user and return it's information
	 *
	 * In this method we authenticate an user in the LDAP server with the specified username and password
	 * If successful, we return the user information. Otherwise, we'll return false and throw exceptions with error information
	 *
	 * @access public
	 * @param string $user Username to be authenticated
	 * @param string $password Password to be authenticated
	 * @return mixed User information, as an array, on successful authentication, false on error
	 */
	public function auth($user, $password) {
		/**
		 * We bind using the provided information in order to check if the user exists
		 * in the directory and his credentials are valid
		 */
		
		if ( preg_match ( '/=/', $user ) ) {
			$this->user = $user;
		} else {
			$this->user = "uid=$user," . $this->dn;
		}
		
		$this->password = $password;
		$this->bind = ldap_bind($this->ldap, $this->user, $this->password);
		if( $this->bind ) {
			return true;
		} else {
			return false;
		}
	}

	
	/**

	 */
 	public function reformatUser() 
 	{

		$obj = array ();

		if ( isset ( $this->data['count'] ) ) {
			$tc = $this->data['count'];
		}
		for ( $z=0; $z<$tc; $z++)
		{
			$a = $this->data[$z];
			if ( is_array ( $a ) ) 
			{
				if ( isset ($a['uid'][0]) ) {
					$uid = $a['uid'][0];
				} else {
					$uid = "id-" . $z;
				}
				$obj[$uid] = array ();
				foreach ( array_keys ( $a )  as $k ) 
				{
					if ( is_string ( $k ) ) {
						if ( isset ($a[$k]['count']) ) {
							$count = $a[$k]['count'];
							for ($i=0; $i<$count; $i++ ) {
								if ($i == 0) {
									if ( $count == 1) {
										$obj[$uid]{$k} = $a[$k][$i];
									} else {
										$obj[$uid]{$k} = array ();
										$obj[$uid]{$k}[] = $a[$k][$i];
									}
								} else {
									$obj[$uid]{$k}[] = $a[$k][$i];
								}
							}
						}
					}
				}
			}
		}

		if  ( $tc > 1 )
		{
			$this->obj = $obj;
		} else {
			$this->selfObj = $obj;
		}
		return true;
	}
	/**
	 * Reformat Group into php assoicative arrays.
	 *
		gdata['group'][${GroupName}][${LDAP_GROUP_ATTS}]
		gdata['group'][${GroupName}]['memberuid'] = array ( )
			Numbericly indexed array of users.
		gdata['group'][${GroupName}]['lut'][${UID}]
			Hash/assoiative array of users.
		gdata['users'][${UID}][${GroupName}]
			Hash/assoiative array of users and their groups.
	 */
	public function reformatGroup()
	{
		for ($i=0; $i<$this->gdata['count']; $i++) 
		{
			// cn= group name	
			$gn =  $this->gdata[$i]['cn'][0];
			for ($j=0; $j<$this->gdata[$i]['count']; $j++ ) {
				$k =  $this->gdata[$i][$j];
				for ($l=0; $l<$this->gdata[$i][$k]['count']; $l++) {
					$value = $this->gdata[$i][$k][$l];
					$this->gobj{'group'}{$gn}{$k}[] = $value;
					if (  $k == "memberuid" ) {
						$this->gobj{'group'}{$gn}{'lut'}{$value} = 1;
						$u = $this->gdata[$i][$k][$l];
						$this->gobj{'user'}{$u}{$gn} = 1;
					}
				}
			}		
		}
	}
	/**
	 * getGroup
	 *
		Genenic fetching/searching for groups.
	*/

	public function getGroup($filter, $attributes = null) 
	{
		if($attributes !== null) {
			$search = ldap_search($this->ldap, $this->gdn, $filter, $attributes);
			if(!$search) {
				$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
				// throw new Exception($error);
				error_log ( $error );
				return false;
			}
			$this->gdata = ldap_get_entries($this->ldap, $search);
			$this->reformatGroup();
			return true;
		} else {
			$search = ldap_search($this->ldap, $this->gdn, $filter);
			if(!$search) {
				$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
				//throw new Exception($error);
				error_log ( $error );
				return false;
			}
			$this->gdata = ldap_get_entries($this->ldap, $search);
			$this->reformatGroup();
			return true;
		}
	
	}
	
	/**
	 * getUsersGroup
	 *
		Returns all the users groups.
		Caches ALL groups into gdata and gobj.
	*/

	public function getUsersGroup($user, $assoc = FALSE )
	{

		if ( $this->gobj == null ) {
			$this->getGroup("objectclass=posixGroup");
		}

		if (isset ($this->gobj{'user'}{$user}) && $assoc )
		{
			return $this->gobj{'user'}{$user};

		} elseif   (isset ($this->gobj{'user'}{$user}) && $assoc == FALSE )
		{
			return array_keys ( $this->gobj{'user'}{$user} );
		} else {
			return array();
		}
	}

	/**
	 * getUsers
	 *
		Genenic fetching/searching for users.
	*/

	public function getUsers($filter, $attributes = null) {
		if($attributes !== null) {
			$search = ldap_search($this->ldap, $this->dn, $filter, $attributes);
			error_log ( "getUsers:search:attrs " . json_encode ( $search ) );
			if(!$search) {
				$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
				error_log ( $error );
				//throw new Exception($error);
				return false;
			}
			$this->data = ldap_get_entries($this->ldap, $search);
			$this->reformatUser();
			return true;
		} else {
			$search = ldap_search($this->ldap, $this->dn, $filter);
			error_log ( "getUsers:search " . json_encode ( $search ) );
			if(!$search) {
				$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
				error_log ( $error );
				//throw new Exception($error);
				return false;
			}
			$this->data = ldap_get_entries($this->ldap, $search);
			$this->reformatUser();

			return true;
		}
	}
	
	public function getUser($user = NULL )
	{
		if ( $user == NULL )
		{
			$user = $this->user;
		}
		return ( $this->getUsers( "(uid=$user)" ) );

	}

	/**
	 * Inserts a new user in LDAP
	 *
	 * This method will take an array of information and create a new entry in the 
	 * LDAP directory using that information.
	 *
	 * @access public
	 * @param string $uid Username that will be created
	 * @param array $data Array of user information to be inserted
	 * @return bool Returns true on success and false on error
	 */
	public function addUser($user, $data) {
		$add = ldap_add($this->ldap, "uid=$user," . $this->dn, $data);
		if(!$add) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			//throw new Exception($error);
			error_log ( $error );
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Removes an existing user in LDAP
	 *
	 * This method will remove an existing user from the LDAP directory
	 *
	 * @access public
	 * @param string $uid Username that will be removed
	 * @return bool Returns true on success and false on error
	 */
	public function removeUser($user) {
		$delete = ldap_delete($this->ldap, "uid=$user," . $this->dn);
		if(!$delete) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			error_log ( $error );
			//throw new Exception($error);
			return false;
		} else {
			return true;
		}
	}

	
	/**
	 * Modifies an existing user in LDAP
	 *
	 * This method will take an array of information and modify an existing entry 
	 * in the LDAP directory using that information.
	 *
	 * @access public
	 * @param string $uid Username that will be modified
	 * @param array $data Array of user information to be modified
	 * @return bool Returns true on success and false on error
	 */
	public function modifyUser($user, $data) {
		$modify = ldap_modify($this->ldap, "uid=$user," . $this->dn, $data);
		if(!$modify) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			error_log ( $error );
			//throw new Exception($error);
			return false;
		} else {
			return true;
		}
	}
	public function modDelUserAttr($user, $data) {
		$modify = ldap_mod_del($this->ldap, "uid=$user," . $this->dn, $data);
		if(!$modify) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			error_log ( $error );
			//throw new Exception($error);
			return false;
		} else {
			return true;
		}
	}

	public function addObjectClass($user, $oc)
	{
		$this->getUser($user);
		$data = array ();
		$data['objectClass'] = $this->selfObj[$user]['objectclass'];
		array_push ($data['objectClass'], $oc);
		$this->modifyUser($user, $data );
	}


	public function removeObjectClass($user, $oc)
	{
		$this->modDelUserAttr($user, array ( 'objectClass' => $oc ) );
	}


	public function addGroupMember($group, $user) {
		$modify = ldap_modify($this->ldap, "cn=$group," . $this->gdn, array ('memberUid' => $user ) );
		if(!$modify) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			error_log ( $error );
			//throw new Exception($error);
			return false;
		} else {
			return true;
		}
	}
	public function delGroupMember($group, $user) {
		$modify = ldap_mod_del($this->ldap, "cn=$group," . $this->gdn, array ('memberUid' => $user ) );
		if(!$modify) {
			$error = ldap_errno($this->ldap) . ": " . ldap_error($this->ldap);
			error_log ( $error );
			//throw new Exception($error);
			return false;
		} else {
			return true;
		}
	}


	
	public function getAttrs($user) {
		$atts = array();
		$this->getUsers("(uid=$user)");
		foreach ( $this->obj{'objectclass'} as $oc ) {
			$lut{$oc} = 1;
		}
		
		// print_r ( $lut );
		$search = ldap_read($this->ldap, "", "objectclass=*", array('*', 'subschemasubentry'));
		$entries = ldap_get_entries($this->ldap, $search);
		$schemadn = $entries[0]["subschemasubentry"][0];
	
		# print "Searching ". $schemadn . "\n\n";
		// Read all objectclass, attributetype from subschema
		$schsearch = ldap_read($this->ldap, $schemadn, "objectClass=subSchema", array('objectclasses', 'attributetypes'));
		$schentries = ldap_get_entries($this->ldap, $schsearch);
		$count = $schentries[0]["attributetypes"]["count"];
		# print_r ($schentries);
		#  print "Printing all attribute types \n";
		#  for ($i=0; $i<$count; $i++) {  print $schentries[0]["attributetypes"][$i] . "\n"; }
		$count = $schentries[0]["objectclasses"]["count"];
		# print "Printing all objectclasses \n\n\n\n";
		for ($i=0; $i<$count; $i++) {
			 $s = $schentries[0]["objectclasses"][$i];
	 
			 #( 1.3.6.1.1.1.2.7 NAME 'ipNetwork' DESC 'Abstraction of an IP network' SUP top STRUCTURAL MUST ( cn $ ipNetworkNumber ) MAY ( ipNetmaskNumber $ l $ description $ manager ) )
			 $p = '/(NAME) \'([\w\d]+)\' ((DESC) \'(.+)\' )?(SUP) (\w+) (AUXILIARY|STRUCTURAL) (MAY|MUST) ((\()?([\w\d\$\ ]*)(\))|([\w\d]+)) ((MAY) ((\()?([\w\d\$\ ]*)(\))|([\w\d]+))?)?/';
			 preg_match_all ( $p, $s, $m );
			 	 
			 $o = array();
			 for ( $z=0; $z<count($m); $z++) {
				if (! isset($m[$z][0])) { continue; }
				switch ( $m[$z][0] ) {
					case "NAME":
						$z++;
						$o{'NAME'} = $m[$z][0];
					break;
					case "DESC":
						$z++;
						$o{'DESC'} = $m[$z][0];
					break;
					case "SUP":
						$z++;
						$o{'SUP'} = $m[$z][0];
					break;
					case "STRUCTURAL":
					case "AUXILIARY":
						$o{$m[$z][0]} = $m[$z][0];
					break;
					case "MAY":
					case "MUST":
						$k = $m[$z][0];
						$z++;
						$d = $m[$z][0];
						$d = str_replace ( "( ", "", $d );
						$d = str_replace ( " )", "", $d );
						$o{$k} = explode (' $ ', $d);				
					break;
				}

			 }

			if ( isset ( $o['NAME'] ) ) 
				{ 
					$n = $o{'NAME'}; 
					if ( isset ( $lut{$n} ) ) 
						{
							if ( isset ( $o{'MUST'} ) ) {
								$atts = array_merge ( $atts, $o{'MUST'} );
							}
							if ( isset ( $o{'MAY'} ) ) 
							{
								$atts = array_merge ( $atts, $o{'MAY'} );
							}
						} 
				}

			}
     

		sort ($atts);
		return array_unique ( $atts );
     
	}
		
	public function sshapasswd($input){
			mt_srand((double)(microtime(true) ^ posix_getpid()));
			$salt = pack("CCCC", mt_rand(0,255), mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));

			$passwd_sha1 = sha1($input . $salt, TRUE);

			$result = '{SSHA}' . base64_encode($passwd_sha1 . $salt);

			if (!$this->checkssha($result, $input))
					return null;
			else
					return $result;

	}

	public function checkssha ($input, $passwd){
			$orig = base64_decode(substr($input, 6));
			$hash = substr($orig, 0, 20);
			$salt = substr($orig, 20, 4);

			if (sha1($passwd . $salt, TRUE) == $hash){
					return TRUE;
			} else {
					return FALSE;
			}
	}

	public function check_ldap_passwd($username, $passwd){
		return $this->auth($username, $passwd);
	}

	public function change_ldap_passwd($username, $passwd, $new){

			if(!$this->check_ldap_passwd($username, $passwd))
				return NULL;
			$r = $this->modifyUser($username, array ( 'userPassword' => $this->sshapasswd($new) ) );
			return $r;
	}

	
	/**
	 * Close the LDAP connection
	 *
	 * @access public
	 */
	public function close() {
		ldap_close($this->ldap);
	}
}
