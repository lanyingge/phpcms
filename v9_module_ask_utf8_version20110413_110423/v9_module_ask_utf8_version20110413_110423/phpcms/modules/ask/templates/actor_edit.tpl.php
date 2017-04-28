<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>

<script type="text/javascript">
function doCheck(){
	if ($('#member_actor').val()=='') {
	alert("请选择头衔系列");
	$('#member_actor').focus();
	return false;
	}
	if ($('#grade').val()=='') {
	alert("请填写级别");
	$('#grade').focus();
	return false;
}
	if ($('#actor').val()=='') {
	alert("请填写头衔");
	$('#actor').focus();
	return false;
}
	if ($('#min').val()=='') {
	alert("请填写最少积分");
	$('#min').focus();
	return false;
}
}
</script>
<div class="pad_10">
<form action="?m=ask&c=ask&a=actor_edit&askid=<? echo $id?>" method="post" name="myform" id="myform" onsubmit="return doCheck();">
<table width="100%" cellpadding="3" cellspacing="1"  class="table_form">
<tr>
<th width="25%">所属系列：</th>
<td align="left" valign="middle" colspan="5" >
<input name='id' type='hidden' value='<? echo $id?>'>
<? echo $type_selected?></td>
</tr>
<tr>
<th><?php echo L('grade')?>：</th>
<td align="left" valign="middle" colspan="5" ><input name='info[grade]' type='text' id='grade' value='<? echo $grade?>' size='20' maxlength='10'></td>
</tr>
<tr>
<th><?php echo L('actor')?>：</th>
<td align="left" valign="middle" colspan="5" ><input name='info[actor]' type='text' id='actor' value='<? echo $actor?>' size='20' maxlength='10'></td>
</tr>
<tr>
<th><?php echo L('point')?>：</th>
<td align="left" valign="middle" colspan="5" ><input name='info[min]' type='text' id='min' size='6' maxlength='10' value="<? echo $min?>">&nbsp;&nbsp;---&nbsp;&nbsp;<input name='info[max]' type='text' id='max' size='6' maxlength='10' value="<? echo $max?>"></td>
</tr>
<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=ask&c=ask&a=actor_edit"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>

</form>
</div>

</body>
</html> 