
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

#-----------------------------------------------------------------------------
#-- person
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `person`;


CREATE TABLE `person`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(32)  NOT NULL,
	`bio` TEXT,
	`image` TEXT,
	`no_followers` INTEGER,
	`fullname` VARCHAR(255),
	`website` TEXT,
	`status` VARCHAR(255) default '' NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `person_U_1` (`username`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- tag
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `tag`;


CREATE TABLE `tag`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`slug` VARCHAR(32)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `tag_U_1` (`slug`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- relation
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `relation`;


CREATE TABLE `relation`
(
	`person_id` INTEGER  NOT NULL,
	`following_id` INTEGER  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	INDEX `relation_FI_1` (`person_id`),
	CONSTRAINT `relation_FK_1`
		FOREIGN KEY (`person_id`)
		REFERENCES `person` (`id`),
	INDEX `relation_FI_2` (`following_id`),
	CONSTRAINT `relation_FK_2`
		FOREIGN KEY (`following_id`)
		REFERENCES `person` (`id`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- personTag
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `personTag`;


CREATE TABLE `personTag`
(
	`person_id` INTEGER  NOT NULL,
	`tag_id` INTEGER  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	INDEX `personTag_FI_1` (`person_id`),
	CONSTRAINT `personTag_FK_1`
		FOREIGN KEY (`person_id`)
		REFERENCES `person` (`id`),
	INDEX `personTag_FI_2` (`tag_id`),
	CONSTRAINT `personTag_FK_2`
		FOREIGN KEY (`tag_id`)
		REFERENCES `tag` (`id`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- topic
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `topic`;


CREATE TABLE `topic`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64)  NOT NULL,
	`slug` VARCHAR(64)  NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `topic_U_1` (`slug`)
)Type=InnoDB;

#-----------------------------------------------------------------------------
#-- topicPerson
#-----------------------------------------------------------------------------

DROP TABLE IF EXISTS `topicPerson`;


CREATE TABLE `topicPerson`
(
	`person_id` INTEGER  NOT NULL,
	`topic_id` INTEGER  NOT NULL,
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`),
	INDEX `topicPerson_FI_1` (`person_id`),
	CONSTRAINT `topicPerson_FK_1`
		FOREIGN KEY (`person_id`)
		REFERENCES `person` (`id`),
	INDEX `topicPerson_FI_2` (`topic_id`),
	CONSTRAINT `topicPerson_FK_2`
		FOREIGN KEY (`topic_id`)
		REFERENCES `topic` (`id`)
)Type=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
