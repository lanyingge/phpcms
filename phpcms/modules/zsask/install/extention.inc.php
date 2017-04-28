<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');


$parentid = $menu_db->insert(array('name'=>'zsask', 'parentid'=>'29', 'm'=>'zsask', 'c'=>'zsask', 'a'=>'init', 'data'=>'', 'listorder'=>20, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'check_question', 'parentid'=>$parentid, 'm'=>'zsask', 'c'=>'zsask', 'a'=>'check_question', 'data'=>'', 'listorder'=>1, 'display'=>1));
$menu_db->insert(array('name'=>'cat_list', 'parentid'=>$parentid, 'm'=>'zsask', 'c'=>'zsask', 'a'=>'cat_list', 'data'=>'', 'listorder'=>2, 'display'=>1));
$menu_db->insert(array('name'=>'add_cat', 'parentid'=>$parentid, 'm'=>'zsask', 'c'=>'zsask', 'a'=>'add', 'data'=>'', 'listorder'=>3, 'display'=>1));
$menu_db->insert(array('name'=>'ask_settings', 'parentid'=>$parentid, 'm'=>'zsask', 'c'=>'zsask', 'a'=>'settings', 'data'=>'', 'listorder'=>4, 'display'=>1));

$language = array('zsask'=>'问答模块', 'check_question'=>'审核', 'cat_list' => '管理栏目', 'add_cat' => '添加栏目', 'ask_settings'=> '问答配置');

?>