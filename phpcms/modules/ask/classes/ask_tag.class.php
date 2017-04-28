<?php
class ask_tag {
	private $db;
	public function __construct() {
		$this->db = pc_base::load_model('ask_model');
		$this->db2 = pc_base::load_model('answer_model');
		$this->db_credit = pc_base::load_model('ask_credit_model');
		$this->category = getcache('category_ask_'.get_siteid(),'commons');
	}
	/**
	 * 初始化模型
	 * @param $catid
	 */

	/**
	 * 分页统计
	 * @param $data
	 */
	public function count($data) {
		if($data['action'] == 'lists') {
			$catid = intval($data['catid']);
			$status = intval($data['status']);
			$flag = intval($data['flag']);
			if(isset($data['where'])) {
				$sql = $data['where'];
			} else {
				$sql = '';
				$sql = " siteid = ".get_siteid();
				if($catid){
					if($this->category[$catid]['child']) {
						$catids_str = $this->category[$catid]['arrchildid'];
						$pos = strpos($catids_str,',')+1;
						$catids_str = substr($catids_str, $pos);
						$sql .= " AND catid IN ($catids_str)";
					} else {
						$sql .= " AND catid='$catid'";
					}
				}
				$sql .= intval($data['thumb']) ? " AND thumb != ''" : '';
				$sql .= intval($data['status']) ? " AND status ='$status'" : ' AND status >2';
				$sql .= intval($data['flag']) ? " AND flag = 1" : '';
			}
			return $this->db->count($sql);
		}
	}
	
	/**
	 * 问题列表页标签
	 * @param $data
	 */
	public function lists($data) {
		$catid = intval($data['catid']);
		$status = intval($data['status']);
		$flag = intval($data['flag']);
		if(isset($data['where'])) {
			$sql = $data['where'];
		} else {
			$sql = '';
			$sql = " siteid = ".get_siteid();
			if($catid){
				if($this->category[$catid]['child']) {
					$catids_str = $this->category[$catid]['arrchildid'];
					$pos = strpos($catids_str,',')+1;
					$catids_str = substr($catids_str, $pos);
					$sql .= " AND catid IN ($catids_str)";
				} else {
					$sql .= " AND catid='$catid'";
				}
			}
			$sql .= intval($data['thumb']) ? " AND thumb != ''" : '';
			$sql .= intval($data['status']) ? " AND status ='$status'" : ' AND status >2';
			$sql .= intval($data['flag']) ? " AND flag = 1" : '';
		}
		
		
		$order = $data['order'];

		$return = $this->db->select($sql, '*', $data['limit'], $order, '', 'askid');
						
		//调用副表的数据
		if (isset($data['moreinfo']) && intval($data['moreinfo']) == 1) {
			$ids = array();
			foreach ($return as $v) {
				if (isset($v['askid']) && !empty($v['askid'])) {
					$ids[] = $v['askid'];
				} else {
					continue;
				}
			}
			if (!empty($ids)) {
				$this->db->table_name = $this->db->table_name.'_posts';
				$ids = implode('\',\'', $ids);
				$r = $this->db->select("`askid` IN ('$ids')", '*', '', '', '', 'pid');
				if (!empty($r)) {
					foreach ($r as $k=>$v) {
						if (isset($return[$k])) $return[$k] = array_merge($v, $return[$k]);
					}
				}
			}
		}
		return $return;
	}
	
	/**
	 * 相关文章标签
	 * @param $data
	 */
	public function relation($data) {
		$catid = intval($data['catid']);
		$order = $data['order'];
		$sql = "`status` > 2";
		$limit = $data['id'] ? $data['limit']+1 : $data['limit'];
		if($data['relation']) {
			$relations = explode('|',$data['relation']);
			$relations = array_diff($relations, array(null));
			$relations = implode(',',$relations);
			$sql = " `askid` IN ($relations)";
			$key_array = $this->db->select($sql, '*', $limit, $order,'','id');
		} elseif($data['keywords']) {
			$keywords = str_replace('%', '',$data['keywords']);
			$keywords_arr = explode(' ',$keywords);
			$key_array = array();
			$number = 0;
			$i =1;
			foreach ($keywords_arr as $_k) {
				$sql2 = $sql." AND `keywords` LIKE '%$_k%'".(isset($data['id']) && intval($data['id']) ? " AND `askid` != '".abs(intval($data['id']))."'" : '');
				$r = $this->db->select($sql2, '*', $limit, '','','askid');
				$number += count($r);
				foreach ($r as $id=>$v) {
					if($i<= $data['limit'] && !in_array($id, $key_array)) $key_array[$id] = $v;
					$i++;
				}
				if($data['limit']<$number) break;
			}
		}
		if($data['id']) unset($key_array[$data['id']]);
		return $key_array;
	}
	
