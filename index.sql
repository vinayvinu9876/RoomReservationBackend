CREATE SCHEMA roomreservation;

CREATE TABLE roomreservation.features ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	feature_name         VARCHAR(300)  NOT NULL    ,
	feature_desc         VARCHAR(1000)  NOT NULL    ,
	`status`             VARCHAR(100)  NOT NULL    ,
	created_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	total_available      INT  NOT NULL    
 ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.priority ( 
	id                   INT  NOT NULL AUTO_INCREMENT   PRIMARY KEY,
	name                 VARCHAR(100)  NOT NULL    ,
	`desc`               VARCHAR(1000)  NOT NULL    ,
	created_on           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_on           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	`status`             VARCHAR(30)  NOT NULL    ,
	priority_no          INT  NOT NULL    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.rooms ( 
	room_id              INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	room_name            VARCHAR(300)  NOT NULL    ,
	room_desc            VARCHAR(1000)      ,
	room_capacity        INT      ,
	`status`             VARCHAR(40)  NOT NULL    ,
	created_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_at           TIMESTAMP  NOT NULL DEFAULT (now())   
 ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.room_down_time ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	room_id              INT  NOT NULL    ,
	`desc`               VARCHAR(1000)  NOT NULL    ,
	start                TIME  NOT NULL DEFAULT (curtime())   ,
	end                  TIME  NOT NULL DEFAULT (curtime())   ,
	day                  VARCHAR(20)  NOT NULL    ,
	created_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	`status`             VARCHAR(100)  NOT NULL DEFAULT ('1')   ,
	CONSTRAINT fk_room_down_time_rooms FOREIGN KEY ( room_id ) REFERENCES roomreservation.rooms( room_id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.room_features ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	feature_id           INT  NOT NULL    ,
	room_id              INT  NOT NULL    ,
	created_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	`status`             VARCHAR(100)  NOT NULL    ,
	total_available      INT  NOT NULL    ,
	CONSTRAINT fk_room_features_features FOREIGN KEY ( feature_id ) REFERENCES roomreservation.features( id ) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT fk_room_features_rooms FOREIGN KEY ( room_id ) REFERENCES roomreservation.rooms( room_id ) ON DELETE CASCADE ON UPDATE CASCADE
 ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.room_reservation ( 
	reservation_id       INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	room_id              INT  NOT NULL    ,
	start_timestamp      TIMESTAMP  NOT NULL    ,
	end_timestamp        TIMESTAMP  NOT NULL DEFAULT (now())   ,
	reservation_description VARCHAR(1000)      ,
	reservation_requirements VARCHAR(1000)      ,
	reserved_by_email    VARCHAR(100)  NOT NULL    ,
	created_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_at           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	`status`             VARCHAR(100)  NOT NULL    ,
	priority_id          INT  NOT NULL    ,
	attendees_email      TEXT      ,
	no_of_attendees      INT  NOT NULL    ,
	CONSTRAINT fk_room_reservation_priority FOREIGN KEY ( priority_id ) REFERENCES roomreservation.priority( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_room_reservation_rooms FOREIGN KEY ( room_id ) REFERENCES roomreservation.rooms( room_id ) ON DELETE CASCADE ON UPDATE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE INDEX fk_features_rooms ON roomreservation.features ( `status` );

CREATE INDEX fk_rooms_room_status ON roomreservation.rooms ( `status` );

CREATE INDEX fk_room_down_time_rooms ON roomreservation.room_down_time ( room_id );

CREATE INDEX fk_room_features_features ON roomreservation.room_features ( feature_id );

CREATE INDEX fk_room_features_rooms ON roomreservation.room_features ( room_id );

CREATE INDEX fk_room_features_status ON roomreservation.room_features ( `status` );

CREATE INDEX fk_room_reservation_rooms ON roomreservation.room_reservation ( room_id );

CREATE INDEX fk_room_reservation ON roomreservation.room_reservation ( `status` );

CREATE INDEX fk_room_reservation_priority ON roomreservation.room_reservation ( priority_id );

ALTER TABLE roomreservation.room_reservation MODIFY attendees_email TEXT     COMMENT 'comma separated emails of of attendees';

INSERT INTO roomreservation.features( id, feature_name, feature_desc, `status`, created_at, updated_at, total_available ) VALUES ( 3, 'HP Television', '12" Sony Telivision with utlra brightness. Remote has been lost last month. Can control using phone', 'active', '2022-04-22 05.14.02 pm', '2022-04-22 05.14.02 pm', 1);
INSERT INTO roomreservation.features( id, feature_name, feature_desc, `status`, created_at, updated_at, total_available ) VALUES ( 4, 'Microphone', 'Microphone with 200meter audible range. That doesnt work. ', 'active', '2022-04-22 05.14.47 pm', '2022-04-22 05.14.47 pm', 1);
INSERT INTO roomreservation.priority( id, name, `desc`, created_on, updated_on, `status`, priority_no ) VALUES ( 0, 'Mid Priority', 'Any TL and other mid level importance meeting can choose this', '2022-04-27 04.07.22 pm', '2022-04-27 04.07.22 pm', 'active', 2);
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 1, 'meeting room12', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 10, 'active', '2022-04-21 06.15.59 pm', '2022-04-22 06.26.13 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 2, 'Meeting room1', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 10, 'active', '2022-04-21 06.16.06 pm', '2022-04-21 06.16.06 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 3, 'Meeting room3', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 10, 'active', '2022-04-21 06.18.41 pm', '2022-04-21 06.18.41 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 4, 'Meeting room4', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 1000, 'active', '2022-04-21 07.38.00 pm', '2022-04-21 07.38.00 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 5, 'meeting room10', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 1000, 'active', '2022-04-21 08.00.45 pm', '2022-04-21 08.00.45 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 6, 'Meeting room6', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 1000, 'active', '2022-04-22 06.42.55 pm', '2022-04-22 06.42.55 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 7, 'Meeting room7', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 100, 'active', '2022-04-26 04.14.16 pm', '2022-04-26 04.14.16 pm');
INSERT INTO roomreservation.rooms( room_id, room_name, room_desc, room_capacity, `status`, created_at, updated_at ) VALUES ( 8, 'Meeting room100', 'Located at 2nd floor. Rightside to the entrance. Avaialble for board meeting and other small meetings for shorter time.', 100, 'active', '2022-04-26 04.38.55 pm', '2022-04-26 04.38.55 pm');
INSERT INTO roomreservation.room_down_time( id, room_id, `desc`, start, end, day, created_at, updated_at, `status` ) VALUES ( 1, 1, 'hello world we are doing great. But need some maintenacne', '10:00:00', '11:00:00', 'mon', '2022-04-25 05.01.54 pm', '2022-04-25 09.45.31 pm', 'active');
INSERT INTO roomreservation.room_down_time( id, room_id, `desc`, start, end, day, created_at, updated_at, `status` ) VALUES ( 3, 2, 'hello world we are partying for while', '09:00:00', '10:00:00', 'mon', '2022-04-25 10.43.24 pm', '2022-04-25 10.43.24 pm', 'inactive');
INSERT INTO roomreservation.room_features( id, feature_id, room_id, created_at, updated_at, `status`, total_available ) VALUES ( 7, 3, 2, '2022-04-22 08.53.28 pm', '2022-04-25 03.41.18 pm', 'active', 5);
INSERT INTO roomreservation.room_features( id, feature_id, room_id, created_at, updated_at, `status`, total_available ) VALUES ( 8, 4, 1, '2022-04-25 03.18.29 pm', '2022-04-25 03.18.29 pm', 'active', 5);


CREATE TABLE roomreservation.media ( 
	id                   INT  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	filename             VARCHAR(500)  NOT NULL    ,
	url                  TEXT  NOT NULL    ,
	created_on           TIMESTAMP  NOT NULL DEFAULT (now())   ,
	updated_on           DATE  NOT NULL DEFAULT (curdate())   
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE roomreservation.room_media ( 
	id                   INT  NOT NULL PRIMARY KEY AUTO_INCREMENT,
	room_id              INT  NOT NULL,
	media_id             INT  NOT NULL,
	`status`             VARCHAR(100)  NOT NULL    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE INDEX fk_room_media_media ON roomreservation.room_media ( media_id );

CREATE INDEX fk_room_media_rooms ON roomreservation.room_media ( room_id );

ALTER TABLE roomreservation.room_media ADD CONSTRAINT fk_room_media_media FOREIGN KEY ( media_id ) REFERENCES roomreservation.media( id ) ON DELETE NO ACTION ON UPDATE NO ACTION;

alter table priority add column role_ids varchar(4) after id;