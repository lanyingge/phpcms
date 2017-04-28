<?php
defined('IN_PHPCMS') or exit('No permission resources.');

pc_base::load_sys_class('form','',0);
pc_base::load_app_func('global');
pc_base::load_sys_class('format', '', 0);
class index {
	
	protected  $question, $answer, $comment, $zsask, $hits, $userid, $admin, $config, $IP;
	
	function __construct() {
		$this->question = pc_base::load_model('ask_question_model');
		$this->answer = pc_base::load_model('ask_answer_model');
		$this->comment = pc_base::load_model('ask_comment_model');
		$this->zsask = pc_base::load_model('ask_zsask_model');
		$this->userid = param::get_cookie('_userid');
		$this->hits = pc_base::load_model('hits_model');
		$this->admin = session_code('', 1);
		$this->config = getcache('settings', 'zsask');
		
		$this->IP = ip();
	}
	
	function init() {
		/*$askcategorys = getcache('categorys', 'zsask');
		
		$SEO = seo(1,'','重庆装饰装修视频新闻-重庆家装网');
		$page = max(intval($_GET['page']), 1);
		include template('zsask', 'index');*/
		$this->qlist();
	}
	
	function qlist() {
		$askcategorys = getcache('categorys', 'zsask');
		$caturl = APP_PATH.'index.php?m=zsask&a=qlist&cid=';
		
		$catid = intval($_GET['cid']) ? intval($_GET['cid']) : 0;
		
		$SEO = seo(1,'','重庆装饰问答网-重庆家装网');
		$page = max(intval($_GET['page']), 1);
		include template('zsask', 'qlist');
	}
	
	//提问
	function question() {
		$SEO = seo(1,'','重庆装饰问答网-重庆家装网');
		if (isset($_POST['dosubmit']) || isset($_GET['dosubmit'])) {
			
			if (!$this->config['agree_question']) check_member($this->userid);//检查配置是否同意游客提问
			if ($this->config['check_question'])  $insert['status'] = 1; //状态1开启问题审核
			
			$question = safe_replace(strip_tags($_POST['question']));
			
			$insert['question'] = trim($question) ? $question : showmessage('请输入你的问题！');
			$insert['content']  = safe_replace(strip_tags($_POST['content']));
			$insert['catid']    = intval($_POST['catid']) ? intval($_POST['catid']) : showmessage('请选择分类！');
			
			$insert['userid']   = $this->userid;
			$insert['ip']       = $this->IP;
			$insert['addtime']  = SYS_TIME;
			$insert['updatetime']=SYS_TIME;
			
			$id = $this->question->insert($insert, TRUE);
			if ($id) $this->hits->insert(array('hitsid'=>'zsask-'.$id, 'catid'=>9998));
			showmessage('操作成功！', HTTP_REFERER);
			
		}
		$catid = intval($_GET['cid']) ? intval($_GET['cid']) : '';
		include template('zsask', 'question');
	}
	
