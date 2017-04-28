<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_validator = '';
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript" src="<?php echo APP_PATH.'statics/videos/jquery.inputdefault.js'?>"></script>

<script type="text/javascript">
<!--
var charset = '<?php echo CHARSET ?>';
var uploadurl = '<?php echo pc_base::load_config("system","upload_url")?>';

$(function(){
	$('[fs]').inputDefault();
	
	var $result = $('#result');
	
	$('#submit').click(function(){
		var url = $('#url').val();
		if(!url){
			$result.html("<font color=red>请输入视频地址</font>");
			return false;
		}
		
		$result.html("<font color=green>视频加载中...</font>");
		
		$('.open_vedio').live('click', function(){
			$(this).hide();
			$(this).next('.vedio').show();
		});
		
		$('.close_vedio').live('click', function(){
			$(this).parent().parent().hide();
			$(this).parent().parent().prev('.open_vedio').show();
		});
		
		
		$.ajax({
			url: '?m=videos&c=videos&a=server&pc_hash=<?php echo $_SESSION["pc_hash"]?>',
			data: {url: url},
			dataType: 'json',
			type: 'POST',
			success: function(json){
				if (!json) return false;
				if(!json.status){
					$result.html("<font color=red>暂不支持该视频地址</font>");
					return false;
				}else{
					var html = "<div style='border:1px #CCC solid; padding:3px; float:left;'>"
						+ "		<a class='open_vedio' href='javascript:;' alt='"+json.data.title+"'>"
						+ "		<img width='136' height='104' style='display:block;' title='点击预览视频' src='"+json.data.img+"' /></a>"
						+ "		<div class='vedio' style='display:none;'>"
						+ "			<div>"
						+ "				<a style='float:right' target='_new' href='"+json.data.url+"'>"+json.data.title+"</a>"
						+ "				<a class='close_vedio' href='javascript:;'>关闭</a>"
						+ "			</div>"
						+ json.data.object
						+ "		</div>"
						+ "</div>"
						+ '<input type="submit" id="dosubmit" class="button" name="dosubmit" value="保存" style="margin:20px;"/>';
					$('#title').val(json.data.title);
					$('#thumb').val(json.data.img);
					$('#content').val(json.data.object);
					$result.html(html);
					return false;
				}
			}
		});
	});
});

function eg_show() {
	if ($('#on_c').html() =='更多例子') {
		$('#eg_show').show();
		$('#on_c').html('隐藏例子');
	}else {
		$('#eg_show').hide();
		$('#on_c').html('更多例子');
	}
}
//-->
</script>
<div class="common-form">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
      概述：根据视频URL抓取视频信息，包括缩略图、链接地址、播放地址等。eg: http://video.sina.com.cn/v/b/46909166-1290055681.html&nbsp;&nbsp;
      <a href="javascript:;" onclick="eg_show()" id="on_c" style="color:red">更多例子</a>
      <div id="eg_show" style="display:none">
     * http://v.qq.com/cover/o/o9tab7nuu0q3esh.html?vid=97abu74o4w3_0<br />
     * http://v.qq.com/play/97abu74o4w3.html<br />
     * http://v.qq.com/cover/d/dtdqyd8g7xvoj0o.html<br />
     * http://v.qq.com/page/p/W/o/p0010Y2kjWo.html<br />
     * http://v.qq.com/cover/d/dtdqyd8g7xvoj0o/9SfqULsrtSb.html<br />
	 * http://v.youku.com/v_show/id_XMjI4MDM4NDc2.html<br />
	 * http://www.tudou.com/playlist/p/l13087099.html<br />
	 * http://www.tudou.com/programs/view/ufg-A3tlcxk/<br />
	 * http://www.56.com/u68/v_NjI2NTkxMzc.html<br />
	 * http://www.letv.com/ptv/vplay/1168109.html<br />
	 * http://video.sina.com.cn/v/b/46909166-1290055681.html<br />
     * http://v.ku6.com/film/show_520/3X93vo4tIS7uotHg.html<br />
     * http://v.ku6.com/special/show_4926690/Klze2mhMeSK6g05X.html<br />
     * http://v.ku6.com/show/7US-kDXjyKyIInDevhpwHg...html<br />
     * 搜狐TV http://my.tv.sohu.com/u/vw/5101536<br />
      </div>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" id="myform" action="" method="post">
<table width="100%" class="table_form contentWrap">
      <tr><th>视频播放页地址：</th><td>
    <input type="text" id="url" name="post[surl]" fs="支持优酷、土豆、酷六、56、乐视、搜狐" size="80" value="" /> 
    <input id="submit" type="button" class="button" value="抓取内容" />
      </td></tr>
      <tr><th>标题：</th><td>
    <input type="text" name="post[title]" id="title" size="80" />
      </td></tr>
      
      <tr><th>缩略图：</th><td>
    <?php echo form::images('post[thumb]', 'thumb')?>
      </td></tr>
      
      <tr><th>内容：</th><td>
    <textarea name="post[content]" id="content" style="width:750px;height:50px;"/></textarea>
      </td></tr>
      <tr><th>抓取内容预览：</th><td id="result">
      </td></tr>
</table>
</form>
<!--table_form_off-->
</div>

</body>
</html>