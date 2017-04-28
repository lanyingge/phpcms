<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class ask_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->credit = pc_base::load_model('ask_credit_model');
		$this->db2 = pc_base::load_model('answer_model');
		$this->db3 = pc_base::load_model('category_model');
		$this->db_setting = 'default';
		$this->table_name = 'ask';
		$this->table_posts = 'ask_posts';
		$this->siteid = get_siteid();
		$this->categorys = getcache('category_ask_'.$this->siteid,'commons');
		parent::__construct();
	} 
	
	//添加问题
	function add($info,$posts)
	{
		$M = getcache('ask', 'commons');
		$M = $M[1];
		if(!is_array($info) || !is_array($posts)) return false;
		$info['siteid'] = $posts['siteid']= $this->siteid;
		$this->db->insert($info,$this->table_name);
		$posts['askid'] = $this->db->insert_id();
		
		
		if($info['reward'] || $info['anonymity']){
			pc_base::load_app_class('spend','pay',0);
		}
		if($info['reward']>0)
		{
			$this->credit->update_credit($info['userid'], $info['username'], $info['reward'], 0);
			
			spend::point($info['reward'], L('reword_diff'), $info['userid'], $info['username'], '', '', $flag);
		}
		if($info['anonymity'])
		{
			$this->credit->update_credit($info['userid'], $info['username'], $M['anybody_score'], 0);
			spend::point($M['anybody_score'], L('anonymous_diff'), $info['userid'], $info['username'], '', '', $flag);
		}
		//检测表
		$posts_table_name = $this->db2->posts_table($info['catid']);
		$this->db2->table_name = $posts_table_name;
		$this->db2->insert($posts);
		$this->db->update(array('items'=>'+=1'),$this->db_tablepre."category","catid='$info[catid]'");
		return $posts['askid'];
	}
	
	//修改问题
	function edit($id, $info, $posts, $userid = 0)
	{
		$id = intval($id);
		if(!$id || !is_array($info) || !is_array($posts)) return false;
		$this->check_filed($info,array('catid','title','reward','anonymity','flag','status'));
		$this->check_filed($posts,array('message','status'));
		if($userid) $sql = " AND userid=$userid AND status<4";
		$this->update($info,"askid=$id $sql");
		$a = $this->get_one(array('askid'=>$id,'siteid'=>$this->siteid));
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		return $this->db2->update($posts, "askid=$id AND isask=1 $sql");
	}
	
	function check_filed($data,$fields)
	{
		foreach($data AS $k=>$v)
		{
			if(!in_array($k,$fields)) showmessage('无权修改'.$k.'字段');
		}
	}
	
	//设为最佳答案
	function accept_answer($id, $pid)
	{
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		$id = intval($id);
		$pid = intval($pid);
		if(!$id || !$pid) return false;
		$a = $this->db->get_one("userid,catid,status,reward",$this->table_name,"askid='$id'");
		if(!$a) return false;
		if($a['status']>4) showmessage(L('no_edit'), HTTP_REFERER);
		if($a['userid']!=$_userid) showmessage(L('no_edit_other_people_info'), HTTP_REFERER);
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		$this->status($id,5);
		$this->db2->update(array('optimal'=>1,'solvetime'=>SYS_TIME),"pid=$pid AND askid=$id");
		$r = $this->db2->get_one("pid=$pid","userid,username");
		$this->db->update(array('acceptcount'=>'+=1'),$this->db_tablepre."member","userid=$r[userid]");
		
		if($M['answer_bounty_credit'] || $M['return_credit']){
			pc_base::load_app_class('receipts','pay',0);
		}
		
		if($M['answer_bounty_credit'])
		{
			$this->credit->update_credit($r['userid'], $r['username'], $M['answer_bounty_credit'], 1);
			receipts::point($M['answer_bounty_credit'],$r['userid'], $r['username'], $flag,'selfincome',L('answer_bounty_credit'),$r['username']);
		}
		if($M['return_credit'])
		{
			@extract($this->db->get_one("userid,username,ischeck",$this->table_name,"askid=$id"));
			if($ischeck)
			{
				$this->credit->update_credit($userid, $username, $M['return_credit'], 1);
				receipts::point($M['return_credit'],$userid, $username, $flag,'selfincome',L('return_credit'),$username);
			}
		}
		if(intval($a['reward'])){
			$this->credit->update_credit($r['userid'], $r['username'], intval($a['reward']), 1);
			receipts::point($a['reward'],$r['userid'], $r['username'], $flag,'selfincome',L('reward_score'),$r['username']);
		}
		return true;
	}
	
	function getnumber($userid, $flag = 0)
	{
		$userid = intval($userid);
		$sql = $num = '';
		if($flag)
		{
			$sql = ' AND isask=0';
		}else{
			$sql = ' AND isask=1';
		}
		$categorys = $this->db3->select(array('siteid'=>$this->siteid,'module'=>'ask','parentid'=>'0'),'catid,siteid',20000,'listorder ASC');
		foreach ($categorys as $r) {
			//检测表
			$posts_table_name = $this->db2->posts_table($r['catid']);
			$this->db2->table_name = $posts_table_name;
			$r = $this->db2->get_one("userid='$userid' $sql","count(pid) AS num");
			$num = $num + $r['num'];
		}
		
		return $num;
	}
	
	function status($id, $status, $userid = 0)
	{
		$id = intval($id);
		$status = intval($status);
		if($userid) $sql = " AND userid='$userid'";
		$this->db->update(array('status'=>$status),$this->table_name,"askid='$id' $sql");
		return true;
	}
	
	function addscore($id, $point = 0)
	{
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$r_m = $this->db->get_one("point",$this->db_tablepre."member","userid='$_userid'");
		if(!$r_m) return false;
		$_point = $r_m['point'];
		if($point > $_point) return false;
		$id = intval($id);
		$point = intval($point);
		
		$r = $this->db->get_one("userid,username",$this->table_name,"askid='$id'");
		if($r['userid']!=$_userid) showmessage(L('no_edit_other_people_info'), HTTP_REFERER);
		
		$this->db->update("reward=reward+$point,endtime=endtime+432000",$this->table_name,"askid='$id' AND userid=$_userid");
		$this->db->update("flag=2",$this->table_name,"askid=$id AND flag=0 AND reward >= $M[height_score]");

		$this->credit->update_credit($_userid,  $_username, $point, 0);
		pc_base::load_app_class('spend','pay',0);
		spend::point($point, L('enhances_credit'), $_userid, $_username, '', '', $flag);
		return true;
	}
	
	
}
?>