CREATE TABLE application (
 id int(10) unsigned NOT NULL AUTO_INCREMENT,
 name varchar(128) NOT NULL DEFAULT '',
 number int(11) NOT NULL,
 email varchar(64),
 bdate DATE,
 gen varchar(8) NOT NULL,
 biography varchar(512),
 checkbox varchar(8) NOT NULL,
 PRIMARY KEY (id)
);

CREATE TABLE prog_lang(
 id int(10) unsigned NOT NULL AUTO_INCREMENT,
 id_lang_name int(64) unsigned NOT NULL,
 PRIMARY KEY (id),
 FOREIGN KEY (id_lang_name) REFERENCES prog(id_lang_name),
 FOREIGN KEY (id) REFERENCES application(id)
);

CREATE TABLE prog(
 id_lang_name int(10) unsigned NOT NULL AUTO_INCREMENT,
 lang_name varchar(64) NOT NULL,
 PRIMARY KEY (id_lang_name)
);
