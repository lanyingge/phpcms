<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
 	$("#message").formValidator({onshow:"<?php echo L("input").L('answer_message')?>",onfocus:"<?php echo L("input").L('answer_message')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('answer_message')?>"});
	 
	})
//-->
</script>

<div class="pad_10">
<form action="?m=ask&c=answer&a=edit&askid=<?php echo $posts['askid']; ?>&pid=<?php echo $posts['pid']; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="10%"><?php echo L('answer_message')?>：</th>
		<td><textarea name="posts[message]" id="message" cols="80" rows="6"><?php echo $posts['message']; ?></textarea></td>
	</tr>
	<tr>
		<th><?php echo L('pass')?>：</th>
		<td><input name="posts[status]" type="radio" value="3" <?php if($posts['status']==3){echo "checked";}?>>&nbsp;<?php echo L('yes')?>&nbsp;&nbsp;<input
			name="posts[status]" type="radio" value="1" <?php if($posts['status']==1){echo "checked";}?>>&nbsp;<?php echo L('no')?></td>
	</tr>


<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=ask&c=answer&a=edit&pid=<?php echo $posts['pid']; ?>"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html> 