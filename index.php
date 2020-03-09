<?php
	ini_set('include_path', ini_get('include_path').":".dirname(__FILE__)."/inc");
	require_once "config.inc";

	$class_name = '';		// passed by reference
	$class_method = 'index';// passed by reference
	
	try
	{
		api::api_parse_uri($_SERVER["REQUEST_URI"], $class_name, $class_method);
	}
	catch (Exception $e)
	{
		$msg = $e->getMessage();
	
		$object = new api();
		$object->dao = new dao(0);
		$object->response = new response();
		$object->response->default_values(false, $msg);

		$object->db_obj = $object->dao->dao_connect();
		$object->api_log_error($msg, $e->getTraceAsString());
		$object->db_obj->close();
		
		echo json_encode($object->response);
		exit;
	}
	
	$object = new $class_name();
	$object->dao = new dao();
	$object->response = new response();
	$object->post = '';

	if($json = file_get_contents('php://input'))
	{
		if(!$object->post = json_decode($json))
		{
			$object->msg = "Incorrect post data format. idx-001".$json;
			$object->response->default_values(false, $object->msg);
		
			$object->db_obj = $object->dao->dao_connect();
			$object->api_log_error($object->msg, "/index unreadable json format.".$json);
			$object->db_obj->close();
			echo json_encode($object->response);
			exit;
		}
	}
	
	if(method_exists($object, $class_method))
	{
		try {
			
			$object->db_obj = $object->dao->dao_connect();
			
			if(!$object->$class_method())
			{
				@$msg = "Unable to execute request. idx-002";
				throw new Exception($msg);
			return false;
			}
			
			$object->response->default_values(true, 'Success');

		} catch (Exception $e)
		{
			$object->msg = $e->getMessage();
			$object->response->default_values(false, $object->msg);

			$object->db_obj = $object->dao->dao_connect();
			$object->api_log_error($object->msg, $e->getTraceAsString());
			$object->db_obj->close();
			echo json_encode($object->response);
			exit;
		}
	} else {
	
		$msg = "401: Unable to process request at this time. xxx";
	
		$object->response->default_values(false, $msg);
		
		$object->db_obj = $object->dao->dao_connect();
		$object->api_log_error($msg, "");
		$object->db_obj->close();
		
		echo json_encode($object->response);
		exit;
	}
	
	echo json_encode($object->response);
?>