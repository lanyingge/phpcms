<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = '';
include $this->admin_tpl('header', 'admin');
?>

<script type="text/javascript">
var charset = '<?php echo CHARSET ?>';
var uploadurl = '<?php echo pc_base::load_config("system","upload_url")?>';
</script>
<div class="common-form pad-lr-10">


<form name="myform" id="myform" action="" method="post">
<table width="100%" class="table_form contentWrap">
<tr><td colspan="2">
<div class="explain-col"> &nbsp;&nbsp;&nbsp;&nbsp;支持栏目最大层级三，多了也无用 &nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo ASK_LIST;?>" target="_blank">前台分类页</a></div>
</td></tr>
      <tr><th>父级栏目：</th>
      <td>
      <select name="info[parentid]" id="parentid">
      <option value="0">顶级栏目</option>
      <?php get_category();?>
      </select>
		<?php if ($pid) { echo '<script type="text/javascript"> $("#parentid").val("'.$pid.'");</script>';}?>
      </td></tr>
      
      <tr><th>添加方式：</th><td>
      <input type="radio" name="addmethod" value="1" onclick="addm(1);" checked />单个添加 
      
      <input type="radio" name="addmethod" value="2" onclick="addm(2);" />批量添加 
      </td></tr>
      
      <tr><th>栏目名称：</th><td id="addcat">
    <input type="text" name="info[catname]" id="catname" />
      </td></tr>
      
      <tr><th>描述：</th>
      <td>
    <textarea name="info[description]" id="description" style="width:350px;height:100px;"></textarea>
      </td></tr>
      
</table>
<div class="btn" style="padding-left:200px;">
<input type="submit" name="dosubmit" id="dosubmit" value=" 提 交 " class="button"/>
</div>
</form>
<!--table_form_off-->
</div>

<script type="text/javascript">

	function addm(m) {
		if (m ==2) {
			$('#addcat').html('<textarea name="info[catname]" id="catname" style="width:200px;height:150px;"></textarea> 一行一个');
		}else {
			$('#addcat').html('<input type="text" name="info[catname]" id="catname" class="input-text" />');
		} 
	}
</script>
</body>
</html>
