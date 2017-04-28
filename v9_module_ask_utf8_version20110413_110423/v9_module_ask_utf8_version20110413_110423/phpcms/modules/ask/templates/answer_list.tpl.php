<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('center_frame').src = '?m=ask&c=answer&a=public_categorys&type=add&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>';
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
	<UL>
<?php if(!$_GET['catid']) {?>
		<?php
		if(is_array($categorys)){
			foreach($categorys as $cat){
		?>
					<li style="font-size:14px; float:left; width:25%; height:30px;"><a href="index.php?m=ask&c=answer&a=init&menuid=<?php echo $_GET['menuid'];?>&catid=<?=$cat['catid']?>&dosubmit=1"><?=$cat['catname']?></a> <span style="color:#999999">(<?= $cat_posts_num[$cat[catid]]?>)</span></li>
		<?php
			}
		}
		?>
	</UL>

<?php }else {?>


<form name="searchform" action="" method="get" >
<input type="hidden" value="ask" name="m">
<input type="hidden" value="answer" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" value="<?php echo $_GET['menuid']?>" name="menuid">
<input type="hidden" value="<?php echo $_GET['catid']?>" name="info[catid]">
<div class="explain-col search-form">
<?php echo L('keywords')?>  <input type="text" value="<?php echo $keywords?>" class="input-text" name="info[keywords]"> 
<?php echo L('username')?>  <input type="text" value="<?php echo $username?>" class="input-text" name="info[username]"> 
<?php echo L('addtime')?>  <?php echo form::date('info[start_addtime]',$start_addtime)?><?php echo L('to')?>   <?php echo form::date('info[end_addtime]',$end_addtime)?> 
<input type="checkbox" name="info[status]" value="1" <?php if($_GET[info]['status']==1) echo "checked"; ?> /> 待审
<input type="checkbox" name="info[optimal]" value="1" <?php if($_GET[info]['optimal']==1) echo "checked"; ?> /> 被采纳
<input type="checkbox" name="info[candidate]" value="1" <?php if($_GET[info]['candidate']==1) echo "checked"; ?> /> 投票
<input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
<a href="index.php?m=ask&c=answer&a=init&catid=<?php echo $_GET['catid']?>&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>">清空条件</a>
</div>
</form>

<form name="myform" id="myform" action="?m=ask&c=answer&a=delete&catid=<?php echo $_GET['catid']?>" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
		<th align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('pid[]');"></td>
		<th align="center">PID</td>
		<th align="center"><?php echo L('ask_id')?></td>
		<th width="50%" align="center"><?php echo L('answer_description')?></th>
		<th align="center"><?php echo L('answer_author')?></td>
		<th align="center"><?php echo L('answer_addtime')?></td>
		<th align="center"><?php echo L('status')?></td>
		<th align="center"><?php echo L('operations_manage')?></td>
	</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>

	<tr>
		   <td align="center"><input type="checkbox" name="pid[]"  id="checkbox" value="<?=$info['pid']?>"></td>
			<td align="center"><?=$info['pid']?></td>
			<td align="center"><? echo $info['askid']?></td>
			<td align="left"><a href="javascript:view_answer('<? echo $info['askid']?>', '<? echo $info['pid']?>', '<? echo $info['catid']?>')"><?=str_cut($info['message'],150)?></a></td>
			<td align="center"><a href="?mod=member&file=member&action=view&userid=<?=$info['userid']?>"><?=$info['username']?></a></td>
			<td align="center"><?=date('m-d H:i',$info['addtime'])?></td>
			<td align="center"><?php if($info['status']==1) echo "<font color=#ff3300>待审</font>"; ?> <?php if($info['optimal']) echo "<font color=#009900>已采纳</font>";else echo '未采纳';?></td>
			<td align="center"><a href="javascript:view_ask('<? echo $info['askid']?>')">原帖</a> | <a href="###"
			onclick="edit(<?php echo $info['askid']?>,<?php echo $info['pid']?>, '修改回答')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a href="index.php?m=ask&c=answer&a=delete&askid=<?=$info['askid']?>&pid=<?=$info['pid']?>&catid=<?=$info['catid']?>" onclick="return confirm('您确定要删除此项吗？')">删除</a>
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
<input name="dosubmit" type="submit" class="button" value="<?php echo L('delete')?>">

	<?php if($_GET[info]['status']==1){ ?>
	&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=answer&a=check&catid=<?php echo $_GET['catid']?>&status=3'" value="<?php echo L('pass_check')?>"/>
	<?php }else{ ?>
	&nbsp;&nbsp;<input type="submit" class="button" name="dosubmit" onClick="document.myform.action='?m=ask&c=answer&a=check&catid=<?php echo $_GET['catid']?>&status=1'" value="<?php echo L('unpass_check')?>"/>
	<?php } ?>
	
</div>
<div id="pages"><?php echo $pages?></div>
</form>
<?php } ?>
</div>
<script type="text/javascript">

function edit(askid, id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=ask&c=answer&a=edit&askid='+askid+'&pid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function view_answer(askid,pid,catid) {
	window.top.art.dialog({id:'view_answer'}).close();
	window.top.art.dialog({title:'查看回答',id:'view_answer',iframe:'?m=ask&c=answer&a=view_answer&askid='+askid+'&pid='+pid+'&catid='+catid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'view_answer'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'view_answer'}).close()});
}
function view_ask(askid) {
	window.top.art.dialog({id:'view_ask'}).close();
	window.top.art.dialog({title:'查看问题',id:'view_ask',iframe:'?m=ask&c=ask&a=view_ask&askid='+askid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'view_ask'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'view_ask'}).close()});
}

</script>
</body>
</html>
