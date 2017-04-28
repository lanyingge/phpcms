<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#catid").formValidator({onshow:"<?php echo L("select").L('catname')?>",onfocus:"<?php echo L("select").L('catname')?>"}).inputValidator({min:1,onerror:"<?php echo L("select").L('catname')?>"});
	$("#title").formValidator({onshow:"<?php echo L("input").L('title')?>",onfocus:"<?php echo L("input").L('title')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('title')?>"});
 	$("#message").formValidator({onshow:"<?php echo L("input").L('ask_message')?>",onfocus:"<?php echo L("input").L('ask_message')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('ask_message')?>"});
	 
	})
//-->
</script>

<div class="pad_10">
<form action="?m=ask&c=ask&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="20%"><?php echo L('category')?>:</th>
		<td>
		<span id="load_catid"></span>
		<input type="hidden" name="ask[catid]" id="catid" value="">
		<a href="javascript:category_reload();"><?php echo L('reselect')?></a>
<script type="text/javascript">
		function category_load(id)
		{
			$.get('<?php echo $APP_PATH ?>?m=ask&c=load&a=category&pc_hash=<?php echo $_SESSION['pc_hash'] ?>', { id: id },
				  function(data){
					$('#load_catid').append(data);
				  });
		}
		function category_reload()
		{
			$('#load_catid').html('');
			category_load(0);
		}
		function category_change_reload(id)
		{	
			
			$('#catid').val(id);
			$('#load_catid').html('');
			category_load(0);
			category_load(id);
		}
		category_load(0);
</script>
		</td>
	</tr>
	<tr>
		<th width="100"><?php echo L('title')?>：</th>
		<td><input type="text" name="ask[title]" id="title" size="80" class="input-text" onBlur="$.post('api.php?op=get_keywords&number=3&sid='+Math.random()*5, {data:$('#title').val()}, function(data){if(data && $('#keywords').val()=='') $('#keywords').val(data); })"/></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('keywords')?>：</th>
		<td><input type="text" name="ask[keywords]" id="keywords" size="30" class="input-text"><?php echo L('keywords_tips')?></td>
	</tr>
	<tr>
		<th width="100"><?php echo L('username')?>：</th>
		<td><input type="text" name="ask[username]" id="link_username"
			size="30" class="input-text"></td>
	</tr>

 
	<tr>
		<th><?php echo L('ask_message')?>：</th>
		<td><textarea name="posts[message]" id="message" cols="80" rows="6"></textarea></td>
	</tr>

 
 	<tr>
		<th><?php echo L('reward_score')?>：</th>
		<td><select name="ask[reward]" id="reward">
	<option value=0>0</option>
	<option value=5>5</option>
	<option value=10>10</option>
	<option value=15>15</option>
	<option value=20>20</option>
	<option value=30>30</option>
	<option value=45>45</option>
	<option value=60>60</option>
	<option value=80>80</option>
	<option value=100>100</option>
</select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="anonymity"  name="ask[anonymity]" value="1"><?php echo L('anonymity_set')?></td>
	</tr>
	<tr>
		<th><?php echo L('status')?>：</th>
		<td><input name="ask[status]" type="radio" value="3" checked>&nbsp;<?php echo L('status3')?>&nbsp;&nbsp;<input
			name="ask[status]" type="radio" value="5">&nbsp;<?php echo L('status5')?></td>
	</tr>


<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=ask&c=ask&a=add"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html> 