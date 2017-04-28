<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('format','',0);
pc_base::load_sys_class('form','',0);
class answer extends admin {
	function __construct() {
		parent::__construct();
		$this->M = new_html_special_chars(getcache('ask', 'commons'));
		$this->db = pc_base::load_model('ask_model');
		$this->db2 = pc_base::load_model('answer_model');
		$this->db3 = pc_base::load_model('category_model');
		$this->db_m = pc_base::load_model('member_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_ask_'.$this->siteid,'commons');
		
	}

	public function init() {
		$job = intval($_GET['job']);
		$askid = intval($_GET['askid']);
		$where = '';
		$_GET['dosubmit'] = 1;
		if($_GET['dosubmit']){
			extract($_GET['info']);
			$_GET['catid'] = $catid ? $catid : $_GET['catid'];
			if($_GET['catid']){
				if($this->categorys[$_GET[catid]]['child']){
					$where .= "AND `catid` IN(".$this->categorys[$_GET[catid]]['arrchildid'].") ";		
				}else{
					$where .= "AND `catid`= ".$_GET['catid']." ";	
				}
			}
			if($keywords) $where .= "AND `message` LIKE '%$keywords%' ";
			if($username) $where .= "AND `username` LIKE '%$username%' ";
			if($start_addtime && $end_addtime) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$where .= "AND `addtime` >= '$start' AND  `addtime` <= '$end'";				
			}
			if($status) $where .= "AND `status`=$status  AND isask=0 ";	
			if($optimal) $where .= "AND `optimal`=$optimal ";	
			if($candidate) $where .= "AND `candidate`=1 ";				
			if($where) $where = substr($where, 3);
		}	
		$_GET['catid'] = intval($_GET['catid']);
		
		if(!$_GET['catid']){
			$categorys = $this->db3->select(array('siteid'=>$this->siteid,'module'=>'ask','parentid'=>'0'),'catid,catname,siteid',20000,'listorder ASC');
			foreach ($categorys as $r) {
				//检测表
				$posts_table_name = $this->db2->posts_table($r['catid']);
				$this->db2->table_name = $posts_table_name;
				$p = $this->db2->get_one("isask=0","count(pid) AS num");
				$cat_posts_num[$r[catid]] = $p[num];
			}
		}
		else
		{
			$answer_status =array(1=>L('status1'),3=>L('status3'));
			//检测表
			$posts_table_name = $this->db2->posts_table(intval($_GET['catid']));
			$this->db2->table_name = $posts_table_name;
			$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
			$infos = $this->db2->listinfo($where,$order = 'pid DESC,askid DESC',$page, $pages = '20');
			$pages = $this->db2->pages;
			$show_dialog = true;
			$siteid = $this->get_siteid();
		}
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ask&c=ask&a=add\', title:\''.L('ask_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('ask_add'));

		include $this->admin_tpl('answer_list');
	}

	 
	//添加问题
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
			$_username = param::get_cookie('admin_username');
			$_POST['ask']['addtime'] = SYS_TIME;
			$_POST['ask']['siteid'] = $this->get_siteid();
			$_POST['ask']['sysadd'] = 1;
			$_POST['posts']['sysadd'] = 1;
			
			$_POST['ask']['username'] = $_username;
			$siteid = $this->get_siteid();
			if(empty($_POST['ask']['title'])) {
				showmessage(L('title_noempty'),HTTP_REFERER);
			}
			if(empty($_POST['posts']['message'])) {
				showmessage(L('message_noempty'),HTTP_REFERER);
			}
			$askid = $this->db->insert($_POST['ask'],true);
			if(!$askid) return FALSE; 
			//$this->db3->update(array('items'=>'items+1'),array('catid'=>9));
			$pid = $this->db2->insert(array('askid'=>$askid,'isask'=>1,'message'=>$_POST['posts']['message'],'addtime'=>$_POST['ask']['addtime'],'userid'=>$_userid,'status'=>1,'username'=>$_username,'siteid'=>$siteid),true);
			
 			
			showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
			
