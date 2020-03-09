<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class response {
	
	public function __construct()
	{
		$this->status = new stdClass();
		$this->data = new stdClass();
		$this->status->sys = SYS_STATUS;
		$this->status->session = (Bool)true;
		$this->response_execution = 0;
	}

	/**
	* Utility: default_values($_type, $_msg)
	*
	* <pre>
	* Set default response vars for API response.
	* </pre>
	* @access	public
	* @param	string
	* @param	string
	* @return	Boolean
	*/
	public function default_values(String $_type, String $_msg): Bool
	{
		$this->response_epoch = microtime(true);
		$this->response_type = $_type;
		$this->response_message = $_msg;
		$this->response_execution = round(($this->response_epoch - TIME_BEGIN), 4);

	return (Bool)true;
	}

}
?>