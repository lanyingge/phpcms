<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('format','',0);
pc_base::load_sys_class('form','',0);
pc_base::load_app_func('global');
class ask extends admin {
	function __construct() {
		if (!module_exists(ROUTE_M)) showmessage(L('module_not_exists')); 
		parent::__construct();
		$this->M = new_html_special_chars(getcache('ask', 'commons'));
		$this->db = pc_base::load_model('ask_model');
		$this->db2 = pc_base::load_model('answer_model');
		$this->db3 = pc_base::load_model('category_model');
		$this->db_m = pc_base::load_model('member_model');
		$this->db_actor = pc_base::load_model('ask_actor_model');
		$this->db_credit = pc_base::load_model('ask_credit_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_ask_'.$this->siteid,'commons');
	}

	public function init() {
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
			if($keywords) $where .= "AND `title` LIKE '%$keywords%' ";
			if($username) $where .= "AND `username` LIKE '%$username%' ";
			if($start_addtime && $end_addtime) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$where .= "AND `addtime` >= '$start' AND  `addtime` <= '$end'";				
			}
			if($status) $where .= "AND `status`=$status ";			
			if($flag) $where .= "AND `flag`=$flag ";			
			if($where) $where = substr($where, 3);
		}		
			
		$infos = array();
		$ask_status =array(1=>L('status1'),3=>L('status3'),5=>L('status5'),6=>L('status6'));
		$ask_flag =array(1=>L('vote_question'),3=>L('elite_question'));
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'askid DESC',$page, $pages = '9');
		$pages = $this->db->pages;
		$show_dialog = true;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ask&c=ask&a=add\', title:\''.L('ask_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('ask_add'));
		$CATEGORYS = getcache('category_ask_'.$this->get_siteid(),'commons');
		include $this->admin_tpl('ask_list');
	}

	 
	//添加问题
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
			$_username = param::get_cookie('admin_username');
			$_userid = param::get_cookie('userid');
			$_POST['ask']['catid'] = intval($_POST['ask']['catid']);
			$_POST['ask']['reward'] = intval($_POST['ask']['reward']);
			$_POST['ask']['title'] = htmlspecialchars($_POST['ask']['title']);
			$_POST['ask']['addtime'] = $_POST['posts']['addtime'] = SYS_TIME;
			$_POST['ask']['endtime'] = $_POST['ask']['addtime']+1296000;
			$_POST['ask']['siteid'] = $this->get_siteid();
			$_POST['ask']['sysadd'] = 1;
			$_POST['posts']['sysadd'] = 1;
			$siteid = $this->get_siteid();
			if(empty($_POST['ask']['title'])) {
				showmessage(L('title_noempty'),HTTP_REFERER);
			}
			if(empty($_POST['posts']['message'])) {
				showmessage(L('message_noempty'),HTTP_REFERER);
			}
			$_POST['posts']['message'] = strip_tags($_POST['posts']['message']);
			
			//检测表
			$posts_table_name = $this->db2->posts_table($_POST['ask']['catid']);
			if (!$this->db->table_exists(str_replace($this->db->db_tablepre,'',$posts_table_name))) showmessage(L('illegal_parameters'), HTTP_REFERER);
			
			
			$askid = $this->db->insert($_POST['ask'],true);
			if(!$askid) return FALSE; 
			$this->db2->table_name = $posts_table_name;
			$pid = $this->db2->insert(array('askid'=>$askid,'catid'=>$_POST['ask']['catid'],'isask'=>1,'message'=>$_POST['posts']['message'],'addtime'=>$_POST['posts']['addtime'],'userid'=>$_userid,'status'=>3,'username'=>$_username,'userid'=>$_userid,'siteid'=>$siteid),true);
 			$this->db3->update(array('items'=>'+=1'),array('catid'=>$_POST['ask']['catid']));
			
			showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
			
 			include $this->admin_tpl('ask_add');
		}

	}
	
	public function edit() {
		if(isset($_POST['dosubmit'])){
 			$askid = intval($_GET['askid']);
			if($askid < 1) return false;
			$_POST['ask']['catid'] = $_POST['posts']['catid'] = intval($_POST['ask']['catid']);
			$_POST['old_catid'] = intval($_POST['old_catid']);
			$_POST['ask']['reward'] = intval($_POST['ask']['reward']);
			$_POST['ask']['title'] = htmlspecialchars($_POST['ask']['title']);
			if(!is_array($_POST['ask']) || empty($_POST['ask'])) return false;
			if((!$_POST['ask']['title']) || empty($_POST['ask']['title'])) return false;
			if((!$_POST['posts']['message']) || empty($_POST['posts']['message'])) return false;
			$this->db->update($_POST['ask'],array('askid'=>$askid,'siteid'=>$this->get_siteid()));
			$posts_table_name = $this->db2->posts_table($_POST['ask']['catid']);
			$this->db2->table_name = $posts_table_name;
			$this->db2->update($_POST['posts'],array('askid'=>$askid,'isask'=>1,'siteid'=>$this->get_siteid()));
			if(intval($_POST['old_catid'])!=$_POST['ask']['catid']){
				$this->db3->update(array('items'=>'+=1'),array('catid'=>$_POST['ask']['catid']));
				$this->db3->update(array('items'=>'-=1'),array('catid'=>$_POST['old_catid']));
			}
			showmessage(L('operation_success'),'?m=ask&c=ask&a=init&catid='.$_GET['catid'],'', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出链接内容
			$ask = $this->db->get_one(array('askid'=>$_GET['askid']));
			//检测表
			$posts_table_name = $this->db2->posts_table($ask['catid']);
			$this->db2->table_name = $posts_table_name;
			$posts = $this->db2->get_one(array('askid'=>$_GET['askid'],'isask'=>1,'siteid'=>$this->siteid));
			if(!$ask || !$posts) showmessage(L('illegal_parameters'), HTTP_REFERER);
			$CATEGORYS = getcache('category_ask_'.$this->get_siteid(),'commons');
 			include $this->admin_tpl('ask_edit');
		}

	}
	
	/**
	 * 删除问题 
	 * @param	intval	$askid	问题ID，递归删除
	 */
	public function delete() {
  		if((!isset($_GET['askid']) || empty($_GET['askid'])) && (!isset($_POST['askid']) || empty($_POST['askid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['askid'])){
				foreach($_POST['askid'] as $askid_arr) {
					$ask = $this->db->get_one(array('askid'=>$askid_arr),'userid,catid');
					if(!$ask) showmessage(L('illegal_parameters'), HTTP_REFERER);
 					//批量删除问题
					$this->db->delete(array('askid'=>$askid_arr));
					//得到回复表
					$posts_table_name = $this->db2->posts_table($ask['catid']);
					$this->db2->table_name = $posts_table_name;
					$this->db2->delete(array('askid'=>$askid_arr));
					$this->db3->update(array('items'=>'-=1'),array('catid'=>$ask[catid]));
					$this->db->delete(array('askid'=>$askid_arr),$this->db_tablepre."ask_vote");
				}
				showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
			}else{
				$askid = intval($_GET['askid']);
				if($askid < 1) return false;
				$ask = $this->db->get_one(array('askid'=>$askid),'userid,catid');
				if(!$ask) showmessage(L('illegal_parameters'), HTTP_REFERER);
				//删除问题
				$result = $this->db->delete(array('askid'=>$askid));
				//得到回复表
				$posts_table_name = $this->db2->posts_table($ask['catid']);
				$this->db2->table_name = $posts_table_name;
				$result = $this->db2->delete(array('askid'=>$askid));
				$this->db3->update(array('items'=>'-=1'),array('catid'=>$ask[catid]));
				$this->db->delete(array('askid'=>$askid),$this->db_tablepre."ask_vote");
				if($result){
					showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
				}else {
					showmessage(L("operation_failure"),'?m=ask&c=ask&catid='.$_GET['catid']);
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	 
	/**
	 * 模块配置
	 */
	public function setting() {
		//读取配置文件
		$data = array();
 		$siteid = $this->get_siteid();//当前站点 
		//更新模型数据库,重设setting 数据. 
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'ask'));
		$setting = string2array($data[0]['setting']);
		$now_seting = $setting[$siteid]; //当前站点配置
		if(isset($_POST['dosubmit'])) {
			//多站点存储配置文件
 			$setting[$siteid] = $_POST['setting'];
  			setcache('ask', $setting, 'commons');  
			//更新模型数据库,重设setting 数据. 
  			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$set = array2string($setting);
			$m_db->update(array('setting'=>$set), array('module'=>ROUTE_M));
			showmessage(L('setting_updates_successful'), '?m=ask&c=ask&a=setting');
		} else {
			pc_base::load_sys_class('form', '', 0);
			@extract($now_seting);
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ask&c=ask&a=add\', title:\''.L('ask_add').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('ask_add'));
 			include $this->admin_tpl('setting');
		}
	}
	
	
	//推荐 elite
 	public function elite(){
		if(isset($_POST['dosubmit'])) {
			if((!isset($_GET['askid']) || empty($_GET['askid'])) && (!isset($_POST['askid']) || empty($_POST['askid']))) {
				showmessage(L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($_POST['askid'])){//批量推荐
					foreach($_POST['askid'] as $askid_arr) {
						$this->db->update(array('flag'=>1),array('askid'=>$askid_arr));
					}
					showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
				}else{//单个推荐
					$askid = intval($_GET['askid']);
					if($askid < 1) return false;
					$result = $this->db->update(array('flag'=>1),array('askid'=>$askid));
					if($result){
						showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
					}else {
						showmessage(L("operation_failure"),'?m=ask&c=ask&catid='.$_GET['catid']);
					}
				}
			}
		}
	}
	//取消推荐 un_elite
 	public function un_elite(){
		if(isset($_POST['dosubmit'])) {
			if((!isset($_GET['askid']) || empty($_GET['askid'])) && (!isset($_POST['askid']) || empty($_POST['askid']))) {
				showmessage(L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($_POST['askid'])){//批量推荐
					foreach($_POST['askid'] as $askid_arr) {
						$this->db->update(array('flag'=>0),array('askid'=>$askid_arr));
					}
					showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
				}else{//单个推荐
					$askid = intval($_GET['askid']);
					if($askid < 1) return false;
					$result = $this->db->update(array('flag'=>0),array('askid'=>$askid));
					if($result){
						showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
					}else {
						showmessage(L("operation_failure"),'?m=ask&c=ask&catid='.$_GET['catid']);
					}
				}
			}
		}
	}
	
	//审核
 	public function check(){
		if(isset($_POST['dosubmit'])) {
			$status = intval($_GET['status']);
			if(!in_array($status,array(1,3))) showmessage(L('illegal_parameters'), HTTP_REFERER);
			
			//得到回复表
			$this->db2->table_name = $posts_table_name = $this->db2->posts_table(intval($_GET['catid']));
			
			if((!isset($_GET['askid']) || empty($_GET['askid'])) && (!isset($_POST['askid']) || empty($_POST['askid']))) {
				showmessage(L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($_POST['askid'])){//批量审核
					foreach($_POST['askid'] as $askid_arr) {
						$this->db->update(array('status'=>$status),array('askid'=>$askid_arr));
						$this->db2->update(array('status'=>$status),array('askid'=>$askid_arr,'isask'=>1,'siteid'=>$this->siteid));
					}
					showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
				}else{//单个审核
					$askid = intval($_GET['askid']);
					if($askid < 1) return false;
					$result = $this->db->update(array('status'=>$status),array('askid'=>$askid));
					$this->db2->update(array('status'=>$status),array('askid'=>$askid,'isask'=>1,'siteid'=>$this->siteid));
					if($result){
						showmessage(L('operation_success'),'?m=ask&c=ask&catid='.$_GET['catid']);
					}else {
						showmessage(L("operation_failure"),'?m=ask&c=ask&catid='.$_GET['catid']);
					}
				}
			}
		}
	}

    public function view_ask() {
		$_GET['askid'] = intval($_GET['askid']);
		$array = $this->show($_GET['askid']);
		$CATEGORYS = getcache('category_ask_'.$this->get_siteid(),'commons');
	if($array)
	{
		$have_answer = false;
		foreach($array AS $k=>$v)
		{
			if($v['isask'])
			{
				$title = $v['title'];
				$message = $M['use_editor'] ? $v['message'] : trim_textarea($v['message']);
				$reward = $v['reward'];
				$userid = $v['userid'];
				$username = $v['username'];
				$status = $v['status'];
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
			}
			elseif($v['optimal'])
			{
				$solvetime = $v['solvetime'];
				$answer = $v['message'];
				$answertime = $v['addtime'];
				$answer = $v['message'];
				$optimail_username = $v['username'];
				$optimal_actor = actor($v['actortype'], $v['point']);
			}
			else
			{
				if($v['userid'] == $_userid) $have_answer = true;
				$infos[$k]['pid'] = $v['pid'];
				$infos[$k]['userid'] = $v['userid'];
				$infos[$k]['username'] = $v['username'];
				$infos[$k]['addtime'] = $v['addtime'];
				$infos[$k]['candidate'] = $v['candidate'];
				$infos[$k]['anonymity'] = $v['anonymity'];
				$infos[$k]['actor'] = actor($v['actortype'], $v['point']);
				$infos[$k]['message'] = $M['use_editor'] ? $v['message'] : trim_textarea($v['message']);
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
		include $this->admin_tpl('ask_view');
	}
		

	}
 
 	public function show()
	{
		$id = intval($_GET['askid']);
		$a = $this->db->get_one("askid='$id'","userid,catid,status");
		$posts_table_name = $this->db2->posts_table($a['catid']);
		$this->db2->table_name = $posts_table_name;
		$array = array();
		$result = $this->db->query("SELECT * FROM `".$this->db2->table_name."` WHERE askid='$id' AND siteid='".$this->get_siteid()."' ORDER BY `pid` DESC");
		$data = $this->db2->fetch_array($result);
		foreach($data as $r) 
		{
			if($r['isask']) {
				$arr = $this->db->get_one(array('askid'=>$_GET['askid'],'siteid'=>$this->get_siteid()));
				$r['title'] = $arr['title'];
				$r['reward'] = $arr['reward'];
				$r['status'] = $arr['status'];
				$r['answercount'] = $arr['answercount'];
				$r['flag'] = $arr['flag'];
				$r['endtime'] = $arr['endtime'];
				$r['catid'] = $arr['catid'];
				$r['anonymity'] = $arr['anonymity'];
				if((SYS_TIME>$r['endtime']) && !$arr['ischeck'])
				{
					$this->db->update(array('ischeck'=>1),array('askid'=>$id));
					$this->db->update(array('flag'=>1),array('askid'=>$id,'answercount'=>'>1'));
					$this->db2->update(array('candidate'=>1),array('askid'=>$id));
					$this->db->update(array('ischeck'=>1),array('askid'=>$id));
				}
			}
			$userids[] = $r['userid'];
			$array[] = $r;
		}
		if($userids)
		{
			$userids = implodeids($userids);
			$data = $this->db_m->listinfo("userid IN ($userids)");
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
			return $_array;
		}
		else
		{
			return $array;
		}
	}
	
	public function actor() {
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db_actor->listinfo('siteid='.$this->get_siteid(),$order = 'id DESC',$page, $pages = '10');
		$pages = $this->db_actor->pages;
		$TYPES = explode("\n", $this->M[1]['member_group']);
		$show_dialog = true;
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\'?m=ask&c=ask&a=actor_add\', title:\''.L('add_actor').'\', width:\'700\', height:\'450\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_actor'));
		include $this->admin_tpl('actor_list');
	}
	public function actor_add() {
 		if(isset($_POST['dosubmit'])) {
			if(isset($_POST['typeid']) && is_array($_POST['grade']))
			{
				$actor_array = array();
				$actor_array['typeid'] = $_POST['typeid'];
				foreach($_POST['actors'] as $key => $value)
				{
					if(!trim($_POST['grade'][$key])) continue;
					$actor_array['grade'] = $_POST['grade'][$key];
					$actor_array['actor'] = $_POST['actors'][$key];
					$actor_array['min'] = $_POST['min'][$key];
					$actor_array['max'] = $_POST['max'][$key];
					$actor_array['siteid'] = $this->get_siteid();
					$id = $this->db_actor->insert($actor_array,true);
				}
				showmessage(L('add_success'),'blank','','','window.top.right.location.reload();window.top.art.dialog({id:"add"}).close();
');
			}else{
				include $this->admin_tpl('actor_add');
			}
			
		}else{
			pc_base::load_sys_class('form','',0);
			$member_actors = explode("\n",$this->M[1]['member_group']);
			$type_selected = form::select($member_actors,'','name="typeid" id="typeid"');
			
			include $this->admin_tpl('actor_add');
		}
		$this->public_actor_cache();
		
	}
	public function actor_edit() {
 		if(isset($_POST['dosubmit'])) {
			if(!$_POST['id'] || !is_array($_POST['info'])) return false;
			$this->db_actor->update($_POST['info'],array('id'=>$_POST['id'],'siteid'=>$this->get_siteid()));
			showmessage(L('operation_success'),'?m=ask&c=ask&a=actor','', 'edit');
		}else{
			pc_base::load_sys_class('form','',0);
			$id = intval($_GET['id']);
			if(!$id) showmessage(L('illegal_parameters'), HTTP_REFERER);
			$r = $this->db_actor->get_one(array('id'=>$id,'siteid'=>$this->get_siteid()));
			@extract($r);
			$member_actors = explode("\n",$this->M[1]['member_group']);
			$type_selected = form::select($member_actors,$typeid,'name="info[typeid]" id="typeid"');
			
			include $this->admin_tpl('actor_edit');
		}
		$this->public_actor_cache();
		
	}
	/**
	 * 删除头衔 
	 * @param	intval	$id	头衔ID，递归删除
	 */
	public function actor_delete() {
  		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $id_arr) {
 					//批量删除头衔
					$this->db_actor->delete(array('id'=>$id_arr,'siteid'=>$this->get_siteid()));
				}
				showmessage(L('operation_success'),'?m=ask&c=ask&a=actor');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除头衔
				$result = $this->db_actor->delete(array('id'=>$id,'siteid'=>$this->get_siteid()));
				if($result){
					showmessage(L('operation_success'),'?m=ask&c=ask&a=actor');
				}else {
					showmessage(L("operation_failure"),'?m=ask&c=ask&a=actor');
				}
			}
		}
		$this->public_actor_cache();
	}
	/**
	 * 生成头衔缓存
	 */
	public function public_actor_cache() {
		$TYPES = explode("\n", $this->M[1]['member_group']);
		$array = array();
		foreach($TYPES AS $k=>$v)
		{
			$sql = "typeid='$k' AND siteid=".get_siteid();
			$result = $this->db_actor->select($sql,'*','','','','id');
			foreach($result as $r) 
			{
				$infos[$k][] = $r;
			}
			
		}
		
		setcache('actor_'.$this->get_siteid(), $infos, 'ask');
		return true;
 	}
	
	public function credit() {
		$credit = pc_base::load_model('ask_credit_model');
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $credit->listinfos('', '', 1, 50);
		$pages = $this->db_credi->pages;
		include $this->admin_tpl('credit_list');
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
						$strs = "<span class='\$icon_type'>\$add_icon<a href='?m=ask&c=ask&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid&dosubmit=1' target='right' onclick='open_list(this)'>\$catname</a></span>";

						$strs2 = "<a href='?m=ask&c=ask&a=\$type&menuid=".$_GET['menuid']."&catid=\$catid&dosubmit=1' target='right' onclick='open_list(this)'>\$catname</a>";
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