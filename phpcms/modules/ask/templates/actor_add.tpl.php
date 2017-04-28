<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<? if (!$_GET['step']) { ?>
<script type="text/javascript">
function doCheck(){
	if(document.myform.member_actor.value==''){
	alert("请选择头衔系列");
	document.myform.member_actor.focus();
	return false;
	}
	if (document.myform.actor_num.value=='') {
	alert("请填写头衔数");
	document.myform.actor_num.focus();
	return false;
}
}
</script>

<div class="pad_10">
<form action="?m=ask&c=ask&a=actor_add&step=1" method="post" name="myform" id="myform" onsubmit="return doCheck();">
<table width="100%" cellpadding="3" cellspacing="1"  class="table_form">
<tr>
	<th width="25%">请选择头衔的系列:&nbsp;&nbsp;&nbsp;&nbsp;</th>
	<td width="80%" align="left" valign="middle" colspan="5" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo $type_selected?></td>
</tr>
<tr>
	<th>此系列分多少个头衔:&nbsp;&nbsp;&nbsp;&nbsp;</th>
	<td width="80%" align="left" valign="middle" colspan="5" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='actor_num' type='text' id='actor_num' value='18' size='20' maxlength='10'></td>
</tr>
<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=ask&c=ask&a=actor_add&step=1"> <input
		type="submit" name="dosubmit" id="dosubmit"
		value=" <?php echo L('next')?> "></td>
	</tr>
</table>

</form>
</div>
<? }else { ?>
<div class="pad_10">
<form action="?m=ask&c=ask&a=actor_add" method="post" name="myform" id="myform">
<input name='typeid' type='hidden' value='<? echo $_POST['typeid']?>'>
<table width="100%" cellpadding="3" cellspacing="3"  class="table_list">
<tr >
<th width="33%"><?php echo L('grade')?></th>
<th><?php echo L('actor')?></th>
<th><?php echo L('point')?></th>
</tr>
<?php for($i=0; $i<$_POST['actor_num']; $i++) {?>
<tr >
<td align="center"><input name='grade[]' type='text' id='grade<? echo $i?>' size='20' maxlength='30'></td>
<td align="center"><input name='actors[]' type='text' id='actor<? echo $i?>' size='30' maxlength='30'></td>
<td align="center"><input name='min[]' type='text' id='min<? echo $i?>' size='6' maxlength='10'>&nbsp;&nbsp;---&nbsp;&nbsp;<input name='max[]' type='text' id='max<? echo $i?>' size='6' maxlength='10'></td>
</tr>
<?php }?>
<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=ask&c=ask&a=actor_add&step=2"> <input
		type="submit" name="dosubmit" id="dosubmit"  class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>
</table>

</form>
</div>
<? }?>
</body>
</html> 