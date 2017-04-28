<?php

define('ASK_PATH', APP_PATH.'index.php?m=zsask');//问答首页路径
define('ASK_LIST', APP_PATH.'index.php?m=zsask&a=qlist&cid=');//问答分类列表页路径
define('ASK_SHOW', APP_PATH.'index.php?m=zsask&a=answer&qid=');//问答显示页路径
define('ASK_QSTN', APP_PATH.'index.php?m=zsask&a=question&cid='); //提问页路径

/*根据回答取得问题
 * $qid
 */
function get_qinfo($qid) {
	if (!$qid) return '';
	$db = pc_base::load_model('ask_question_model');
	$rs = $db->get_one(array('qid'=>$qid));
	return $rs;
}


	/*
	 * 取得栏目路径
	 * $catid
	 */
	function get_catpath($catid = '') {
		$askcategorys = getcache('categorys', 'zsask');
		$catpath = '';
		if ($catid) {
			$catname = $askcategorys[$catid]['catname'];
			$parentid = $askcategorys[$catid]['parentid'];
			$catpath = ' > <a href="'.ASK_LIST.$catid.'">'.$catname.'</a>';
			if ($parentid) {
				$_catname = $askcategorys[$parentid]['catname'];
				$_parentid = $askcategorys[$parentid]['parentid'];
				$catpath = ' > <a href="'.ASK_LIST.$parentid.'">'.$_catname.'</a>'.$catpath;
				if ($_parentid) {
					$_catname = $askcategorys[$_parentid]['catname'];
					$_parentid = $askcategorys[$_parentid]['parentid'];
					$catpath = '> <a href="'.ASK_LIST.$_parentid.'">'.$_catname.'</a>'.$catpath;
				}
			}
		}
		return $catpath;
	}	

	/*
	 * 获取子栏目ID
	 * $parentid
	 * 返回 catid IN ();
	 */
	function get_catids($parentid = 0) {
		$cdb = pc_base::load_model('ask_category_model');
		$where = 1;
		if ($parentid) {
			$rs = $cdb->select(array('parentid'=>$parentid), 'catid');
			
			if (empty($rs)) {
				$where = " catid=$parentid";
			}else {
				foreach ($rs as $v) {
					$r[] = $v['catid'];
				}
				$_where = to_sqls($r, '', 'parentid');
				$_rs = $cdb->select($_where, 'catid');
				
				if (is_array($_rs)) {
					foreach ($_rs as $v) {
						$r[] = $v['catid'];
					}
				}
				$where = to_sqls($r, '', 'catid');
			}
		}
		return $where;
	}


	/*
	 * 统计栏目数据
	 * $catid 
	 */
	function get_question($catid = 0) {
		$db = pc_base::load_model('ask_question_model');
		$where = get_catids($catid);
		$where .= ' AND status !=1';
		$count = $db->count($where);
		return $count;
	}
	
	function get_lastanswer($qid) {
		$db = pc_base::load_model('ask_answer_model');
		//$sql = 'qid='.$qid.' AND status !=1';
		$rs = $db->get_one(array('qid'=>$qid), '*', 'addtime DESC');
		return $rs['addtime'];
	}

	function get_answer($qid) {
		$db = pc_base::load_model('ask_answer_model');
		$sql = 'qid='.$qid.' AND status !=1';
		$count = $db->count($sql);
		return $count;
	}

	
	function get_comment($qid, $aid = '') {
		$db = pc_base::load_model('ask_comment_model');
		if ($aid) {
			$sql = 'qid='.$qid.' AND aid='.$aid.' AND status !=1';
			$count = $db->count($sql);
		}else {
			$count = $db->count(array('qid'=>$qid));
		}
		return $count;
	}

	function get_cinfo($qid, $aid) {
		$db = pc_base::load_model('ask_comment_model');
		$sql = 'qid='.$qid.' AND aid='.$aid.' AND status !=1';
		$infos = $db->select($sql);
		return $infos;
	}


/*
 * 
 * 追问列表
 */

	function add_answer($aid, $qid) {
		$db = pc_base::load_model('ask_answer_model');
		
		$rs = $db->select(array('zid'=>$aid, 'qid'=>$qid), '*', '', 'aid ASC');
		
		return $rs;
	}



/*
 * 追问列表
 * 弃用递归调用的方法，改用以zid时间排序的方法。
 * 
 */
	function __add_answer($aid, $f = 0, $answerarr) {
	
		$db = pc_base::load_model('ask_answer_model');
		$rs = $db->get_one(array('zid'=>$aid));
		if (!$rs && $f ==0) return FALSE;
		if ($rs['zid']) {
			$f +=1;
			$answerarr[$f] = $rs;
			return add_answer($rs['aid'], $f, $answerarr);
		}else {
			return $answerarr; 
		}
	}




/*
 * 返回父栏目ID和栏目层级
 * 
 * $str string （parentid,grade）
 * 
 */
 	function get_pidgd($str) {
  		if (!$str) {
  			$grade = 1; $_parentid = 0;
  		}else {
  			$grade = intval(substr($str, -1, 1) +1);
  			if ($grade >3 || $grade <0) {
  				$_parentid = 0;
  				$grade = 1;
  			}else{
  				$_parentid = intval(substr($str, 0, -2));
  			}
  		}
  		$arr[] = $_parentid;
  		$arr[] = $grade;
  		return $arr;
	 }


/*
 * 问答分类
 */