	//回答
	function answer() {
		$qid = intval($_GET['qid']) ? intval($_GET['qid']) : (intval($_POST['qid']) ? intval($_POST['qid']) : showmessage('错误，问题不存在！'));
		if (!$qid) showmessage('错误，问题不存在！');
		
		$info = $this->question->get_one('qid='.$qid.' AND status!=1');
		if (!$info && !$this->admin) showmessage('问题正在审核或已删除！');
		$ts_answer = $this->answer->select('qid='.$qid.' AND zid=0 AND status !=1', '*', '', 'status DESC, addtime ASC');
		if (isset($_POST['dosubmit'])) {
			if ($info['status'] ==99) showmessage('该问题已经解决了，请换个问题回答吧！');
			if ($info['status'] ==98) showmessage('问题已经关闭！');
			
			if (!$this->config['agree_answer']) check_member($this->userid);//检查配置是否同意游客回答
			if ($this->config['check_answer'])  $status = 1; //状态1开启回答审核
			
			$_answer = safe_replace(strip_tags($_POST['answer']));
			if (trim($_answer)){
				
				$_userid = $this->userid;
				if ($_userid == $info['userid'] && $_userid) showmessage('您不可以回答自己的问题！');
				if ($this->IP == $info['ip'] && !$this->admin) showmessage('您不可以回答自己的问题！');
				
				foreach ($ts_answer as $an) {
					$_u[] = $an['userid'];
					$_p[] = $an['ip'];
				}
				if (in_array($this->IP, $_p) && !$this->admin) showmessage('不可重复回答同一问题，您可以完善你的回答！');
				if (in_array($this->userid, $_u) && $this->userid) showmessage('不可重复回答同一问题，您可以完善你的回答！');
				$this->answer->insert(array('content' =>$_answer, 'qid' =>$qid, 'userid' =>$_userid, 'status'=>$status,
											 'ip'=>$this->IP, 'addtime'=>SYS_TIME, 'updatetime'=>SYS_TIME));
				showmessage('回答成功！', HTTP_REFERER);
			}else {
				showmessage('请输入你的答案！');
			}
		}
		
		$SEO = seo(1,'', $info['question'].' - 重庆装饰问答网 - 重庆家装网');
		$page = max(intval($_GET['page']), 1);
		include template('zsask', 'answer');
	}
	
	//完善回答
	function editAnswer() {
		$qid = intval($_GET['qid']) ? intval($_GET['qid']) : (intval($_POST['qid']) ? intval($_POST['qid']) : showmessage('错误，问题不存在！'));
		$aid = intval($_GET['aid']) ? intval($_GET['aid']) : (intval($_POST['aid']) ? intval($_POST['aid']) : showmessage('错误，问题不存在！'));
		if (!$qid) showmessage('错误，问题不存在！');
		
		if (!$this->config['agree_answer']) check_member($this->userid);//检查配置是否同意游客回答
		if ($this->config['check_answer'])  $status = 1; //状态1开启回答审核
			
		$info = $this->answer->get_one(array('qid'=>$qid, 'aid'=>$aid));
		if (!$info) showmessage('问题不存在或已删除！');
		
		if (isset($_POST['dosubmit'])) {
			if ($info['status'] ==99) showmessage('该问题已经解决了，请换个问题回答吧！');
			if ($info['status'] ==98) showmessage('问题已经关闭！');
			$_answer = safe_replace(strip_tags($_POST['answer']));
			if (trim($_answer)){
				
				$_userid = $this->userid;
				if ($_userid != $info['userid'] && $_userid && $info['userid']) showmessage('非法操作！');
				if ($this->IP != $info['ip'] && !$this->admin) showmessage('不正当操作！');
				
				$this->answer->update(array('content' =>$_answer, 'userid'=>$this->userid, 'status'=>$status, 'updatetime'=>SYS_TIME), 
									  array('aid'=>$aid, 'qid'=>$qid));
				showmessage('回答成功！', HTTP_REFERER, '', 'zq');
			}else {
				showmessage('请输入你的答案！');
			}
		}
		include template('zsask', 'editanswer');
	}
	
	//追问
	function addAnswer() {
		
		$aid = intval($_GET['aid']) ? intval($_GET['aid']) : showmessage('错误，答案不存在！');
		$qid = intval($_GET['qid']) ? intval($_GET['qid']) : showmessage('错误，问题不存在！');
		
		check_member($this->userid);
		//if ($this->config['check_answer'])  $status = 1; //状态1开启回答审核
			
		if (isset($_POST['dosubmit'])) {
			$status = 1;//1-提问者，2-回答者 （只允许两者之间会话）
			$_addanswer = safe_replace(strip_tags($_POST['addanswer']));
			
			$a_rs = $this->answer->get_one(array('aid'=>$aid, 'qid'=>$qid, 'zid'=>0));
			if ($this->userid ==$a_rs['userid']) $status = 2;
			if (!$a_rs) showmessage('AQ不对应！');
			
			$rs = $this->question->get_one(array('qid'=>$qid, 'status' =>0));
			if (!$rs) showmessage('问题状态错误！');
			if ($status ==1 && $this->userid !=$rs['userid']) showmessage('非法操作！'); 
			if ($status ==2 && $this->IP == $a_rs['ip'] && !$this->admin) showmessage('不正当操作！');
			
			if (!trim($_addanswer)) showmessage('内容为空！');
			$this->answer->insert(array('qid'=>$qid, 'zid'=>$aid, 'content'=>$_addanswer, 
										'userid'=>$this->userid, 'ip'=>$this->IP, 'addtime'=>SYS_TIME, 'updatetime'=>SYS_TIME));
			showmessage('回答成功！', HTTP_REFERER, '', 'zq');
		}
		include template('zsask', 'addanswer');
		
	}
	
