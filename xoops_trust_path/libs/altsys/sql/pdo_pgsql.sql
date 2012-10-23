# CREATE TABLE `tablename` will be queried as
# CREATE TABLE `prefix_dirname_tablename`

CREATE TABLE language_constants (
  mid smallint NOT NULL default 0,
  language varchar(32) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  value text,
  PRIMARY KEY (mid,language,name)
);

