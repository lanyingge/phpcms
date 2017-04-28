DROP TABLE IF EXISTS `phpcms_ask`;
CREATE TABLE `phpcms_ask` (
  `askid` mediumint(8) unsigned NOT NULL auto_increment,
  `siteid` smallint(5) unsigned NOT NULL default '0',
  `catid` smallint(5) unsigned NOT NULL default '0',
  `title` char(80) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `reward` smallint(5) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL default '0',
  `endtime` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `flag` tinyint(1) unsigned NOT NULL default '0',
  `answercount` tinyint(3) unsigned NOT NULL default '0',
  `anonymity` tinyint(1) unsigned NOT NULL default '0',
  `hits` mediumint(8) unsigned NOT NULL default '0',
  `ischeck` tinyint(1) unsigned NOT NULL default '0',
  `sysadd` tinyint(1) unsigned NOT NULL default '0',
  `searchid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`askid`),
  KEY `catid` (`catid`,`status`,`askid`,`siteid`),
  KEY `status` (`status`,`askid`,`siteid`),
  KEY `userid` (`userid`,`status`,`askid`,`siteid`),
  KEY `answercount` (`answercount`,`catid`,`askid`,`siteid`),
  KEY `flag` (`flag`,`status`,`askid`,`siteid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `phpcms_ask_actor`;
CREATE TABLE `phpcms_ask_actor` (
  `id` int(6) unsigned NOT NULL auto_increment,
  `siteid` smallint(5) unsigned NOT NULL default '0',
  `typeid` tinyint(3) unsigned NOT NULL default '0',
  `grade` char(20) NOT NULL,
  `actor` char(30) NOT NULL,
  `min` smallint(5) unsigned NOT NULL default '0',
  `max` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `typeid` (`typeid`,`id`)
) TYPE=MyISAM;

INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, 

`min`, `max`) VALUES(1, 1, 0, '一级', '试用期', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(2, 1, 0, '二级', '助理', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(3, 1, 0, '三级', '助理', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(4, 1, 0, '四级', '经理', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(5, 1, 0, '五级', '经理', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(6, 1, 1, '一级', '魔法学徒', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(7, 1, 1, '二级', '见习魔法师', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(8, 1, 1, '三级', '见习魔法师', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(9, 1, 1, '四级', '魔法师', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(10, 1, 1, '五级', '魔法师', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(11, 1, 2, '一级', '童生', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(12, 1, 2, '二级', '秀才', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(13, 1, 2, '三级', '秀才', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(14, 1, 2, '四级', '举人', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(15, 1, 2, '五级', '举人', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(16, 1, 3, '一级', '兵卒', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(17, 1, 3, '二级', '门吏', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(18, 1, 3, '三级', '门吏', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(19, 1, 3, '四级', '千总', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(20, 1, 3, '五级', '千总', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(21, 1, 4, '一级', '初学弟子', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(22, 1, 4, '二级', '初入江湖', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(23, 1, 4, '三级', '初入江湖', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(24, 1, 4, '四级', '江湖新秀', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(25, 1, 4, '五级', '江湖新秀', 2501, 5000);

DROP TABLE IF EXISTS `phpcms_ask_credit`;
CREATE TABLE `phpcms_ask_credit` (
  `cid` mediumint(8) unsigned NOT NULL auto_increment,
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(20) NOT NULL,
  `premonth` smallint(5) unsigned NOT NULL default '0',
  `month` smallint(5) unsigned NOT NULL default '0',
  `preweek` smallint(5) unsigned NOT NULL default '0',
  `week` smallint(5) unsigned NOT NULL default '0',
  `addtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `userid` (`userid`),
  KEY `premonth` (`premonth`,`userid`),
  KEY `preweek` (`preweek`,`userid`)
) TYPE=MyISAM;



DROP TABLE IF EXISTS `phpcms_ask_vote`;
CREATE TABLE `phpcms_ask_vote` (
  `voteid` int(11) unsigned NOT NULL auto_increment,
  `askid` mediumint(8) NOT NULL default '0',
  `pid` int(10) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `addtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`voteid`),
  KEY `pid` (`pid`,`userid`),
  KEY `askid` (`askid`)
) TYPE=MyISAM;

ALTER TABLE `phpcms_member` ADD `actortype` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `phpcms_member` ADD `answercount` mediumint(8) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `phpcms_member` ADD `acceptcount` smallint(5) UNSIGNED NOT NULL DEFAULT '0';


INSERT INTO `phpcms_urlrule` (`module`, `file`, `ishtml`, `urlrule`, `example`) VALUES
('ask', 'category', 0, 'index.php?m=ask&c=index&a=lists&catid={$catid}|index.php?m=ask&c=index&a=lists&catid={$catid}&page={$page}', 'index.php?m=ask&c=index&a=lists&catid=1&page=1'),
('ask', 'show', 0, 'ask/show-{$catid}-{$id}.html', 'ask/show-1-2.html'),
('ask', 'show', 0, 'index.php?m=ask&c=index&a=show&id={$id}|index.php?m=ask&c=index&a=show&id={$id}&page={$page}', 'index.php?m=ask&c=index&a=show&id=1&page=1'),
( 'ask', 'category', 0, 'ask/list-{$catid}-{$page}.html', 'ask/list-1-1.html');
