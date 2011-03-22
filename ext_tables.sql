CREATE TABLE tx_ptextbase_cache_state (
   id int(11) NOT NULL auto_increment,
   identifier varchar(128) NOT NULL DEFAULT '',
   crdate int(11) unsigned NOT NULL DEFAULT '0',
   content mediumtext,
   lifetime int(11) unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY (id),
   KEY cache_id (`identifier`)
);

CREATE TABLE tx_ptextbase_cache_state_tags (
   id int(11) NOT NULL auto_increment,
   identifier varchar(128) NOT NULL DEFAULT '',
   tag varchar(128) NOT NULL DEFAULT '',
   PRIMARY KEY (id),
   KEY cache_id (`identifier`),
   KEY cache_tag (`tag`)
);