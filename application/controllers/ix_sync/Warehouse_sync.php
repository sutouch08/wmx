<?php
class Warehouse_sync extends PS_Controller
{
  public $menu_code = 'DBWRHS';
  public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
  
  public function __construct()
  {
    parent::__construct();    
    $this->load->model('ix_sync/warehouse_sync_model');
    $this->load->model('masters/warehouse_model');
  }

  public function count_warehouses()
  {
    $total = $this->warehouse_sync_model->count_warehouses();
    echo json_encode($total);
  }

  public function get_warehouses($limit = 1000, $offset = 0)
  {    
    $res = $this->warehouse_sync_model->get_warehouses($limit, $offset);
    $count = 0;
    
    if(!empty($res))
    {
      foreach($res as $rs)
      {
        $count++;
        $id = $this->warehouse_model->get_id($rs->code);

        if(empty($id))
        {
          $arr = [
            'code' => $rs->code,
            'name' => $rs->name,
            'role' => $rs->role,
            'active' => $rs->active,
            'sell' => $rs->sell,
            'prepare' => $rs->prepare,
            'lend' => $rs->lend,
            'auz' => $rs->auz,
            'old_code' => get_null($rs->old_code),
            'is_consignment' => $rs->is_consignment,
            'is_pos' => $rs->is_pos,
            'default_zone' => $rs->default_zone,
            'limit_amount' => $rs->limit_amount,
            'position' => $rs->position,            
            'user' => $this->_user->uname,
            'last_sync' => now()
          ];

          $this->warehouse_model->add($arr);          
        }
        else 
        {
          $arr = [
            'code' => $rs->code,
            'name' => $rs->name,
            'role' => $rs->role,
            'active' => $rs->active,
            'sell' => $rs->sell,
            'prepare' => $rs->prepare,
            'lend' => $rs->lend,
            'auz' => $rs->auz,
            'old_code' => get_null($rs->old_code),
            'is_consignment' => $rs->is_consignment,
            'is_pos' => $rs->is_pos,
            'default_zone' => $rs->default_zone,
            'limit_amount' => $rs->limit_amount,
            'position' => $rs->position,            
            'update_user' => $this->_user->uname,
            'last_sync' => now()
          ];

          $this->warehouse_model->update_by_id($id, $arr);
        }
      }
    }

    $arr = array(
      'offset' => $count
    );

    echo json_encode($arr);
  }
}
