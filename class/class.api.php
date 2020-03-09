<?php

class api {
	
	public $db_obj;

	function __construct()
	{
		$this->class_name = false;
		$this->class_method = false;
		$this->hash = "";

		$this->current_microtime = microtime(true);
		$this->current_timestamp = date('Y-m-j H:i:s', $this->current_microtime);
		$this->current_epoch = (Int)$this->current_microtime;
	}
	
	/**
	* Utility: index()
	*
	* <pre>
	* Returns false.
	* </pre>
	* @access	public
	* @return	object
	*/
	public function index()
	{

	return false;
	}

	/**
	* Database: plot_route(): Bool
	*
	* <pre>
	* Plot route from airport 1 to airport2
	* </pre>
	* @access	public
	* @param	int
	* @param	int
	* @return	boolean
	*/
	public function plot_route(): Bool
	{
		$this->max_miles = 500; // max miles for each hop in route
		$array = ['id_airport1','id_airport2'];
		
		for($i = 0; $i < count($array); $i++)
		{
			if(@!$this->post->{$array[$i]} || @$this->post->{$array[$i]} == "")
			{
				@$msg = "Missing required post variable. ".$name[$i]." eid:api-001";
				throw new Exception($msg);
			}
			if(preg_match('/[^0-9]/',$this->post->{$array[$i]}))
			{
				@$msg = "Incorrect post variable type. ".$name[$i]." eid:api-002";
				throw new Exception($msg);
			}
		}
		
		if($this->post->id_airport1 == $this->post->id_airport2)
		{
			@$msg = "Airport identifiers are the same. eid:api-002";
			throw new Exception($msg);
		}
		
		/* Fetch the begin/end data we need to plot this course */
		$result = $this->db_obj->query("call distance_two_airport(".$this->post->id_airport1.", ".$this->post->id_airport2.")");

		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-003 ";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-004";
			throw new Exception($msg);
		}

		$begin_end = $result->fetch_object();

		if($begin_end->distance_m < $this->max_miles)
		{
			return true;
		}
		
		$result->free_result();
		$this->db_obj->next_result();
		
		$array = [];
		$this->next_hop($this->post->id_airport1, $this->post->id_airport2, $array);

