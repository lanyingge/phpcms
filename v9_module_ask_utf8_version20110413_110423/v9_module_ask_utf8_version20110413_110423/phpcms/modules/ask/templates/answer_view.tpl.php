<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>

<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
<fieldset>
	<legend><?php echo L('answer_message')?></legend>
	<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="10%"><?php echo L('answer_author')?></th>
		<td><?php echo $posts['username']; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('answer_addtime')?></th>
		<td><? echo date('Y-m-d H:i:s',$posts['addtime'])?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('answer_message')?></th>
		<td><?php echo trim_textarea($posts['message']); ?></td>
	</tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('ask')?></legend>
	<table width="100%" class="table_form">
	<tr>
			<th width="15%"><?php echo L('ask')?>：</th>
			<td><?php echo $ask['title']; ?></td>
	</tr>
	<tr>
			<th width="15%"><?php echo L('catname')?>：</th>
			<td><?php echo $CATEGORYS[$ask[catid]][catname];?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_author')?>：</th>
		<td><?php echo $ask['username']; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_addtime')?>：</th>
		<td><? echo date('Y-m-d H:i:s',$ask['addtime'])?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('status')?>：</th>
		<td><?php if($ask['status']==3) echo "<font color=#009900>待解决</font>";else if($ask['status']==5) echo "<font color=#FF9900>已解决</font>";else if($ask['status']==6) echo "<font color=#3300FF>已关闭</font>";?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('reward_score')?>：</th>
		<td><?php echo $ask['reward']; ?></td>
	</tr>
	<?php if($ask['flag']){ ?>
	<tr>
		<th width="10%"><?php echo L('ask_flag')?>：</th>
		<td><?php if($ask['flag']==3) echo "<font color=#009900>".L('elite')."</font>";?></td>
	</tr>
	<?php } ?>
	<tr>
		<th width="10%"><?php echo L('ask_answercount')?>：</th>
		<td><?php echo $ask['answercount']; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_hits')?>：</th>
		<td><?php echo $ask['hits']; ?></td>
	</tr>
				
	</table>
</fieldset>


</div>
<div class="bk15"></div>
<input type="button" class="dialog" name="dosubmit" id="dosubmit" onclick="window.top.art.dialog({id:'view_answer'}).close();"/>
</div>
</div>

</body>
</html> 