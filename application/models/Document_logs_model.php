<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Document_logs_model extends CI_Model
{
	private $tb = 'document_logs';
  public $logs;

  public function __construct()
  {
    parent::__construct();
    $this->logs = $this->load->database('logs', TRUE);
  }

	public function add_logs($ds = array())
	{
		return $this->logs->insert($this->tb, $ds);
	}


	public function get_logs($code)
	{
		$rs = $this->logs->where('code', $code)->get($this->tb);

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}

} //---
?>
