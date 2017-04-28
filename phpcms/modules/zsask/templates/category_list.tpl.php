<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">

<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> <a href="?m=zsask&c=zsask&a=update_cache">更新栏目缓存</a>
		</div>
		</td>
		</tr>
    </tbody>
</table>

<form name="myform" id="myform" action="?m=zsask&c=zsask&a=listorder&dosubmit=1" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
            <th width="38">排序</th>
            <th width="30">catid</th>
            <th>栏目名称</th>
            <th width="30" align="center">访问</th>
			<th>管理操作</th>
            </tr>
	</thead>
<tbody>
<?php
get_category(0, 3, 1);
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input name="dosubmit" type="submit" class="button" onclick="myform.submit();"
	value="<?php echo L('listorder')?>">&nbsp;&nbsp;
</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

	function d_isplay(catid, status) {
		$.get('?m=zsask&c=zsask&a=display&cid='+ catid +'&status='+ status +'&pc_hash=<?php echo $_SESSION["pc_hash"];?>', function (data) {
			if (data =='1') {
				if (status ==1) {
					$('#dspl_'+ catid).html('<a href="javascript:;" onclick="d_isplay('+ catid +')">禁用</a>');
				}else {
					$('#dspl_'+ catid).html('<a href="javascript:;" onclick="d_isplay('+ catid +', 1)" style="color:red">启用</a>');
				}
				//window.top.art.dialog({content:"操作成功！",lock:true,width:'200',height:'50',time:1.5},function(){return false});
			}
		});
	}
</script>
</body>
</html>
