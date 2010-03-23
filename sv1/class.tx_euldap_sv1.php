<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Norman Seibert (seibert@entios.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Service 'LDAP-Authentication' for the 'cc_svauthdemo' extension.
 *
 * @author	Norman Seibert <seibert@entios.de>
 */

include_once(t3lib_extMgm::extPath('eu_ldap').'/mod1/class.tx_euldap_div.php');

class tx_euldap_sv1 extends tx_sv_authbase {
	var $prefixId = 'tx_euldap_sv1';		// Same as class name
	var $scriptRelPath = 'sv1/class.tx_euldap_sv1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'eu_ldap';	// The extension key.
	var $conf;
	
	/* Inits some variables
	 *
	 * @return	void
	 */
	function init()	{
		global $TYPO3_CONF_VARS;
			// exit if no LDAP support in PHP
		if (!extension_loaded('ldap')) {
			t3lib_div::devLog('No ldap extension in PHP', 'eu_ldap', 3);
			return false;
		}
		$this->conf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['eu_ldap']);
		
		return parent::init();
	}
	
	function getUser()	{
		$OK = false;
		$user = null;
		// $user['authenticated'] = false;
		if ($this->conf['logLevel'] > 0) t3lib_div::devLog('getUser() called', 'eu_ldap', 0);
		if(!$this->info['userSession']['uid']) {
			if ($this->conf['logLevel'] > 1) t3lib_div::devLog('no session found', 'eu_ldap', 0);
			if ($this->login['uname'])	{
				if ($this->conf['logLevel'] == 1) t3lib_div::devLog('user name: '.$this->login['uname'], 'eu_ldap', 0);
				if ($this->conf['logLevel'] == 2) t3lib_div::devLog('user name / password: '.$this->login['uname'].' / '.$this->login['uident'], 'eu_ldap', 0);
				//$user['username'] = $this->login['uname'];
				//$user['password'] = $this->login['uident'];
				$password = $this->login['uident_text'];
				
				if ($this->db_groups['table'] == 'be_groups') {
					$whereclause = 'deleted = 0 AND hidden = 0';
				} else {
					$whereclause = 'deleted = 0 AND hidden = 0 AND pid = '.$this->db_user['checkPidList'];
				}
				
				$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid, title',
					$this->db_groups['table'],
					$whereclause
				);
				
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
					$arrGroups[] = $row;
				}
				
				$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tx_euldap_server',
						($this->db_user['table'] == 'be_users'?'authenticate_be IN (1,2) ':'authenticate_be IN (0,2) '.$this->db_user['check_pid_clause']),
						'',
						'sorting'
				);
				
				if ($this->conf['logLevel'] == 2) {
					$sql = $GLOBALS['TYPO3_DB']->SELECTquery(
						'*',
						'tx_euldap_server',
						($this->db_user['table'] == 'be_users'?'authenticate_be IN (1,2) ':'authenticate_be IN (0,2) '.$this->db_user['check_pid_clause']),
						'',
						'sorting'
					);
					t3lib_div::devLog('looking for LDAP server records: '.$sql, 'eu_ldap', 0);
				}
				
				$objLdap = new tx_euldap_div;
				
				//debug($this->login['uname']);
				//debug($password);
				
				while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) && !($OK)) {
					if ($this->conf['logLevel'] == 1) t3lib_div::devLog('checking server: '.$row['server'], 'eu_ldap', 0);
					$ldapres = $objLdap->checkNTUser($row,$this->login['uname'],$password);
					if (is_array($ldapres)) {
						if ($this->conf['logLevel'] >= 1) t3lib_div::devLog('Login successful', 'eu_ldap', -1);
						if ($this->db_user['check_pid_clause']) {
							$pid = $this->db_user['checkPidList'];
						} else {
							$pid = '';
						}
						if ($row['automatic_import']) {
							if ($this->conf['logLevel'] >= 1) t3lib_div::devLog('Importing user '.$this->login['uname'], 'eu_ldap', 0);
							$objLdap->import_singleuser($arrGroups, $ldapres, $row, $pid, $this->db_user['table']);
						}
						$OK = true;
						$loginFailure = false;
						$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							$this->db_user['table'],
							"username = '".$this->login['uname']."'".$this->db_user['check_pid_clause'].$this->db_user['enable_clause']
						);
						$user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres);
						// $user['authenticated'] = true;
					} else {
						if ($this->conf['logLevel'] == 1) t3lib_div::devLog('Login failed', 'eu_ldap', 2);
					}
				}
			}
		}
		return $user;
	}

	/**
	 * authenticate a user
	 *
	 * @param	array		Data of user.
	 * @return	boolean
	 */
function authUser(&$user)	{
	global $TYPO3_CONF_VARS;
	$OK = 100;
	$this->pObj->challengeStoredInCookie = false;
	
	if ($this->login['uname'])	{
		$OK = 0;

		$OK = $user['authenticated'];

		if(!$OK)     {
				// Failed login attempt (wrong password) - write that to the log!
			if ($this->writeAttemptLog) {
				$this->writelog(255,3,3,1,
					"Login-attempt from %s (%s), username '%s', password not accepted!",
					array($this->info['REMOTE_ADDR'], $this->info['REMOTE_HOST'], $this->login['uname']));
			}
			if ($this->conf['logLevel'] == 1) t3lib_div::devLog('Password not accepted: '.$password, 'eu_ldap', 2);
		}
		
		$OK = $OK ? 200 : ($this->conf['onlyLDAP'] ? 0 : 100);
	}
	return $OK;
}

}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eu_ldap/sv1/class.tx_euldap_sv1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eu_ldap/sv1/class.tx_euldap_sv1.php"]);
}

?>