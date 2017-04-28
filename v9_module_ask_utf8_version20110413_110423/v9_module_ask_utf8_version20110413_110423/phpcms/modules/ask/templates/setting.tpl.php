<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="?m=ask&c=ask&a=setting">
<table cellpadding="0" cellspacing="1" class="table_form">
	<tr>
      <th width='16%' >发布提问是否需要审核</th>
      <td><input name='setting[publish_check]' type='radio' value="1" <?php if($publish_check) echo "checked";?>> 是 <input name='setting[publish_check]' type='radio' value="0" <?php if(!$publish_check) echo "checked";?>> 否</td>
    </tr>
    <tr>
    	<th>发表问题是否开启验证</th>
        <td><input name='setting[publish_code]' type='radio' value="1" <?php if($publish_code) echo "checked";?>> 是 <input name='setting[publish_code]' type='radio' value="0" <?php if(!$publish_code) echo "checked";?>> 否        
        </td>
    </tr>
	<tr>
      <th>回答是否需要审核</th>
      <td><input name='setting[answer_check]' type='radio' value="1" <?php if($answer_check) echo "checked";?>> 是 <input name='setting[answer_check]' type='radio' value="0" <?php if(!$answer_check) echo "checked";?>> 否</td>
    </tr>
	<tr>
      <th>回答是否需要开启验证</th>
      <td><input name='setting[answer_code]' type='radio' value="1" <?php if($answer_code) echo "checked";?>> 是 <input name='setting[answer_code]' type='radio' value="0" <?php if(!$answer_code) echo "checked";?>> 否</td>
    </tr>
	<tr>
      <th>高分上限设置</th>
      <td><input name='setting[height_score]' type='text' id='height_score' value='<?=$height_score?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>匿名默认扣除积分设定</th>
      <td><input name='setting[anybody_score]' type='text' id='anybody_score' value='<?=$anybody_score?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>会员回答问题奖励积分</th>
      <td><input name='setting[answer_give_credit]' type='text' id='answer_give_credit' value='<?=$answer_give_credit?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>回答问题每日最多可获得积分</th>
      <td><input name='setting[answer_max_credit]' type='text' id='answer_max_credit' value='<?=$answer_max_credit?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>回答被采纳为最佳答案奖励积分</th>
      <td><input name='setting[answer_bounty_credit]' type='text' id='answer_bounty_credit' value='<?=$answer_bounty_credit?>' size='5' maxlength='50'></td>
    </tr>
    <tr>
      <th>会员投票奖励积分</th>
      <td><input name='setting[vote_give_credit]' type='text' id='vote_give_credit' value='<?=$vote_give_credit?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>会员投票每日最多可获得积分</th>
      <td><input name='setting[vote_max_credit]' type='text' id='vote_max_credit' value='<?=$vote_max_credit?>' size='5' maxlength='50'></td>
    </tr>
	<tr>
      <th>提问上线后被删除扣除积分</th>
      <td><input name='setting[del_question_credit]' type='text' id='del_question_credit' value='<?=$del_question_credit?>' size='5' maxlength='50'> 提问上线后，被管理员删除，扣除提问用户<?=$del_question_credit?>分，答复者不扣</td>
    </tr>
	<tr>
      <th>回答上线后被删除扣除积分</th>
      <td><input name='setting[del_answer_credit]' type='text' id='del_answer_credit' value='<?=$del_answer_credit?>' size='5' maxlength='50'> 回答上线后，被管理员删除，扣除回答用户<?=$del_answer_credit?>分</td>
    </tr>
	<tr>
      <th>问题15天内不处理扣除积分</strong> <br /></th>
      <td><input name='setting[del_day15_credit]' type='text' id='del_day15_credit' value='<?=$del_day15_credit?>' size='5' maxlength='50'> 问题到期，提问用户不作处理（不做最佳答案判断、不通过提高悬赏延期问题有效时间，不关闭问题，或不转入投票流程），在问题直接过期或自动转投票时扣除提问用户<?=$del_day15_credit?>分</td>
    </tr>
	<tr>
      <th>处理过期问题返回积分</strong> <br /></th>
      <td><input name='setting[return_credit]' type='text' id='return_credit' value='<?=$return_credit?>' size='5' maxlength='50'> 过期自动转投票问题选出最佳答案或提问者对过期问题进行处理，包括采纳最佳答案和选择无满意答案，提问者都可以获得系统返还的<?=$return_credit?>分</td>
    </tr>

	<tr>
      <th>会员角色系列名称</th>
      <td><textarea name='setting[member_group]' id='member_group' cols='40' rows='5'><?=$member_group?></textarea> 每行填写一个</td>
    </tr>
	<tr>
      <th>启用编辑器</th>
      <td><input name='setting[use_editor]' type='radio' value="1" <?php if($use_editor) echo "checked";?>> 是 <input name='setting[use_editor]' type='radio' value="0" <?php if(!$use_editor) echo "checked";?>> 否</td>
    </tr>
	<tr>
      <th><?php echo L('category_urlrules');?>：</th>
      <td>
	<div id="category_ruleid">
	<?php
		echo form::urlrule('ask','category',0,$category_ruleid,'name="setting[category_ruleid]"');
	?>
	</div>
	</td>
    </tr>
	
	<tr>
      <th><?php echo L('show_urlrules');?>：</th>
      <td><div id="show_ruleid">
	  <?php
		echo form::urlrule('ask','show',0,$show_ruleid,'name="setting[show_ruleid]"');
	?>
	</div>
	</td>
    </tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('ok')?> " class="button">&nbsp;</td>
	</tr>
</table>
</form>

</body>
</html>
 