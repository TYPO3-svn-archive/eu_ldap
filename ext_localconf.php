<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");


$config = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['eu_ldap']);

if ($config['enableFE'] && !$config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE';
}

if (!$config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserBE,authUserBE';
}

if ($config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE,getUserBE,authUserBE';
}

// register Service with highest priority
t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_euldap_sv1' /* sv key */,
	array(

		'title' => 'LDAP-Authentication',
		'description' => 'Authentication service for LDAP (FE and BE).',
		'subtype' => $subTypes,
		'available' => 1,
		'priority' => 100,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv1/class.tx_euldap_sv1.php',
		'className' => 'tx_euldap_sv1',
	)
);

$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_euldap_import'] = array(
	'extension' => $_EXTKEY, // Selbsterklärend
	'title' => 'LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:schedulertask.name', // Der Titel der Aufgabe
	'description' => 'LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:schedulertask.description', // Die Beschreibung der Aufgabe
	// 'additionalFields' => 'tx_extkey_TaskName_AdditionalFieldProvider' // Zusätzliche Felder
);
?>