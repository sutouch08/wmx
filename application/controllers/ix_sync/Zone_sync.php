<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Zone_sync extends PS_Controller
{
  public $menu_code = 'DBZONE';
  public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';

  public function __construct()
  {
    parent::__construct();
    $this->load->model('masters/zone_model');
    $this->load->model('ix_sync/zone_sync_model');
  }

  public function count_zone()
  {
    $total = $this->zone_sync_model->count_zone();
    echo json_encode($total);
  }

  public function get_zones($limit = 1000, $offset = 0)
  {
    $res = $this->zone_sync_model->get_zones($limit, $offset);
    $count = 0;

    if (!empty($res))
    {
      foreach ($res as $rs)
      {
        $count++;
        $id = $this->zone_model->get_id($rs->code);

        if (empty($id))
        {
          $arr = [
            'code' => $rs->code,
            'name' => $rs->name,
            'warehouse_code' => $rs->warehouse_code,
            'active' => $rs->active,
            'user' => $this->_user->uname,
            'last_sync' => now()
          ];

          $this->zone_model->add($arr);
        }
        else
        {
          $arr = [
            'code' => $rs->code,
            'name' => $rs->name,
            'warehouse_code' => $rs->warehouse_code,
            'active' => $rs->active,
            'update_user' => $this->_user->uname,
            'last_sync' => now()
          ];

          $this->zone_model->update_by_id($id, $arr);
        }
      }
    }

    $arr = array(
      'offset' => $count
    );

    echo json_encode($arr);
  }
}
