CREATE TABLE log (
  lid serial,
  uid int NOT NULL default 0,
  ip varchar(255) NOT NULL default '0.0.0.0',
  type varchar(255) NOT NULL default '',
  agent varchar(255) NOT NULL default '',
  description text,
  extra text,
  timestamp timestamp,
  PRIMARY KEY (lid)
);
CREATE INDEX log_uid_idx ON log (uid);
CREATE INDEX log_ip_idx ON log (ip);
CREATE INDEX log_type_idx ON log (type);
CREATE INDEX log_timestamp_idx ON log (timestamp);

CREATE TABLE access (
  ip varchar(255) NOT NULL default '0.0.0.0',
  request_uri varchar(255) NOT NULL default '',
  malicious_actions varchar(255) NOT NULL default '',
  expire int NOT NULL default 0
);
CREATE INDEX access_ip_idx ON access (ip);
CREATE INDEX access_request_uri_idx ON access (request_uri);
CREATE INDEX access_malicious_actions_idx ON access (malicious_actions);
CREATE INDEX access_expire_idx ON access (expire);

