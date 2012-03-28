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



CREATE TABLE tx_ptextbase_tree_node (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    label  tinytext,
    namespace  tinytext,
    lft   int(11) DEFAULT '0' NOT NULL,
    rgt   int(11) DEFAULT '0' NOT NULL,
    root  int(11) DEFAULT '0' NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_wsid int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(30) DEFAULT '' NOT NULL,
    t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
    t3ver_count int(11) DEFAULT '0' NOT NULL,
    t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
    t3_origuid int(11) DEFAULT '0' NOT NULL,

    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY lft (lft),
    KEY rgt (rgt),
    KEY root (root)
);