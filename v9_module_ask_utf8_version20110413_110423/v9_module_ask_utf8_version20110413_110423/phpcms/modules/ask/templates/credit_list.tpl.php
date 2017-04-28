<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=ask&c=ask&a=credit" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
<th align="center">排名</th>
<th align="center">用户名</th>
<th align="center">总积分</th>
<th align="center">上月积分</th>
<th align="center">上周积分</th>
<th align="center">等级</th>
<th align="center">最后登录</th>
<th align="center">登录次数</th>
<th align="center">回答数</th>
<th align="center">被采纳数</th>
</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
<td align="center"><?php echo $info['orderid'];?></td>
<td align="center"><?php echo $info['username'];?></td>
<td align="center"><?php echo $info['point'];?></td>
<td align="center"><?php echo $info['premonth'];?></td>
<td align="center"><?php echo $info['preweek'];?></td>
<td align="center"><?php echo $info['grade'];?></td>
<td align="center"><?php echo $info['lastdate'];?></td>
<td align="center"><?php echo $info['loginnum'];?></td>
<td align="center"><?php echo $info['answercount'];?></td>
<td align="center"><?php echo $info['acceptcount'];?></td>
</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>

<div id="pages"><?php echo $pages?></div>
</form>
</div>
</body>
</html>
