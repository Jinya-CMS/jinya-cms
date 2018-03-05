
 The following SQL statements will be executed:

     ALTER TABLE theme ADD primary_menu_id INT DEFAULT NULL, ADD secondary_menu_id INT DEFAULT NULL, ADD footer_menu_id INT DEFAULT NULL, CHANGE scss_variables scss_variables JSON NOT NULL;
     ALTER TABLE theme ADD CONSTRAINT FK_9775E70874EAD26 FOREIGN KEY (primary_menu_id) REFERENCES menu (id);
     ALTER TABLE theme ADD CONSTRAINT FK_9775E7089C91FD2 FOREIGN KEY (secondary_menu_id) REFERENCES menu (id);
     ALTER TABLE theme ADD CONSTRAINT FK_9775E708AC3C935A FOREIGN KEY (footer_menu_id) REFERENCES menu (id);
     CREATE INDEX IDX_9775E70874EAD26 ON theme (primary_menu_id);
     CREATE INDEX IDX_9775E7089C91FD2 ON theme (secondary_menu_id);
     CREATE INDEX IDX_9775E708AC3C935A ON theme (footer_menu_id);
     ALTER TABLE access_log CHANGE created_at created_at DATETIME DEFAULT '2017-10-31' NOT NULL;
