<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class index {
private $db;
	function __construct() {
		pc_base::load_app_func('global');
		pc_base::load_sys_class('format','',0);
		$this->M = new_html_special_chars(getcache('ask', 'commons'));
		$this->db = pc_base::load_model('ask_model');
		$this->db2 = pc_base::load_model('answer_model');
		$this->db3 = pc_base::load_model('category_model');
		$this->db_m = pc_base::load_model('member_model');
		$this->db_actor = pc_base::load_model('ask_actor_model');
		$this->db_credit = pc_base::load_model('ask_credit_model');
		$siteid = get_siteid();
  		define("SITEID",$siteid);
	}
	
	public function init() {
		$siteid = SITEID;
 		$setting = getcache('ask', 'commons');
		$SEO = seo(SITEID, '', L('ask'), '', '');
		include template('ask', 'index');
	}
	
	public function show() {
		pc_base::load_sys_class('form','',0);
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username',L('phpcms_friends'));
		$_userid = param::get_cookie('_userid');
		$r_m = $this->db_m->get_one(array('userid'=>$_userid,'siteid'=>SITEID));
		$_point = $r_m['point'];
		
		$id = intval($_GET['id']);
		if(!$id) showmessage(L('illegal_parameters'), HTTP_REFERER);
		$array = array();
		$a = $this->db->get_one(array('askid'=>$id,'siteid'=>SITEID));
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		if (!$this->db2->table_exists(str_replace($this->db2->db_tablepre,'',$posts_table_name))) showmessage(L('info_does_not_exists'), HTTP_REFERER);
		$result = $this->db2->select("askid=$id AND status>2 AND siteid=".SITEID,'*');
		foreach ($result as $r) {
			if($r['isask']) {
				$arr = $this->db->get_one(array('askid'=>$id,'siteid'=>SITEID));
				$r['title'] = $arr['title'];
				$r['reward'] = $arr['reward'];
				$r['status'] = $arr['status'];
				$r['answercount'] = $arr['answercount'];
				$r['flag'] = $arr['flag'];
				$r['endtime'] = $arr['endtime'];
				$r['catid'] = $arr['catid'];
				$r['hits'] = $arr['hits'];
				$r['anonymity'] = $arr['anonymity'];
				$rs['keywords'] = $arr['keywords'];
				$r['keywords'] = explode(' ',$arr['keywords']);
				
				if((SYS_TIME>$r['endtime']) && !$arr['ischeck'])
				{
					$this->db->update(array('ischeck'=>'1'),array('askid'=>$id));
					$this->db->update(array('flag'=>'3'),'askid='.$id.' AND answercount>1');
					$this->db2->update(array('candidate'=>'1'),array('askid'=>$id));
					$this->db_credit->update($arr['userid'], $arr['username'], $M['del_day15_credit'], 0);
					pc_base::load_app_class('spend','pay',0);
					spend::point($M['del_day15_credit'], L('ask_15days_no_deal_with'), $arr['userid'], $arr['username'], '', '', $flag);
				}
			}
			$userids[] = $r['userid'];
			$array[] = $r;
		}
		if($userids)
		{
			$userids = implode(',',$userids);
			$data = $this->db_m->select("userid IN ($userids) AND siteid=".SITEID,'*');
			foreach($data AS $r)
			{
				$userinfo[$r['userid']]['actortype'] = $r['actortype'];
				$userinfo[$r['userid']]['point'] = $r['point'];
			}
			foreach($array AS $arr)
			{
				$arr['actortype'] = $userinfo[$arr['userid']]['actortype'];
				$arr['point'] = $userinfo[$arr['userid']]['point'];
				$_array[] = $arr;
			}
			$array = $_array;
		}
		else
		{
			$array = $array;
		}
		
		
		if($array)
		{
			
			$have_answer = false;
			foreach($array AS $k=>$v)
			{
				if($v['isask'])
				{
					$title = $v['title'];
					$message = $this->M[1]['use_editor'] ? $v['message'] : trim_textarea($v['message']);
					$reward = $v['reward'];
					$userid = $v['userid'];
					$username = $v['username'];
					$nickname = get_nickname($v['userid']);
					$status = $v['status'];
					$hits = $v['hits'];
					$flag = $v['flag'];
					$addtime = $v['addtime'];
					$actor = actor($v['actortype'], $v['point']);
					$answercount = $v['answercount'];
					$result = count_down($v['endtime']);
					$day = $result[0];
					$hour = $result[1];
					$minute = $result[2];
					$catid = $v['catid'];
					$anonymity = $v['anonymity'];
					$keywords = $v['keywords'];
				}
				elseif($v['optimal'])
				{
					$best_answer_pid = $v['pid'];
					$best_answer_vote_1 = $v['best_answer_vote_1'];
					$best_answer_vote_2 = $v['best_answer_vote_2'];
					$totalnum = $best_answer_vote_1+$best_answer_vote_2;
					$best_answer_vote_1_per = round(($best_answer_vote_1/$totalnum)*100,1)."%";
					$best_answer_vote_2_per = round(($best_answer_vote_2/$totalnum)*100,1)."%";
					$solvetime = $v['solvetime'];
					$answer = $v['message'];
					$answertime = $v['addtime'];
					$answer = trim_textarea($v['message']);
					$optimail_username = $v['username'];
					$optimail_userid = $v['userid'];
					$optimail_nickname = get_nickname($v['userid']);
					$optimal_actor = actor($v['actortype'], $v['point']);
				}
				else
				{
					if($v['userid'] == $_userid) $have_answer = true;
					$infos[$k]['pid'] = $v['pid'];
					$infos[$k]['userid'] = $v['userid'];
					$infos[$k]['username'] = $v['username'];
					$infos[$k]['nickname'] = get_nickname($v['userid']);
					$infos[$k]['addtime'] = $v['addtime'];
					$infos[$k]['candidate'] = $v['candidate'];
					$infos[$k]['anonymity'] = $v['anonymity'];
					$infos[$k]['actor'] = actor($v['actortype'], $v['point']);
					$infos[$k]['message'] = $this->M[1]['use_editor'] ? $v['message'] : trim_textarea($v['message']);
				}
			}
			if($v['optimal'])
			{
				$answercount = $answercount-1;
			}
			if($userid == $_userid)
			{
				$isask = 1;
			}
			else
			{
				$isask = 0;
			}
			if(isset($action) && $action == 'vote')
			{
				if($flag==1) exit;
				$tpl = 'vote';
			}
			else
			{
				$tpl = 'show';
			}
			if($status==1) showmessage(L('info_does_not_exists'), HTTP_REFERER);
			if($userid!=$_userid){
				$this->db->update(array('hits'=>'+=1'),array('askid'=>$id));
			}
			$description = $answer ? str_cut($answer,'200') :str_cut($message,'200');
			$SEO = seo(SITEID, $catid, $title, $description, $seo_keywords);
			include template('ask', $tpl);
		}else{
			showmessage(L('info_does_not_exists'), HTTP_REFERER);
		}
	}
	public function lists() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		
		$catid = intval($_GET['catid']);
		if(!$catid) showmessage(L('category_not_exists'),'blank');
		$siteids = getcache('category_ask','commons');
		$siteid = $siteids[$catid];
		$CATEGORYS = getcache('category_ask_'.$siteid,'commons');
		if(!isset($CATEGORYS[$catid])) showmessage(L('category_not_exists'),'blank');
		$CAT = $CATEGORYS[$catid];
		$siteid = $GLOBALS['siteid'] = $CAT['siteid'];
		extract($CAT);
		$setting = string2array($setting);
		//SEO
		if(!$setting['meta_title']) $setting['meta_title'] = $catname;
		$SEO = seo($siteid, '',$setting['meta_title'],$setting['meta_description'],$setting['meta_keywords']);
		define('STYLE',$setting['template_list']);
		$page = $_GET['page'];
		
		$arrparentid = explode(',', $arrparentid);
		$top_parentid = $arrparentid[1] ? $arrparentid[1] : $catid;
		$array_child = array();
		$self_array = explode(',', $arrchildid);
		//获取一级栏目ids
		foreach ($self_array as $arr) {
			if($arr!=$catid && $CATEGORYS[$arr][parentid]==$catid) {
				$array_child[] = $arr;
			}
		}
		$arrchildid = implode(',', $array_child);

		
		include template('ask', 'list');
	}
	
	public function browse() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		
		$CATEGORYS = getcache('category_ask_'.get_siteid(),'commons');
		//SEO
		if(!$setting['meta_title']) $setting['meta_title'] = $catname;
		
		if($_GET['type']=='nosolve'){
			$meta_title = $this_name = "待解决问题";
		}elseif($_GET['type']=='solve'){
			$meta_title = $this_name = "已解决问题";
		}elseif($_GET['type']=='vote'){
			$meta_title = $this_name = "投票中的问题";
		}elseif($_GET['type']=='high'){
			$meta_title = $this_name = "高分问题";
		}else{
			$meta_title = $this_name = "全部问题";
		}
		$SEO = seo(get_siteid(), '',$meta_title."_问吧",$setting['meta_description'],$setting['meta_keywords']);
		$page = $_GET['page'];
		
		include template('ask', 'browse');
	}
	
	public function post_answer() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : '';
		$SITE = siteinfo(SITEID);
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		if (!$_userid) {
			$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
			showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
		}
		
		if($_POST['dosubmit']){
			
			if(!$id) showmessage(L('illegal_parameters'), HTTP_REFERER);
			if (!$_userid) {
				showmessage(L('please_login'), APP_PATH.'index.php?m=member&c=index&a=login');
			}
			if ($M['answer_code']) {
				$session_storage = 'session_'.pc_base::load_config('system','session_storage');
				pc_base::load_sys_class($session_storage);
				session_start();
				$code = isset($_POST['code']) && trim($_POST['code']) ? strtolower(trim($_POST['code'])) : showmessage(L('please_enter_code'), HTTP_REFERER);
				if ($code != $_SESSION['code']) {
					showmessage(L('code_error'), HTTP_REFERER);
				}
			}
			$posts['message'] = isset($_POST['message']) && trim($_POST['message']) ? trim($_POST['message']) : showmessage(L('please_enter_content'), HTTP_REFERER);
			if(strlen($posts['message'])>1000) showmessage(L('answer_limit_1000'), HTTP_REFERER);
			
			$posts['userid'] = $_userid;
			$posts['username'] = $_username;
			if($M['answer_check'])
			{
				$posts['status'] = 1;
			}
			else
			{
				$posts['status'] = 3;
			}
			$posts['addtime'] = SYS_TIME;
			$posts['message'] = $M['use_editor'] ? strip_tags($posts['message']) : strip_tags($posts['message']);
			if($this->db2->add($id,$posts))
			{
				$forward = ask_url($catid,$id);
				if($M['answer_check'])
				{
					showmessage(L('waitting_admin_check'),$forward);
				}
				else
				{
					showmessage(L('your_answer_submit_success'),$forward);
				}
			}
			else
			{
				showmessage(L('answer_has_answered_and_ischecking'),$forward);
			}
		}
	}
	
	public function question() {
		pc_base::load_sys_class('form','',0);
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username',L('phpcms_friends'));
		$_userid = param::get_cookie('_userid');
		$pc_hash = $_SESSION['pc_hash'];
		$r_m = $this->db_m->get_one(array('userid'=>$_userid,'siteid'=>SITEID));
		$_point = $r_m['point'];
		
		if (!$_userid) {
			$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
			showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
		}
		
		if($_POST['dosubmit'])
		{
			if (!$_userid) {
				$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
				showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
			}
			if ($M['publish_code']) {
				$session_storage = 'session_'.pc_base::load_config('system','session_storage');
				pc_base::load_sys_class($session_storage);
				session_start();
				$code = isset($_POST['code']) && trim($_POST['code']) ? strtolower(trim($_POST['code'])) : showmessage(L('please_enter_code'), HTTP_REFERER);
				if ($code != $_SESSION['code']) {
					showmessage(L('code_error'), HTTP_REFERER);
				}
			}
			
			$info['catid'] = $posts['catid'] = intval($_POST['info']['catid']);
			$info['reward'] = intval($_POST['info']['reward']);
			$info['title'] = htmlspecialchars($_POST['info']['title']);
			$info['anonymity'] = intval($_POST['info']['anonymity']);
			if($info['title'] == '') showmessage(L('title_no_allow_blank'), HTTP_REFERER);
			if(!$info['catid']) showmessage(L('select_category'), HTTP_REFERER);
			if($info['reward'] > $_point) showmessage(L('credit_is_poor'), HTTP_REFERER);
			$posts['isask'] = 1;
			foreach($info AS $key=>$val)
			{
				if(!in_array($key,array('title','catid','reward','anonymity'))) unset($info[$key]);
			}
			$posts['message'] = isset($_POST['message']) && trim($_POST['message']) ? trim($_POST['message']) : showmessage(L('please_enter_content'), HTTP_REFERER);
			if(strlen($posts['message'])>1000) showmessage(L('answer_limit_1000'), HTTP_REFERER);
			$info['addtime'] = $posts['addtime'] = SYS_TIME;
			$info['endtime'] = SYS_TIME+1296000;
			$info['userid'] = $posts['userid'] = $_userid;
			$info['username'] = $posts['username'] = $_username;

			if($info['anonymity'] && intval($M['anybody_score']+$info['reward'])>$_point)
			{
				$info['anonymity'] = 0;
			}
			
			
			if($M['publish_check'])
			{
				$info['status'] = $posts['status'] = 1;
				$id = $this->db->add($info,$posts);
				$url = "index.php?m=ask&c=index";
				showmessage(L('waiting_check'),$url);
			}
			else
			{
				$info['status'] = $posts['status'] = 3;
				if($info['reward'] >= $M['height_score']) $info['flag'] = 2;
				$id = $this->db->add($info,$posts);
				$url = ask_url($info['catid'],$id);
				showmessage(L('publish_success'),$url);
			}
		}
		
		$title = stripslashes($_POST['title']);
		$SEO['title'] = "我要提问";
		include template('ask', 'question');
		
	}
	
	public function vote() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		if (!$_userid) {
			$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
			showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
		}
		$id = intval($_GET['id']);
		$a = $this->db->get_one("askid=$id AND siteid=".SITEID,'catid');
		//检测表
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		$result = $this->db2->select("askid=$id AND status>2 AND isask=0 AND siteid=".SITEID,'pid');
		foreach ($result as $r) {
			$pids[] = $r['pid'];
		}
		if($this->db2->exchange($id,$pids, 1, 1, $_userid))
		{
			showmessage(L('exchange_ask_to_vote'),ask_url($catid,$id));
		}else{
			showmessage(L('submit_failure'), HTTP_REFERER);
		}
	}
	
	//查看投票
	public function view_vote() {
		$id = intval($_GET['id']);
		$r = $this->db->get_one("askid=$id AND flag=1 AND status>2","title,catid");
		if(!$r)
		{
			echo L('illegal_parameters');
			exit;
		}
		//检测表
		$posts_table_name = $this->db2->posts_table($r['catid']);
		$this->db2->table_name = $posts_table_name;
		$infos = $this->db2->vote_result($id);
		$num = $this->db2->count("askid='$id' AND isask=0 AND candidate=1 AND siteid=".SITEID);
		include template('ask', 'view_vote');
	}
	

	
}
?>