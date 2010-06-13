<?php

########################################################################
# Extension Manager/Repository config file for ext "eu_ldap".
#
# Auto generated 07-06-2010 21:06
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'LDAP',
	'description' => 'LDAP Integration',
	'category' => 'module',
	'shy' => 0,
	'version' => '2.8.6',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Norman Seibert',
	'author_email' => 'seibert@entios.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.7.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:24:{s:9:"Thumbs.db";s:4:"01bc";s:16:"ext_autoload.php";s:4:"1a11";s:21:"ext_conf_template.txt";s:4:"fba9";s:12:"ext_icon.gif";s:4:"207f";s:17:"ext_localconf.php";s:4:"f489";s:15:"ext_php_api.dat";s:4:"7d5b";s:14:"ext_tables.php";s:4:"e78e";s:14:"ext_tables.sql";s:4:"4a75";s:25:"icon_tx_euldap_server.gif";s:4:"300a";s:16:"locallang_db.xml";s:4:"9885";s:7:"tca.php";s:4:"d758";s:14:"doc/manual.sxw";s:4:"5e69";s:28:"mod1/class.tx_euldap_div.php";s:4:"0d3b";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"eae2";s:14:"mod1/index.php";s:4:"0d69";s:18:"mod1/locallang.php";s:4:"746e";s:18:"mod1/locallang.xml";s:4:"5b80";s:22:"mod1/locallang_mod.xml";s:4:"4a3f";s:19:"mod1/moduleicon.gif";s:4:"207f";s:36:"scheduler/class.tx_euldap_import.php";s:4:"1bdb";s:46:"scheduler/class.tx_euldap_import_addfields.php";s:4:"6f55";s:27:"sv1/class.tx_euldap_sv1.php";s:4:"b29a";s:27:"sv2/class.tx_euldap_sv2.php";s:4:"f7b9";}',
	'suggests' => array(
	),
);

?>