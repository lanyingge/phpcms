<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'ask', 'parentid'=>29, 'm'=>'ask', 'c'=>'ask', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'answer', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'answer', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'actor', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'ask', 'a'=>'actor', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'credit', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'ask', 'a'=>'credit', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'ask_setting', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'ask', 'a'=>'setting', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'add_category', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'category', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'list_category', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'category', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'cache_category', 'parentid'=>$parentid, 'm'=>'ask', 'c'=>'category', 'a'=>'public_cache', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$ask_db = pc_base::load_model('ask_model');

$language = array('ask'=>'问吧','ask_manage'=>'问题管理', 'answer'=>'回答管理', 'actor'=>'头衔管理', 'credit'=>'积分管理', 'ask_setting'=>'模块配置', 'add_category'=>'添加栏目', 'list_category'=>'栏目管理', 'cache_category'=>'更新栏目缓存');
?>