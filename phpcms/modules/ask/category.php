<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class category extends admin {
	private $db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('category_model');
		$this->siteid = $this->get_siteid();
	}
	/**
	 * 管理栏目
	 */
	public function init () {
		$show_pc_hash = '';
		$tree = pc_base::load_sys_class('tree');
		$models = getcache('model','commons');
		$sitelist = getcache('sitelist','commons');

		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$categorys = array();
		//读取缓存
		$result = getcache('category_ask_'.$this->siteid,'commons');
		$show_detail = count($result) < 500 ? 1 : 0;
		$parentid = $_GET['parentid'] ? $_GET['parentid'] : 0;
		$html_root = pc_base::load_config('system','html_root');
		$types = array(0 => L('category_type_system'),1 => L('category_type_page'),2 => L('category_type_link'));
		if(!empty($result)) {
			foreach($result as $r) {
				$r['str_manage'] = '';
				if(!$show_detail) {
					if($r['parentid']!=$parentid) continue;
					$r['parentid'] = 0;
					$r['str_manage'] .= '<a href="?m=ask&c=category&a=init&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('manage_sub_category').'</a> | ';
				}
				$r['str_manage'] .= '<a href="?m=ask&c=category&a=add&parentid='.$r['catid'].'&menuid='.$_GET['menuid'].'&s='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('add_sub_category').'</a> | ';
				
				$r['str_manage'] .= '<a href="?m=ask&c=category&a=edit&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'&type='.$r['type'].'&pc_hash='.$_SESSION['pc_hash'].'">'.L('edit').'</a> | <a href="javascript:confirmurl(\'?m=ask&c=category&a=delete&catid='.$r['catid'].'&menuid='.$_GET['menuid'].'\',\''.L('confirm',array('message'=>addslashes($r['catname']))).'\')">'.L('delete').'</a> ';
				$r['typename'] = $types[$r['type']];
				$r['display_icon'] = $r['ismenu'] ? '' : ' <img src ="'.IMG_PATH.'icon/gear_disable.png" title="'.L('not_display_in_menu').'">';
				if($r['type'] || $r['child']) {
					$r['items'] = '';
				}
				$r['help'] = '';
				$setting = string2array($r['setting']);
				if($r['url']) {
					if(preg_match('/^(http|https):\/\//', $r['url'])) {
						$catdir = $r['catdir'];
						$prefix = $r['sethtml'] ? '' : $html_root;
						if($this->siteid==1) {
							$catdir = $prefix.'/'.$r['parentdir'].$catdir;
						} else {
							$catdir = $prefix.'/'.$sitelist[$this->siteid]['dirname'].$html_root.'/'.$catdir;
						}
						if($r['type']==0 && $setting['ishtml'] && strpos($r['url'], '?')===false && substr_count($r['url'],'/')<4) $r['help'] = '<img src="'.IMG_PATH.'icon/help.png" title="'.L('tips_domain').$r['url'].'&#10;'.L('directory_binding').'&#10;'.$catdir.'/">';
					} else {
						$r['url'] = substr($sitelist[$this->siteid]['domain'],0,-1).$r['url'];
					}
					$r['url'] = "<a href='$r[url]' target='_blank'>".L('vistor')."</a>";
				} else {
					$r['url'] = "<a href='?m=ask&c=category&a=public_cache&menuid=".$_GET['menuid']."&module=ask'><font color='red'>".L('update_backup')."</font></a>";
				}
				$categorys[$r['catid']] = $r;
			}
		}
		$str  = "<tr>
					<td align='center'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input-text-c'></td>
					<td align='center'>\$id</td>
					<td >\$spacer\$catname\$display_icon</td>
					<td>\$typename</td>
					<td align='center'>\$items</td>
					<td align='center'>\$url</td>
					<td align='center' >\$str_manage</td>
				</tr>";
		$tree->init($categorys);
		$categorys = $tree->get_tree(0, $str);
		include $this->admin_tpl('category_manage');
	}
	/**
	 * 添加栏目
	 */
	public function add() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$_POST['info']['type'] = intval($_POST['type']);
			
			if(isset($_POST['batch_add']) && empty($_POST['batch_add'])) {
				if($_POST['info']['catname']=='') showmessage(L('input_catname'));
				/*if($_POST['info']['type']!=2) {
					if($_POST['info']['catdir']=='') showmessage(L('input_dirname'));
					if(!$this->public_check_catdir(0,$_POST['info']['catdir'])) showmessage(L('catname_have_exists'));
				}*/
			}
			
			$_POST['info']['siteid'] = $this->siteid;
			$_POST['info']['module'] = 'ask';
			$setting = $_POST['setting'];
			if($_POST['info']['type']!=2) {
				$setting['category_ruleid'] = $M['category_ruleid'];
				$_POST['info']['url'] = '';
			}
			
			//内容生成静态配置
			$setting['show_ruleid'] = $M['show_ruleid'];
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);
			
			$end_str = $old_end =  '<script type="text/javascript">window.top.art.dialog({id:"test"}).close();window.top.art.dialog({id:"test",content:\'<h2>'.L("add_success").'</h2><span style="fotn-size:16px;">'.L("following_operation").'</span><br /><ul style="fotn-size:14px;"><li><a href="?m=ask&c=category&a=public_cache&menuid='.$_GET['menuid'].'&module=ask" target="right"  onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_1").'</a></li><li><a href="'.HTTP_REFERER.'" target="right" onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_2").'</a></li></ul>\',width:"400",height:"200"});</script>';
			if(!isset($_POST['batch_add']) || empty($_POST['batch_add'])) {
				$catname = CHARSET == 'gbk' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
				$letters = gbk_to_pinyin($catname);
				$_POST['info']['letter'] = strtolower(implode('', $letters));
				$_POST['info']['catdir'] = $_POST['info']['catdir'] ? $_POST['info']['catdir'] : str_replace(array('/'),array('_'),$_POST['info']['letter']);
				$catid = $this->db->insert($_POST['info'], true);
				
				//创建回复表
				if($_POST['info']['parentid']=='0' && $_POST['info']['module']=='ask' && $catid){
					$this->creat_posts_table($catid);
				}
				$this->update_priv($catid, $_POST['priv_roleid']);
				$this->update_priv($catid, $_POST['priv_groupid'],0);
			} else {
				$end_str = '';
				$batch_adds = explode("\n", $_POST['batch_add']);
				foreach ($batch_adds as $_v) {
					if(trim($_v)=='') continue;
					$names = explode('|', $_v);
					$catname = $names[0];
					$_POST['info']['catname'] = $names[0];
					$letters = gbk_to_pinyin($catname);
					$_POST['info']['letter'] = strtolower(implode('', $letters));
					$_POST['info']['catdir'] = trim($names[1]) ? trim($names[1]) : str_replace(array('/'),array('_'),trim($_POST['info']['letter']));
					if(!$this->public_check_catdir(0,$_POST['info']['catdir'])) {
						$end_str .= $end_str ? ','.$_POST['info']['catname'].'('.$_POST['info']['catdir'].')' : $_POST['info']['catname'].'('.$_POST['info']['catdir'].')';
						continue;
					}
					$catid = $this->db->insert($_POST['info'], true);
					$this->update_priv($catid, $_POST['priv_roleid']);
					$this->update_priv($catid, $_POST['priv_groupid'],0);
				}
				$end_str = $end_str ? L('follow_catname_have_exists').$end_str : $old_end;
			}
			$this->cache();
			showmessage(L('add_success').$end_str);
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global');

			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_validator = '';
			if(isset($_GET['parentid'])) {
				$parentid = $_GET['parentid'];
				$r = $this->db->get_one(array('catid'=>$parentid));
				if($r) extract($r,EXTR_SKIP);
				$setting = string2array($setting);
			}
			
			pc_base::load_sys_class('form','',0);
			$type = $_GET['s'];
			if($type==0) {
				$exists_model = false;
				$models = getcache('model','commons');	
				foreach($models as $_m) {
					if($this->siteid == $_m['siteid']) {
						$exists_model = true;
						break;
					}
				}
				if(!$exists_model) showmessage(L('please_add_model'),'?m=content&c=sitemodel&a=init&menuid='.$_GET['menuid'],5000);
				include $this->admin_tpl('category_add');
			} elseif ($type==1) {
				include $this->admin_tpl('category_page_add');
			} else {
				include $this->admin_tpl('category_link');
			}
		}
	}
	/**
	 * 修改栏目
	 */
	public function edit() {
		$M = getcache('ask', 'commons');
		$M = $M[1];
		
		if(isset($_POST['dosubmit'])) {
			pc_base::load_sys_func('iconv');
			$catid = 0;
			$catid = intval($_POST['catid']);
			$setting = $_POST['setting'];
			//栏目生成静态配置
			if($_POST['type'] != 2) {
				$setting['category_ruleid'] = $M['category_ruleid'];
			}
			//内容页URL规则
			$setting['show_ruleid'] = $M['show_ruleid'];
			if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
			$_POST['info']['sethtml'] = $setting['create_to_html_root'];
			$_POST['info']['setting'] = array2string($setting);
			$_POST['info']['module'] = 'ask';
			$catname = CHARSET == 'gbk' ? $_POST['info']['catname'] : iconv('utf-8','gbk',$_POST['info']['catname']);
			$letters = gbk_to_pinyin($catname);
			$_POST['info']['letter'] = strtolower(implode('', $letters));
			$_POST['info']['catdir'] = $_POST['info']['catdir'] ? $_POST['info']['catdir'] : str_replace(array('/'),array('_'),$_POST['info']['letter']);
			
			//应用权限设置到子栏目
			if($_POST['priv_child']) {
				$arrchildid = $this->db->get_one(array('catid'=>$catid), 'arrchildid');
				if(!empty($arrchildid['arrchildid'])) {
					$arrchildid_arr = explode(',', $arrchildid['arrchildid']);
					if(!empty($arrchildid_arr)) {
						foreach ($arrchildid_arr as $arr_v) {
							$this->update_priv($arr_v, $_POST['priv_groupid'], 0);
						}
					}
				}
				
			}
			
			$this->db->update($_POST['info'],array('catid'=>$catid,'siteid'=>$this->siteid));
			$this->update_priv($catid, $_POST['priv_roleid']);
			$this->update_priv($catid, $_POST['priv_groupid'],0);
			$this->cache();
			//更新附件状态
			if($_POST['info']['image'] && pc_base::load_config('system','attachment_stat')) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($_POST['info']['image'],'catid-'.$catid,1);
			}
			showmessage(L('operation_success').'<script type="text/javascript">window.top.art.dialog({id:"test"}).close();window.top.art.dialog({id:"test",content:\'<h2>'.L("operation_success").'</h2><span style="fotn-size:16px;">'.L("edit_following_operation").'</span><br /><ul style="fotn-size:14px;"><li><a href="?m=ask&c=category&a=public_cache&menuid='.$_GET['menuid'].'&module=ask" target="right"  onclick="window.top.art.dialog({id:\\\'test\\\'}).close()">'.L("following_operation_1").'</a></li></ul>\',width:"400",height:"200"});</script>','?m=ask&c=category&a=init&module=ask&menuid='.$_GET['menuid']);
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			
			
			$show_validator = $catid = $r = '';
			$catid = intval($_GET['catid']);
			pc_base::load_sys_class('form','',0);
			$r = $this->db->get_one(array('catid'=>$catid));
			if($r) extract($r);
			$setting = string2array($setting);
			
			$this->priv_db = pc_base::load_model('category_priv_model');
			$this->privs = $this->priv_db->select(array('catid'=>$catid));
			
			$type = $_GET['type'];
			if($type==0) {
				include $this->admin_tpl('category_edit');
			}
		}	
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			foreach($_POST['listorders'] as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('catid'=>$id));
			}
			$this->cache();
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 删除栏目
	 */
	public function delete() {
		$catid = intval($_GET['catid']);
		$categorys = getcache('category_ask_'.$this->siteid,'commons');
		if($categorys[$catid][items]) showmessage(L('category_does_not_allow_delete'));
		$this->delete_child($catid);
		$this->db->delete(array('catid'=>$catid));
		if($categorys[$catid]['parentid']=='0') $this->creat_posts_table($catid,$delete = 1);
		$this->cache();
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 递归删除栏目
	 * @param $catid 要删除的栏目id
	 */
	private function delete_child($catid) {
		$catid = intval($catid);
		if (empty($catid)) return false;
		$r = $this->db->get_one(array('parentid'=>$catid));
		if($r) {
			$this->delete_child($r['catid']);
			$this->db->delete(array('catid'=>$r['catid']));
		}
		return true;
	}
	/**
	 * 更新缓存
	 */
	public function cache() {
		$categorys = array();

		$array = array();
		$categorys = $this->db->select(array('siteid'=>$this->siteid,'module'=>'ask'),'catid,siteid',20000,'listorder ASC');
		foreach ($categorys as $r) {
			$array[$r['catid']] = $r['siteid'];
		}
		setcache('category_ask',$array,'commons');
		$categorys = $this->categorys = array();
		$this->categorys = $this->db->select(array('siteid'=>$this->siteid,'module'=>'ask'),'*',10000,'listorder ASC');
		foreach($this->categorys as $r) {
			unset($r['module']);
			$setting = string2array($r['setting']);
			$r['create_to_html_root'] = $setting['create_to_html_root'];
			$r['ishtml'] = $setting['ishtml'];
			$r['content_ishtml'] = $setting['content_ishtml'];
			$r['category_ruleid'] = $setting['category_ruleid'];
			$r['show_ruleid'] = $setting['show_ruleid'];
			$r['workflowid'] = $setting['workflowid'];
			$r['isdomain'] = '0';
			if(!preg_match('/^(http|https):\/\//', $r['url'])) {
				$r['url'] = siteurl($r['siteid']).$r['url'];
			} elseif ($r['ishtml']) {
				$r['isdomain'] = '1';
			}
			
			//得到顶级栏目ID,写入栏目缓存
			$pp = string2array("array(".$r['arrparentid'].")");
			if(count($pp)>1){
				$r['topparentid'] = $pp[1];
			}else{
				$r['topparentid'] = $r['catid'];
			}
			
			$categorys[$r['catid']] = $r;
		}
		setcache('category_ask_'.$this->siteid,$categorys,'commons');
		return true;
	}
	/**
	 * 更新缓存并修复栏目
	 */
	public function public_cache() {
		$this->repair();
		$this->cache();
		showmessage(L('operation_success'),'?m=ask&c=category&a=init&module=ask&menuid='.$_GET['menuid']);
	}
	/**
	* 修复栏目数据
	*/
	private function repair() {
		pc_base::load_sys_func('iconv');
		@set_time_limit(600);
		$html_root = pc_base::load_config('system','html_root');
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->db->select(array('siteid'=>$this->siteid,'module'=>'ask'), '*', '', 'listorder ASC, catid ASC', '', 'catid');
		
		$this->get_categorys($categorys);
		if(is_array($this->categorys)) {
			foreach($this->categorys as $catid => $cat) {
				if($cat['type'] == 2) continue;
				$arrparentid = $this->get_arrparentid($catid);
				$setting = string2array($cat['setting']);
				$arrchildid = $this->get_arrchildid($catid);
				$child = is_numeric($arrchildid) ? 0 : 1;
				if($categorys[$catid]['arrparentid']!=$arrparentid || $categorys[$catid]['arrchildid']!=$arrchildid || $categorys[$catid]['child']!=$child) $this->db->update(array('arrparentid'=>$arrparentid,'arrchildid'=>$arrchildid,'child'=>$child),array('catid'=>$catid));

				$parentdir = $this->get_parentdir($catid);
				$catname = $cat['catname'];
				$letters = gbk_to_pinyin($catname);
				$letter = strtolower(implode('', $letters));
				$listorder = $cat['listorder'] ? $cat['listorder'] : $catid;
				
				
				$url = $this->update_url($catid);
				$url = APP_PATH.$url;
				if($cat['url']!=$url) $this->db->update(array('url'=>$url), array('catid'=>$catid));
				
				
				
				if($categorys[$catid]['parentdir']!=$parentdir || $categorys[$catid]['sethtml']!=$sethtml || $categorys[$catid]['letter']!=$letter || $categorys[$catid]['listorder']!=$listorder) $this->db->update(array('parentdir'=>$parentdir,'sethtml'=>$sethtml,'letter'=>$letter,'listorder'=>$listorder), array('catid'=>$catid));
			}
		}
		
		//删除在非正常显示的栏目
		foreach($this->categorys as $catid => $cat) {
			if($cat['parentid'] != 0 && !isset($this->categorys[$cat['parentid']])) {
				$this->db->delete(array('catid'=>$catid));
			}
		}
		return true;
	}
	/**
	 * 获取父栏目是否生成到根目录
	 */
	private function get_sethtml($catid) {
		foreach($this->categorys as $id => $cat) {
			if($catid==$id) {
				$parentid = $cat['parentid'];
				if($this->categorys[$parentid]['sethtml']) {
					$this->sethtml = 1;
				}
				if($parentid) {
					$this->get_sethtml($parentid);
				}
			}
		}
	}
	
	/**
	 * 找出子目录列表
	 * @param array $categorys
	 */
	private function get_categorys($categorys = array()) {
		if (is_array($categorys) && !empty($categorys)) {
			foreach ($categorys as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k=>$_v) {
					if($_v['parentid']) $result[] = $_v;
				}
				$this->get_categorys($r);
			}
		} 
		return true;
	}
	/**
	* 更新栏目链接地址
	*/
	private function update_url($catid) {
		$catid = intval($catid);
		if (!$catid) return false;
		$url = pc_base::load_app_class('ask_url', 'ask'); //调用URL实例
		return $url->category_url($catid);
	}

	/**
	 * 
	 * 获取父栏目ID列表
	 * @param integer $catid              栏目ID
	 * @param array $arrparentid          父目录ID
	 * @param integer $n                  查找的层次
	 */
	private function get_arrparentid($catid, $arrparentid = '', $n = 1) {
		if($n > 5 || !is_array($this->categorys) || !isset($this->categorys[$catid])) return false;
		$parentid = $this->categorys[$catid]['parentid'];
		$arrparentid = $arrparentid ? $parentid.','.$arrparentid : $parentid;
		if($parentid) {
			$arrparentid = $this->get_arrparentid($parentid, $arrparentid, ++$n);
		} else {
			$this->categorys[$catid]['arrparentid'] = $arrparentid;
		}
		$parentid = $this->categorys[$catid]['parentid'];
		return $arrparentid;
	}

	/**
	 * 
	 * 获取子栏目ID列表
	 * @param $catid 栏目ID
	 */
	private function get_arrchildid($catid) {
		$arrchildid = $catid;
		if(is_array($this->categorys)) {
			foreach($this->categorys as $id => $cat) {
				if($cat['parentid'] && $id != $catid && $cat['parentid']==$catid) {
					$arrchildid .= ','.$this->get_arrchildid($id);
				}
			}
		}
		return $arrchildid;
	}
	/**
	 * 获取父栏目路径
	 * @param  $catid
	 */
	function get_parentdir($catid) {
		if($this->categorys[$catid]['parentid']==0) return '';
		$r = $this->categorys[$catid];
		$setting = string2array($r['setting']);
		$url = $r['url'];
		$arrparentid = $r['arrparentid'];
		unset($r);
		if (strpos($url, '://')===false) {
			if ($setting['creat_to_html_root']) {
				return '';
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				foreach($arrparentid as $id) {
					if($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
				}
				return implode('/', $arrcatdir).'/';
			}
		} else {
			if ($setting['create_to_html_root']) {
				if (preg_match('/^((http|https):\/\/)?([^\/]+)/i', $url, $matches)) {
					$url = $matches[0].'/';
					$rs = $this->db->get_one(array('url'=>$url), '`parentdir`,`catid`');
					if ($catid == $rs['catid']) return '';
					else return $rs['parentdir'];
				} else {
					return '';
				}
			} else {
				$arrparentid = explode(',', $arrparentid);
				$arrcatdir = array();
				krsort($arrparentid);
				foreach ($arrparentid as $id) {
					if ($id==0) continue;
					$arrcatdir[] = $this->categorys[$id]['catdir'];
					if ($this->categorys[$id]['parentdir'] == '') break;
				}
				krsort($arrcatdir);
				return implode('/', $arrcatdir).'/';
			}
		}
	}
	/**
	 * 检查目录是否存在
	 * @param  $return_method 返回方法
	 * @param  $catdir 目录
	 */
	public function public_check_catdir($return_method = 1,$catdir = '') {
		$old_dir = '';
		$catdir = $catdir ? $catdir : $_GET['catdir'];
		$old_dir = $_GET['old_dir'];
		$r = $this->db->get_one(array('siteid'=>$this->siteid,'module'=>'ask','catdir'=>$catdir));
		if($r && $old_dir != $r['catdir']) {
			//目录存在
			if($return_method) {
				exit('0');
			} else {
				return false;
			}
		} else {
			if($return_method) {
				exit('1');
			} else {
				return true;
			}
		}
	}
	
	/**
	 * 更新权限
	 * @param  $catid
	 * @param  $priv_datas
	 * @param  $is_admin
	 */
	private function update_priv($catid,$priv_datas,$is_admin = 1) {
		$this->priv_db = pc_base::load_model('category_priv_model');
		$this->priv_db->delete(array('catid'=>$catid,'is_admin'=>$is_admin));
		if(is_array($priv_datas) && !empty($priv_datas)) {
			foreach ($priv_datas as $r) {
				$r = explode(',', $r);
				$action = $r[0];
				$roleid = $r[1];
				$this->priv_db->insert(array('catid'=>$catid,'roleid'=>$roleid,'is_admin'=>$is_admin,'action'=>$action,'siteid'=>$this->siteid));
			}
		}
	}

	/**
	 * 检查栏目权限
	 * @param $action 动作
	 * @param $roleid 角色
	 * @param $is_admin 是否为管理组
	 */
	private function check_category_priv($action,$roleid,$is_admin = 1) {
		$checked = '';
		foreach ($this->privs as $priv) {
			if($priv['is_admin']==$is_admin && $priv['roleid']==$roleid && $priv['action']==$action) $checked = 'checked';
		}
		return $checked;
	}
	/**
	 * 重新统计栏目信息数量
	 */
	public function count_items() {
		$this->content_db = pc_base::load_model('content_model');
		$result = getcache('category_ask_'.$this->siteid,'commons');
		foreach($result as $r) {
			if($r['type'] == 0) {
				$modelid = $r['modelid'];
				$this->content_db->set_model($modelid);
				$number = $this->content_db->count(array('catid'=>$r['catid']));
				$this->db->update(array('items'=>$number),array('catid'=>$r['catid']));
			}
		}
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * json方式加载模板
	 */
	public function public_tpl_file_list() {
		$style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
		$catid = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : 0;
		$batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
		if ($catid) {
			$cat = getcache('category_ask_'.$this->siteid,'commons');
			$cat = $cat[$catid];
			$cat['setting'] = string2array($cat['setting']);
		}
		pc_base::load_sys_class('form','',0);
		if($_GET['type']==1) {
			$html = array('page_template'=>form::select_template($style, 'content',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
		} else {
			$html = array('category_template'=> form::select_template($style, 'content',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
			);
		}
		if ($_GET['module']) {
			unset($html);
			if ($_GET['templates']) {
				$templates = explode('|', $_GET['templates']);
				if ($_GET['id']) $id = explode('|', $_GET['id']);
				if (is_array($templates)) {
					foreach ($templates as $k => $tem) {
						$t = $tem.'_template';
						if ($id[$k]=='') $id[$k] = $tem;
						$html[$t] = form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
					}
				}
			}
			
		}
		if (CHARSET == 'gbk') {
			$html = array_iconv($html, 'gbk', 'utf-8');
		}
		echo json_encode($html);
	}

	/**
	 * 快速进入搜索
	 */
	public function public_ajax_search() {
		if($_GET['catname']) {
			if(preg_match('/([a-z]+)/i',$_GET['catname'])) {
				$field = 'letter';
				$catname = strtolower(trim($_GET['catname']));
			} else {
				$field = 'catname';
				$catname = trim($_GET['catname']);
				if (CHARSET == 'gbk') $catname = iconv('utf-8','gbk',$catname);
			}
			$result = $this->db->select("$field LIKE('$catname%') AND siteid='$this->siteid' AND child=0",'catid,type,catname,letter',10);
			if (CHARSET == 'gbk') {
				$result = array_iconv($result, 'gbk', 'utf-8');
			}
			echo json_encode($result);
		}
	}
	/**
	 * json方式读取风格列表，推送部分调用
	 */
	public function public_change_tpl() {
		pc_base::load_sys_class('form','',0);
		$models = getcache('model','commons');
		$modelid = intval($_GET['modelid']);
		if($_GET['modelid']) {
			$style = $models[$modelid]['default_style'];
			$category_template = $models[$modelid]['category_template'];
			$list_template = $models[$modelid]['list_template'];
			$show_template = $models[$modelid]['show_template'];
			$html = array(
				'template_list'=> $style, 
				'category_template'=> form::select_template($style, 'content',$category_template,'name="setting[category_template]"','category'), 
				'list_template'=>form::select_template($style, 'content',$list_template,'name="setting[list_template]"','list'),
				'show_template'=>form::select_template($style, 'content',$show_template,'name="setting[show_template]"','show')
			);
			if (CHARSET == 'gbk') {
				$html = array_iconv($html, 'gbk', 'utf-8');
			}
			echo json_encode($html);
		}
	}
	/**
	 * 批量修改
	 */
	public function batch_edit() {
		$categorys = getcache('category_ask_'.$this->siteid,'commons');
		if(isset($_POST['dosubmit'])) {
			
			pc_base::load_sys_func('iconv');	
			$catid = intval($_POST['catid']);
			$post_setting = $_POST['setting'];
			//栏目生成静态配置
			$infos = $info = array();
			$infos = $_POST['info'];
			if(empty($infos)) showmessage(L('operation_success'));
			$this->attachment_db = pc_base::load_model('attachment_model');
			foreach ($infos as $catid=>$info) {
				$setting = string2array($categorys[$catid]['setting']);
				if($_POST['type'] != 2) {
					if($post_setting[$catid]['ishtml']) {
						$setting['category_ruleid'] = $_POST['category_html_ruleid'][$catid];
					} else {
						$setting['category_ruleid'] = $_POST['category_php_ruleid'][$catid];
						$info['url'] = '';
					}
				}
				foreach($post_setting[$catid] as $_k=>$_setting) {
					$setting[$_k] = $_setting;
				}
				//内容生成静态配置
				if($post_setting[$catid]['content_ishtml']) {
					$setting['show_ruleid'] = $_POST['show_html_ruleid'][$catid];
				} else {
					$setting['show_ruleid'] = $_POST['show_php_ruleid'][$catid];
				}
				if($setting['repeatchargedays']<1) $setting['repeatchargedays'] = 1;
				$info['sethtml'] = $post_setting[$catid]['create_to_html_root'];
				$info['setting'] = array2string($setting);
				
				$info['module'] = 'content';
				$catname = CHARSET == 'gbk' ? $info['catname'] : iconv('utf-8','gbk',$info['catname']);
				$letters = gbk_to_pinyin($catname);
				$info['letter'] = strtolower(implode('', $letters));
				$this->db->update($info,array('catid'=>$catid,'siteid'=>$this->siteid));

				//更新附件状态
				if($info['image'] && pc_base::load_config('system','attachment_stat')) {
					$this->attachment_db->api_update($info['image'],'catid-'.$catid,1);
				}
			}
			$this->public_cache();
			showmessage(L('operation_success'),'?m=ask&c=category&a=init&module=ask&menuid='.$_GET['menuid']);
		} else {
			if(isset($_POST['catids'])) {
				//获取站点模板信息
				pc_base::load_app_func('global');
				$template_list = template_list($this->siteid, 0);
				foreach ($template_list as $k=>$v) {
					$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
					unset($template_list[$k]);
				}
				
				$show_validator = $show_header = '';
				$catid = intval($_GET['catid']);
				$type = $_POST['type'] ? intval($_POST['type']) : 0;
				pc_base::load_sys_class('form','',0);
				
				if(empty($_POST['catids'])) showmessage(L('illegal_parameters'));
				$batch_array = $workflows = array();
				foreach ($categorys as $catid=>$cat) {
					if($cat['type']==$type && in_array($catid, $_POST['catids'])) {
						$batch_array[$catid] = $cat;
					}
				}
				if(empty($batch_array)) showmessage(L('please_select_category')); 
				$workflows = getcache('workflow_'.$this->siteid,'commons');
				if($workflows) {
					$workflows_datas = array();
					foreach($workflows as $_k=>$_v) {
						$workflows_datas[$_v['workflowid']] = $_v['workname'];
					}
				}
				
				if($type==1) {
					include $this->admin_tpl('category_batch_edit_page');
				} else {
					include $this->admin_tpl('category_batch_edit');
				}
			} else {
				$type = isset($_GET['select_type']) ? intval($_GET['select_type']) : 0;
				
				$tree = pc_base::load_sys_class('tree');
				$tree->icon = array('&nbsp;&nbsp;│ ','&nbsp;&nbsp;├─ ','&nbsp;&nbsp;└─ ');
				$tree->nbsp = '&nbsp;&nbsp;';
				$category = array();
				foreach($categorys as $catid=>$r) {
					if($this->siteid != $r['siteid'] || ($r['type']==2 && $r['child']==0)) continue;
					$category[$catid] = $r;
				}
				$str  = "<option value='\$catid' \$selected>\$spacer \$catname</option>";
	
				$tree->init($category);
				$string .= $tree->get_tree(0, $str);
				include $this->admin_tpl('category_batch_select');
			}
		}	
	}
	
	
	/**
	 * 创建新的回复ask_posts_*数据表
	 * @param integer $pid 创建新的回复表
	 * @param integer $delete 是否执行删除操作
	 */
	public function creat_posts_table($catid = '',$delete = 0) {
		$catid = intval($catid);
		if(!$catid) return false;
		//删除表
		if($delete && $this->db->table_exists('ask_posts_'.$catid)){
			$this->db->query("DROP TABLE IF EXISTS `".$this->db->db_tablepre."ask_posts_".$catid."`;");
			return true;
		}
		if ($this->db->table_exists('ask_posts_'.$catid))  return false;
		if ($this->db->query("CREATE TABLE `".$this->db->db_tablepre."ask_posts_".$catid."` (`pid` int(10) unsigned NOT NULL auto_increment,
					  `siteid` smallint(5) unsigned NOT NULL default '0',
					  `askid` mediumint(8) unsigned NOT NULL default '0',
					  `catid` smallint(5) unsigned NOT NULL default '0',
					  `isask` tinyint(1) unsigned NOT NULL default '0',
					  `message` mediumtext NOT NULL,
					  `addtime` int(10) unsigned NOT NULL default '0',
					  `candidate` tinyint(1) unsigned NOT NULL default '0',
					  `optimal` tinyint(1) unsigned NOT NULL default '0',
					  `reversion` tinyint(1) unsigned NOT NULL default '0',
					  `userid` int(10) unsigned NOT NULL,
					  `status` tinyint(1) unsigned NOT NULL default '0',
					  `anonymity` tinyint(1) unsigned NOT NULL default '0',
					  `username` char(20) NOT NULL,
					  `solvetime` int(10) unsigned NOT NULL default '0',
					  `votecount` smallint(5) unsigned NOT NULL default '0',PRIMARY KEY  (`pid`),
					  `best_answer_vote_1` smallint(5) unsigned NOT NULL default '0',
					  `best_answer_vote_2` smallint(5) unsigned NOT NULL default '0',
					  KEY `askid` (`askid`,`status`,`pid`,`siteid`,`catid`),
					  KEY `userid` (`userid`,`askid`,`siteid`,`catid`),
					  KEY `catid` (`catid`))
					  ENGINE=MyISAM;")) {
			return $catid;
		} else {
			return false;
		}
	}
}
?>