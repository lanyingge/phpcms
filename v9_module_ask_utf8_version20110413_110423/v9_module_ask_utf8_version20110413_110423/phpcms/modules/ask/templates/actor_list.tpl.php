<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=ask&c=ask&a=actor_delete" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('id[]');"></th>
			<th width="10%"  >ID</th>
			<th width="14%"  >所属系列</th>
			<th width="10%"  >等级</th>
			<th width="18%"  >头衔名称</th>
			<th width="30%"  >积分</th>
			<th width="15%"  >管理操作</th>
			</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center"><input type="checkbox" name="id[]" value="<?php echo $info['id']?>"></td>
		<td  style="text-align:center;"><? echo $info['id']?></td>
		<td  style="text-align:center;"><? echo $TYPES[$info['typeid']]?></td>
		<td  style="text-align:center;"><? echo $info['grade']?></td>
		<td  style="text-align:center;"><? echo $info['actor'];?></td>
		<td  style="text-align:center;"><? echo $info['min'];?>&nbsp;--&nbsp;<? echo $info['max']?></td>
		<td  style="text-align:center;"><a href="###"
			onclick="edit(<?php echo $info['id']?>, '<? echo $info['typeid']?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> | <a href="index.php?m=ask&c=ask&a=actor_delete&id=<? echo $info['id']?>&typeid=<? echo $info['typeid']?>" onclick="return confirm('您确定要删除此项吗？')">删除</a>
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
	value="<?php echo L('delete')?>"></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, typeid) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> <?php echo L('actor')?> ',id:'edit',iframe:'?m=ask&c=ask&a=actor_edit&id='+id+'&typeid='+typeid,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>
</body>
</html>
