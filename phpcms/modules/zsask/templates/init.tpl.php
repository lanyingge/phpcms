<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<?php echo $catmenu; ?>
<form name="myform" id="myform" action="?m=zsask&c=zsask&a=listorder&dosubmit=1" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			 <th width="16"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
            <th width="37"><?php echo L('listorder');?></th>
            <th width="40">ID</th>
			<th>问题 ( 最后回复 )</th>
			<th width="100">所属栏目</th>
            <th width="150">回答 | 评论 | 浏览</th>
            <th width="70">发布人</th>
            <th width="118">更新时间</th>
			<th width="110">管理操作</th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="ids[]" value="<?php echo $info['qid']?>"></td>
		<td align="center" width="35">
		<input name='listorders[<?php echo $info['qid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
		<td align="center" width="35"><?php echo $info['qid'];?></td>
		
		<td><a href="<?php if (empty($info['url'])) {echo ASK_SHOW.$info['qid'];}else{echo $info['url'];}?>" target="_blank"><?php echo $info['question']?></a> 
		 [<?php if ($info['aid']) {echo '<span style="color:green">已解决</span>';}
		 		elseif ($info['status'] ==1) {echo '<span id="qid_'.$info['qid'].'"><a style="color:red" href="javascript:;" onclick="pass_question('.$info['qid'].')">未审核</a></span>';}
		 		else {echo '<span style="color:#999">'.format_date(get_lastanswer($info['qid'])).'</span>';}?>]
		</td>
		
		<td><?php echo $ZSASKCATE[$info['catid']]['catname']?></td>
		<td align="center">
		<?php echo get_answer($info['qid']);?> | 
		<?php echo get_comment($info['qid']);?> | 
		<?php echo get_counts($info['qid'], '', 'zsask');?>
		</td>
		<td align="center"><?php if ($info['userid']) { echo get_nickname($info['userid'], 'username'); }else {echo $info['ip'];} ?></td>
		<td align="center"><?php echo format_date($info['updatetime'])?></td>
		<td align="center"><a href="<?php echo APP_PATH.'index.php?m=zsask&a=answer&qid='.$info['qid'];?>" target="_blank">修改</a>
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
<input name="dosubmit" type="submit" class="button" onclick="myform.submit();"
	value="<?php echo L('listorder')?>">&nbsp;&nbsp;
	<input class="button" type="button" 
	onclick="myform.action='?m=zsask&c=zsask&a=check_question&dosubmit=1';checkuid()" value="通过审核">&nbsp;&nbsp;
	<input type="button" class="button" name="btn_del" 
	onClick="document.myform.action='?m=zsask&c=zsask&a=delquestion&dosubmit=1';confirm_delete()" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function pass_question(qid) {
	$.get('?m=zsask&c=zsask&a=check_question&ajax=1&pc_hash=<?php echo $_SESSION["pc_hash"];?>&qid='+ qid, function(data) {
		if (data =="1") {
			window.top.art.dialog({content:"审核成功！",lock:true,width:'200',height:'50',time:1},function(){return false});
			$('#qid_'+ qid).html('');
		}else {
			alert('审核失败！');
		}
	});
}

function confirm_delete(){
	if(confirm('你确认要删除选中？')) checkuid();
	else 
		return false;
}
function checkuid() {
	var ids='';
	$("input[name='ids[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {//alert(ids);
		window.top.art.dialog({content:"至少选中一项！",lock:true,width:'200',height:'50',time:1.5},function(){return false});
		return false;
	}else {
		$('#myform').submit();
	}
}

function edit(id, title) {

	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({yesText:'关闭',title:title,
		id:'edit',iframe:'index.php?m=zsask&c=zsask&a=edit&id='+id,width:'900',height:'600'}, 
		function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;
		var form = d.document.getElementById('dosubmit');form.click();return false;}, 
		function(){window.top.art.dialog({id:'edit'}).close()});
	
}

<?php if (self::$check_question || self::$check_answer || self::$check_comment) {?>
$('.subnav em:eq(1)').append('<img style="padding-bottom:2px" src="<?php echo IMG_PATH;?>icon/new.png">');
<?php }?>

</script>
</body>
</html>
