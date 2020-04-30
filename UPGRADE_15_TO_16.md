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
		foreign key (page_id) references page (id)
			on delete cascade;

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
```