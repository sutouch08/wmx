<?php
class Discount_rule_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    $rs = $this->db->insert('discount_rule', $ds);
    if($rs)
    {
      return $this->db->insert_id();
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update('discount_rule', $ds);
    }

    return FALSE;
  }


  public function get($id)
  {
    $rs = $this->db
    ->select('r.*, p.code AS policy_code, p.name AS policy_name, p.active AS policy_status')
    ->from('discount_rule AS r')
    ->join('discount_policy AS p', 'r.id_policy = p.id', 'left')
    ->where('r.id', $id)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return NULL;
  }


	public function get_policy_id($id)
	{
		$rs = $this->db->select('id_policy')->where('id', $id)->get('discount_rule');
		if($rs->num_rows() === 1)
		{
			return $rs->row()->id_policy;
		}

		return NULL;
	}
  /*
  |----------------------------------
  | BEGIN ใช้สำหรับแสดงรายละเอียดในหน้าพิมพ์
  |----------------------------------
  */

  public function getCustomerRuleList($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer AS cr')
    ->join('customers AS cs', 'cr.customer_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getCustomerGroupRule($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer_group AS cr')
    ->join('customer_group AS cs', 'cr.group_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getCustomerTypeRule($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer_type AS cr')
    ->join('customer_type AS cs', 'cr.type_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getCustomerKindRule($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer_kind AS cr')
    ->join('customer_kind AS cs', 'cr.kind_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getCustomerAreaRule($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer_area AS cr')
    ->join('customer_area AS cs', 'cr.area_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getCustomerClassRule($id)
  {
    $rs = $this->db
    ->select('cs.code, cs.name')
    ->from('discount_rule_customer_class AS cr')
    ->join('customer_class AS cs', 'cr.class_code = cs.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductRule($id)
  {
    $rs = $this->db
    ->select('product_code AS code')
    ->where('id_rule', $id)
    ->get('discount_rule_product');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductModelRule($id)
  {
    $rs = $this->db
    ->select('model_code AS code')
    ->where('id_rule', $id)
    ->get('discount_rule_product_model');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductSegmentRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_segment AS sr')
    ->join('product_segment AS ps', 'sr.segment_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductClassRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_class AS sr')
    ->join('product_class AS ps', 'sr.class_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductFamilyRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_family AS sr')
    ->join('product_family AS ps', 'sr.family_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductMainGroupRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_main_group AS sr')
    ->join('product_main_group AS ps', 'sr.main_group_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductGroupRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_group AS sr')
    ->join('product_group AS ps', 'sr.group_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductSportTypeRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_sport_type AS sr')
    ->join('product_sport_type AS ps', 'sr.sport_type_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductCollectionRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_collection AS sr')
    ->join('product_collection AS ps', 'sr.collection_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductTypeRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_type AS sr')
    ->join('product_type AS ps', 'sr.type_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductKindRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_kind AS sr')
    ->join('product_kind AS ps', 'sr.kind_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductGenderRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_gender AS sr')
    ->join('product_gender AS ps', 'sr.gender_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductBrandRule($id)
  {
    $rs = $this->db
    ->select('ps.code, ps.name')
    ->from('discount_rule_product_brand AS sr')
    ->join('product_brand AS ps', 'sr.brand_code = ps.code', 'left')
    ->where('sr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getProductYearRule($id)
  {
    $rs = $this->db
    ->select('year')
    ->where('id_rule', $id)
    ->get('discount_rule_product_year');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getFreeProductRule($id)
  {
    $rs = $this->db
    ->select('product_code AS code')
    ->where('id_rule', $id)
    ->get('discount_rule_free_product');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function getChannelsRule($id)
  {
    $rs = $this->db
    ->select('ch.name')
    ->from('discount_rule_channels AS cr')
    ->join('channels AS ch', 'cr.channels_code = ch.code', 'left')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getPaymentRule($id)
  {
    $rs = $this->db
    ->select('pm.name')
    ->from('discount_rule_payment AS cr')
    ->join('payment_method AS pm', 'cr.payment_code = pm.code')
    ->where('cr.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  /*
  |----------------------------------
  | BEGIN ใช้สำหรับหน้ากำหนดเงื่อนไข
  |----------------------------------
  */
  public function getRuleCustomerId($id)
  {
    $rs = $this->db
    ->select('r.*, c.name AS customer_name')
    ->from('discount_rule_customer AS r')
    ->join('customers AS c', 'r.customer_id = c.id', 'left')
    ->where('r.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getRuleCustomerGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->group_code] = $rd->group_code;
      }
    }
    return $sc;
  }


  public function getRuleCustomerType($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_type');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->type_code] = $rd->type_code;
      }
    }
    return $sc;
  }


  public function getRuleCustomerKind($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_kind');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->kind_code] = $rd->kind_code;
      }
    }
    return $sc;
  }


  public function getRuleCustomerArea($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_area');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->area_code] = $rd->area_code;
      }
    }

    return $sc;
  }


  public function getRuleCustomerClass($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_customer_class');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->class_code] = $rd->class_code;
      }
    }

    return $sc;
  }


  public function getRuleProduct($id)
  {
    $rs = $this->db
    ->select('r.*, p.name AS product_name')
    ->from('discount_rule_product AS r')
    ->join('products AS p', 'r.product_id = p.id', 'left')
    ->where('r.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getRuleProductModel($id)
  {
    $rs = $this->db
    ->select('r.*, p.name AS model_name')
    ->from('discount_rule_product_model AS r')
    ->join('product_model AS p', 'r.model_id = p.id', 'left')
    ->where('r.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getRuleProductMainGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_main_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->main_group_code] = $rd->main_group_code;
      }
    }

    return $sc;
  }


  public function getRuleProductGroup($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_group');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->group_code] = $rd->group_code;
      }
    }

    return $sc;
  }

  public function getRuleProductSegment($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_segment');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->segment_code] = $rd->segment_code;
      }
    }

    return $sc;
  }


  public function getRuleProductClass($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_class');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->class_code] = $rd->class_code;
      }
    }

    return $sc;
  }


  public function getRuleProductFamily($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_family');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->family_code] = $rd->family_code;
      }
    }

    return $sc;
  }


  public function getRuleProductType($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_type');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->type_code] = $rd->type_code;
      }
    }

    return $sc;
  }


  public function getRuleProductKind($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_kind');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->kind_code] = $rd->kind_code;
      }
    }

    return $sc;
  }


  public function getRuleProductGender($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_gender');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->gender_code] = $rd->gender_code;
      }
    }

    return $sc;
  }


  public function getRuleProductSportType($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_Sport_type');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->sport_type_code] = $rd->sport_type_code;
      }
    }

    return $sc;
  }


  public function getRuleProductCollection($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_collection');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->collection_code] = $rd->collection_code;
      }
    }

    return $sc;
  }


  public function getRuleProductYear($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_year');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->year] = $rd->year;
      }
    }

    return $sc;
  }


  public function getRuleProductBrand($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_product_brand');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->brand_code] = $rd->brand_code;
      }
    }

    return $sc;
  }


  public function getRuleChannels($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_channels');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->channels_code] = $rd->channels_code;
      }
    }

    return $sc;
  }


  public function getRulePayment($id)
  {
    $sc = array();
    $rs = $this->db->where('id_rule', $id)->get('discount_rule_payment');
    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[$rd->payment_code] = $rd->payment_code;
      }
    }

    return $sc;
  }


  public function getRuleFreeProduct($id)
  {
    $rs = $this->db
    ->select('r.*, p.name AS product_name')
    ->from('discount_rule_free_product AS r')
    ->join('products AS p', 'r.product_id = p.id', 'left')
    ->where('r.id_rule', $id)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //-------------- Discount Section -------------//
  public function add_free_product(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_free_product', $ds);
    }

    return FALSE;
  }


  public function drop_free_product($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_free_product');
  }


  //-------------- Customer Section ------------//
  public function add_customer(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer', $ds);
    }

    return FALSE;
  }


  public function add_customer_group(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer_group', $ds);
    }

    return FALSE;
  }


  public function add_customer_kind(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer_kind', $ds);
    }

    return FALSE;
  }


  public function add_customer_type(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer_type', $ds);
    }

    return FALSE;
  }


  public function add_customer_area(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer_area', $ds);
    }

    return FALSE;
  }


  public function add_customer_class(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_customer_class', $ds);
    }

    return FALSE;
  }


  public function drop_customer($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer');
  }


  public function drop_customer_group($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer_group');
  }


  public function drop_customer_kind($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer_kind');
  }


  public function drop_customer_type($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer_type');
  }


  public function drop_customer_area($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer_area');
  }


  public function drop_customer_class($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_customer_class');
  }


  //----------------------  Product Section ------------------//
  public function add_sku(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product', $ds);
    }

    return FALSE;
  }


  public function add_model(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_model', $ds);
    }

    return FALSE;
  }


  public function add_product_main_group(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_main_group', $ds);
    }

    return FALSE;
  }


  public function add_product_group(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_group', $ds);
    }

    return FALSE;
  }


  public function add_product_segment(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_segment', $ds);
    }

    return FALSE;
  }


  public function add_product_class(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_class', $ds);
    }

    return FALSE;
  }


  public function add_product_family(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_family', $ds);
    }

    return FALSE;
  }


  public function add_product_kind(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_kind', $ds);
    }

    return FALSE;
  }


  public function add_product_type(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_type', $ds);
    }

    return FALSE;
  }


  public function add_product_gender(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_gender', $ds);
    }

    return FALSE;
  }


  public function add_product_sport_type(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_sport_type', $ds);
    }

    return FALSE;
  }


  public function add_product_collection(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_collection', $ds);
    }

    return FALSE;
  }


  public function add_product_brand(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_brand', $ds);
    }

    return FALSE;
  }


  public function add_product_year(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_product_year', $ds);
    }

    return FALSE;
  }


  public function drop_sku($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product');
  }


  public function drop_model($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_model');
  }


  public function drop_product_main_group($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_main_group');
  }


  public function drop_product_group($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_group');
  }


  public function drop_product_segment($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_segment');
  }


  public function drop_product_class($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_class');
  }


  public function drop_product_family($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_family');
  }


  public function drop_product_kind($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_kind');
  }


  public function drop_product_type($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_type');
  }


  public function drop_product_gender($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_gender');
  }


  public function drop_product_sport_type($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_sport_type');
  }


  public function drop_product_collection($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_collection');
  }


  public function drop_product_brand($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_brand');
  }


  public function drop_product_year($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_product_year');
  }


  //--------------- Channels Section --------------///
  public function add_channels(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_channels', $ds);
    }

    return FALSE;
  }


  public function drop_channels($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_channels');
  }


  //----------------- Payment Section ------------------///
  public function add_payment(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert('discount_rule_payment', $ds);
    }

    return FALSE;
  }


  public function drop_payment($id)
  {
    return $this->db->where('id_rule', $id)->delete('discount_rule_payment');
  }

  /*
  |----------------------------------
  | END ใช้สำหรับหน้ากำหนดเงื่อนไข
  |----------------------------------
  */


  public function update_policy($id_rule, $id_policy)
  {
    return $this->db->set('id_policy', $id_policy)->where('id', $id_rule)->update('discount_rule');
  }



  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('discount_rule AS r')
    ->join('discount_policy AS p', 'r.id_policy = p.id', 'left')
    ->where('r.isDeleted', 0);

    if(isset($ds['code']) && $ds['code'] != "" && $ds['code'] != NULL)
    {
      $this->db
      ->group_start()
      ->like('r.code', $ds['code'])
      ->or_like('r.name', $ds['code'])
      ->group_end();
    }

    if(isset($ds['policy']) && $ds['policy'] != "" && $ds['policy'] != NULL)
    {
      $this->db
      ->group_start()
      ->like('p.code', $ds['policy'])
      ->or_like('p.name', $ds['policy'])
      ->group_end();
    }

    if(isset($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('r.type', $ds['type']);
    }

    if(isset($ds['rule_status']) && $ds['rule_status'] != "" && $ds['rule_status'] != NULL && $ds['rule_status'] != "all")
    {
      $this->db->where('r.active', $ds['rule_status']);
    }

    if(isset($ds['policy_status']) && $ds['policy_status'] != "" && $ds['policy_status'] != NULL && $ds['policy_status'] != "all")
    {
      $this->db->where('p.active', $ds['policy_status']);
    }

    return $this->db->count_all_results();
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, p.code AS policy_code, p.active AS policy_status')
    ->from('discount_rule AS r')
    ->join('discount_policy AS p', 'r.id_policy = p.id', 'left')
    ->where('r.isDeleted', 0);

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('r.code', $ds['code'])
      ->or_like('r.name', $ds['code'])
      ->group_end();
    }

    if(isset($ds['policy']) && $ds['policy'] != "" && $ds['policy'] != NULL)
    {
      $this->db
      ->group_start()
      ->like('p.code', $ds['policy'])
      ->or_like('p.name', $ds['policy'])
      ->group_end();
    }

    if(isset($ds['type']) && $ds['type'] != 'all')
    {
      $this->db->where('r.type', $ds['type']);
    }

    if(isset($ds['rule_status']) && $ds['rule_status'] != "" && $ds['rule_status'] != NULL && $ds['rule_status'] != "all")
    {
      $this->db->where('r.active', $ds['rule_status']);
    }

    if(isset($ds['policy_status']) && $ds['policy_status'] != "" && $ds['policy_status'] != NULL && $ds['policy_status'] != "all")
    {
      $this->db->where('p.active', $ds['policy_status']);
    }

    $rs = $this->db->order_by('r.code', 'DESC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_policy_rules($id_policy)
  {
    $rs = $this->db->where('id_policy', $id_policy)->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }




  public function get_active_rule()
  {
    $rs = $this->db->where('active', 1)->where('id_policy IS NULL')->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }



  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM discount_rule WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }



  public function search($txt)
  {
    $rs = $this->db->select('id')
    ->like('code', $txt)
    ->like('name', $txt)
    ->get('discount_rule');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  public function delete_rule($id)
  {
    //--- start transection
    $this->db->trans_start();

    //--- 1.
    $this->db->where('id_rule', $id)->delete('discount_rule_product_style');

    //--- 2.
    $this->db->where('id_rule', $id)->delete('discount_rule_product_group');

    //--- 3
    $this->db->where('id_rule', $id)->delete('discount_rule_product_sub_group');

    //--- 4
    $this->db->where('id_rule', $id)->delete('discount_rule_product_category');

    //--- 5
    $this->db->where('id_rule', $id)->delete('discount_rule_product_type');

    //--- 6
    $this->db->where('id_rule', $id)->delete('discount_rule_product_kind');

    //--- 7
    $this->db->where('id_rule', $id)->delete('discount_rule_product_brand');

    //--- 8
    $this->db->where('id_rule', $id)->delete('discount_rule_product_year');

    //--- 9
    $this->db->where('id', $id)->delete('discount_rule');

    //--- end transection
    $this->db->trans_complete();

    return $this->db->trans_status();
  }

} //--- end class

 ?>
