<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>

<div class="pad-lr-10">
<div class="table-list">
<div class="common-form">
<fieldset>
	<legend><?php echo L('ask')?></legend>
	<table width="100%" class="table_form">
	<tr>
			<th width="15%"><?php echo L('ask')?>：</th>
			<td><?php echo $title; ?></td>
	</tr>
	<?php if($message){ ?>
	<tr>
			<th width="15%"><?php echo L('ask_message')?>：</th>
			<td><?php echo $message; ?></td>
	</tr>
	<?php } ?>
	<tr>
			<th width="15%"><?php echo L('catname')?>：</th>
			<td><?php echo $CATEGORYS[$catid][catname];?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_author')?>：</th>
		<td><?php echo $username; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_addtime')?>：</th>
		<td><? echo date('Y-m-d H:i:s',$addtime)?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('status')?>：</th>
		<td><?php if($status==3) echo "<font color=#009900>待解决</font>";else if($status==5) echo "<font color=#FF9900>已解决</font>";else if($status==6) echo "<font color=#3300FF>已关闭</font>";?></td>
	</tr>
	<?php if($reward){ ?>
	<tr>
		<th width="10%"><?php echo L('reward_score')?>：</th>
		<td><?php echo $reward; ?></td>
	</tr>
	<?php } ?>
	<?php if($flag){ ?>
	<tr>
		<th width="10%"><?php echo L('ask_flag')?>：</th>
		<td><?php if($flag==3) echo "<font color=#009900>".L('elite')."</font>";?></td>
	</tr>
	<?php } ?>
	<tr>
		<th width="10%"><?php echo L('ask_answercount')?>：</th>
		<td><?php echo $answercount; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('ask_hits')?>：</th>
		<td><?php echo $hits; ?></td>
	</tr>
	<?php if($status==5){ ?>
	<tr>
		<th width="10%"><?php echo L('solvetime')?>：</th>
		<td><? echo date('Y-m-d H:i:s',$solvetime)?></td>
	</tr>
	<?php } ?>
				
	</table>
</fieldset>
<?php if($status==5){ ?>
<fieldset>
	<legend><?php echo L('best_answer')?></legend>
	<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="10%"><?php echo L('answer_author')?></th>
		<td><?php echo $optimail_username; ?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('answer_addtime')?></th>
		<td><? echo date('Y-m-d H:i:s',$answertime)?></td>
	</tr>
	<tr>
		<th width="10%"><?php echo L('answer_message')?></th>
		<td><?php echo trim_textarea($answer); ?></td>
	</tr>
</table>
</fieldset>
<div class="bk15"></div>
<?php } ?>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('other_answer')?> <?php echo $answercount ?></legend>
	<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
<?php if(is_array($infos)){
	foreach($infos as $k=>$info){
		?>
	<tr <?php if($k%2==1) echo "style='color:red'";?>>
		<th width="10%"><?php echo L('answer_author')?></th>
		<td><?php echo $info[username]; ?>  (<?php echo $info[actor]; ?>)</td>
	</tr>
	
	<tr <?php if($k%2==1) echo "style='color:red'";?>>
		<th width="10%"><?php echo L('answer_addtime')?></th>
		<td><? echo date('Y-m-d H:i:s',$info[addtime])?></td>
	</tr>
	<tr <?php if($k%2==1) echo "style='color:red'";?>>
		<th width="10%"><?php echo L('answer_message')?></th>
		<td><?php echo trim_textarea($info[message]); ?></td>
	</tr>
	<tr>
		<th width="10%"></th>
		<td>&nbsp;</td>
	</tr>
<?php
 }
} ?>
</table>
</fieldset>

</div>
<div class="bk15"></div>
<input type="button" class="dialog" name="dosubmit" id="dosubmit" onclick="window.top.art.dialog({id:'view_answer'}).close();"/>
</div>
</div>

</body>
</html> 