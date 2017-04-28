<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class query {
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
	
	//设为最佳答案
	public function accept_answer() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$id = intval($_GET['id']);
		$pid = intval($_GET['pid']);
		$this->db->accept_answer($id, $pid);
		$forward = $forward ? $forward : '';
		showmessage(L('optimal_answer'), ask_url($catid,$id));
	}
	
	//修改回复
	public function editanswer() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		if($_POST['dosubmit'])
		{
			$askid = intval($_POST['askid']);
			$pid = intval($_POST['pid']);
			$a = $this->db->get_one(array('askid'=>$askid,'siteid'=>SITEID),'catid,status');
			if($a['status']>4) showmessage(L('no_edit'), HTTP_REFERER);
			$posts_table_name = $this->db2->posts_table($a['catid']);
			$this->db2->table_name = $posts_table_name;
			$r = $this->db2->get_one(array('askid'=>$askid,'pid'=>$pid,'siteid'=>SITEID));
			if($r['userid']!=$_userid) showmessage(L('no_edit_other_people_info'), HTTP_REFERER);
			if(strlen($_POST['answertext']) > 10000) showmessage(L('answer_limit_1000'), HTTP_REFERER);
			$posts['message'] = $M['use_editor'] ? $_POST['answertext'] : strip_tags($_POST['answertext']);
			$this->db2->edit($pid, $posts, $_userid);
			showmessage(L('operation_success'), ask_url($catid,$askid)."#p".$pid);
		}
	}
	
	//增加悬赏
	public function addscore() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		if($_POST['dosubmit'])
		{
			$id = intval($_POST['id']);
			$point = intval($_POST['point']);
			$this->db->addscore($id, $point);
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	
	//结束问题
	public function over() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		$id = intval($_GET['id']);
		$this->db->status($id, 6, $_userid);
		
		if($M['return_credit'])
		{
			extract($this->db->get_one("askid='$id'","userid,ischeck",$this->table_name));
			if($userid!=$_userid) showmessage(L('no_edit_other_people_info'), HTTP_REFERER);
			if($ischeck){
				pc_base::load_app_class('receipts','pay',0);
				receipts::point($M['return_credit'],$userid, $username, $flag,'selfincome',L('return_credit'),$username);
			}
		}
		showmessage(L('operation_success'), HTTP_REFERER);

	}
	
	//投票
	public function vote() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		if (!$_userid) {
			$forward= isset($_GET['forward']) ?  urlencode($_GET['forward']) : urlencode(get_url());
			showmessage(L('please_login', '', 'member'), '?m=member&c=index&a=login&forward='.$forward);
		}
		
		$id = intval($_POST['id']);
		$pid = intval($_POST['pid']);
		
		
		$a = $this->db->get_one("askid=$id AND siteid=".SITEID,'catid');
		//检测表
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		
		$r = $this->db2->get_one("pid='$pid'","candidate,status");
		if($r['candidate']!=1 || $r['status']<3)
		{	
			echo L('submit_failure');
			exit;
		}
		
		if($this->db2->vote($id, $pid))
		{
			echo L('thinks_your_vote');
		}
		else
		{
			echo L('your_have_vote');
		}

	}
	
	
	//给最佳答案投票
	public function best_answer_vote() {
		$id = intval($_POST['id']);
		$pid = intval($_POST['pid']);
		$method_id = intval($_POST['result']);
		if(!in_array($method_id,array(1,2))) 
		{
			echo L('submit_failure');
			exit;
		}
		
		$cookies = param::get_cookie('best_answer_vote_pids');
		$cookie = explode(',', $cookies);
		if (in_array($pid, $cookie)) {
			echo L('your_have_vote_best_answer');
			exit;
		}
		
		$a = $this->db->get_one("askid=$id AND siteid=".SITEID,'catid,status');
		if($a['status']!=5)
		{
			echo L('submit_failure');
			exit;
		}
		//检测表
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		
		$r = $this->db2->get_one("pid='$pid' AND optimal=1","candidate,status,optimal");
		if($r['candidate']==1 || $r['status']<3 || $r['optimal']!=1 || !$r)
		{	
			echo L('submit_failure');
			exit;
		}
		
		$result = $this->db2->update(array('best_answer_vote_'.$method_id=>'+=1'),"pid=$pid AND optimal=1 AND siteid=".SITEID);
		if($result)
		{
			param::set_cookie('best_answer_vote_pids', $cookies.','.$pid);
			echo L('thinks_your_vote');
		}
		else
		{
			echo L('your_have_vote');
		}

	}
	
	
	

	
}
?>