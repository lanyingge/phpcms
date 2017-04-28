<?php
defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

class tag {
	private $db;
	function __construct() {
		pc_base::load_app_func('global');
		$this->db = pc_base::load_model('ask_model');
	}
	/**
	 * 按照模型搜索
	 */
	public function init() {
		$catid = intval($_GET['catid']);
		$siteid = get_siteid();
		$this->categorys = getcache('category_ask_'.$siteid,'commons');
		if($catid && !isset($this->categorys[$catid])) showmessage(L('missing_part_parameters'));
		if(isset($_GET['tag']) && trim($_GET['tag']) != '') {
			$tag = safe_replace(strip_tags($_GET['tag']));
		} else {
			showmessage(L('illegal_operation'));
		}
		$CATEGORYS = $this->categorys;

		$siteurl = siteurl($siteid);
		$page = $_GET['page'];
		$datas = $infos = array();
		if($_GET['type']=='nosolve'){
			$sql = " AND status = 3 ";
		}elseif($_GET['type']=='solve'){
			$sql = " AND status = 5 ";
		}elseif($_GET['type']=='vote'){
			$sql = " AND flag = 1 ";
		}elseif($_GET['type']=='high'){
			$sql = " AND flag = 2 ";
		}else{
			$sql = " AND status > 2 ";
		}
		$infos = $this->db->listinfo("`keywords` LIKE '%$tag%' $sql",'askid DESC',$page,20);
		$total = $this->db->number;
		if($total>0) {
			$pages = $this->db->pages;
			foreach($infos as $_v) {
				if(strpos($_v['url'],'://')===false) $_v['url'] = $siteurl.$_v['url'];
				$datas[] = $_v;
			}
		}
		$SEO = seo($siteid, $catid, $tag);
		include template('ask','tag');
	}
}
?>