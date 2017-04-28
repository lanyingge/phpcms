<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = '';
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
var charset = '<?php echo CHARSET ?>';
var uploadurl = '<?php echo pc_base::load_config("system","upload_url")?>';
</script>
<div class="common-form">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" id="myform" action="" method="post">
<table width="100%" class="table_form contentWrap">
      <tr><th>父级栏目：</th>
      <td>
      <select name="parentid" id="parentid">
      <option value="0">顶级栏目</option>
      <?php get_category();?>
      </select>
      <?php  echo '<script type="text/javascript"> 
      $("#parentid").val("'.$info['parentid'].','.($info['grade']-1).'");
      $("#parentid option[value=\''.$info['catid'].','.$info['grade'].'\']").remove();
      </script>';?>
      </td></tr>
      
      <tr><th>栏目名称：</th><td>
      
	<input type="text" name="info[catname]" id="catname" value="<?php echo $info['catname'];?>" />
    	
      </td></tr>
      
      <tr><th>描述：</th>
      <td>
    <textarea name="info[description]" id="description" style="width:350px;height:100px;"><?php echo $info['description'];?></textarea>
      </td></tr>
      
      <tr><th>状态：</th>
      <td>
		<input type="radio" name="info[status]" id="status" value="99" <?php if ($info['status'] ==99) echo 'checked';?> /> 正常 
		<input type="radio" name="info[status]" id="status" value="0" <?php if ($info['status'] == 0) echo 'checked';?> />  关闭
      </td></tr>
      
</table>
<div class="btn" style="padding-left:200px;">
<input type="hidden" name="pid" value="<?php echo $info['parentid'];?>" />
<input type="submit" name="dosubmit" id="dosubmit" value=" 提 交 " class="button" />
</div>
</form>
<!--table_form_off-->
</div>

</body>
</html>