<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class answer_model extends model {
	public $table_name;
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->credit = pc_base::load_model('ask_credit_model');
		$this->db_setting = 'default';
		$this->table_name = 'ask_posts';
		$this->table_member = 'member';
		$this->siteid = get_siteid();
		$this->CATEGORYS = getcache('category_ask_'.$this->siteid,'commons');
		parent::__construct();
	} 
	
	//添加回答
	function add($id,$posts)
	{
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username',$SITE['name'].L('phpcms_friends'));
		$_userid = param::get_cookie('_userid');
		
		$id = intval($id);
		if(!$id || !is_array($posts)) return false;
		$posts['askid'] = $id;
		$posts['siteid']= $this->siteid;
		if($r) return false;
		$a = $this->db->get_one('catid',$this->db_tablepre."ask","askid=$id AND siteid=".$this->siteid);
		//检测表
		$posts_table_name = $this->posts_table($a['catid']);
		$this->db->table_name = $this->table_name = $posts_table_name;
		$r = $this->db->get_one("pid",$this->db->table_name,"askid=$id AND userid='$_userid'");
		$posts['catid'] = $a['catid'];
		$this->insert($posts);
		$this->db->update(array('answercount'=>'+=1'),$this->db_tablepre.$this->table_member,"userid='$_userid'");
		if($M['answer_give_credit'])
		{
			@extract($this->db->get_one("count(pid) AS num",$this->db->table_name,"userid='$_userid' AND isask=0"));
			$maxnum = floor($M['answer_max_credit']/$M['answer_give_credit']);
			if($num<=$maxnum)
			{
				$this->credit->update_credit($_userid, $_username, $M['answer_give_credit'], 1);
				pc_base::load_app_class('receipts','pay',0);
				receipts::point($M['answer_give_credit'],$_userid, $_username, $flag,'selfincome',L('reply_reward'),$_username);
			}
		}
		return $this->db->update(array('answercount'=>'+=1'),$this->db_tablepre."ask","askid='$id'");
	}
	
	//修改回答
	function edit($id, $posts, $userid)
	{
		$id = intval($id);
		$userid = intval($userid);
		if(!$id || !is_array($posts)) return false;
		if($userid) $sql = " AND userid=$userid";
		return $this->db->update($posts, $this->table_name, "pid=$id $sql");
	}
	//投票
	function vote($id,$pid)
	{
		$M = getcache('ask', 'commons');
		$M = $M[1];
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		
		$id = intval($id);
		$pid = intval($pid);
		$userid = intval($_userid);
		if(!$id || !$pid || !$userid) return false;
		$r = $this->db->get_one("count(voteid) AS num",$this->db_tablepre."ask_vote","askid=$id AND userid=$userid");
		if($r['num']>0) return false;
		$a = $this->db->get_one('catid',$this->db_tablepre."ask","askid=$id AND siteid=".$this->siteid);
		//检测表
		$posts_table_name = $this->posts_table($a['catid']);
		$this->table_name = $posts_table_name;
		$this->update(array('votecount'=>'+=1'),"pid=$pid");
		if($M['vote_give_credit'])
		{
			$maxnum = floor($M['vote_max_credit']/$M['vote_give_credit']);
			if($r['num']<=$maxnum)
			{
				$this->credit->update_credit($_userid, $_username, $M['vote_give_credit'], 1);
				pc_base::load_app_class('receipts','pay',0);
				receipts::point($M['vote_give_credit'],$_userid, $_username, $flag,'selfincome',L('votes_the_reward_integral'),$_username);
			}
		}
		$posts['askid'] = $id;
		$posts['pid'] = $pid;
		$posts['userid'] = $userid;
		$posts['addtime'] = SYS_TIME;
		return $this->db->insert($posts, $this->db_tablepre."ask_vote");
	}
	
	function exchange($askid,$ids, $flag = 0, $isvote = 0, $userid = 0)
	{
		$askid = intval($askid);
		$userid = intval($userid);
		if(!$askid) return false;
		if(!is_array($ids)) return false;
		if($isvote)
		{
			foreach($ids AS $id)
			{
				$this->db->update(array('candidate'=>'1'),$this->table_name,"pid=$id AND askid=$askid");
			}
		}
		$sql = '';
		if($userid) $sql = "AND userid=$userid";
		
		return $this->db->update(array('flag'=>$flag),$this->db_tablepre."ask","askid=$askid $sql");
	}
	
	//查看投票
	function vote_result($id)
	{
		$id = intval($id);
		@extract($this->db->get_one("sum(votecount) AS totalnum",$this->table_name,"askid=$id AND candidate=1 AND status=3"));
		if($totalnum==0) $totalnum = 1;
		$result = $this->db->select('*',$this->table_name,"askid='$id' AND candidate=1 AND siteid=".SITEID);
		foreach ($result as $r) {
			$r['width'] =round(($r['votecount']/$totalnum)*100,1)."%";
			$array[] = $r;
		}
		return $array;
	}
	
	//查看投票
	function posts_table($catid)
	{
		$catid = intval($catid);
		return $this->db_tablepre."ask_posts_".$this->CATEGORYS[$catid]['topparentid'];
	}
	
}
?>