	/**
	 * 排行榜标签
	 * @param $data
	 */
	public function hits($data) {
		$catid = intval($data['catid']);

		$sql = $desc = $ids = '';
		$array = $ids_array = array();
		$order = $data['order'];
		$sql = "username!=''";
		$result = $this->db_credit->select($sql, '*', $data['limit'] , $order, '');
		
		foreach ($result as $id) {
			$array[] = $id;
		}
		return $array;
	}
	/**
	 * 栏目标签
	 * @param $data
	 */
	public function category($data) {
		$data['catid'] = intval($data['catid']);
		$array = array();
		$siteid = $data['siteid'] && intval($data['siteid']) ? intval($data['siteid']) : get_siteid();
		$categorys = getcache('category_ask_'.$siteid,'commons');
		$site = siteinfo($siteid);
		$i = 1;
		foreach ($categorys as $catid=>$cat) {
			if($i>$data['limit']) break;
			if((!$cat['ismenu']) || $siteid && $cat['siteid']!=$siteid) continue;
			if (strpos($cat['url'], '://') === false) {
				$cat['url'] = substr($site['domain'],0,-1).$cat['url'];
			}
			if($cat['parentid']==$data['catid']) {
				$array[$catid] = $cat;
				$i++;
			}
		}
		return $array;
	}
	
	/**
	 * 可视化标签
	 */
	public function pc_tag() {
		pc_base::load_app_func('global');
		$positionlist = getcache('position','commons');
		$sites = pc_base::load_app_class('sites','admin');
		$sitelist = $sites->pc_tag_list();
		
		foreach ($positionlist as $_v) if($_v['siteid'] == get_siteid() || $_v['siteid'] == 0) $poslist[$_v['posid']] = $_v['name'];
		return array(
			'action'=>array('lists'=>L('list','', 'ask'),'position'=>L('position','', 'ask'), 'category'=>L('subcat', '', 'ask'), 'relation'=>L('related_articles', '', 'ask'), 'hits'=>L('top', '', 'ask')),
			'lists'=>array(
				'catid'=>array('name'=>L('catid', '', 'ask'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>1)),
				'order'=>array('name'=>L('sort', '', 'ask'), 'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc', '', 'ask'), 'updatetime DESC'=>L('updatetime_desc', '', 'ask'), 'listorder ASC'=>L('listorder_asc', '', 'ask'))),
				'thumb'=>array('name'=>L('thumb', '', 'ask'), 'htmltype'=>'radio','data'=>array('0'=>L('all_list', '', 'ask'), '1'=>L('thumb_list', '', 'ask'))),
				'moreinfo'=>array('name'=>L('moreinfo', '', 'ask'), 'htmltype'=>'radio', 'data'=>array('1'=>L('yes'), '0'=>L('no')))
			),
			'position'=>array(
				'posid'=>array('name'=>L('posid', '', 'ask'),'htmltype'=>'input_select','data'=>$poslist,'validator'=>array('min'=>1)),
				'catid'=>array('name'=>L('catid', '', 'ask'),'htmltype'=>'input_select_category','data'=>array('type'=>0),'validator'=>array('min'=>0)),
				'thumb'=>array('name'=>L('thumb', '', 'ask'), 'htmltype'=>'radio','data'=>array('0'=>L('all_list', '', 'ask'), '1'=>L('thumb_list', '', 'ask'))),			
				'order'=>array('name'=>L('sort', '', 'ask'), 'htmltype'=>'select','data'=>array('listorder DESC'=>L('listorder_desc', '', 'ask'),'listorder ASC'=>L('listorder_asc', '', 'ask'),'id DESC'=>L('id_desc', '', 'ask'))),
			),
			'category'=>array(
				'siteid'=>array('name'=>L('siteid'), 'htmltype'=>'input_select', 'data'=>$sitelist),
				'catid'=>array('name'=>L('catid', '', 'ask'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0))
			),
			'relation'=>array(
				'catid'=>array('name'=>L('catid', '', 'ask'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0), 'validator'=>array('min'=>1)),
				'order'=>array('name'=>L('sort', '', 'ask'), 'htmltype'=>'select','data'=>array('id DESC'=>L('id_desc', '', 'ask'), 'updatetime DESC'=>L('updatetime_desc', '', 'ask'), 'listorder ASC'=>L('listorder_asc', '', 'ask'))),
				'relation'=>array('name'=>L('relevant_articles_id', '', 'ask'), 'htmltype'=>'input'),
				'keywords'=>array('name'=>L('key_word', '', 'ask'), 'htmltype'=>'input')
			),
			'hits'=>array(
				'catid'=>array('name'=>L('catid', '', 'ask'), 'htmltype'=>'input_select_category', 'data'=>array('type'=>0), 'validator'=>array('min'=>1)),
				'day'=>array('name'=>L('day_select', '', 'ask'), 'htmltype'=>'input', 'data'=>array('type'=>0)),
			),
				
		);
	}
}