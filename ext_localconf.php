<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$config = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['eu_ldap']);

if ($config['enableFE'] && !$config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE';
	$TYPO3_CONF_VARS['FE']['loginSecurityLevel'] = 'normal';
}

if (!$config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserBE,authUserBE';
	$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
}

if ($config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE,getUserBE,authUserBE';
	$TYPO3_CONF_VARS['FE']['loginSecurityLevel'] = 'normal';
	$TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
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

// Workaround for plaintext authentication
t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_euldap_sv2' /* sv key */,
	array(

		'title' => 'LDAP-Authentication',
		'description' => 'Authentication service for LDAP (FE and BE).',
		'subtype' => $subTypes,
		'available' => 1,
		'priority' => 90,
		'quality' => 50,
		'os' => '',
		'exec' => '',
		'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv2/class.tx_euldap_sv2.php',
		'className' => 'tx_euldap_sv2',
	)
);

$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['tx_euldap_import'] = array(
	'extension' => 'eu_ldap', // Selbsterklärend
	'title' => 'LLL:EXT:eu_ldap/locallang_db.xml:schedulertask.name', // Der Titel der Aufgabe
	'description' => 'LLL:EXT:eu_ldap/locallang_db.xml:schedulertask.description', // Die Beschreibung der Aufgabe
	// 'additionalFields' => 'tx_extkey_TaskName_AdditionalFieldProvider' // Zusätzliche Felder
);

?>