			//print_r($types);exit;
 			include $this->admin_tpl('ask_add');
		}

	}
	
 
	public function edit() {
		$ask = $this->db->get_one(array('askid'=>$_GET['askid']));
		//检测表
		$posts_table_name = $this->db2->posts_table($ask['catid']);
		$this->db2->table_name = $posts_table_name;
			
		if(isset($_POST['dosubmit'])){
 			$pid = intval($_GET['pid']);
			if($pid < 1) return false;
			if((!$_POST['posts']['message']) || empty($_POST['posts']['message'])) return false;
			$this->db2->update($_POST['posts'],array('pid'=>$pid,'siteid'=>$this->get_siteid()));
			showmessage(L('operation_success'),'?m=ask&c=answer&a=init','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			
			//解出链接内容
			$posts = $this->db2->get_one(array('pid'=>$_GET['pid']));
			if(!$posts) showmessage(L('illegal_parameters'), HTTP_REFERER);
 			include $this->admin_tpl('answer_edit');
		}

	}
	
	/**
	 * 删除回答 
	 * @param	intval	$pid	回答ID，递归删除
	 */
	public function delete() {
  		if((!isset($_GET['pid']) || empty($_GET['pid'])) && (!isset($_POST['pid']) || empty($_POST['pid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['pid'])){
				foreach($_POST['pid'] as $askid_arr) {
					//检测表
					$posts_table_name = $this->db2->posts_table($_GET['catid']);
					$this->db2->table_name = $posts_table_name;
					
					$ask = $this->db2->get_one(array('pid'=>$askid_arr),'userid,askid');
 					//批量删除回答
					$this->db2->delete(array('pid'=>$askid_arr));
					$this->db_m->update(array('answercount'=>'-=1'),array('userid'=>$ask[userid]));
					$this->db->update(array('answercount'=>'-=1'),"askid='$ask[askid]'");
				}
				showmessage(L('operation_success'),'?m=ask&c=answer&catid='.$_GET['catid']);
			}else{
				$pid = intval($_GET['pid']);
				if($pid < 1) return false;
				//检测表
				$posts_table_name = $this->db2->posts_table($_GET['catid']);
				$this->db2->table_name = $posts_table_name;
				$ask = $this->db2->get_one(array('pid'=>$_GET['pid']));
				if(!$ask) showmessage(L('illegal_parameters'), HTTP_REFERER);
				//删除回答
				$result = $this->db2->delete(array('pid'=>$pid));
				$this->db_m->update(array('answercount'=>'-=1'),array('userid'=>$ask[userid]));
				$this->db->update(array('answercount'=>'-=1'),"askid='$ask[askid]'");
				if($result){
					showmessage(L('operation_success'),'?m=ask&c=answer&catid='.$_GET['catid']);
				}else {
					showmessage(L("operation_failure"),'?m=ask&c=answer&catid='.$_GET['catid']);
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	
	
	//审核
 	public function check(){
		if(isset($_POST['dosubmit'])) {
			$status = intval($_GET['status']);
			if(!in_array($status,array(1,3))) showmessage(L('illegal_parameters'), HTTP_REFERER);
			
			//得到回复表
			$this->db2->table_name = $posts_table_name = $this->db2->posts_table(intval($_GET['catid']));
			
			if((!isset($_GET['pid']) || empty($_GET['pid'])) && (!isset($_POST['pid']) || empty($_POST['pid']))) {
				showmessage(L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($_POST['pid'])){//批量审核
					foreach($_POST['pid'] as $pid_arr) {
						$this->db2->update(array('status'=>$status),array('pid'=>$pid_arr,'isask'=>0,'siteid'=>$this->siteid));
					}
					showmessage(L('operation_success'),'?m=ask&c=answer&catid='.$_GET['catid']);
				}else{//单个审核
					$pid = intval($_GET['pid']);
					if($pid < 1) return false;
					$result =$this->db2->update(array('status'=>$status),array('pid'=>$pid,'isask'=>0,'siteid'=>$this->siteid));
					if($result){
						showmessage(L('operation_success'),'?m=ask&c=answer&catid='.$_GET['catid']);
					}else {
						showmessage(L("operation_failure"),'?m=ask&c=answer&catid='.$_GET['catid']);
					}
				}
			}
		}
	}
	
	public function view_answer() {
		$show_validator = $show_scroll = $show_header = true;
		$_GET['askid'] = intval($_GET['askid']);
		$_GET['pid'] = intval($_GET['pid']);
		
		//得到回复表
		$this->db2->table_name = $posts_table_name = $this->db2->posts_table(intval($_GET['catid']));
		//解出链接内容
		$ask = $this->db->get_one(array('askid'=>$_GET['askid']));
		$posts = $this->db2->get_one(array('pid'=>$_GET['pid']));
		if(!$ask) showmessage(L('illegal_parameters'), HTTP_REFERER);
		if(!$posts) showmessage(L('illegal_parameters'), HTTP_REFERER);
		$CATEGORYS = getcache('category_ask_'.$this->get_siteid(),'commons');
		include $this->admin_tpl('answer_view');

	}
	
	/**
	 * 显示栏目菜单列表
	 */
	public function public_categorys() {
		$show_header = '';
		$from = isset($_GET['from']) && in_array($_GET['from'],array('block')) ? $_GET['from'] : 'ask';
		$tree = pc_base::load_sys_class('tree');

		$categorys = array();
		if(!empty($this->categorys)) {
			foreach($this->categorys as $r) {
				if($r['siteid']!=$this->siteid ||  ($r['type']==2 && $r['child']==0)) continue;
				$r['icon_type'] = $r['vs_show'] = '';
					$r['type'] = 'init';
					$r['add_icon'] = "<img src='".IMG_PATH."add_content.gif'> ";
				$categorys[$r['catid']] = $r;
			}
		}
		if(!empty($categorys)) {
			$tree->init($categorys);
				switch($from) {

					default:
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=ask&c=answer&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid&dosubmit=1' target='right' onclick='open_list(this)'>\$catname</a></span>";

						$strs2 = "<a href='?m=ask&c=answer&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid&dosubmit=1' target='right' onclick='open_list(this)'>\$catname</a>";
						break;
				}
			$categorys = $tree->get_treeview(0,'category_tree',$strs,$strs2);
		} else {
			$categorys = L('please_add_category');
		}
        include $this->admin_tpl('category_tree');
		exit;
	}
 
	 
	
	
}
?>