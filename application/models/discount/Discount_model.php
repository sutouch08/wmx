<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Discount_model extends CI_Model
{
	private $dp = "discount_policy";
	private $dr = "discount_rule";

  public function __construct()
  {
    parent::__construct();
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/orders_model');
		$this->load->helper('discount');
  }


	public function getRuleCode($id)
	{
		$rs = $this->db->select('code')->where('id', $id)->get($this->dr);

		if($rs->num_rows() === 1)
		{
			return $rs->row()->code;
		}

		return NULL;
	}


	public function get_available_policy($date)
	{
		$date = $date == "" ? date('Y-m-d') : $date;

		$rs = $this->db->select('id')
		->where('active', 1)
		->where('start_date <=', $date)
		->where('end_date >=', $date)
		->get($this->dp);

		if($rs->num_rows() > 0)
		{
			$arr = array();

			foreach($rs->result() as $rd)
			{
				$arr[] = $rd->id;
			}

			return $arr;
		}

		return NULL;
	}


	public function get_rule_list(object $pd, object $cs, $price, $qty, $payment_code, $channels_code, $date, array $policy_ids = array())
	{
		if( ! empty($policy_ids))
		{
			$rs = $this->db
			->distinct()
			->select('r.*')
			->select('pl.code AS policy_code, pl.name AS policy_name')
			->from('discount_rule AS r')
			->join('discount_policy AS pl', 'r.id_policy = pl.id', 'left')
			->join('discount_rule_product AS p', 'r.id = p.id_rule', 'left')
			->join('discount_rule_product_model AS pm', 'r.id = pm.id_rule', 'left')
			->join('discount_rule_product_main_group AS pmg', 'r.id = pmg.id_rule', 'left')
			->join('discount_rule_product_group AS pg', 'r.id = pg.id_rule', 'left')
			->join('discount_rule_product_segment AS ps', 'r.id = ps.id_rule', 'left')
			->join('discount_rule_product_class AS pc', 'r.id = pc.id_rule', 'left')
			->join('discount_rule_product_family AS pf', 'r.id = pf.id_rule', 'left')
			->join('discount_rule_product_type AS pt', 'r.id = pt.id_rule', 'left')
			->join('discount_rule_product_kind AS pk', 'r.id = pk.id_rule', 'left')
			->join('discount_rule_product_gender AS pgd', 'r.id = pgd.id_rule', 'left')
			->join('discount_rule_product_sport_type AS pst', 'r.id = pst.id_rule', 'left')
			->join('discount_rule_product_collection AS pct', 'r.id = pct.id_rule', 'left')
			->join('discount_rule_product_brand AS pb', 'r.id = pb.id_rule', 'left')
			->join('discount_rule_product_year AS y', 'r.id = y.id_rule', 'left')
			->join('discount_rule_customer AS c', 'r.id = c.id_rule', 'left')
			->join('discount_rule_customer_group AS cg', 'r.id = cg.id_rule', 'left')
			->join('discount_rule_customer_type AS ct', 'r.id = ct.id_rule', 'left')
			->join('discount_rule_customer_kind AS ck', 'r.id = ck.id_rule', 'left')
			->join('discount_rule_customer_area AS ca', 'r.id = ca.id_rule', 'left')
			->join('discount_rule_customer_class AS g', 'r.id = g.id_rule', 'left')
			->join('discount_rule_channels AS ch', 'r.id = ch.id_rule', 'left')
			->join('discount_rule_payment AS py', 'r.id = py.id_rule', 'left')
			->where_in('id_policy', $policy_ids)
			->where('r.active', 1)
			->where('r.type !=', 'F')
			->group_start()->where('r.all_product', 1)->or_where('r.all_product', 0)->group_end()
			->group_start()->where('p.product_id IS NULL', NULL, FALSE)->or_where('p.product_id', $pd->id)->group_end()
			->group_start()->where('pm.model_code IS NULL', NULL, FALSE)->or_where('pm.model_code', $pd->model_code)->group_end()
			->group_start()->where('pmg.main_group_code IS NULL', NULL, FALSE)->or_where('pmg.main_group_code', $pd->main_group_code)->group_end()
			->group_start()->where('pg.group_code IS NULL', NULL, FALSE)->or_where('pg.group_code', $pd->group_code)->group_end()
			->group_start()->where('ps.segment_code IS NULL', NULL, FALSE)->or_where('ps.segment_code', $pd->segment_code)->group_end()
			->group_start()->where('pc.class_code IS NULL', NULL, FALSE)->or_where('pc.class_code', $pd->class_code)->group_end()
			->group_start()->where('pf.family_code IS NULL', NULL, FALSE)->or_where('pf.family_code', $pd->family_code)->group_end()
			->group_start()->where('pt.type_code IS NULL', NULL, FALSE)->or_where('pt.type_code', $pd->type_code)->group_end()
			->group_start()->where('pk.kind_code IS NULL', NULL, FALSE)->or_where('pk.kind_code', $pd->kind_code)->group_end()
			->group_start()->where('pgd.gender_code IS NULL', NULL, FALSE)->or_where('pgd.gender_code', $pd->gender_code)->group_end()
			->group_start()->where('pst.sport_type_code IS NULL', NULL, FALSE)->or_where('pst.sport_type_code', $pd->sport_type_code)->group_end()
			->group_start()->where('pct.collection_code IS NULL', NULL, FALSE)->or_where('pct.collection_code', $pd->collection_code)->group_end()
			->group_start()->where('pb.brand_code IS NULL', NULL, FALSE)->or_where('pb.brand_code', $pd->brand_code)->group_end()
			->group_start()->where('y.year IS NULL', NULL, FALSE)->or_where('y.year', $pd->year)->group_end()
			->group_start()->where('r.all_customer', 1)->or_where('r.all_customer', 0)->group_end()
			->group_start()->where('c.customer_id IS NULL', NULL, FALSE)->or_where('c.customer_id', $cs->id)->group_end()
			->group_start()->where('cg.group_code IS NULL', NULL, FALSE)->or_where('cg.group_code', $cs->group_code)->group_end()
			->group_start()->where('ct.type_code IS NULL', NULL, FALSE)->or_where('ct.type_code', $cs->type_code)->group_end()
			->group_start()->where('ck.kind_code IS NULL', NULL, FALSE)->or_where('ck.kind_code', $cs->kind_code)->group_end()
			->group_start()->where('ca.area_code IS NULL', NULL, FALSE)->or_where('ca.area_code', $cs->area_code)->group_end()
			->group_start()->where('g.class_code IS NULL', NULL, FALSE)->or_where('g.class_code', $cs->class_code)->group_end()
			->group_start()->where('ch.channels_code IS NULL', NULL, FALSE)->or_where('ch.channels_code', $channels_code)->group_end()
			->group_start()->where('py.payment_code IS NULL', NULL, FALSE)->or_where('py.payment_code', $payment_code)->group_end()
			->group_start()->where('r.minQty', 0)->or_where('r.minQty <=', $qty)->group_end()
			->group_start()->where('r.minAmount', 0)->or_where('r.minAmount <=', ($price * $qty))->group_end()
			->order_by('r.priority', 'DESC')
			->get();
			
			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_free_item_rule_list(object $pd, object $cs, $amount, $qty, $payment_code, $channels_code, $date, $can_group = 0, array $policy_ids = array())
	{
		if( ! empty($policy_ids))
		{
			$this->db
			->distinct()
			->select('r.*')
			->select('pl.code AS policy_code, pl.name AS policy_name')
			->from('discount_rule AS r')
			->join('discount_policy AS pl', 'r.id_policy = pl.id', 'left')
			->join('discount_rule_product AS p', 'r.id = p.id_rule', 'left')
			->join('discount_rule_product_model AS pm', 'r.id = pm.id_rule', 'left')
			->join('discount_rule_product_main_group AS pmg', 'r.id = pmg.id_rule', 'left')
			->join('discount_rule_product_group AS pg', 'r.id = pg.id_rule', 'left')
			->join('discount_rule_product_segment AS ps', 'r.id = ps.id_rule', 'left')
			->join('discount_rule_product_class AS pc', 'r.id = pc.id_rule', 'left')
			->join('discount_rule_product_family AS pf', 'r.id = pf.id_rule', 'left')
			->join('discount_rule_product_type AS pt', 'r.id = pt.id_rule', 'left')
			->join('discount_rule_product_kind AS pk', 'r.id = pk.id_rule', 'left')
			->join('discount_rule_product_gender AS pgd', 'r.id = pgd.id_rule', 'left')
			->join('discount_rule_product_sport_type AS pst', 'r.id = pst.id_rule', 'left')
			->join('discount_rule_product_collection AS pct', 'r.id = pct.id_rule', 'left')
			->join('discount_rule_product_brand AS pb', 'r.id = pb.id_rule', 'left')
			->join('discount_rule_product_year AS y', 'r.id = y.id_rule', 'left')
			->join('discount_rule_customer AS c', 'r.id = c.id_rule', 'left')
			->join('discount_rule_customer_group AS cg', 'r.id = cg.id_rule', 'left')
			->join('discount_rule_customer_type AS ct', 'r.id = ct.id_rule', 'left')
			->join('discount_rule_customer_kind AS ck', 'r.id = ck.id_rule', 'left')
			->join('discount_rule_customer_area AS ca', 'r.id = ca.id_rule', 'left')
			->join('discount_rule_customer_class AS g', 'r.id = g.id_rule', 'left')
			->join('discount_rule_channels AS ch', 'r.id = ch.id_rule', 'left')
			->join('discount_rule_payment AS py', 'r.id = py.id_rule', 'left')
			->where_in('id_policy', $policy_ids)
			->where('r.active', 1)
			->where('r.type', 'F');

			if($can_group)
			{
				$this->db->where('r.canGroup', 1);
			}

			$this->db
			->group_start()->where('r.all_product', 1)->or_where('r.all_product', 0)->group_end()
			->group_start()->where('p.product_id IS NULL', NULL, FALSE)->or_where('p.product_id', $pd->id)->group_end()
			->group_start()->where('pm.model_code IS NULL', NULL, FALSE)->or_where('pm.model_code', $pd->model_code)->group_end()
			->group_start()->where('pmg.main_group_code IS NULL', NULL, FALSE)->or_where('pmg.main_group_code', $pd->main_group_code)->group_end()
			->group_start()->where('pg.group_code IS NULL', NULL, FALSE)->or_where('pg.group_code', $pd->group_code)->group_end()
			->group_start()->where('ps.segment_code IS NULL', NULL, FALSE)->or_where('ps.segment_code', $pd->segment_code)->group_end()
			->group_start()->where('pc.class_code IS NULL', NULL, FALSE)->or_where('pc.class_code', $pd->class_code)->group_end()
			->group_start()->where('pf.family_code IS NULL', NULL, FALSE)->or_where('pf.family_code', $pd->family_code)->group_end()
			->group_start()->where('pt.type_code IS NULL', NULL, FALSE)->or_where('pt.type_code', $pd->type_code)->group_end()
			->group_start()->where('pk.kind_code IS NULL', NULL, FALSE)->or_where('pk.kind_code', $pd->kind_code)->group_end()
			->group_start()->where('pgd.gender_code IS NULL', NULL, FALSE)->or_where('pgd.gender_code', $pd->gender_code)->group_end()
			->group_start()->where('pst.sport_type_code IS NULL', NULL, FALSE)->or_where('pst.sport_type_code', $pd->sport_type_code)->group_end()
			->group_start()->where('pct.collection_code IS NULL', NULL, FALSE)->or_where('pct.collection_code', $pd->collection_code)->group_end()
			->group_start()->where('pb.brand_code IS NULL', NULL, FALSE)->or_where('pb.brand_code', $pd->brand_code)->group_end()
			->group_start()->where('y.year IS NULL', NULL, FALSE)->or_where('y.year', $pd->year)->group_end()
			->group_start()->where('r.all_customer', 1)->or_where('r.all_customer', 0)->group_end()
			->group_start()->where('c.customer_id IS NULL', NULL, FALSE)->or_where('c.customer_id', $cs->id)->group_end()
			->group_start()->where('cg.group_code IS NULL', NULL, FALSE)->or_where('cg.group_code', $cs->group_code)->group_end()
			->group_start()->where('ct.type_code IS NULL', NULL, FALSE)->or_where('ct.type_code', $cs->type_code)->group_end()
			->group_start()->where('ck.kind_code IS NULL', NULL, FALSE)->or_where('ck.kind_code', $cs->kind_code)->group_end()
			->group_start()->where('ca.area_code IS NULL', NULL, FALSE)->or_where('ca.area_code', $cs->area_code)->group_end()
			->group_start()->where('g.class_code IS NULL', NULL, FALSE)->or_where('g.class_code', $cs->class_code)->group_end()
			->group_start()->where('ch.channels_code IS NULL', NULL, FALSE)->or_where('ch.channels_code', $channels_code)->group_end()
			->group_start()->where('py.payment_code IS NULL', NULL, FALSE)->or_where('py.payment_code', $payment_code)->group_end()
			->group_start()->where('r.minQty', 0)->or_where('r.minQty <=', $qty)->group_end()
			->group_start()->where('r.minAmount', 0)->or_where('r.minAmount <=', $amount)->group_end()
			->order_by('r.priority', 'DESC');

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;
	}


	public function get_free_item_list($id_rule)
	{
		$rs = $this->db
		->select('product_id, product_code, id_rule, id_policy')
		->from('discount_rule_free_product AS fd')
		->join('discount_rule AS r', 'fd.id_rule = r.id', 'left')
		->where('fd.id_rule', $id_rule)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function policy_id_in(array $ds = array())
	{
		$ids = "0";

		if( ! empty($ds))
		{
			foreach($ds as $id)
			{
				$ids .= ", {$id}";
			}
		}

		return $ids;
	}
} //--- end class
?>
