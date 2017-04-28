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

`min`, `max`) VALUES(1, 1, 0, 'һ��', '������', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(2, 1, 0, '����', '����', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(3, 1, 0, '����', '����', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(4, 1, 0, '�ļ�', '����', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(5, 1, 0, '�弶', '����', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(6, 1, 1, 'һ��', 'ħ��ѧͽ', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(7, 1, 1, '����', '��ϰħ��ʦ', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(8, 1, 1, '����', '��ϰħ��ʦ', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(9, 1, 1, '�ļ�', 'ħ��ʦ', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(10, 1, 1, '�弶', 'ħ��ʦ', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(11, 1, 2, 'һ��', 'ͯ��', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(12, 1, 2, '����', '���', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(13, 1, 2, '����', '���', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(14, 1, 2, '�ļ�', '����', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(15, 1, 2, '�弶', '����', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(16, 1, 3, 'һ��', '����', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(17, 1, 3, '����', '����', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(18, 1, 3, '����', '����', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(19, 1, 3, '�ļ�', 'ǧ��', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(20, 1, 3, '�弶', 'ǧ��', 2501, 5000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(21, 1, 4, 'һ��', '��ѧ����', 0, 100);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(22, 1, 4, '����', '���뽭��', 101, 500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(23, 1, 4, '����', '���뽭��', 501, 1000);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(24, 1, 4, '�ļ�', '��������', 1001, 2500);
INSERT INTO `phpcms_ask_actor` (`id`, `siteid`, `typeid`, `grade`, `actor`, `min`, 

`max`) VALUES(25, 1, 4, '�弶', '��������', 2501, 5000);

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
