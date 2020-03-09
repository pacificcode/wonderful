<?php

class dao {

	protected $db_host;
	protected $db_user;
	protected $db_passwd;
	protected $db_database;
	
	public $db_obj;
	
	function __construct()
	{
		$this->db_host 		= DB_HOST;
		$this->db_user 		= DB_USER;
		$this->db_passwd 	= DB_PASSWD;
		$this->db_database 	= DB_DATABASE;
	}

	/**
	* Utility: dao_connect() connect to DB assigned by __construct()
	*
	* <pre>
	* connect to DB assigned by __construct().
	* Pass in:
	* $_id_network at object construct;
	* </pre>
	* @access	public
	* @return	object
	*/
	public function dao_connect(): Object
	{
		//  CREATE USER 'airport'@'localhost' IDENTIFIED WITH mysql_native_password BY 'airport';
		//  GRANT ALL PRIVILEGES ON airport.* TO 'airport'@'localhost';

		@$this->db_obj = new mysqli($this->db_host,$this->db_user,$this->db_passwd,$this->db_database);

		if ($this->db_obj->connect_errno)
		{
			@$msg =  "Connection failure: dao 002 (" . $this->db_obj->connect_errno . ") " . $this->db_obj->connect_error;
			throw new Exception($msg);
		return false;
		}	
		
		if(!$this->db_obj->autocommit(false))
		{
			@$msg =  "Configuration error: dao 003";
			throw new Exception($msg);
		return false;
		}

	return $this->db_obj;
	}
}


?>