#
# Table structure for table 'tx_euldap_server'
#
CREATE TABLE tx_euldap_server (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	server varchar(255) DEFAULT 'ldap://' NOT NULL,
	servertype tinyint(4) DEFAULT '0' NOT NULL,
	domain varchar(255) DEFAULT '' NOT NULL,
	port int(11) DEFAULT '389' NOT NULL,
	base_dn varchar(255) DEFAULT '' NOT NULL,
	filter text NOT NULL,
	memberof tinyint(4) unsigned DEFAULT '1' NOT NULL,
	build_group varchar(255) DEFAULT '' NOT NULL,
	only_emailusers tinyint(4) unsigned DEFAULT '0' NOT NULL,
	automatic_import tinyint(4) unsigned DEFAULT '1' NOT NULL,
	name varchar(255) DEFAULT 'displayname' NOT NULL,
	username varchar(255) DEFAULT 'uid' NOT NULL,
	mail varchar(255) DEFAULT 'mail' NOT NULL,
	phone varchar(255) DEFAULT 'telephonenumber' NOT NULL,
	fax varchar(255) DEFAULT 'facsimiletelephonenumber' NOT NULL,
	address varchar(255) DEFAULT 'streetaddress' NOT NULL,
	city varchar(255) DEFAULT 'l' NOT NULL,
	zip varchar(255) DEFAULT 'postalcode' NOT NULL,
	country varchar(255) DEFAULT 'countrycode' NOT NULL,
	www varchar(255) DEFAULT 'wwwhomepage' NOT NULL,
	user varchar(255) DEFAULT '' NOT NULL,
	password varchar(255) DEFAULT '' NOT NULL,
	version tinyint(4) unsigned DEFAULT '3' NOT NULL,
	authenticate_be tinyint(4) unsigned DEFAULT '0' NOT NULL,
	doitfe tinyint(3) unsigned DEFAULT '0' NOT NULL,
	matchgrps tinytext NOT NULL,
	timestamp varchar(255) DEFAULT 'logintime' NOT NULL,
	map_additional_fields tinytext DEFAULT '' NOT NULL,
	fe_group varchar(255) DEFAULT '' NOT NULL,
	be_group varchar(255) DEFAULT '' NOT NULL,
	feuser_pid int(11) unsigned DEFAULT '0' NOT NULL,
	characterset varchar(255) DEFAULT 'utf-8' NOT NULL,
			    
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE fe_groups (
	description varchar(255) DEFAULT '' NOT NULL,
	eu_ldap tinyint(1) DEFAULT '0' NOT NULL
);

CREATE TABLE be_groups (
	description varchar(255) DEFAULT '' NOT NULL,
	eu_ldap tinyint(1) DEFAULT '0' NOT NULL
);

CREATE TABLE fe_users (
	description varchar(255) DEFAULT '' NOT NULL,
	tx_euldap_dn varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE be_users (
	description varchar(255) DEFAULT '' NOT NULL,
	tx_euldap_dn varchar(255) DEFAULT '' NOT NULL
);

CREATE TABLE tx_euldap_scheduler (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    sorting int(10) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    action int(11) DEFAULT '0' NOT NULL,
    usertable int(11) DEFAULT '0' NOT NULL,
    pages text,
    intervalvalue int(11) DEFAULT '0' NOT NULL,
    intervalunit int(11) DEFAULT '0' NOT NULL,
    lastrun int(11) DEFAULT '0' NOT NULL,
    
    PRIMARY KEY (uid),
    KEY parent (pid)
);