		$result = $this->db_obj->query("call get_airport(".$this->post->id_airport1.")");

		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-005 ";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-006";
			throw new Exception($msg);
		}

		array_unshift($array, $result->fetch_object());
		$result->free_result();
		$this->db_obj->next_result();

		$result = $this->db_obj->query("call get_airport(".$this->post->id_airport2.")");

		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-007 ";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-008";
			throw new Exception($msg);
		}

		array_push($array, $result->fetch_object());
		$result->free_result();
		$this->db_obj->next_result();
		
		$this->response->data = $array;

	return (Bool)true;
	}

	public function next_hop($id_airport1, $id_airport2, &$array)
	{
		$result = $this->db_obj->query("call plot_route(".$id_airport1.", ".$id_airport2.")");
		
		if($result->num_rows == 0 || count($array) == 5)
		{
			$result->free_result();
			$this->db_obj->next_result();
			return true;
		}
		$array[] = $result->fetch_object();
		$result->free_result();
		$this->db_obj->next_result();
		if($array[count($array)-1]->distance_m == 0)
		{
			$result->free_result();
			$this->db_obj->next_result();
			return true;
		}

		$this->next_hop($array[count($array)-1]->id_airport, $id_airport2, $array);
		
	return true;		
	}
	
	/**
	* Database: airport_by_country(): Bool
	*
	* <pre>
	* Fetch 2 closest airports by country. Returns 2 selections
	* </pre>
	* @access	public
	* @param	string
	* @param	string
	* @return	boolean
	*/
	public function airport_by_country(): Bool
	{
		$array = ['country1','country2'];
		
		for($i = 0; $i < count($array); $i++)
		{
			if(@!$this->post->{$array[$i]} || @$this->post->{$array[$i]} == "")
			{
				@$msg = "Missing required post variable. ".$name[$i]." eid:api-001";
				throw new Exception($msg);
			}
			if(preg_match('/[^a-zA-Z-\. ]/',$this->post->{$array[$i]}))
			{
				@$msg = "Incorrect post variable type. ".$name[$i]." eid:api-002";
				throw new Exception($msg);
			}
		}
		
		if($this->post->country1 == $this->post->country2)
		{
				@$msg = "Country identifiers are the same. eid:api-003";
				throw new Exception($msg);
		}
		
		$result = $this->db_obj->query("call airport_by_country('".$this->post->country1."', '".$this->post->country2."')");

		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-004";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-005";
			throw new Exception($msg);
		}
		
		$this->response->data = $result->fetch_object();

	return (Bool)true;
	}

	/**
	* Database: airport_by_radius(): Bool
	*
	* <pre>
	* Fetch all airports in a given radius from lat lon.
	* </pre>
	* @access	public
	* @param	float
	* @param	float
	* @param	int
	* @return	boolean
	*/
	public function airport_by_radius(): Bool
	{
		$array = ['lat','lon','radius'];
		
		for($i = 0; $i < count($array); $i++)
		{
			if(@!$this->post->{$array[$i]} || @$this->post->{$array[$i]} == "")
			{
				@$msg = "Missing required post variable. ".$name[$i]." eid:api-001";
				throw new Exception($msg);
			}
			if(preg_match('/[^0-9-\.]/',$this->post->{$array[$i]}))
			{
				@$msg = "Incorrect post variable type. ".$name[$i]." eid:api-002";
				throw new Exception($msg);
			}
		}
		$result = $this->db_obj->query("call distance_airport_to_radius(".$this->post->lat.", ".$this->post->lon.",".$this->post->radius." )");
		
		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-003";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-004";
			throw new Exception($msg);
		}

		$out = [];
		
		for($i = 0; $i < $result->num_rows; $i++)
		{
			$out[] = $result->fetch_object();
		}
		
		$this->response->data = $out;

	return (Bool)true;
	}
	/**
	* Database: distance_between(): Bool
	*
	* <pre>
	* Fetch distance and related data between 2 airport ids.
	* </pre>
	* @access	public
	* @param	int
	* @param	int
	* @return	object/boolean
	*/
	public function distance_between(): Bool
	{
		$array = ['id_airport1','id_airport2'];
		
		for($i = 0; $i < count($array); $i++)
		{
			if(@!$this->post->{$array[$i]} || @$this->post->{$array[$i]} == "")
			{
				@$msg = "Missing required post variable. ".$name[$i]." eid:api-001";
				throw new Exception($msg);
			}
			if(preg_match('/[^0-9]/',$this->post->{$array[$i]}))
			{
				@$msg = "Incorrect post variable type. ".$name[$i]." eid:api-002";
				throw new Exception($msg);
			}
		}

		$result = $this->db_obj->query("call distance_two_airport(".$this->post->id_airport1." , ".$this->post->id_airport2." )");

		if($this->db_obj->errno != 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-003";
			throw new Exception($msg);
		}

		if($result->num_rows == 0)
		{
			@$msg = "Unable to execute request at this time. eid:api-004";
			throw new Exception($msg);
		}

		$this->response->data = $result->fetch_object();

	return (Bool)true;	
	}
		
	/**
	* Database: api_log_error($_error_msg)
	*
	* <pre>
	* Enter error description into sb_log_error.
	* </pre>
	* @access	public
	* @param	string
	* @param	string
	* @return	int
	*/
	public function api_log_error($_error_msg, $_trace)
	{
		$explode = explode(".", $_SERVER['HTTP_HOST']);
		$server = $explode[0];

		$query = "
		INSERT INTO log_error
		SET
		json_session = '".json_encode($_SESSION)."',
		stack_trace = '".addslashes($_trace)."',
		request_uri = '".addslashes($_SERVER["REQUEST_URI"])."',
		msg_0 = '".addslashes(trim($server)."...".trim(shell_exec('hostname')))."',
		msg_1 = '".addslashes(trim($_error_msg))."'
		";
		
		$this->db_obj->query($query);
		
		if($this->db_obj->errno != 0 || !$this->db_obj->insert_id)
		{
			@$msg = $this->db_obj->error_list[0]['error'];
			throw new Exception($msg);
		}
		
		$this->db_obj->commit();
		
	return $this->db_obj->insert_id;
	}
	
	/**
	* UTILITY: api_parse_uri($_uri, &$_class_name, &$_class_method)
	*
	* <pre>
	* Parse $_SERVER["REQUEST_URI"]
	* </pre>
	*	@access	public
	*	@param	string
	*	@param	string
	*	@param	string
	*	@return	boolean
	*/
	public static function api_parse_uri($_uri, &$_class_name, &$_class_method): Bool
	{
		// This request is goin to the index function
		// so we dont need to do any further processing
		if($_uri === '/')
		{
			header("HTTP/1.1 404 Not Found. '$_uri' is not accessable on this server.");
			@$msg = "404: Not Found. '$_uri' is not accessable on this server.";
			throw new Exception($msg);
		}
			
		$explode = explode('/', $_uri);

		if($explode[count($explode)-1] == "")
		{
			array_pop($explode);
		}

		switch(count($explode))
		{
			case 2:
			
				$_class_name = strtolower($explode[1]);
			break;

			case 3:
			
				$_class_name = strtolower($explode[1]);
				$_class_method = strtolower($explode[2]);
			break;
		}

		$file = 'class/class.' . $_class_name . '.php';

		if (!file_exists($file))
		{
			@$msg = "401: Unauthorized. You do not have access to '$_uri' on this server";
			throw new Exception($msg);
		}

	return (Bool)true;
	}
}

?>