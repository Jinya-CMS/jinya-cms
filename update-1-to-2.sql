CREATE TABLE configuration (
  id                        INT AUTO_INCREMENT NOT NULL,
  current_frontend_theme_id INT DEFAULT NULL,
  current_designer_theme_id INT DEFAULT NULL,
  invalidate_api_key_after  INT                NOT NULL,
  UNIQUE INDEX UNIQ_A5E2A5D7C1E1F4D5 (current_frontend_theme_id),
  UNIQUE INDEX UNIQ_A5E2A5D71B972663 (current_designer_theme_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
ALTER TABLE configuration
  ADD CONSTRAINT FK_A5E2A5D7C1E1F4D5 FOREIGN KEY (current_frontend_theme_id) REFERENCES theme (id);
ALTER TABLE configuration
  ADD CONSTRAINT FK_A5E2A5D71B972663 FOREIGN KEY (current_designer_theme_id) REFERENCES theme (id);
ALTER TABLE access_log
  CHANGE created_at created_at DATETIME DEFAULT '2017-10-31' NOT NULL;
ALTER TABLE form
  ADD name VARCHAR(255) NOT NULL,
  CHANGE history history JSON NOT NULL;
CREATE UNIQUE INDEX UNIQ_5288FD4F5E237E06
  ON form (name);
ALTER TABLE form_item
  ADD position INT NOT NULL,
  CHANGE history history JSON NOT NULL;
CREATE UNIQUE INDEX idx_form_item_position_form
  ON form_item (position, form_id);
ALTER TABLE gallery
  CHANGE background background LONGTEXT DEFAULT NULL,
  CHANGE history history JSON NOT NULL;
ALTER TABLE menu_item
  ADD position INT NOT NULL;
CREATE UNIQUE INDEX idx_menu_item_parent_position
  ON menu_item (parent_id, position);
CREATE UNIQUE INDEX idx_menu_item_menu_position
  ON menu_item (menu_id, position);
ALTER TABLE page
  ADD name VARCHAR(255) NOT NULL,
  CHANGE history history JSON NOT NULL;
CREATE UNIQUE INDEX UNIQ_140AB6205E237E06
  ON page (name);
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
