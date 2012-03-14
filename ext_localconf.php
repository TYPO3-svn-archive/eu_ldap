<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$config = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['eu_ldap']);

if ($config['enableFE'] && !$config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE';
	if ($TYPO3_CONF_VARS['FE']['loginSecurityLevel'] != 'rsa') $TYPO3_CONF_VARS['FE']['loginSecurityLevel'] = 'normal';
}

if (!$config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserBE,authUserBE';
	if ($TYPO3_CONF_VARS['BE']['loginSecurityLevel'] != 'rsa') $TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
}

if ($config['enableFE'] && $config['enableBE']) {
	$subTypes = 'getUserFE,authUserFE,getUserBE,authUserBE';
	if ($TYPO3_CONF_VARS['FE']['loginSecurityLevel'] != 'rsa') $TYPO3_CONF_VARS['FE']['loginSecurityLevel'] = 'normal';
	if ($TYPO3_CONF_VARS['BE']['loginSecurityLevel'] != 'rsa') $TYPO3_CONF_VARS['BE']['loginSecurityLevel'] = 'normal';
}

// register Service with highest priority
if ($config['enableFE'] || $config['enableBE']) {
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
}

?>