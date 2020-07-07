DROP DATABASE if exists microwave_radio_path;
CREATE DATABASE if not exists `microwave_radio_path` CHARACTER SET utf8;
drop user if exists 'lamp2user'@'localhost';
GRANT ALL PRIVILEGES ON `microwave_radio_path`.* TO 'lamp2user'@'localhost' identified by '!Lamp12!';

USE microwave_radio_path;

drop table if exists path_info;
drop table if exists path_end;
drop table if exists path_mid;

CREATE TABLE if not exists path_info (
  pt_id int(11) NOT NULL AUTO_INCREMENT,
  pt_name varchar(100) NOT NULL UNIQUE,
  pt_frequency float NOT NULL ,
  pt_description varchar(255) NOT NULL ,
  pt_note text,
  PRIMARY KEY (`pt_id`)
);

create table if not exists path_end (
	ed_id int(11) not null auto_increment,
	ed_pt_id int(11) not null,
	ed_distance float not null,
	ed_ground_height float not null,
	ed_antenna_height float not null,
	ed_antenna_type varchar(10) not null,
	ed_antenna_length float not null,
	primary key (ed_id),
	key `path_id` (ed_pt_id),
	foreign key (ed_pt_id) references path_info(pt_id)
);

create table if not exists path_mid(
	md_id int(11) not null auto_increment,
	md_pt_id int(11) not null,
	md_distance float not null,
	md_ground_height float not null,
	md_terrain_type varchar(50) not null,
	md_obstruction_height float not null,
	md_obstruction_type varchar(50) not null,
	primary key (md_id),
	key `path_id` (md_pt_id),
	foreign key (md_pt_id) references path_info(pt_id)
);

