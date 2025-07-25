<?php
class Products_model extends CI_Model
{
  private $tb = "products";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_id($code)
  {
    $rs = $this->db->select('id')->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }

  public function count_rows(array $ds = array())
  {
    if( ! empty($ds))
    {
      if( ! empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if( ! empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if( ! empty($ds['barcode']))
      {
        $this->db->like('barcode', $ds['barcode']);
      }

      if( ! empty($ds['color']))
      {
        $this->db->like('color_code', $ds['color']);
      }

      if( ! empty($ds['size']))
      {
        $this->db->like('size_code', $ds['size']);
      }

      if( ! empty($ds['price']))
      {
        $operater = ! empty($ds['operater']) ? $ds['operater'] : 'less_than';

        if($operater === 'more_than')
        {
          $this->db->where('products.price >=', $ds['price'], FALSE);
        }
        else
        {
          $this->db->where('products.price <=', $ds['price'], FALSE);
        }
      }

      if(isset($ds['main_group']) && $ds['main_group'] != 'all')
      {
        $this->db->where('main_group_code', $ds['main_group']);
      }

      if(isset($ds['group']) && $ds['group'] != 'all')
      {
        $this->db->where('group_code', $ds['group']);
      }

      if(isset($ds['segment']) && $ds['segment'] != 'all')
      {
        $this->db->where('segment_code', $ds['segment']);
      }

      if(isset($ds['class']) && $ds['class'] != 'all')
      {
        $this->db->where('class_code', $ds['class']);
      }

      if(isset($ds['type']) && $ds['type'] != 'all')
      {
        $this->db->where('type_code', $ds['type']);
      }

      if(isset($ds['kind']) && $ds['kind'] != 'all')
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if(isset($ds['gender']) && $ds['gender'] != 'all')
      {
        $this->db->where('gender_code', $ds['gender']);
      }

      if(isset($ds['sport_type']) && $ds['sport_type'] != 'all')
      {
        $this->db->where('sport_type_code', $ds['sport_type']);
      }

      if(isset($ds['collection']) && $ds['collection'] != 'all')
      {
        $this->db->where('collection_code', $ds['collection']);
      }

      if(isset($ds['brand']) && $ds['brand'] != 'all')
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if(isset($ds['year']) && $ds['year'] != 'all')
      {
        $this->db->where('year', $ds['year']);
      }

      if(isset($ds['active']) && $ds['active'] != 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds))
    {
      if( ! empty($ds['code']))
      {
        $this->db->like('code', $ds['code']);
      }

      if( ! empty($ds['name']))
      {
        $this->db->like('name', $ds['name']);
      }

      if( ! empty($ds['barcode']))
      {
        $this->db->like('barcode', $ds['barcode']);
      }

      if( ! empty($ds['color']))
      {
        $this->db->like('color_code', $ds['color']);
      }

      if( ! empty($ds['size']))
      {
        $this->db->like('size_code', $ds['size']);
      }

      if( ! empty($ds['price']))
      {
        $operater = ! empty($ds['operater']) ? $ds['operater'] : 'less_than';

        if($operater === 'more_than')
        {
          $this->db->where('products.price >=', $ds['price'], FALSE);
        }
        else
        {
          $this->db->where('products.price <=', $ds['price'], FALSE);
        }
      }

      if(isset($ds['main_group']) && $ds['main_group'] != 'all')
      {
        $this->db->where('main_group_code', $ds['main_group']);
      }

      if(isset($ds['group']) && $ds['group'] != 'all')
      {
        $this->db->where('group_code', $ds['group']);
      }

      if(isset($ds['segment']) && $ds['segment'] != 'all')
      {
        $this->db->where('segment_code', $ds['segment']);
      }

      if(isset($ds['class']) && $ds['class'] != 'all')
      {
        $this->db->where('class_code', $ds['class']);
      }

      if(isset($ds['type']) && $ds['type'] != 'all')
      {
        $this->db->where('type_code', $ds['type']);
      }

      if(isset($ds['kind']) && $ds['kind'] != 'all')
      {
        $this->db->where('kind_code', $ds['kind']);
      }

      if(isset($ds['gender']) && $ds['gender'] != 'all')
      {
        $this->db->where('gender_code', $ds['gender']);
      }

      if(isset($ds['sport_type']) && $ds['sport_type'] != 'all')
      {
        $this->db->where('sport_type_code', $ds['sport_type']);
      }

      if(isset($ds['collection']) && $ds['collection'] != 'all')
      {
        $this->db->where('collection_code', $ds['collection']);
      }

      if(isset($ds['brand']) && $ds['brand'] != 'all')
      {
        $this->db->where('brand_code', $ds['brand']);
      }

      if(isset($ds['year']) && $ds['year'] != 'all')
      {
        $this->db->where('year', $ds['year']);
      }

      if(isset($ds['active']) && $ds['active'] != 'all')
      {
        $this->db->where('active', $ds['active']);
      }
    }

    $this->db->order_by('id','DESC');
    $this->db->limit($perpage, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_by_id($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function get_status($field, $id)
  {
    $rs = $this->db->select($field)->where('id', $id)->get($this->tb);
    if($rs->num_rows() == 1)
    {
      return $rs->row()->$field;
    }

    return 0;
  }


  public function get_barcode($code)
  {
    $rs = $this->db->select('barcode')->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->barcode;
    }

    return NULL;
  }


  public function get_product_by_barcode($barcode)
  {
    $rs = $this->db->where('barcode', $barcode)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function set_status($field, $id, $val)
  {
    return $this->db->set($field, $val)->where('id', $id)->update($this->tb);
  }


  public function delete_item($code)
  {
    return $this->db->where('code', $code)->delete($this->tb);
  }


  public function delete_item_by_id($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_item($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_with_old_code($code)
  {
    $rs = $this->db->where('code', $code)->or_where('old_code', $code)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_old_code($code)
  {
    $rs = $this->db->where('old_code', $code)->order_by('date_upd', 'DESC')->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }


  public function get_model_code($code)
  {
    $rs = $this->db->select('model_code')->where('code', $code)->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->model_code;
    }

    return NULL;
  }


  public function get_model_items($code)
  {
    $this->db
    ->select('p.*')
    ->from('products AS p')
    ->join('product_color AS c', 'p.color_code = c.code', 'left')
    ->join('product_size AS s', 'p.size_code = s.code', 'left')
    ->where('p.model_code', $code)
    ->order_by('c.code', 'ASC')
    ->order_by('s.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_items_by_color($model, $color)
  {
    $rs = $this->db->where('model_code', $model)->where('color_code', $color)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_item_by_color_and_size($model, $color, $size)
  {
    $rs = $this->db
    ->where('model_code', $model)
    ->where('color_code', $color)
    ->where('size_code', strval($size))
    ->limit(1)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function countAttribute($model_code)
	{
		$color = $this->db->where('model_code', $model_code)->where('color_code is NOT NULL')->where('color_code !=', '')->group_by('model_code')->get($this->tb);
		$size  = $this->db->where('model_code', $model_code)->where('size_code is NOT NULL')->where('size_code !=', '')->group_by('model_code')->get($this->tb);
		return $color->num_rows() + $size->num_rows();
	}


  public function get_unbarcode_items($model)
  {
    $this->db->select('code');
    $this->db->where('model_code', $model);
    $this->db->where('barcode IS NULL', NULL, FALSE);
    $rs = $this->db->get($this->tb);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_barcode($code, $barcode)
  {
    return $this->db->set('barcode', $barcode)->where('code', $code)->update($this->tb);
  }


  public function is_exists_barcode($barcode, $id = NULL)
  {
    if( ! empty($id))
    {
      $this->db->where('id !=', $id);
    }

    $count =  $this->db->where('barcode', $barcode)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists($code)
  {
    $count = $this->db->where('code', $code)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_name($name, $code = NULL)
  {
    if( ! empty($code))
    {
      $this->db->where('code !=', $code);
    }

    $count =  $this->db->where('name', $name)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_count_stock($code)
  {
    $count = $this->db->where('code', $code)->where('count_stock', 1)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_model($model)
  {
    $count = $this->db->where('model_code', $model)->count_all_results($this->tb);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_disactive_all($model_code)
  {
    $count = $this->db->where('model_code', $model_code)->where('active', 1)->count_all_results($this->tb);

    return $count > 0 ? FALSE : TRUE;
  }


  public function count_color($model_code)
  {
    $count = $this->db
    ->where('model_code', $model_code)
    ->where('color_code is NOT NULL')
    ->where('color_code != ', '')
    ->group_by('color_code')
    ->count_all_results($this->tb);

    return $count;
  }


  public function count_size($model_code)
  {
    $count = $this->db
    ->where('model_code', $model_code)
    ->where('size_code is NOT NULL')
    ->where('size_code != ', '')
    ->group_by('size_code')
    ->count_all_results($this->tb);

    return $count;
  }


  public function get_all_colors($model_code)
  {
    $rs = $this->db
    ->select('c.code, c.name')
    ->from('products AS p')
    ->join('product_color AS c', 'p.color_code = c.code', 'left')
    ->where('p.model_code', $model_code)
    ->group_by('p.color_code')
    ->order_by('p.color_code', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_sizes($model_code)
  {
    $rs = $this->db
    ->select('s.code, s.name, s.position')
    ->from('products AS p')
    ->join('product_size AS s', 'p.size_code = s.code', 'left')
    ->where('p.model_code', $model_code)
    ->group_by('p.size_code')
    ->order_by('s.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_model_sizes_cost_price($model_code)
  {
    $rs = $this->db
    ->select('s.code, p.cost, p.price')
    ->from('products AS p')
    ->join('product_size AS s', 'p.size_code = s.code')
    ->where('p.model_code', $model_code)
    ->group_by('s.code')
    ->order_by('s.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_cost_price_by_size($code, $size, $cost, $price)
  {
    if( ! empty($code))
    {
      $this->db
      ->set('cost', $cost)
      ->set('price', $price)
      ->where('model_code', $code)
      ->where('size_code', $size);

      return $this->db->update($this->tb);
    }

    return FALSE;
  }


  public function get_unit_code($code)
  {
    $rs = $this->db
    ->select('unit_code')
    ->where('code', $code)
    ->get($this->tb);
    if($rs->num_rows() === 1)
    {
      return $rs->row()->unit_code;
    }

    return NULL;
  }


  public function has_transection($code)
  {
    $od = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_details');
    $oc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('order_transform_detail');
    $rc = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_product_detail');
    $rt = $this->db->select('product_code')->where('product_code', $code)->count_all_results('receive_transform_detail');
    $tf = $this->db->select('product_code')->where('product_code', $code)->count_all_results('transfer_detail');
    $cn = $this->db->select('product_code')->where('product_code', $code)->count_all_results('return_order_detail');

    $all = $od+$oc+$rc+$rt+$tf+$cn;

    return $all > 0 ? TRUE : FALSE;
  }


	public function get_attribute($code)
	{
		$rs = $this->db
		->select('pd.*')
		->select('co.name AS color_name, si.name AS size_name')
		->select('pg.name AS group_name, pu.name AS sub_group_name')
		->select('pc.name AS category_name, pk.name AS kind_name')
		->select('pt.name AS type_name, br.name AS brand_name')
    ->select('cl.name AS collection_name')
		->from('products AS pd')
		->join('product_color AS co', 'pd.color_code = co.code', 'left')
		->join('product_size AS si', 'pd.size_code = si.code', 'left')
		->join('product_group AS pg', 'pd.group_code = pg.code', 'left')
		->join('product_sub_group AS pu', 'pd.sub_group_code = pu.code', 'left')
		->join('product_category AS pc', 'pd.category_code = pc.code', 'left')
		->join('product_kind AS pk', 'pd.kind_code = pk.code', 'left')
		->join('product_type AS pt', 'pd.type_code = pt.code', 'left')
		->join('product_brand AS br', 'pd.brand_code = br.code', 'left')
    ->join('product_collection AS cl', 'pd.collection_code = cl.code', 'left')
		->where('pd.code', $code)
		->get();

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function get_all_year()
  {
    $rs = $this->db->select('year')
    ->where('year IS NOT NULL', NULL, FALSE)
    ->group_by('year')
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}
?>
