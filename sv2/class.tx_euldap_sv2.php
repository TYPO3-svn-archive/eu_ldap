<?php

class tx_euldap_sv2 extends tx_sv_authbase {
	var $prefixId = 'tx_euldap_sv2';		// Same as class name
	var $scriptRelPath = 'sv2/class.tx_euldap_sv2.php';	// Path to this script relative to the extension dir.
	var $extKey = 'eu_ldap';	// The extension key.
	var $conf;
	
	function initAuth($subType, array $loginData, array $authenticationInformation, t3lib_userAuth &$parentObject) {
		$this->pObj = $parentObject;
		$this->loginData = $loginData;
		$this->authInfo = $authenticationInformation;
		$this->password = $this->loginData['uident_text'];
		$this->username = $this->loginData['uname'];
	}
	
	function compareUident($user) {

		$OK = 0;
		
		if ($this->challengeStoredInCookie)	{
			session_start();
			if ($_SESSION['login_challenge'] !== $this->loginData['chalvalue']) {
				if ($this->writeDevLog) 	t3lib_div::devLog('PHP Session stored challenge "'.$_SESSION['login_challenge'].'" and submitted challenge "'.$this->loginData['chalvalue'].'" did not match, so authentication failed!', 't3lib_userAuth', 2);
				$this->logoff();
				return FALSE;
			}
		}

		if ((string)$this->loginData['uident_superchallenged'] === (string)md5($user['username'].':'.$user['password'].':'.$this->loginData['chalvalue']))	{
			$OK = 200;
		}
		return $OK;
	}

	/**
	 * Find a user (eg. look up the user record in database when a login is sent)
	 *
	 * @return	mixed		user array or false
	 */
	function getUser()	{
		$user = false;

		if ($this->loginData['status']=='login' && $this->username)	{
			
			$user = $this->fetchUserRecord($this->username,'',$this->authInfo['db_user']);

			if(!is_array($user)) {
					// Failed login attempt (no username found)
				$this->writelog(255,3,3,2,
					"Login-attempt from %s (%s), username '%s' not found!!",
					Array($this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $this->username));	// Logout written to log
				t3lib_div::sysLog(
					sprintf( "Login-attempt from %s (%s), username '%s' not found!", $this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $this->username ),
					'Core',
					0
				);
			} else {
				if ($this->writeDevLog) 	t3lib_div::devLog('User found: '.t3lib_div::arrayToLogString($user, array($this->db_user['userid_column'],$this->db_user['username_column'])), 'tx_sv_auth');
			}
		}
		return $user;
	}

	/**
	 * Authenticate a user (Check various conditions for the user that might invalidate its authentication, eg. password match, domain, IP, etc.)
	 *
	 * @param	array		Data of user.
	 * @return	boolean
	 */
	function authUser($user)	{
		$OK = 100;

		if ($this->username)	{
				// Checking password match for user:
			$OK = $this->compareUident($user);
			if(!$OK)     {
					// Failed login attempt (wrong password) - write that to the log!
				if ($this->writeAttemptLog) {
					$this->writelog(255,3,3,1,
						"Login-attempt from %s (%s), username '%s', password not accepted!",
						Array($this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $this->username));
					t3lib_div::sysLog(
						sprintf( "Login-attempt from %s (%s), username '%s', password not accepted!", $this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $this->username ),
						'Core',
						0
					);
				}
				if ($this->writeDevLog) 	t3lib_div::devLog('Password not accepted: '.$this->loginData['uident'], 'tx_sv_auth', 2);
			}

				// Checking the domain (lockToDomain)
			if ($OK && $user['lockToDomain'] && $user['lockToDomain']!=$this->authInfo['HTTP_HOST'])	{
					// Lock domain didn't match, so error:
				if ($this->writeAttemptLog) {
					$this->writelog(255,3,3,1,
						"Login-attempt from %s (%s), username '%s', locked domain '%s' did not match '%s'!",
						Array($this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $user[$this->db_user['username_column']], $user['lockToDomain'], $this->authInfo['HTTP_HOST']));
					t3lib_div::sysLog(
						sprintf( "Login-attempt from %s (%s), username '%s', locked domain '%s' did not match '%s'!", $this->authInfo['REMOTE_ADDR'], $this->authInfo['REMOTE_HOST'], $user[$this->db_user['username_column']], $user['lockToDomain'], $this->authInfo['HTTP_HOST'] ),
						'Core',
						0
					);
				}
				$OK = false;
			}
		}
		return $OK;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eu_ldap/sv2/class.tx_euldap_sv2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/eu_ldap/sv2/class.tx_euldap_sv2.php"]);
}

?>