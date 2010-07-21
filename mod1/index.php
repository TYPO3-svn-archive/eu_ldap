<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2003 Norman Seibert (seibert@entios.de)
	*  All rights reserved
	*
	*  This script is part of the Typo3 project. The Typo3 project is
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
	* Module 'LDAP Integration' for the 'eu_ldap' extension.
	*
	* @author    Norman Seibert <seibert@entios.de>
	*/
	 
	 
	 
	// DEFAULT initialization of a module [BEGIN]
	unset($MCONF);
	require ('conf.php');
	require_once ('class.tx_euldap_div.php');
	require ($BACK_PATH.'init.php');
	require ($BACK_PATH.'template.php');
	// include ('locallang.php');
	require_once (PATH_t3lib.'class.t3lib_scbase.php');
	$BE_USER->modAccess($MCONF, 1);
	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]
	 
	class tx_euldap_module1 extends t3lib_SCbase {
		var $pageinfo;
		var $tx_euldap_div;
		 
		/**
 * @return	[type]		...
 */
		function init() {
			global $AB, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $HTTP_GET_VARS, $HTTP_POST_VARS, $CLIENT, $TYPO3_CONF_VARS;
			
			$LANG->includeLLFile('EXT:eu_ldap/mod1/locallang.xml');
			 
			parent::init();
			 
			/*
			if (t3lib_div::GPvar('clear_all_cache'))    {
			$this->include_once[]=PATH_t3lib.'class.t3lib_tcemain.php';
			}
			*/
		}
		 
		/**
 * Adds items to the->MOD_MENU array. Used for the function menu selector.
 * 
 * @return	[type]		...
 */
		function menuConfig() {
			global $LANG;
			$this->MOD_MENU = Array (
			'function' => Array (
			'1' => $LANG->getLL('function1'),
				'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
				'4' => $LANG->getLL('function4'),
				'5' => $LANG->getLL('function5'),
				'6' => $LANG->getLL('function6'),
				'7' => $LANG->getLL('function7'),
				)
			);
			parent::menuConfig();
		}
		 
		// If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
		/**
 * Main function of the module. Write the content to $this->content
 * 
 * @return	[type]		...
 */
		function main() {
			global $AB, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $HTTP_GET_VARS, $HTTP_POST_VARS, $CLIENT, $TYPO3_CONF_VARS;
			
			$this->tx_euldap_div = t3lib_div::makeInstance('tx_euldap_div');
			 
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			 
			// if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
			if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id) || ($BE_USER->user["uid"] && !$this->id))  {
				 
				// Draw the header.
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				$this->doc->form = '<form action="" method="POST">';
				 
				// JavaScript
				$this->doc->JScode = '
					<script language="javascript">
					script_ended = 0;
					function jumpToUrl(URL)    {
					document.location = URL;
					}
					</script>
					';
				$this->doc->postCode = '
					<script language="javascript">
					script_ended = 1;
					if (top.theMenu) top.theMenu.recentuid = '.intval($this->id).';
					</script>
					';
				 
				$headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']).'<br>'.$LANG->php3Lang['labels']['path'].': '.t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], 50);
				 
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->section('', $this->doc->funcMenu($headerSection, t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function'])));
				 
				// Render content:
				$this->content .= $this->doc->divider(10);
				$this->moduleContent();	 
				 
				// ShortCut
				if ($BE_USER->mayMakeShortcut()) {
					$this->content .= $this->doc->spacer(20).$this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
				}
				 
				$this->content .= $this->doc->spacer(10);
			} else {
				// If no access or if ID == zero
				 
				$this->doc = t3lib_div::makeInstance('mediumDoc');
				$this->doc->backPath = $BACK_PATH;
				 
				$this->content .= $this->doc->startPage($LANG->getLL('title'));
				$this->content .= $this->doc->header($LANG->getLL('title'));
				$this->content .= $this->doc->spacer(5);
				$this->content .= $this->doc->spacer(10);
			}
		}
		 
		/**
 * Prints out the module HTML
 * 
 * @return	[type]		...
 */
		function printContent() {
			global $SOBE;
			 
			$this->content .= $this->doc->endPage();
			echo $this->content;
		}
		 
		/**
 * [Describe function...]
 * 
 * @return	[type]		...
 */
		function moduleContent() {
			global $LANG, $HTTP_POST_VARS;
			//      debug($GLOBALS['HTTP_GET_VARS']);
			//      debug($GLOBALS['HTTP_POST_VARS']);
			
			$this->tx_euldap_div->initChar('');
			
			$doCommand = false;
			
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'count(*) as cnt',
				'tx_euldap_server',
				'pid IN ('.$this->id.')'
			);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres);
			if ($row['cnt']) {
				if ($HTTP_POST_VARS['submit']) {
					$doCommand = true;
				} else {
					$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tx_euldap_server',
						'pid IN ('.$this->id.')'
					);
					$content = '';
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
						$params = '&edit[tx_euldap_server]['.$row['uid'].']=edit';
						$serverLink = '<a href="#" style="text-decoration:underline" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], $this->script)).'">'.$row['server'].'</a>';
						$content .= '<div><input type="checkbox" name="useServer[]" checked value="'.$row['uid'].'"> '.$serverLink.':'.$row['port'].' ('.$row['base_dn'].')</div>';
					}
					
					$this->content .= $this->doc->spacer(10);
					$this->content .= $this->doc->section($LANG->getLL('ldapServers'), $content);
					$this->content .= $this->doc->spacer(10);
					switch((string)$this->MOD_SETTINGS['function']) {
						case 2:
						case 3:
						case 5:
						case 6:
							$content = '<input type="checkbox" name="importGroups" value="1" checked /> '.$LANG->getLL('importGroups').'</p><br/>';
							$this->content .= $this->doc->section($LANG->getLL('groupHandling'), $content);
							$this->content .= $this->doc->spacer(10);
							break;
					}
					$this->content .= $this->doc->divider(10);
					$this->content .= $this->doc->spacer(10);
					$this->content .= '<p><input type="submit" name="submit" value="'.$LANG->getLL('submit').'" /></p>';
				}
			} else {
				$this->content .= $LANG->getLL('no_servers');
			}
			
			if ($doCommand && $this->id && $HTTP_POST_VARS['useServer']) {
				$importGroups = 0;
				$useServer = implode(', ', $HTTP_POST_VARS['useServer']);
				if ($HTTP_POST_VARS['importGroups'] == '1') $importGroups = 1;
				switch((string)$this->MOD_SETTINGS['function']) {
					case 1: //summary
						// Frontend-Users
						$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'count(*)',
							'fe_users',
							'NOT deleted AND pid = '.$this->id
						);
						if ($dbres) $row = $GLOBALS['TYPO3_DB']->sql_fetch_row($dbres);
						$content = '<div>'.$row[0].' '.$LANG->getLL('users').'</div>';
						$this->content .= $this->doc->section('Frontend:', $content, 0, 1);
						// Backend-Users
						$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'count(*)',
							'be_users',
							'NOT deleted'
						);
						if ($dbres) $row = $GLOBALS['TYPO3_DB']->sql_fetch_row($dbres);
						$content = '<div>'.$row[0].' '.$LANG->getLL('users').'</div>';
						$this->content .= $this->doc->section('Backend:', $content, 0, 1);
						// LDAP
						$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'tx_euldap_server',
							'pid IN ('.$this->id.')'
						);
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
							$ldapserver = $row['server'];
							$ldapres = $this->tx_euldap_div->search_ldap($row, '*');
							$content = '<div>'.$ldapres['count'].' '.$LANG->getLL('users').'</div>';
							$this->content .= $this->doc->section($ldapserver.":", $content, 0, 1);
						}
						break;
					case 2: //update FE-users
						$content = $this->index_update_users('fe', $this->id, $importGroups, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('users').":", $content, 0, 1);
						break;
					case 5: //update BE-users
						$content = $this->index_update_users('be', 0, $importGroups, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('users').":", $content, 0, 1);
						break;
					case 3: //import FE-users
						$content = $this->index_import_users('fe', $this->id, $importGroups, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('new').' '.$LANG->getLL('users').":", $content, 0, 1);
						break;
					case 6: //import BE-users
						$content = $this->index_import_users('be', 0, $importGroups, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('new').' '.$LANG->getLL('users').":", $content, 0, 1);
						break;
					case 4: //delete FE-users
						$content = $this->index_delete_users('fe_users', $this->id, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('deleted').' '.$LANG->getLL('users').":", $content, 0, 1);
						break;
					case 7: //delete BE-users
						$content = $this->index_delete_users('be_users', 0, $useServer);
						$this->content .= $this->doc->section($LANG->getLL('deleted').' '.$LANG->getLL('users').":", $content, 0, 1);
						break;
				}
			}
		}
		 
		/**
 * [Describe function...]
 * 
 * @param	[type]		$user_prefix: ...
 * @param	[type]		$pid: ...
 * @return	[type]		...
 */
		function index_update_users($user_prefix, $pid, $importGroups, $useServer) {
			global $LANG;
			
			$this->tx_euldap_div->initChar('');
			$this->tx_euldap_div->importGroups = $importGroups;
			
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, title',
				$user_prefix.'_groups',
				sprintf('deleted = 0 AND hidden = 0 %s', ($this->checkPid?' AND pid = '.$this->checkPid_value:''))
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
				$arrGroups[] = $row;
			}
			// load users
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				$user_prefix.'_users',
				'NOT deleted'
			);
			// LDAP
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_euldap_server',
				($this->id?'pid IN ('.$this->id.') AND ':'')
				.'uid IN (0, '.$useServer.') AND '
				.'authenticate_be IN ('.($user_prefix=='fe'?'0,':'').($user_prefix=='be'?'1,':'').'2)'
			);

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
				$arrServers[] = $row;
			}
			unset($dbres);
			$content = $LANG->getLL('rownumber_limit').'<table cellpadding=0 cellspacing=0 border=0>
				<tr>
				<td nowrap><b>'.$LANG->getLL('account').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td nowrap><b>'.$LANG->getLL('name').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td nowrap><b>'.$LANG->getLL('group').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td nowrap><b>'.$LANG->getLL('email').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td nowrap><b>'.$LANG->getLL('ldap').'</b></td>
				</tr>';
			$i = 0;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$arrDisplay = $this->tx_euldap_div->update_user($arrServers, $arrGroups, $row['username'], $user_prefix.'_users', $this->id);
				if ($arrDisplay && ($i < 10)) {
					$i++;
					$content .= '<tr>
						<td nowrap>'.$row['username'].'</td>
						<td></td>
						<td nowrap>'.$arrDisplay['name'].'</td>
						<td></td>
						<td nowrap>'.$arrDisplay['gname'].'</td>
						<td></td>
						<td nowrap>'.$arrDisplay['email'].'</td>
						<td></td>
						<td nowrap>'.$arrDisplay['ldapserver'].'</td>
						</tr>';
				}
			}
			$content .= '</table>';
			return $content;
		}
		 
		/**
 * [Describe function...]
 * 
 * @param	[type]		$user_prefix: ...
 * @param	[type]		$pid: ...
 * @return	[type]		...
 */
		function index_import_users($user_prefix, $pid, $import_groups, $useServer) {
			global $LANG;
			
			$this->tx_euldap_div->initChar('');
			
			srand((double)microtime() * 1000000);
			//load groups
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid, title',
				$user_prefix.'_groups',
				'deleted = 0 AND hidden = 0'
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
				$arrGroups[] = $row;
			}
			// LDAP
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_euldap_server',
				($this->id?'pid IN ('.$this->id.') AND ':'')
				.'uid IN (0, '.$useServer.') AND '
				.'authenticate_be IN ('.($user_prefix=='fe'?'0,':'').($user_prefix=='be'?'1,':'').'2)'
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
				$arrServers[] = $row;
			}
			$content = '<table cellpadding=0 cellspacing=0 border=0>
				<tr>
				<td><b>'.$LANG->getLL('account').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td><b>'.$LANG->getLL('name').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td><b>'.$LANG->getLL('group').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td><b>'.$LANG->getLL('email').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td><b>'.$LANG->getLL('ldap').'</b></td>
				</tr>';
			$i = 0;
			while ($i < count($arrServers)) {
				$content .= $this->tx_euldap_div->import_users($arrServers[$i], $arrGroups, $user_prefix.'_users');
				$i++;
			}
			$content .= '</table>';
			return $content;
		}
		 
		/**
 * [Describe function...]
 * 
 * @param	[type]		$user_table: ...
 * @param	[type]		$pid: ...
 * @return	[type]		...
 */
		function index_delete_users($user_table, $pid, $useServer) {
			global $LANG;
			
			$this->tx_euldap_div->initChar('');
			
			// load users
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				$user_table,
				'pid = '.$pid.' AND deleted = 0'
			);
				// LDAP
			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_euldap_server',
				($this->id?'pid IN ('.$this->id.') AND ':'')
				.'uid IN (0, '.$useServer.') AND '
				.'authenticate_be IN ('.($user_prefix=='fe'?'0,':'').($user_prefix=='be'?'1,':'').'2)'
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
				$arrServers[] = $row;
			}
			$content = '<table cellpadding=0 cellspacing=0 border=0>
				<tr>
				<td><b>'.$LANG->getLL('account').'</b></td>
				<td>&nbsp;&nbsp;</td>
				<td><b>'.$LANG->getLL('email').'</b></td>
				</tr>';
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$display = $this->tx_euldap_div->delete_user($arrServers, $row, $user_table);
				if ($display) {
					$content .= '<tr>
						<td nowrap>'.$row['username'].'</td>
						<td></td>
						<td nowrap>'.$row['email'].'</td>
						</tr>';
				}
			}
			$content .= '</table>';
			return $content;
		}
	}
	 
	 
	 
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eu_ldap/mod1/index.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/eu_ldap/mod1/index.php']);
	}
	 
	 
	 
	 
	// Make instance:
	$SOBE = t3lib_div::makeInstance('tx_euldap_module1');
	$SOBE->init();
	 
	// Include files?
	reset($SOBE->include_once);
	while (list(, $INC_FILE) = each($SOBE->include_once)) {
		include_once($INC_FILE);
	}
	 
	$SOBE->main();
	$SOBE->printContent();
	 
?>
