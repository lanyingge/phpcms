DROP TABLE IF EXISTS `phpcms_ask`;
DROP TABLE IF EXISTS `phpcms_ask_actor`;
DROP TABLE IF EXISTS `phpcms_ask_credit`;
DROP TABLE IF EXISTS `phpcms_ask_posts`;
DROP TABLE IF EXISTS `phpcms_ask_vote`;
ALTER TABLE `phpcms_member` DROP `actortype`;
ALTER TABLE `phpcms_member` DROP `answercount`;
ALTER TABLE `phpcms_member` DROP `acceptcount`;
