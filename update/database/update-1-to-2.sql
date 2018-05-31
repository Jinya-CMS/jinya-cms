CREATE TABLE migration_versions
(
	version VARCHAR (255) NOT NULL PRIMARY KEY
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE api_key (
  api_key     VARCHAR(255) NOT NULL,
  user_id     INT DEFAULT NULL,
  valid_since DATETIME     NOT NULL,
  INDEX IDX_C912ED9DA76ED395 (user_id),
  PRIMARY KEY (api_key)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
CREATE TABLE configuration (
  id                        INT AUTO_INCREMENT NOT NULL,
  current_frontend_theme_id INT DEFAULT NULL,
  invalidate_api_key_after  INT                NOT NULL,
  UNIQUE INDEX UNIQ_A5E2A5D7C1E1F4D5 (current_frontend_theme_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE api_key
  ADD CONSTRAINT FK_C912ED9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);
ALTER TABLE configuration
  ADD CONSTRAINT FK_A5E2A5D7C1E1F4D5 FOREIGN KEY (current_frontend_theme_id) REFERENCES theme (id);
DROP INDEX UNIQ_1483A5E992FC23A8
ON users;
DROP INDEX UNIQ_1483A5E9A0D96FBF
ON users;
DROP INDEX UNIQ_1483A5E9C05FB297
ON users;
ALTER TABLE users
  DROP username,
  DROP username_canonical,
  DROP email_canonical,
  CHANGE email email VARCHAR(255) NOT NULL,
  CHANGE password password LONGTEXT NOT NULL,
  CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL,
  CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74
  ON users (email);
ALTER TABLE menu_item
  DROP FOREIGN KEY FK_D754D550727ACA70;
ALTER TABLE menu_item
  ADD position INT NOT NULL;
ALTER TABLE menu_item
  ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id)
  ON DELETE CASCADE;
ALTER TABLE access_log
  CHANGE created_at created_at DATETIME DEFAULT '2017-10-31' NOT NULL;
ALTER TABLE page
  ADD name VARCHAR(255) NOT NULL,
  CHANGE history history LONGTEXT NOT NULL
COMMENT '(DC2Type:json)';
ALTER TABLE gallery
  CHANGE background background LONGTEXT DEFAULT NULL,
  CHANGE history history LONGTEXT NOT NULL
COMMENT '(DC2Type:json)';
ALTER TABLE route
  DROP FOREIGN KEY FK_2C420799AB44FE0;
ALTER TABLE route
  CHANGE menu_item_id menu_item_id INT NOT NULL;
ALTER TABLE route
  ADD CONSTRAINT FK_2C420799AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)
  ON DELETE CASCADE;
ALTER TABLE form_item
  ADD position INT NOT NULL,
  CHANGE history history LONGTEXT NOT NULL
COMMENT '(DC2Type:json)';
ALTER TABLE form
  ADD name VARCHAR(255) NOT NULL,
  CHANGE history history LONGTEXT NOT NULL
COMMENT '(DC2Type:json)';
CREATE UNIQUE INDEX UNIQ_5288FD4F5E237E06
  ON form (name);
INSERT INTO configuration (current_frontend_theme_id, invalidate_api_key_after) SELECT
                                                                                  current_theme_id,
                                                                                  1
                                                                                FROM frontend_configuration;
DROP TABLE frontend_configuration;