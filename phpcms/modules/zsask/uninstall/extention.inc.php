 <?php 
defined('IN_PHPCMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$type_db = pc_base::load_model('type_model');
$hits_db = pc_base::load_model('hits_model');
$typeid = $type_db->delete(array('module'=>'zsask'));
$hits = $hits_db->delete(' hitsid LIKE "zsask-%" AND catid=9998');

if(!$typeid) return FALSE;
 ?>