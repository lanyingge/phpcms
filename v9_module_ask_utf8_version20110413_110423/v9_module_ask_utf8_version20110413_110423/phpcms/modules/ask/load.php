<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('format','',0);
class load{
	public $siteid;
	function __construct() {
		//parent::__construct();
		$this->M = new_html_special_chars(getcache('ask', 'commons'));
		$this->db = pc_base::load_model('ask_model');
		$this->db2 = pc_base::load_model('ask_posts_model');
		$this->db3 = pc_base::load_model('category_model');
		$this->siteid = get_siteid();
	}

	public function init() {
	}

	/*
	 *栏目AJAX 
	 */
	public function category() {
		$id = $_GET['id'];
		$categorys = array();
		$result = getcache('category_ask_'.$this->siteid,'commons');
		if(!empty($result)) {
			foreach($result as $r) {
				$categorys[$r['catid']] = $r;
			}
		}
		if($id)
		{
			$str = "<span>→</span>";
			$LANG_SELECT = L('please_select');
		}
		else
		{
			$str = '';
			$LANG_SELECT = L('please_select');
		}
		if($id)
		{
			//$str .= '<select onchange="$(\'#catid\').val(this.value);category_load(this.value);" multiple style="height:300px;width:120px;">';
			$str .= '<select onchange="$(\'#catid\').val(this.value);category_load(this.value);" multiple style="height:300px;width:120px;">';
		}else{
			$str .= '<select onchange="category_change_reload(this.options[this.selectedIndex].value)" multiple style="height:300px;width:120px;">';
		}
		$options = '';
		foreach($categorys as $i=>$v)
		{
			if((isset($id) && $v['parentid'] == $id) || $v['module'] == 'ask')  $options .= '<option value="'.$i.'">'.$v['catname'].'</option>';
		}
		if(empty($options)) exit;
		$str .= $options.'</select>';
		
		echo $str;
	}
	 
	
}
?>