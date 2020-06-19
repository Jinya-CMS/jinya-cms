# Upgrade from 15 to 16
## Needed sql scripts

```mysql
alter table uploading_file_chunk drop foreign key FK_3F70FB06FD34D444;
   
alter table uploading_file_chunk
    add constraint FK_uploading_file_uploading_file_chunk
        foreign key (uploading_file_id) references uploading_file (id)
            on delete cascade;

alter table uploading_file drop foreign key FK_C219262693CB796C;

alter table uploading_file
	add constraint FK_uploading_file_file
		foreign key (file_id) references file (id)
			on delete cascade;

alter table theme_page drop foreign key FK_5ECD421DC4663E4;

alter table theme_page
	add constraint FK_page_theme_page
		foreign key (page_id) references page (id);

alter table theme_page drop foreign key FK_C658C22D93CB796C;

alter table theme_page
	add constraint FK_page_theme_page
		foreign key (page_id) references page (id)
			on delete cascade;

alter table known_device drop foreign key FK_3C887E4CA76ED395;

alter table known_device
	add constraint FK_users_known_device
		foreign key (user_id) references users (id)
			on delete cascade;

alter table api_key drop foreign key FK_C912ED9DA76ED395;

alter table api_key
	add constraint FK_users_api_key
		foreign key (user_id) references users (id)
			on delete cascade;

alter table configuration drop foreign key FK_A5E2A5D7C1E1F4D5;

alter table configuration
	add constraint FK_theme_configuration
		foreign key (current_frontend_theme_id) references theme (id);

alter table configuration drop key UNIQ_A5E2A5D7C1E1F4D5;

alter table configuration
    add constraint UNIQ_configuration_current_frontend_theme_id
        unique (current_frontend_theme_id);

drop index name on file;

create unique index UNIQ_file_name
	on file (name);

alter table file drop foreign key FK_8C9F361061220EA6;

alter table file drop foreign key FK_8C9F3610896DBBDE;

alter table file
	add constraint FK_users_file_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table file
	add constraint FK_users_file_updated_by_id
		foreign key (updated_by_id) references users (id)
			on delete set null;

alter table file_tag drop foreign key FK_2CCA391A93CB796C;

alter table file_tag
	add constraint FK_file_file_tag
		foreign key (file_id) references file (id)
			on delete cascade;

alter table file_tag drop foreign key FK_2CCA391ABAD26311;

alter table file_tag
	add constraint FK_tag_file_tag
		foreign key (tag_id) references tag (id)
			on delete cascade;

create unique index UNIQ_page_title
	on page (title);

alter table page drop key UNIQ_140AB6205E237E06;

alter table page drop key UNIQ_140AB620989D9B62;

alter table page
	add constraint UNIQ_page_slug
		unique (slug);

alter table page drop foreign key FK_140AB62061220EA6;

alter table page drop foreign key FK_140AB620896DBBDE;

alter table page
	add constraint FK_users_page_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table page
	add constraint FK_users_page_last_updated_by_id
		foreign key (updated_by_id) references users (id)
			on delete set null;

alter table form drop key UNIQ_5288FD4F5E237E06;

alter table form drop key UNIQ_5288FD4F989D9B62;

alter table form
	add constraint UNIQ_form_slug
		unique (slug);

alter table form
	add constraint UNIQ_form_title
		unique (title);

alter table form drop foreign key FK_5288FD4F61220EA6;

alter table form drop foreign key FK_5288FD4F896DBBDE;

alter table form
	add constraint FK_users_form_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table form
	add constraint FK_users_form_last_updated_by_id
		foreign key (updated_by_id) references users (id)
			on delete set null;

alter table form_item drop foreign key FK_8B3A21095FF69B7D;

alter table form_item drop foreign key FK_8B3A210961220EA6;

alter table form_item drop foreign key FK_8B3A2109896DBBDE;

alter table form_item
	add constraint FK_form_form_item
		foreign key (form_id) references form (id)
			on delete cascade;

alter table form_item
	add constraint FK_users_form_item_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table form_item
	add constraint FK_users_form_item_last_updated_by_id
		foreign key (updated_by_id) references users (id)
			on delete set null;

drop index UNIQ_472B783A989D9B62 on gallery;

create unique index UNIQ_gallery_name
	on gallery (name);

create unique index UNIQ_gallery_slug
	on gallery (slug);

alter table gallery drop foreign key FK_472B783A61220EA6;

alter table gallery drop foreign key FK_472B783A896DBBDE;

alter table gallery
	add constraint FK_users_gallery_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table gallery
	add constraint FK_users_gallery_last_updated_by_id
		foreign key (updated_by_id) references users (id);

alter table gallery_file_position drop foreign key FK_57EA691A4E7AF8F;

alter table gallery_file_position drop foreign key FK_57EA691A93CB796C;

alter table gallery_file_position
	add constraint FK_file_gallery_file_position
		foreign key (file_id) references file (id)
			on delete cascade;

alter table gallery_file_position
	add constraint FK_gallery_gallery_file_position
		foreign key (gallery_id) references gallery (id)
			on delete cascade;

alter table menu_item drop foreign key FK_D754D550727ACA70;

alter table menu_item drop foreign key FK_D754D550CCD7E912;

alter table menu_item
	add constraint FK_menu_item_menu_item_parent
		foreign key (parent_id) references menu_item (id)
			on delete cascade;

alter table menu_item
	add constraint FK_menu_menu_item
		foreign key (menu_id) references menu (id)
			on delete cascade;

alter table message drop foreign key FK_B6BD307F5FF69B7D;

alter table message
	add constraint FK_form_message
		foreign key (form_id) references form (id);

alter table route drop foreign key FK_2C420799AB44FE0;
alter table route drop key UNIQ_2C420799AB44FE0;

alter table route
	add constraint UNIQ_route_menu_item_id
		unique (menu_item_id);

alter table route
	add constraint FK_menu_item_route
		foreign key (menu_item_id) references menu_item (id)
			on delete cascade;

alter table segment drop foreign key FK_1881F5654E7AF8F;

alter table segment drop foreign key FK_1881F5655FF69B7D;

alter table segment drop foreign key FK_1881F56593CB796C;

alter table segment drop foreign key FK_1881F565C4663E4;

alter table segment
	add constraint FK_file_segment
		foreign key (file_id) references file (id)
			on delete cascade;

alter table segment
	add constraint FK_form_segment
		foreign key (form_id) references form (id)
			on delete cascade;

alter table segment
	add constraint FK_gallery_segment
		foreign key (gallery_id) references gallery (id)
			on delete cascade;

alter table segment
	add constraint FK_segment_page_segment
		foreign key (page_id) references segment_page (id)
			on delete cascade;

alter table segment_page drop key UNIQ_CBA9317D5E237E06;

alter table segment_page drop key UNIQ_CBA9317D989D9B62;

alter table segment_page
	add constraint UNIQ_segment_page_name
		unique (name);

alter table segment_page
	add constraint UNIQ_segment_page_slug
		unique (slug);

alter table segment_page drop foreign key FK_CBA9317D61220EA6;

alter table segment_page drop foreign key FK_CBA9317D896DBBDE;

alter table segment_page
	add constraint FK_users_segment_page_creator_id
		foreign key (creator_id) references users (id)
			on delete set null;

alter table segment_page
	add constraint FK_users_segment_page_last_updated_by_id
		foreign key (updated_by_id) references users (id)
			on delete set null;

alter table theme
    drop foreign key FK_9775E70874EAD26;

alter table theme
    drop foreign key FK_9775E7089C91FD2;

alter table theme
    drop foreign key FK_9775E708AC3C935A;

drop index IDX_9775E70874EAD26 on theme;

drop index IDX_9775E7089C91FD2 on theme;

drop index IDX_9775E708AC3C935A on theme;

alter table theme
    drop column primary_menu_id;

alter table theme
    drop column secondary_menu_id;

alter table theme
    drop column footer_menu_id;

alter table theme_file drop foreign key FK_C658C22D59027487;

alter table theme_file
	add constraint FK_theme_theme_file
		foreign key (theme_id) references theme (id);

alter table theme_form drop foreign key FK_184F097259027487;

alter table theme_form drop foreign key FK_184F09725FF69B7D;

alter table theme_form
	add constraint FK_form_theme_form
		foreign key (form_id) references form (id);

alter table theme_form
	add constraint FK_theme_theme_form
		foreign key (theme_id) references theme (id)
			on delete cascade;

alter table theme_gallery drop foreign key FK_1E487D194E7AF8F;

alter table theme_gallery drop foreign key FK_1E487D1959027487;

alter table theme_gallery
	add constraint FK_gallery_theme_gallery
		foreign key (gallery_id) references gallery (id);

alter table theme_gallery
	add constraint FK_theme_theme_gallery
		foreign key (theme_id) references theme (id)
			on delete cascade;

alter table theme_menu drop foreign key FK_37C2CEAE59027487;

alter table theme_menu drop foreign key FK_37C2CEAECCD7E912;

alter table theme_menu
	add constraint FK_menu_theme_menu
		foreign key (menu_id) references menu (id);

alter table theme_menu
	add constraint FK_theme_theme_menu
		foreign key (theme_id) references theme (id)
			on delete cascade;

alter table theme_page drop foreign key FK_5ECD421D59027487;

alter table theme_page
	add constraint FK_theme_theme_page
		foreign key (theme_id) references theme (id)
			on delete cascade;

alter table theme_segment_page drop foreign key FK_5BFE07FF533F2206;

alter table theme_segment_page drop foreign key FK_5BFE07FF59027487;

alter table theme_segment_page
	add constraint FK_segment_page_theme_segment_page
		foreign key (segment_page_id) references segment_page (id);

alter table theme_segment_page
	add constraint FK_theme_theme_segment_page
		foreign key (theme_id) references theme (id)
			on delete cascade;

drop index UNIQ_C219262693CB796C on uploading_file;

create unique index UNIQ_uploading_file_file_id
	on uploading_file (file_id);

drop index UNIQ_1483A5E9E7927C74 on users;

create unique index UNIQ_users_email
	on users (email);

update form_item set type = 'text' where type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType';

update form_item set type = 'email' where type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType';

update form_item set type = 'select' where type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType';

update form_item set type = 'textarea' where type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType';

update form_item set type = 'checkbox' where type = 'Symfony\\Component\\Form\\Extension\\Core\\Type\\CheckboxType';

alter table form drop column name;

alter table page drop column name;

alter table gallery drop column slug;

alter table form drop column slug;

alter table form drop column email_template;

alter table page drop column slug;

alter table segment_page drop column slug;

drop index idx_form_item_position_form on form_item;

alter table form_item drop key idx_form_item_position_form;

alter table form_item drop column created_at;

alter table form_item drop column last_updated_at;

alter table form_item drop foreign key FK_users_form_item_last_updated_by_id;

alter table form_item drop foreign key FK_users_form_item_creator_id;

drop index IDX_8B3A210961220EA6 on form_item;

drop index IDX_8B3A2109896DBBDE on form_item;

alter table form_item drop column creator_id;

alter table form_item drop column updated_by_id;

drop table route;

alter table menu_item drop column page_type;

alter table menu_item
	add artist_id int null;

alter table menu_item
	add page_id int null;

alter table menu_item
	add form_id int null;

alter table menu_item
	add gallery_id int null;

alter table menu_item
	add segment_page_id int null;

alter table menu_item
	add constraint FK_artist_menu_item
		foreign key (artist_id) references users (id)
			on delete cascade;

alter table menu_item
	add constraint FK_form_menu_item
		foreign key (form_id) references form (id)
			on delete cascade;

alter table menu_item
	add constraint FK_gallery_menu_item
		foreign key (gallery_id) references gallery (id)
			on delete cascade;

alter table menu_item
	add constraint FK_page_menu_item
		foreign key (page_id) references page (id)
			on delete cascade;

alter table menu_item
	add constraint FK_segment_page_menu_item
		foreign key (segment_page_id) references segment_page (id)
			on delete cascade;

alter table menu drop column logo;

alter table menu
	add logo int null;

alter table menu
	add constraint FK_file_menu
		foreign key (logo) references file (id)
			on delete set null;

alter table theme_menu drop foreign key FK_menu_theme_menu;

alter table theme_menu
	add constraint FK_menu_theme_menu
		foreign key (menu_id) references menu (id)
			on delete cascade;

alter table theme_gallery drop foreign key FK_gallery_theme_gallery;

alter table theme_gallery
	add constraint FK_gallery_theme_gallery
		foreign key (gallery_id) references gallery (id)
			on delete cascade;

alter table theme_file drop foreign key FK_file_theme_file;

alter table theme_file
	add constraint FK_file_theme_file
		foreign key (file_id) references file (id)
			on delete cascade;

alter table theme_form drop foreign key FK_form_theme_form;

alter table theme_form
	add constraint FK_form_theme_form
		foreign key (form_id) references form (id)
			on delete cascade;

alter table theme_page drop foreign key FK_page_theme_page;

alter table theme_page
	add constraint FK_page_theme_page
		foreign key (page_id) references page (id)
			on delete cascade;

alter table theme_segment_page drop foreign key FK_segment_page_theme_segment_page;

alter table theme_segment_page
	add constraint FK_segment_page_theme_segment_page
		foreign key (segment_page_id) references segment_page (id)
			on delete cascade;

alter table message
	add answer longtext null;

alter table theme
    drop column preview_image;

alter table menu_item
	add route varchar(255) null;

alter table form_item
	add is_from_address bit default false null;

alter table form_item
	add is_subject bit default 0 null;

alter table form_item
	add is_required bit default 0 null;

alter table form_item
	add placeholder varchar(255) null;
```