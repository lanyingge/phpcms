<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class ask_credit_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_m = pc_base::load_model('member_model');
		$this->db_setting = 'default';
		$this->table_name = 'ask_credit';
		$this->table_member = 'member';
		parent::__construct();
		$this->siteid = get_siteid();
	}
	
	function get($userid)
	{
		$userid = intval($userid);
		$result = $this->db->get_one("*",$this->table_name,"userid=$userid");
		return $result;
	}
	
	function listinfos($where = '', $order = '', $page = 1, $pagesize = 50, $flags = 0)
	{
		if(!isset($ACTOR)) $ACTOR = getcache('actor_'.get_siteid(),'ask');
		if($where) $where = " WHERE $where";
		if($order) $order = " ORDER BY $order";
		$page = max(intval($page), 1);
        $offset = $pagesize*($page-1);
        $limit = " LIMIT $offset, $pagesize";
		$r = $this->get_one('',' COUNT(*) AS num');
        $number = $r['number'];
        $this->db->pages;
		$array = array();
		$i = 1;
		$result = $this->db->query("SELECT * FROM $this->table_name $where $order $limit");
		$data = $this->fetch_array($result);
		foreach($data as $r) 
		{
			$userids[] = $userid = $r['userid'];
			$r['orderid'] = $i;
			$_array[] = $array[$userid] = $r;
			$i++;
		}

		if($userids != '')
		{
			$userids = implodeids($userids);
			$data = $this->db_m->listinfo("userid IN ($userids)");
			foreach($data as $r)
			{
				$userid = $r['userid'];
				$credit = $r['point'];
				$r['lastdate'] = date('Y-m-d H:i',$r['lastdate']);

				foreach($ACTOR[$r['actortype']] As $k=>$v)
				{
					if($credit >= $v['min'] && $credit <= $v['max'])
					{
						$r['grade'] = $v['grade'].' '.$v['actor'];
					}
					elseif($credit>$v['max'])
					{
						$r['grade'] = $v['grade'].' '.$v['actor'];
					}
				}
				if($flags)
				{
					$_info[$userid] = $r;
				}
				else
				{
					$info[] = array_merge($array[$userid], $r);
				}
			}
			if($flags)
			{
				foreach($_array As $r)
				{
					$userid = $r['userid'];
					$info[] = array_merge($_info[$userid], $r);
				}
			}
		}
		$info = array_filter($info);
		$this->number = $this->db_m->page;
        $this->db->free_result($result);
		return $info;
	} 
	
	
	function update_credit($userid, $username, $credit = 0, $isadd = 0)
	{
		if(!$userid || !$username) return false;
		if($isadd)
		{
			$r = $this->db->get_one(" COUNT(cid) AS num",$this->table_name,"userid=$userid");
			if($r['num']==0)
			{
				$info['userid'] = $userid;
				$info['username'] = $username;
				$info['addtime'] = SYS_TIME;
				$this->db->insert($info,$this->table_name);
			}
			$timestamp = SYS_TIME;
			$months = date('n',$timestamp);
			$years = date('Y',$timestamp);
			$liveweek = date('w', $timestamp);
			$weeks = $liveweek*86400+date('H')*3600+date('i')*60+date('s');
			$ymdate = mktime(0,0,0,$months,1,$years);
			$credit = intval($credit);

			@extract($this->db->get_one("*",$this->table_name,"userid=$userid"));
			if($timestamp-$addtime>=$weeks)
			{
				$this->db->query("UPDATE $this->table_name SET `preweek`=`week`, `week`='$credit' WHERE userid='$userid'");
				if(($timestamp-$addtime)>=($addtime-$ymdate))
				$this->db->query("UPDATE $this->table_name SET `premonth`=`month`, `month`='$credit' WHERE userid='$userid'");
			}
			else
			{
				$this->db->query("UPDATE $this->table_name SET `week`=`week`+$credit WHERE userid='$userid'");
				if(($timestamp-$addtime)<($addtime-$ymdate))
				$this->db->query("UPDATE $this->table_name SET `month`=`month`+$credit WHERE userid='$userid'");
			}
		}
		else 
		{
			$r = $this->db->get_one(" COUNT(cid) AS num",$this->table_name,"userid=$userid");
			if($r['num']==0)
			{
				$info['userid'] = $userid;
				$info['username'] = $username;
				$info['addtime'] = SYS_TIME;
				$this->db->insert($info,$this->table_name);
			}
			else
			{
				@extract($this->db->get_one("month,week",$this->table_name,"userid=$userid"));
				if(($month && $credit<$week && $credit<$month) || ($week && $credit<$week && $credit<$month))
				{
					$this->db->update("`month`=`month`-$credit, `week`=`week`-$credit",$this->table_name,"userid=$userid");
				}
				elseif($credit>$week)
				{
					$this->db->update("`month`=0, `week`=0",$this->table_name,"userid=$userid");
				}
			}
		}
	}
}
?>