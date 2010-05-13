<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_MODE=="BE")    {
	t3lib_extMgm::addModule("web","txeuldapM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");
}

$TCA["tx_euldap_server"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:eu_ldap/locallang_db.php:tx_euldap_server",		
		"label" => "server",	
//		"rootLevel" => 1,
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_euldap_server.gif",
		'dividers2tabs'     => true,
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "server, port, base_dn, user, password",
	)
);

$TCA['tx_euldap_scheduler'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:eu_ldap/locallang_db.xml:tx_euldap_scheduler',        
        'label'     => 'uid',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_euldap_scheduler.gif',
    ),
);

$tempColumns = Array (
    "title" => Array (        
        "exclude" => 0,        
        "label" => "LLL:EXT:lang/locallang_general.php:LGL.title",
        "config" => Array (
            "type" => "input",    
            "size" => "30",    
            "eval" => "trim",
        )
    ),
);


t3lib_div::loadTCA("fe_groups");
t3lib_extMgm::addTCAcolumns("fe_groups",$tempColumns,1);

t3lib_div::loadTCA("be_groups");
t3lib_extMgm::addTCAcolumns("be_groups",$tempColumns,1);


?>