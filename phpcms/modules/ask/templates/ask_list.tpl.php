<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('center_frame').src = '?m=ask&c=ask&a=public_categorys&type=add&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>';
	window.top.$("#current_pos").data('clicknum',0);
}
$(document).ready(function(){
	setInterval(closeParent,2000);
});
function closeParent() {
	if($('#closeParentTime').html() == '') {
		window.top.$(".left_menu").addClass("left_menu_on");
		window.top.$("#openClose").addClass("close");
		window.top.$("html").addClass("on");
		$('#closeParentTime').html('1');
		window.top.$("#openClose").data('clicknum',1);
	}
}
//-->
</SCRIPT>
<div class="pad-lr-10">


<div class="table-list">
<form name="searchform" action="" method="get" >
<input type="hidden" value="ask" name="m">
<input type="hidden" value="ask" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" value="<?php echo $_GET['menuid']?>" name="menuid">
<input type="hidden" value="<?php echo $_GET['catid']?>" name="info[catid]">
<div class="explain-col search-form">
<?php echo L('keywords')?>  <input type="text" value="<?php echo $keywords?>" class="input-text" name="info[keywords]"> 
<?php echo L('username')?>  <input type="text" value="<?php echo $username?>" class="input-text" name="info[username]"> 
<?php echo L('addtime')?>  <?php echo form::date('info[start_addtime]',$start_addtime)?><?php echo L('to')?>   <?php echo form::date('info[end_addtime]',$end_addtime)?> 
<?php echo form::select($ask_status,$status,'name="info[status]"', L('all_status'))?>  
<?php echo form::select($ask_flag,$flag,'name="info[flag]"', L('all_status'))?>  
<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
<a href="index.php?m=ask&c=ask&a=init&catid=<?php echo $_GET['catid']?>&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>">清空条件</a>
</div>
</form>
<form name="myform" id="myform" action="?m=ask&c=ask&a=delete&catid=<?php echo $_GET['catid']?>" method="post" >
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('askid[]');"></th>
			<th width="35" align="center"><?php echo L('ask_id')?></th>
			<th width="10%" align="center"><?php echo L('ask_catname')?></th>
			<th><?php echo L('ask_title')?></th>
			<th width="4%" align="center"><?php echo L('ask_author')?></th>
			<th width='13%' align="center"><?php echo L('ask_addtime')?></th>
			<th width='4%' align="center"><?php echo L('ask_reward')?></th>
			<th width='5%' align="center"><?php echo L('ask_posts')?></th>
			<th width='4%' align="center"><?php echo L('ask_hits')?></th>
			<th width="6%" align="center"><?php echo L('status')?></th>
			<th width="14%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center"><input type="checkbox" name="askid[]" value="<?php echo $info['askid']?>"></td>
		<td align="center"><?php echo $info['askid']?></td>
		<td align="center"><?php echo $CATEGORYS[$info[catid]][catname];?></td>
		<td><a href="<?php echo ask_url($info['catid'],$info['askid']);?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo $info['title']?></a> <?php if($info['flag']=='1') {echo '<img src="'.IMG_PATH.'icon/small_elite.gif" title="'.L('elite').'">'; } ?></td>
		<td align="center">
		<?php
		if($info['sysadd']==0) {
			echo "<a href='?m=member&c=member&a=memberinfo&username=".urlencode($info['username'])."&pc_hash=".$_SESSION['pc_hash']."' >".$info['username']."</a>"; 
			echo '<img src="'.IMG_PATH.'icon/contribute.png" title="'.L('member_contribute').'">';
		} else {
			echo $info['username'];
		}
		?></td>
		<td align="center"><?php echo format::date($info['addtime'],1);?></td>
		<td align="center"><?php echo $info['reward']?></td>
		<td align="center"><?php echo $info['answercount']?></td>
		<td align="center"><?php echo $info['hits']?></td>
		<td align="center">
		<?php if($info['status']==3) echo "<font color=#009900>待解决</font>";else if($info['status']==5) echo "<font color=#FF9900>已解决</font>";else if($info['status']==6) echo "<font color=#3300FF>已关闭</font>";else if($info['status']==1) echo "<font color=#ff3300>待审</font>";?>
		</td>
		<td align="center"><a href="javascript:view_ask('<? echo $info['askid']?>')">查看</a> | <a href="###"
			onclick="edit(<?php echo $info['askid']?>, '<?php echo new_addslashes($info['title'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='?m=ask&c=ask&a=delete&askid=<?php echo $info['askid']?>&catid=<?php echo $_GET['catid']?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['title'])))?>')"><?php echo L('delete')?></a> 
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('delete')?>">&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=ask&a=elite&catid=<?php echo $_GET['catid']?>'" value="<?php echo L('elite_ask')?>"/>&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=ask&a=un_elite&catid=<?php echo $_GET['catid']?>'" value="<?php echo L('un_elite_ask')?>"/>
	<?php if($_GET[info]['status']==1){ ?>
	&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=ask&a=check&catid=<?php echo $_GET['catid']?>&status=3'" value="<?php echo L('pass_check')?>"/>
	<?php }else{ ?>
	&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=ask&a=check&catid=<?php echo $_GET['catid']?>&status=1'" value="<?php echo L('unpass_check')?>"/>
	<?php } ?>
	</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript">

function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=ask&c=ask&a=edit&askid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function view_ask(askid) {
	window.top.art.dialog({id:'view_ask'}).close();
	window.top.art.dialog({title:'查看问题',id:'view_ask',iframe:'?m=ask&c=ask&a=view_ask&askid='+askid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'view_ask'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'view_ask'}).close()});
}
setcookie('refersh_time', 0);
function refersh_window() {
	var refersh_time = getcookie('refersh_time');
	if(refersh_time==1) {
		window.location.reload();
	}
}
setInterval("refersh_window()", 3000);
</script>
</body>
</html>
