 <?php 
defined('IN_PHPCMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$category_db = pc_base::load_model('category_model');
$urlrule_db = pc_base::load_model('urlrule_model');
$catid = $category_db->delete(array('module'=>'ask'));
$urlrule_db->delete(array('module'=>'ask'));
if(!$catid) return FALSE;
 ?>