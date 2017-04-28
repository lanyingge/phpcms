<?php
	function implodeids($array, $s = ',')
	{
		if(empty($array)) return '';
		return is_array($array) ? implode($s, $array) : $array;
	}
	//功能：倒计时计算
	function count_down($unix_timestamp)
	{ 
		$date = $unix_timestamp-time(); 
		$day = $date/86400; 
		$days = intval($day); 
		$hour = $date/3600 - $days*24; 
		$hours = intval($hour); 
		$minute = $date/60 - $days*1440 - $hours*60; 
		$minutes = intval($minute); 
		$second = $date - $days*86400 - $hours*3600 - $minutes*60; 
		$seconds = intval($second); 
		$result = array($days,$hours,$minutes,$seconds); 
		return $result; 
	}
	function actor($actortype = 0, $credit = 0)
	{
		$ACTOR = getcache('actor_'.get_siteid(),'ask');
		$actortype = intval($actortype);
		foreach($ACTOR[$actortype] As $k=>$v)
		{
			if($credit >= $v['min'] && $credit <= $v['max'])
			{
				$data = $v['grade'].' '.$v['actor'];
			}
			elseif($credit>$v['max'])
			{
				$data = $v['grade'].' '.$v['actor'];
			}
		}
		return $data;
	}
	function ask_url($catid,$askid)
	{
		$url = pc_base::load_app_class('ask_url', 'ask');
		$urls = $url->show($askid, 0, $catid, '');
		return $urls[0];
	}
	function ask_cat_url($catid)
	{
		$catid = intval($catid);
		if (!$catid) return false;
		$url = pc_base::load_app_class('ask_url', 'ask');
		$urls = $url->category_url($catid);
		return $urls;
	}
	/**
 * 当前路径 
 * 返回指定栏目路径层级
 * @param $catid 栏目id
 * @param $symbol 栏目间隔符
 */
function catpos_ask($catid, $symbol=' > '){
	$category_arr = array();
	$siteids = getcache('category_ask','commons');
	$siteid = $siteids[$catid];
	$category_arr = getcache('category_ask_'.$siteid,'commons');
	if(!isset($category_arr[$catid])) return '';
	$pos = '';
	$siteurl = siteurl($category_arr[$catid]['siteid']);
	$arrparentid = array_filter(explode(',', $category_arr[$catid]['arrparentid'].','.$catid));
	foreach($arrparentid as $catid) {
		$url = $category_arr[$catid]['url'];
		if(strpos($url, '://') === false) $url = $siteurl.$url;
		$pos .= '<a href="'.$url.'">'.$category_arr[$catid]['catname'].'</a>'.$symbol;
	}
	return $pos;
}
?>