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
```