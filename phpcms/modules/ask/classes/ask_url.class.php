<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class ask_url{
	private $urlrules,$categorys;
	public function __construct() {
		$this->urlrules = getcache('urlrules','commons');
		$this->M = new_html_special_chars(getcache('ask', 'commons'));
		self::set_siteid();
		$this->categorys = getcache('category_ask_'.$this->siteid,'commons');
		$this->html_root = pc_base::load_config('system','html_root');
	}

	/**
	 * 内容页链接
	 * @param $askid 内容id
	 * @param $page 当前页
	 * @param $catid 栏目id
	 * @param $time 添加时间
	 * @return array 0=>url , 1=>生成路径
	 */
	public function show($askid, $page = 0, $catid = 0, $time = 0) {
		$page = max($page,1);
		$urls = $catdir = '';
		$show_ruleid = $this->M[1]['show_ruleid'];
		$urlrules = $this->urlrules[$show_ruleid];
		if(!$time) $time = SYS_TIME;
		$urlrules_arr = explode('|',$urlrules);
		if($page==1) {
			$urlrule = $urlrules_arr[0];
		} else {
			$urlrule = isset($urlrules_arr[1]) ? $urlrules_arr[1] : $urlrules_arr[0];
		}
		$domain_dir = '';
		$categorydir = $this->get_categorydir($catid);
		$catdir = $category['catdir'];
		$year = date('Y',$time);
		$month = date('m',$time);
		$day = date('d',$time);
		
		$urls = str_replace(array('{$categorydir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$id}','{$page}'),array($categorydir,$catdir,$year,$month,$day,$catid,$askid,$page),$urlrule);
		$url_arr[0] = $url_arr[1] = APP_PATH.$urls;
		return $url_arr;
	}
	
	/**
	 * 获取栏目的访问路径
	 * 在修复栏目路径处重建目录结构用
	 * @param intval $catid 栏目ID
	 * @param intval $page 页数
	 */
	public function category_url($catid, $page = 1) {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		
		$category = $this->categorys[$catid];
		if($category['type']==2) return $category['url'];
		$page = max(intval($page), 1);
		$setting = string2array($category['setting']);
		$category_ruleid = $setting['category_ruleid'];
		$urlrules = $this->urlrules[$M['category_ruleid']];
		$urlrules_arr = explode('|',$urlrules);
		if ($page==1) {
			$urlrule = $urlrules_arr[0];
		} else {
			$urlrule = $urlrules_arr[1];
		}
		$url = str_replace(array('{$catid}', '{$page}'), array($catid, $page), $urlrule);
		if (strpos($url, '\\')!==false) {
				$url = APP_PATH.str_replace('\\', '/', $url);
		}
		
		if (in_array(basename($url), array('index.html', 'index.htm', 'index.shtml'))) {
			$url = dirname($url).'/';
		}
		if (strpos($url, '://')===false) $url = str_replace('//', '/', $url);
		if(strpos($url, '/')===0) $url = substr($url,1);
		return $url;
	}
	/**
	 * 生成列表页分页地址
	 * @param $ruleid 角色id
	 * @param $categorydir 父栏目路径
	 * @param $catdir 栏目路径
	 * @param $catid 栏目id
	 * @param $page 当前页
	 */
	public function get_list_url($ruleid,$categorydir, $catdir, $catid, $page = 1) {
		$urlrules = $this->urlrules[$ruleid];
		$urlrules_arr = explode('|',$urlrules);
		if ($page==1) {
			$urlrule = $urlrules_arr[0];
		} else {
			$urlrule = $urlrules_arr[1];
		}
		$urls = str_replace(array('{$categorydir}','{$catdir}','{$year}','{$month}','{$day}','{$catid}','{$page}'),array($categorydir,$catdir,$year,$month,$day,$catid,$page),$urlrule);
		return $urls;
	}
	
	/**
	 * 获取父栏目路径
	 * @param $catid
	 * @param $dir
	 */
	private function get_categorydir($catid, $dir = '') {
		$setting = array();
		$setting = string2array($this->categorys[$catid]['setting']);
		if ($setting['create_to_html_root']) return $dir;
		if ($this->categorys[$catid]['parentid']) {
			$dir = $this->categorys[$this->categorys[$catid]['parentid']]['catdir'].'/'.$dir;
			return $this->get_categorydir($this->categorys[$catid]['parentid'], $dir);
		} else {
			return $dir;
		}
	}
	/**
	 * 设置当前站点
	 */
	private function set_siteid() {
		if(defined('IN_ADMIN')) {
			$this->siteid = get_siteid();
		} else {
			param::get_cookie('siteid');
			$this->siteid = param::get_cookie('siteid');
		}
	}
}