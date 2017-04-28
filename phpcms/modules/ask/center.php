<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class center {
private $db;
	function __construct() {
		pc_base::load_app_func('global');
		pc_base::load_sys_class('format','',0);
		pc_base::load_sys_class('form','',0);
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
	
	//我的提问
	public function ask() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$CATEGORYS = getcache('category_ask_'.SITEID,'commons');
		
		if(isset($_GET['status'])) $status = intval($_GET['status']);
		if(isset($_GET['flag'])) $flag = intval($_GET['flag']);
		$sql = 'userid='.$_userid.' AND siteid='.SITEID;
		if($status == 3 || $status == 5)
		{
			$sql .= " AND status=".$status;
		}
		else if($flag == 1)
		{
			$sql .= ' AND flag=1';
		}
		else if($flag == -1)
		{
			$endtime = SYS_TIME-1296000;
			$sql .= ' AND status = 3 AND addtime<'.$endtime;
		}
		$page = max(intval($_GET['page']),1);
		$infos = $this->db->listinfo($sql, 'askid DESC', $page, 20);
		$pages = $this->db->pages;
		$SEO['title'] = '提问管理_问吧_'.$PHPCMS['sitename'];
		include template('ask', 'center_ask');
	}
	
	//我的回答
	public function answer() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$CATEGORYS = getcache('category_ask_'.SITEID,'commons');
		$_GET['catid'] = intval($_GET['catid']);
		
		if(!$_GET['catid']){
			$categorys = $this->db3->select(array('siteid'=>SITEID,'module'=>'ask','parentid'=>'0'),'catid,catname,siteid',20000,'listorder ASC');
			foreach ($categorys as $r) {
				//检测表
				$posts_table_name = $this->db2->posts_table($r['catid']);
				$this->db2->table_name = $posts_table_name;
				$p = $this->db2->get_one("userid='$_userid' AND isask=0","count(pid) AS num");
				$cat_posts_num[$r[catid]] = $p[num];
			}
		}
		else
		{
			//检测表
			$posts_table_name = $this->db2->posts_table(intval($_GET['catid']));
			$this->db2->table_name = $posts_table_name;
			$sql = "userid='$_userid' AND isask=0 AND catid IN(".$CATEGORYS[$_GET[catid]][arrchildid].") AND siteid=".SITEID;
			$page = max(intval($_GET['page']),1);
			$infos = $this->db2->listinfo($sql, 'pid DESC', $page, 20);
			$pages = $this->db2->pages;
		}
		include template('ask', 'center_answer');
	}
	
	//头衔管理
	public function actor() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		if($_POST['dosubmit'])
		{	
			$actortype = intval($_POST[info]['actortype']);
			$this->db_m->query("UPDATE ".$this->db_m->db_tablepre."member SET actortype=$actortype WHERE userid='$_userid'");
			$forward = "index.php?m=ask&c=center&a=actor";
			showmessage(L('operation_success'),$forward);
		}else{
			$array = $this->db_m->get_one("userid=$_userid","*");
			$actortype = $array['actortype'];
			$TYPES = explode("\n", $M['member_group']);
			
			$ACTOR = getcache('actor_'.SITEID,'ask');
			include template('ask', 'center_actor');
		}
		
	}
	
	//我的积分
	public function credit() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		$asknumber = $this->db->getnumber($_userid,0);
		$answernumber = $this->db->getnumber($_userid,1);
		$result = $this->db_credit->get($_userid);
		@extract($result);
		$array = $this->db_m->get_one("userid=$_userid","*");
		$actortype = $array['actortype'];
		$_point = $array['point'];
		include template('ask', 'center_credit');
	}
	
	public function edit() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$CATEGORYS = getcache('category_ask_'.SITEID,'commons');
		$pc_hash = $_SESSION['pc_hash'];
		$r_m = $this->db_m->get_one(array('userid'=>$_userid,'siteid'=>SITEID));
		$_point = $r_m['point'];
		
		if(isset($_GET['job'])) $job = $_GET['job'];
		if(isset($_GET['id'])) $id = $_GET['id'];
		if(isset($_GET['pid'])) $pid = $_GET['pid'];
		
		if($job=='ask')
		{
			if($_POST['dosubmit'])
			{
				if(!$id) showmessage(L('illegal_parameters'), HTTP_REFERER);
				if (!$_userid) {
				showmessage(L('please_login'), APP_PATH.'index.php?m=member&c=index&a=login');
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
				
				$info['catid'] = intval($_POST['info']['catid']);
				$info['reward'] = intval($_POST['info']['reward']);
				$info['title'] = htmlspecialchars($_POST['info']['title']);
				$info['anonymity'] = intval($_POST['info']['anonymity']);
				if($info['title'] == '') showmessage(L('title_no_allow_blank'), HTTP_REFERER);
				if(!$info['catid']) showmessage(L('select_category'), HTTP_REFERER);
				if($info['reward'] > $_point) showmessage(L('credit_is_poor'), HTTP_REFERER);
				$posts['message'] = isset($_POST['message']) && trim($_POST['message']) ? trim($_POST['message']) : showmessage(L('please_enter_content'), HTTP_REFERER);
				if(strlen($posts['message'])>1000) showmessage(L('answer_limit_1000'), HTTP_REFERER);
				
				if($M['publish_check'])
				{
					$info['status'] = $posts['status'] = 1;
					$forward = "index.php?m=ask&c=index";
				}else{
					$forward = ask_url($info['catid'],$id);
					$info['status'] = $posts['status'] = 3;
					if($info['reward'] >= $M['height_score']) $info['flag'] = 2;
				}	
				
				if($this->db->edit($id, $info, $posts, $_userid)){
					showmessage(L('operation_success'),$forward);
				}else{
					$forward = ask_url($info['catid'],$id);
					showmessage("操作失败",$forward);
				}
				
			}
			else
			{
				$r = $this->db->get_one("askid=$id","*",$this->table_name);
				if(!$r) showmessage('提问不存在');
				//检测表
				$posts_table_name = $this->db2->posts_table($r['catid']);
				$this->db2->table_name = $posts_table_name;
				$r2 = $this->db2->get_one("askid=$id AND isask=1 AND userid=$_userid","*",$this->db_tablepre.$this->table_name);
				unset($r2['anonymity']);
				$r = $r2 ? array_merge($r,$r2) : $r;
				if(!$r) showmessage('提问不存在');
				extract($r);
				if($status>3) showmessage(L('no_edit'), HTTP_REFERER);
				include template('ask', 'center_edit');
			}
		}
		else
		{
			if($_POST['dosubmit'])
			{
				$r = $this->db->get_one("askid=$id","*",$this->table_name);
				if(!$r) showmessage('提问不存在');
				if($r['status']>3) showmessage(L('no_edit'), HTTP_REFERER);
				
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
				
				
				//检测表
				$posts_table_name = $this->db2->posts_table($r['catid']);
				$this->db2->table_name = $posts_table_name;
				$this->db2->update(array('message'=>$posts[message]),"pid=$pid AND userid='$_userid' AND siteid=".SITEID);
				$forward = "index.php?m=ask&c=center&a=answer";
				showmessage(L('operation_success'),$forward);
			}
			else
			{	
				$r = $this->db->get_one("askid=$id","*",$this->table_name);
				if(!$r) showmessage('提问不存在');
				//检测表
				$posts_table_name = $this->db2->posts_table($r['catid']);
				$this->db2->table_name = $posts_table_name;
				$r2 = $this->db2->get_one("askid=$id AND isask=0 AND pid=$pid AND userid=$_userid","*");
				if($r['status']>3) showmessage(L('no_edit'), HTTP_REFERER);
				unset($r['status']);
				unset($r['anonymity']);
				$r = $r2 ? array_merge($r,$r2) : $r;
				if(!$r) showmessage('提问不存在');
				extract($r);
				
				include template('ask', 'center_edit');
			}

		}
	}
	
	
	

	
}
?>