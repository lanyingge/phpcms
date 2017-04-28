<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><?php include template("content", "header"); ?>
<?php include template("zsask", "header"); ?>

<div class="question">
<?php $cat_path = get_catpath($catid);?>
<div style="margin-bottom:5px;" class="crumbs">
<a href="<?php echo APP_PATH;?>">首页</a><span> &gt; </span>
<a href="<?php echo ASK_PATH;?>">问答</a> &gt; 
<a href="<?php echo ASK_LIST;?>">分类</a><?php echo $cat_path;?></div>

<div class="pre fl">
<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=f4449744959f284572e3c2c51c0611a0&sql=SELECT+%2A+FROM+phpcms_ask_category+WHERE+parentid%3D%24catid+AND+status+%3D99+ORDER+BY+listorder+ASC&return=catinfo\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("SELECT * FROM phpcms_ask_category WHERE parentid=$catid AND status =99 ORDER BY listorder ASC LIMIT 20");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$catinfo = $a;unset($a);?>
<?php if($catinfo) { ?>
<table class="table_form cat">
<tr>
<?php $n=1;if(is_array($catinfo)) foreach($catinfo AS $r) { ?>
<?php if($n%4 !=0) { ?>
<td><a href="<?php echo ASK_LIST;?><?php echo $r['catid'];?>" title="<?php echo $r['description'];?>"><?php echo $r['catname'];?> [<span style="color:red"><?php echo get_question($r[catid]);?></span>]</a></td>
<?php } else { ?>
<td><a href="<?php echo ASK_LIST;?><?php echo $r['catid'];?>" title="<?php echo $r['description'];?>"><?php echo $r['catname'];?> [<span style="color:red"><?php echo get_question($r[catid]);?></span>]</a></td>
</tr><tr>
<?php } ?>
<?php $n++;}unset($n); ?>
</tr>
</table>
<?php } ?>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>

<table class="table_form">
<tr class="trtitle">
<td colspan="3">
<a id="bgc_2" href="javascript:;" onclick="choice(2)" class="bgc">全部</a> 
<a id="bgc_0" href="javascript:;" onclick="choice(0)">待解决</a> 
<a id="bgc_99" href="javascript:;" onclick="choice(99)">已解决</a> 
<a id="bgc_1" href="javascript:;" onclick="choice(1)">零回答</a>
<input type="text" name="keywords" id="keywords" value="<?php echo $_GET['keywords'];?>" class="input-text txt">
<input type="image" src="<?php echo CSS_PATH;?>cqzsv46/images/s_btn.gif" name="dosubmit" onclick="srch()" value="搜 索" class="pointer">&nbsp;&nbsp;
</td>
</tr>
<tr class="trtitle2">
<td>问题（共<span id="total_q"></span>）<?php if($cat_path) { ?>| <a href="<?php echo ASK_LIST;?>">分类首页</a><?php echo $cat_path;?><?php } ?></td>
<td width="40" align="center">回答</td>
<td align="center">日期</td>
</tr>
<?php $where = get_catids($catid); $stus = intval($_GET[s]);?>
<?php if($stus ==1) { ?>
<?php $where .= ' AND status=0';?>

<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=3093692d5615ce976cb6019b2bdccb80&sql=SELECT+qid+FROM+phpcms_ask_answer+WHERE+zid%3D0+AND+status%3D0+GROUP+BY+qid&return=qids\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$r = $get_db->sql_query("SELECT qid FROM phpcms_ask_answer WHERE zid=0 AND status=0 GROUP BY qid LIMIT 20");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$qids = $a;unset($a);?>
<?php $n=1;if(is_array($qids)) foreach($qids AS $_q) { ?>
<?php $qidarr[] = $_q[qid]?>
<?php $n++;}unset($n); ?>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
<?php if($qidarr) { ?>
<?php $where .= ' AND '.to_sqls($qidarr, '', 'qid NOT');?>
<?php } ?>
<?php } elseif ($stus !=2) { ?>
<?php $where .= ' AND status='.$stus;?>
<?php } else { ?>
<?php $where .=' AND status !=1'?>
<?php } ?>

<?php if(!empty($_GET['keywords'])) { ?>
<?php $keywords = new_addslashes(safe_replace(urldecode($_GET['keywords']))); ?>
<?php $where .= " AND question LIKE '%$keywords%'"; ?>
<?php } ?>

<?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"get\" data=\"op=get&tag_md5=6d968925b2a3454c1d2252354f3cc0a3&sql=SELECT+%2A+FROM+phpcms_ask_question+WHERE+%24where+ORDER+BY+qid+DESC&num=25&page=%24page&return=arrq\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}pc_base::load_sys_class("get_model", "model", 0);$get_db = new get_model();$pagesize = 25;$page = intval($page) ? intval($page) : 1;if($page<=0){$page=1;}$offset = ($page - 1) * $pagesize;$r = $get_db->sql_query("SELECT COUNT(*) as count FROM (SELECT * FROM phpcms_ask_question WHERE $where ORDER BY qid DESC) T");$s = $get_db->fetch_next();$pages=pages($s['count'], $page, $pagesize, $urlrule);$r = $get_db->sql_query("SELECT * FROM phpcms_ask_question WHERE $where ORDER BY qid DESC LIMIT $offset,$pagesize");while(($s = $get_db->fetch_next()) != false) {$a[] = $s;}$arrq = $a;unset($a);?>
<?php $n=1;if(is_array($arrq)) foreach($arrq AS $r) { ?>
<tr>
<td>
<div class="qstion">
<a href="<?php echo ASK_LIST;?><?php echo $r['catid'];?>" style="color:#888">[<?php echo $askcategorys[$r['catid']]['catname'];?>] </a> 
<a class="qn" href="<?php if($r[url]) { ?><?php echo $r['url'];?><?php } else { ?><?php echo ASK_SHOW;?><?php echo $r['qid'];?><?php } ?>" target="_blank" title="<?php echo $r['question'];?>"><?php echo $r['question'];?></a>
</div>
</td>

<td align="center">
<?php echo get_answer($r[qid]);?>
</td>

<td align="center">
<?php echo format_date($r[addtime]);?>
</td></tr>
<?php $n++;}unset($n); ?>
<tr><td colspan="3"><div id="pages"><?php echo $pages;?></div></td></tr>
<?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
</table>

<script type="text/javascript">
	function choice(status) {
		window.location = '<?php echo ASK_LIST;?><?php echo $catid;?>&s='+ status;
	}
	
	$('.trtitle a').removeClass('bgc');
	$('#bgc_<?php echo $stus;?>').addClass('bgc');
	var total_q = $('#pages .a1:eq(0)').html();
	if (!total_q) {
		$('#total_q').html('<?php echo count($arrq);?>条');
	}else {
		$('#total_q').html(total_q);
	}

	function srch() {
		var keywords = $('#keywords').val();
		window.location = window.location +'&keywords='+ keywords;
	}
	
</script>

</div>
<?php include template("zsask", "right"); ?>
</div>
<?php include template("content", "footer"); ?>