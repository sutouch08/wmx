<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Master_logs_model extends CI_Model
{
	private $tb = 'master_logs';
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


	public function get_logs($id)
	{
		$rs = $this->logs->where('id', $id)->get($this->tb);

		if($rs->num_rows() == 1)
		{
			return $rs->row();
		}

		return NULL;
	}

} //---
?>