function get_category($parentid = 0, $grade = 2, $style = 0, $format = '├─ ', $icon = '│ ', $iconz = '└─ ', $nbsp = '') {
	if ($style) $nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$db = pc_base::load_model('ask_category_model');
	$where = array('parentid' =>$parentid);
	$categorys = $db->select($where, '*', '', 'listorder ASC');
	$count = count($categorys);
	foreach ((array)$categorys as $k => $v) { 
		if ($v['grade'] ==2) {
			$_format = $nbsp.$format;
			if ($count ==$k+1) $_format = $nbsp.$iconz;
		}elseif ($v['grade'] ==3) {
			$_format = $nbsp.$icon.$nbsp.$format;
			if ($count ==$k+1) $_format = $nbsp.$icon.$nbsp.$iconz;
		}
		if ($style ==1) {
			if ($v['grade'] <3 && $v['grade'] >0) {
				$is_add = '<a href="?m=zsask&c=zsask&a=add&pid='.$v['catid'].','.$v['grade'].'">添加子栏目</a> | ';
			}else {
				$is_add = '<a style="color:#999">添加子栏目</a> | ';
			}
			if ($v['status'] == 99) {
				$display = '<span id="dspl_'.$v['catid'].'"><a href="javascript:;" onclick="d_isplay(\''.$v['catid'].'\', 0)">禁用</a></span> | ';
			}else {
				$display = '<span id="dspl_'.$v['catid'].'"><a href="javascript:;" onclick="d_isplay(\''.$v['catid'].'\', 1)" style="color:red">启用</a></span> | ';
			}
			echo $_tree = '<tr>
					<td align="center">
					<input type="text" class="input-text-c input-text" value="'.$v['listorder'].'" size="3" name="listorders['.$v['catid'].']"></td>
					<td align="center">'.$v['catid'].'</td>
					<td>'.$_format.$v['catname'].'</td>
					<td align="center">
					<a target="_blank" href="#">访问</a></td>
					<td align="center">
					'.$is_add.'
					'.$display.'
					<a href="?m=zsask&c=zsask&a=edit&catid='.$v['catid'].'">修改</a> | 
					<a href="javascript:confirmurl(\'?m=zsask&c=zsask&a=delete&catid='.$v['catid'].'\',\'确认要删除 『 '.$v['catname'].' 』 吗？\')">删除</a>
					</td>
				</tr>';
		}else {
			echo $_tree = '<option value="'.$v['catid'].','.$v['grade'].'">'.$_format.$v['catname'].'</option>';
		}
		if ($v['grade'] <$grade) get_category($v['catid'], $grade, $style);
		
	}
	
}

/*
 * 检查配置
 * $name
 */
function check_config($name) {
	
}

/*
 * 检查用户是否已经登陆
 */
function check_member($userid = '') {
	
	if (!$userid) showmessage('请先登陆！');
	$auth = param::get_cookie('auth');
	$member =  pc_base::load_model('member_model');
	$auth_key = md5(pc_base::load_config('system', 'auth_key').$_SERVER['HTTP_USER_AGENT']);
	list($userid, $password) = explode("\t", sys_auth($auth, 'DECODE', $auth_key));
	$rs = $member->get_one(array('userid'=>$userid, 'password'=>$password));
	if (!$rs) showmessage('会话过期，请重新登陆！');
	
}

/*
 * 1、验证码验证
 * 2、返回管理员userid
 * 3、返回pc_hash
 */
function session_code($code, $userid = 0, $pc_hash = 0) {
	$session_storage = 'session_'.pc_base::load_config('system','session_storage');
	pc_base::load_sys_class($session_storage);
	session_start();
	if ($userid) return $_SESSION['userid'];
	if ($pc_hash) return $_SESSION['pc_hash'];
	if (empty($code)) return '1';//('请输入验证吗！');
	if ($code != $_SESSION['code']) {
		return '2';//exit('验证码错误！')
	}else {
		return '3'; //验证码正确
	}
		
}

/*
 * 获取文章点击数量
 *
 * $id  int 
 * $modelid int 模型id
 */

function get_counts($id, $modelid, $module = '') {
	if (intval($id) && intval($modelid)) {
		$hits_db = pc_base::load_model('hits_model');
		$_hitsid = 'c-'.$modelid.'-'.$id;
		$rs = $hits_db->get_one(array('hitsid'=>$_hitsid));
		//echo $value.'<br>'.$rs['ename'];exit;
		return $rs['views'];
	}elseif($module) {
		$hits_db = pc_base::load_model('hits_model');
		$_hitsid = $module.'-'.$id;
		$rs = $hits_db->get_one(array('hitsid'=>$_hitsid));
		//echo $value.'<br>'.$rs['ename'];exit;
		return $rs['views'];
	}else {
		return '';
	}
}

/*
 * 日期人性化
 * 今天，昨天，前天
 * int $time 10整数型时间
 * int $show 显示方式
 */

function format_date($time, $show = 0) {
	if (strlen($time) !=10) return $time;
	$Y  = date('Y');
	$m  = date('m');
	$d  = date('d');
	$tY = date('Y', $time);
	$tm = date('m', $time);
	$td = date('d', $time);
	$format = date('Y-m-d H:i:s', $time);
	$formatT = date('H:i', $time);
	if ($Y == $tY) {//年份相等
		
		if ($m == $tm) {//月份相等
			
			if ($d == $td) {//当天
				
				return '今天：'.$formatT;
			}elseif (($d-1) == $td) {
				return '昨天：'.$formatT;
			}elseif (($d-2) == $td) {
				return '前天：'.$formatT;
			}else {
				$cd = $d - $td;
				return $cd.' 天前 '.$formatT;
			}
			
		}else {
			return $format;
		}
		
	}else {
		return $format;
	}
}


?>