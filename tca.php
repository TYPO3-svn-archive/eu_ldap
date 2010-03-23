<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_euldap_server"] = Array (
	"ctrl" => $TCA["tx_euldap_server"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "server,port,domain,base_dn"
	),
	"feInterface" => $TCA["tx_euldap_server"]["feInterface"],
	"columns" => Array (
		"server" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.server",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"port" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.port",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"eval" => "int,nospace",
				"default" => "389",
			)
		),
		"version" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.ldapversion",		
			"config" => Array (
				"type" => "select",
                "items" => Array (
                   	Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.ldapversion.I.0", "2"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.ldapversion.I.1", "3"),
                ),
			)
		),
		"domain" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.domain",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"base_dn" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.base_dn",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"filter" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.filter",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
				"default" => "(&(objectClass=user)(objectCategory=person)(samaccountname=<search>))",
			)
		),
		"servertype" => Array (        
			"exclude" => 0,        
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.servertype",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.servertype.I.0", "0"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.servertype.I.1", "1"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.servertype.I.2", "2"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.servertype.I.3", "3"),
				),
			)
		),
		"name" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
				"default" => "displayname",
			)
		),
		"username" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.username",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
				"default" => "uid",
			)
		),
		"address" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.address",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "streetaddress",
			)
		),
		"zip" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.zip",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "postalcode",
			)
		),
		"city" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.city",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "l",
			)
		),
		"country" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.country",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "countrycode",
			)
		),
		"fax" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.fax",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "facsimiletelephonenumber",
			)
		),
		"mail" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.mail",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
				"default" => "mail",
			)
		),
		"phone" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.phone",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "telephonenumber",
			)
		),
		"www" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.www",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
				"default" => "wwwhomepage",
			)
		),
		"memberof" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.memberof",		
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		),
		"build_group" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.build_group",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"map_additional_fields" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.map_additional_fields",		
			"config" => Array (
				"type" => "input",	
				"size" => "255",	
				"eval" => "trim"
			)
		),
		"only_emailusers" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.only_emailusers",		
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		),
		"automatic_import" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.automatic_import",		
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		),
		"authenticate_be" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.authenticate_be",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.authenticate_be.I.0", "0"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.authenticate_be.I.1", "1"),
					Array("LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.authenticate_be.I.2", "2"),
				),
			)
		),
		"user" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.user",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"password" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.password",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim,password",
			)
		),
		"doitfe" => Array (        
		 	"exclude" => 0,        
		     	"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.doitfe",        
			"config" => Array (
				"type" => "check",
			)
		),
		"matchgrps" => Array (        
			"exclude" => 0,        
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.matchgrps",        
			"config" => Array (
				"type" => "input",    
				"size" => "60",
			)
		),
		"timestamp" => Array (        
			"exclude" => 0,        
			"label" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.timestamp",        
			"config" => Array (
				"type" => "input",    
				"size" => "30",    
				"default" => "logintime",
			)
		),
		'fe_group' => Array (		
			'l10n_mode' => 'exclude',
			'exclude' => 1,	
			'label' => 'LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.fe_group',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'fe_groups',
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'be_group' => Array (		
			'l10n_mode' => 'exclude',
			'exclude' => 1,	
			'label' => 'LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server.be_group',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'be_groups',
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "server;;;;1-1-1, port, version, base_dn, filter, servertype, domain, authenticate_be, user;;;;2-2-2, password, name;;;;3-3-3, mail, address;;;;4-4-4, zip, city, country, phone, fax, www, memberof;;;;5-5-5, build_group, only_emailusers, map_additional_fields, automatic_import, doitfe, matchgrps, timestamp, fe_group, be_group"),
		"1" => Array("showitem" => "server;;;;1-1-1, port, version, base_dn, filter, servertype, domain, authenticate_be, user;;;;2-2-2, password, name;;;;3-3-3, mail, address;;;;4-4-4, zip, city, country, phone, fax, www, memberof;;;;5-5-5, build_group, only_emailusers, map_additional_fields, automatic_import, doitfe, matchgrps, timestamp, fe_group, be_group"),
		"2" => Array("showitem" => "server;;;;1-1-1, port, version, base_dn, filter, servertype, authenticate_be, user;;;;2-2-2, password, username;;;;3-3-3, name, mail, address;;;;4-4-4, zip, city, country, phone, fax, www, memberof;;;;5-5-5, build_group, only_emailusers, map_additional_fields, automatic_import, doitfe, matchgrps, timestamp, fe_group, be_group")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

$TCA['tx_euldap_scheduler'] = array (
    'ctrl' => $TCA['tx_euldap_scheduler']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'hidden,title,action,usertable,pages,interval,intervalunit'
    ),
    'feInterface' => $TCA['tx_euldap_scheduler']['feInterface'],
    'columns' => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'title' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.title',        
            'config' => array (
                'type' => 'input',    
                'size' => '30',    
                'eval' => 'required,trim',
            )
        ),
        'action' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.action',        
            'config' => array (
                'type' => 'check',
                'cols' => 4,
                'items' => array (
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.action.I.0', ''),
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.action.I.1', ''),
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.action.I.2', ''),
                ),
            )
        ),
        'usertable' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.usertable',        
            'config' => array (
                'type' => 'check',
                'cols' => 4,
                'items' => array (
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.usertable.I.0', ''),
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.usertable.I.1', ''),
                ),
            )
        ),
        'pages' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.pages',        
            'config' => array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'pages',    
                'size' => 5,    
                'minitems' => 0,
                'maxitems' => 20,
            )
        ),
        'interval' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.interval',        
            'config' => array (
                'type' => 'input',    
                'size' => '5',    
                'max' => '2',    
                'range' => array ('lower'=>0,'upper'=>1000),    
                'eval' => 'required,int',
            )
        ),
        'intervalunit' => array (        
            'exclude' => 0,        
            'label' => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.intervalunit',        
            'config' => array (
                'type' => 'select',
                'items' => array (
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.intervalunit.I.0', '60'),
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.intervalunit.I.1', '3600'),
                    array('LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler.intervalunit.I.2', '86400'),
                ),
                'size' => 1,    
                'maxitems' => 1,
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, action;;;;3-3-3, usertable, pages, interval, intervalunit')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);

?>