	//评论回答
	function commentAnswer() {
		$insert['aid'] = intval($_GET['aid']) ? intval($_GET['aid']) : (intval($_POST['qid']) ? intval($_POST['qid']) : showmessage('错误，答案不存在！'));
		$insert['qid'] = intval($_GET['qid']) ? intval($_GET['qid']) : (intval($_POST['qid']) ? intval($_POST['qid']) : showmessage('错误，答案不存在！'));
		
		if (isset($_POST['dosubmit'])) {
			
			if (!$this->config['agree_comment']) check_member($this->userid);//检查配置是否同意游客
			if ($this->config['check_comment'])  $insert['status'] = 1; //状态1开启
			
			$rs = $this->answer->get_one(array('aid'=>$insert['aid'], 'qid'=>$insert['qid']));
			if (!$rs) showmessage('回答不存在！');
			$insert['content']  = safe_replace(strip_tags($_POST['comment']));
			if (!trim($insert['content'])) showmessage('请输入评语！');
			$insert['ip'] = $this->IP;
			$insert['userid'] = $this->userid;
			$insert['addtime'] = SYS_TIME;
			$insert['updatetime'] = SYS_TIME;
			$cid = $this->comment->insert($insert, TRUE);
		//	showmessage('评论成功！', HTTP_REFERER, '', 'zq');
			$arr['cid'] = $cid;
			$arr['time'] = format_date(SYS_TIME);
			exit(json_encode($arr));
		}
		include template('zsask', 'comment');
		
	}
	
	//满意答案
	function agreeAnswer() {
		$aid = intval($_GET['aid']) ? intval($_GET['aid']) : showmessage('错误，答案不存在！');
		$qid = intval($_GET['qid']) ? intval($_GET['qid']) : showmessage('错误，问题不存在！');
		if (!$this->admin) check_member($this->userid); //管理员无需登陆
		$a_rs = $this->answer->get_one(array('aid'=>$aid, 'qid'=>$qid));
		if (!$a_rs) showmessage('AQ不对应！');
		$rs = $this->question->get_one(array('qid'=>$qid, 'status' =>0));
		if (!$rs) showmessage('问题状态错误！');
		$this->question->update(array('status'=>99, 'aid'=>$aid), array('qid'=>$qid, 'status' =>0));
		$this->answer->update(array('status'=> 9), array('qid'=>$qid, 'aid'=>$aid));
		showmessage('操作成功！', HTTP_REFERER);
	}
	
	//问答分类
	function select_type() {
		$askcategorys = getcache('categorys', 'zsask');
		
		$catid = intval($_GET['cid']) ? intval($_GET['cid']) : '';
		if ($catid) { 
			$str = '';
			foreach ($askcategorys as $r) {
				if ($r['parentid'] ==$catid && $r['status'] ==99) {
					$str .= '<tr class="pointer" onclick="selected(\''.$r['catid'].'\', '.$r['grade'].', \''.$r['catname'].'\', this)"><td>'.$r['catname'].'</td></tr>';
				}
			}
			if ($str) exit($str);
			exit('1');
		}
		
		include template('zsask', 'select_type');
	}
	
	
	
	
	
	
	
}
?>
