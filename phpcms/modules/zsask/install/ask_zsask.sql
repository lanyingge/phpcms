DROP TABLE IF EXISTS `phpcms_ask_zsask`;
CREATE TABLE `phpcms_ask_zsask` (
  `userid` int(10) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(80),
  `score` smallint(5) NOT NULL DEFAULT '0',
  `listorder` int(10) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `phpcms_ask_category`;
CREATE TABLE `phpcms_ask_category` (
  `catid` smallint(5) NOT NULL AUTO_INCREMENT,
  `parentid` smallint(5) NOT NULL DEFAULT '0',
  `grade` tinyint(1) NOT NULL DEFAULT '0',
  `catname` varchar(80),
  `description` varchar(255),
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `listorder` smallint(5) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`catid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `phpcms_ask_question`;
CREATE TABLE `phpcms_ask_question` (
  `qid` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) NOT NULL DEFAULT '0',
  `catid` smallint(5) NOT NULL DEFAULT '0',
  `question` varchar(80),
  `content` text,
  `url` varchar(255),
  `userid` int(10) NOT NULL DEFAULT '0',
  `ip` char(20) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `listorder` int(10) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`qid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `phpcms_ask_answer`;
CREATE TABLE `phpcms_ask_answer` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `qid` int(10) NOT NULL DEFAULT '0',
  `zid` int(10) NOT NULL DEFAULT '0',
  `content` text,
  `userid` int(10) NOT NULL DEFAULT '0',
  `ip` char(20) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `phpcms_ask_comment`;
CREATE TABLE `phpcms_ask_comment` (
  `cid` int(10) NOT NULL AUTO_INCREMENT,
  `qid` int(10) NOT NULL DEFAULT '0',
  `aid` int(10) NOT NULL DEFAULT '0',
  `content` varchar(255),
  `userid` int(10) NOT NULL DEFAULT '0',
  `ip` char(20) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `updatetime` int(10) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`)
) TYPE=MyISAM;

