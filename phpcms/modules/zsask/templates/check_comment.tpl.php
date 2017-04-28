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
            <th width="50">ID</th>
			<th>评论 ( 审核 )</th>
            <th width="70">发布人</th>
            <th width="118">更新时间</th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	$aid = '';
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox" name="ids[]" value="<?php echo $info['cid']?>"></td>
		<td align="center" width="35"><?php echo $info['cid'];?></td>
		
		<td>答：
		<?php if ($aid !=$info['aid']) {$qinfo = get_qinfo($info['aid']); $aid =$info['aid'];}?>
		<a class="blue" href="<?php if (empty($qinfo['url'])) {echo ASK_SHOW.$qinfo['aid'];}else{echo $qinfo['url'];}?>" target="_blank"><?php echo $qinfo['question']?></a>
		<br />评：<?php echo $info['content']?> 
		 [<?php echo '<span id="cid_'.$info['cid'].'"><a style="color:red" href="javascript:;" onclick="pass_answer('.$info['cid'].')">审核</a></span>';	?>]
		</td>
		
		<td align="center"><?php if ($info['userid']) { echo get_nickname($info['userid'], 'username'); }else {echo $info['ip'];} ?></td>
		<td align="center"><?php echo format_date($info['updatetime'])?></td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
	<input class="button" type="button" 
	onclick="myform.action='?m=zsask&c=zsask&a=check_comment&dosubmit=1';checkuid()" value="通过审核">&nbsp;&nbsp;
	<input type="button" class="button" name="btn_del" 
	onClick="document.myform.action='?m=zsask&c=zsask&a=delcomment&dosubmit=1';confirm_delete()" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function pass_answer(cid) {
	$.get('?m=zsask&c=zsask&a=check_comment&ajax=1&pc_hash=<?php echo $_SESSION["pc_hash"];?>&cid='+ cid, function(data) {
		if (data =="1") {
			window.top.art.dialog({content:"审核成功！",lock:true,width:'200',height:'50',time:1},function(){return false});
			$('#cid_'+ cid).parent().parent().fadeOut('slow');
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

</script>
</body>
</html>
