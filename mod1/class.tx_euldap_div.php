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
 * Class for searching ldap-tree, import, update and authentificate
 * users and groups.
 *
 * @author       Norman Seibert <seibert@entios.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   60: class tx_euldap_div
 *   69:     function search_ldap($server_info,$findname)
 *  108:     function additional_fields($map_additional_fields,$ldapres)
 *  130:     function use_memberof($servertype)
 *  156:     function is_in_onlygroups($onlygroup, $groupnamelist)
 *  190:     function assign_groups($server, $arrGroups, $ldapres, &$gid, &$gname, $table, $pid)
 *  341:     function insert_newgrps($table, $grps, $match, $pid)
 *  390:     function update_user($arrServers, $arrGroups, $username, $user_table, $pid)
 *  437:     function update_singleuser($server, $arrGroups, $user, $user_table, $pid)
 *  515:     function import_users($pid, $arrServer, $arrGroups, $user_table)
 *  534:     function import_singleuser($arrGroups, $user, $server, $pid, $user_table, $return=false)
 *  647:     function delete_user($arrServers, $row, $user_table, $delete=true)
 *  690:     function checkNTUser ($server_info,$username,$password)
 *
 * TOTAL FUNCTIONS: 13
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 
 ini_set('display_errors', 1);
 ini_set('error_reporting', E_ALL  & ~E_NOTICE);
 
/**
 * Class for searching ldap-tree, import, update and authentificate
 * users and groups.
 *
 * @author       Norman Seibert <seibert@entios.de>
 */
class tx_euldap_div {

	var $csObj;
	var $remoteChar;
	var $localChar;
	var $conf;
	
	var $importGroups;
	
	function tx_euldap_div() {
		global $TYPO3_CONF_VARS;
		$this->conf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['eu_ldap']);
		$this->initChar('');
		$this->importGroups = 1;
	}
	
	/**
	 * Sets the character sets.
	 * 
	 * 
	 * @return	void
	 */
	function initChar($charset)	{
		global $TYPO3_CONF_VARS;
		if ((isset($GLOBALS['TSFE'])) && (isset($GLOBALS['TSFE']->csConvObj)))	{
			$this->csObj = $GLOBALS['TSFE']->csConvObj;
		} else {
			if(!class_exists('t3lib_cs') && defined('PATH_t3lib')) {
				require_once(PATH_t3lib.'class.t3lib_cs.php');
			}
			$this->csObj = t3lib_div::makeInstance('t3lib_cs');
		}
		$this->remoteChar = $this->csObj->parse_charset($charset ? $charset : 'utf-8');
		$this->localChar = $this->csObj->parse_charset($TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1');
	}

	/**
	 * Gets object from ldap tree if the given findname can be found.
	 *
	 * @param	array		$server_info: containing all server information + filter to do the search
	 * @param	string		$findname: username to look for
	 * @return	array		all ldap entries for user or false
	 */
	function search_ldap($server_info, $findname) {
		if ($findname) {
			
			// convert character set local -> remote
			$this->remoteChar = $this->csObj->parse_charset($server_info['characterset']);
			$findname = $this->csObj->conv($findname, $this->localChar, $this->remoteChar);
			
			$server = $server_info['server'];
			$port = $server_info['port'];
			$version = $server_info['version'];
			$auth_user = $server_info['user'];
			$auth_pass = $server_info['password'];
			$base_dn = $server_info['base_dn'];
			$strfilter = $server_info['filter'];
			$servertype = $server_info['servertype'];
			$filter = str_replace('<search>', $findname, $strfilter);

			if(!extension_loaded('ldap')) die('Your PHP version seems to lack LDAP support. Please install.');

			if (!($connect = @ldap_connect($server, $port))) die('Could not connect to ldap server '.$server);
			if ($version == 3) {
				@ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
				if ($servertype == 1) ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
			}
			if (substr(strtolower($server), 0, 8) == 'ldaps://') {
				if (!function_exists( 'ldap_start_tls' )) die('Function ldap_start_tls not available.');
				@ldap_start_tls($connect);
			}
			@ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
			if (!($bind = @ldap_bind($connect, $auth_user, $auth_pass))) die('Unable to bind to server '.$server);
			
			// if (!($search = @ldap_search($connect, $base_dn, $filter))) die('Unable to search ldap server '.$server);
			// $info = ldap_get_entries($connect, $search);
			
			$info = tx_euldap_div::search($connect, $base_dn, $filter);
			
			// convert character set remote -> local
			if (is_array($info)) $info = tx_euldap_div::convertArray($info, $this->remoteChar, $this->localChar);
			
			return $info;
		}
	}
	
	function search($resource=null,$base_dn=null,$filter,$attrs=array(),$scope='sub',$sort_results=true,$deref=LDAP_DEREF_NEVER,$size_limit=0) {

            switch ($scope) {
                  case 'base':
                        $search = @ldap_read($resource,$base_dn,$filter,$attrs,0,$size_limit,0,$deref);
                        break;
                  case 'one':
                        $search = @ldap_list($resource,$base_dn,$filter,$attrs,0,$size_limit,0,$deref);
                        break;
                  case 'sub':
                  default:
                        $search = @ldap_search($resource,$base_dn,$filter,$attrs,0,$size_limit,0,$deref);
                        break;
            }
            
            if (! $search)
                  return array();

            $return = array();

            # @todo: this needs to convert everything to lowercase to work.
            if (is_array($sort_results))
                  # we sort with the more important attribute (the first one) at the end
                  for ($i = count($sort_results) - 1; $i >= 0; --$i)
                        if (($sort_results[$i] == 'dn') || in_array($sort_results[$i], $attrs))
                              ldap_sort($resource, $search, $sort_results[$i]);

            # Get the first entry identifier
            if ($entry_id = ldap_first_entry($resource, $search)) {
            	  $dn = 0;
                  # Iterate over the entries
                  while ($entry_id) {

                        # Get the distinguished name of the entry
                        //$dn = ldap_get_dn($resource,$entry_id);
                        $DN = ldap_get_dn($resource,$entry_id);

                        //$return[$dn]['dn'] = $dn;
                        $return[$dn]['dn'] = $DN;

                        # Get the attributes of the entry
                        $attrs = ldap_get_attributes($resource, $entry_id);
                        
                        # Iterate over the attributes
                              while (list($attr, $values) = each($attrs)) { 
                                    # Get the number of values for this attribute
                                    $count = $values['count'];

                                    if ($count == 1) {
                                          $return[$dn][strtolower($attr)] = $values[0];
                              		} elseif ($count > 1) {
                                          $return[$dn][strtolower($attr)] = $values;
                              		}
									
                              } # end while attr

                        $entry_id = ldap_next_entry($resource,$entry_id);
                        
                        $dn++;

                  } # End while entry_id
            }

            if (($sort_results === true) && is_array($return))
                  ksort($return);
                  
            $return['count'] = $dn;
            return $return;
      }

	/**
	 * If additional fields should be importet, this function returns the needed Strings for DB-Insert Operation
	 *
	 * @param	string		$map_additional_fields: tablefield0=ldapAttr0,tablefield1=ldapAttr1,...
	 * @param	array		$ldapres: result of ldapquery (attributes in first dimension!) ($ldapres[attribute])
	 * @param	string		$TYP: ["UPDATE"|"INSTERT"] //OBSOLETE!!
	 * @return	array		array of field => value
	 * @todo	must return array of field/value to use DBAL
	 */
	function additional_fields($map_additional_fields, $ldapres, $table) {
		if ($map_additional_fields != "") {
			$map_additional_fields = str_replace(chr(10), '', $map_additional_fields);
			$map_additional_fields = str_replace(chr(13), '', $map_additional_fields);
			$pairs = explode(',', $map_additional_fields);
			$insertArray=array(); //initialise array...
			foreach ($pairs as $value) {
				list($tablekey, $ldapkey) = explode("=", $value);
				$ldapkey = strtolower(trim($ldapkey));
				$tablekey = strtolower(trim($tablekey));
				#$ldapkey = trim($ldapkey);
				#$tablekey = trim($tablekey);
				if ($ldapres[$ldapkey]) {
					$insertArray[$tablekey] = $GLOBALS['TYPO3_DB']->quoteStr($ldapres[$ldapkey], $table);
				}
			}
		}
		return $insertArray;
	}

	/**
	 * [Function returns the needed String for the Attribut Memberof. Because it is diffrent in diffrent LDAP-Servertypes]
	 *
	 * @param	[type]		$servertype: ...
	 * @return	[type]		...
	 */
	function use_memberof($servertype) {
		switch($servertype) {
			case 0:
				$use_memberOf = 'memberOf';
				break;
			case 1:
				$use_memberOf = 'memberOf';
				break;
			case 2:
				$use_memberOf = 'groupmembership';
				break;
			case 3:
				$use_memberOf = 'posixGroup';
				break;
		}

		return $use_memberOf;
	}

	/**
	 * [Function test if User is in one of the specified groups (only_groups)]
	 *
	 * @param	[comma		seperated string]		$onlygroup: ...
	 * @param	[string]		$groupname: ...
	 * @return	[type]		...
	 */
	function is_in_onlygroups($onlygroup, $groupname) {
		$retValue = 0;
		//No Group is selected then all users allowed
		if ($onlygroup == "") {
			$retValue = 1;
		}
		$onlygrouparray = explode(",",$onlygroup);
		if (is_array($onlygrouparray)) {
			foreach ($onlygrouparray as $value) {
				$value = strtolower(trim($value));
				$regExpr = str_replace("?", ".", str_replace("*", ".*", "/^".$value."$/"));
				if (preg_match($regExpr, strtolower($groupname))) $retValue = 1;
			}
		} else {
			$value = strtolower(trim($onlygroup));
			$regExpr = str_replace("?", ".", str_replace("*", ".*", "/^".$value."$/"));
			if (preg_match($regExpr, strtolower($groupname))) $retValue = 1;
		}
		return $retValue;
	}


	/**
	 * assigns ldap and typo3 groups. LDAP groups can be imported if flag is set
	 *
	 * @param	boolean		$use_memberOf: use the memberOf attribute
	 * @param	array		$arrGroups: array with typo3-groups (fe/be)
	 * @param	array		$ldapres: ldap attributes and values
	 * @param	string		$ldapbuildgroup: how to match the typo groups
	 * @param	int		eger	$gid: groupids, output parameter
	 * @param	string		$gname:groupnames, output parameter
	 * @param	string		$match: import only groups begining with this string
	 * @param	string		$addnewgroups: if set groups are imported/created
	 * @param	string		$table: fe_users or be_users
	 * @param	integer		$pageID for the group
	 * @return	void		- uses refvars gid and gname
	 */
	function assign_groups($server, $arrGroups, $ldapres, &$gid, &$gname, $table, $pid) {
		$ldapbuildgroup = $server['build_group'];
		$use_memberOf = $server['memberof'];
		$match = $server['matchgrps'];
		$servertype = $server['servertype'];
		$memberOf = tx_euldap_div::use_memberOf($servertype);
		$addnewgroups = $server['doitfe'];
		$fe_groups = $server['fe_group'];
		$be_groups = $server['be_group'];
		
		if (''.$use_memberOf != '0') {

			$k = 0;
			$gid = '';
			$department = '';
			$gname = '';

			if ($memberOf == 'posixGroup') {
				$uid = $ldapres['uid'];
				$server['filter'] = "(&(objectclass=posixGroup)(|(memberUid=<search>)(gidNumber=" . $ldapres['gidnumber'] . ")))";
				$group_info = tx_euldap_div::search_ldap($server, $uid);
				while ($k < $group_info['count']) {
					if (is_array($group_info[$k]['cn'])) {
						$department = $group_info[$k]['cn'][0];
					} else {
						$department = $group_info[$k]['cn'];
					}
					if ($department) {
						$j = 0;
						$group_found = false;
						while (($j < sizeof($arrGroups)) && !($group_found)) {
							if (strtolower($arrGroups[$j]['title']) == strtolower($department)) {
								$group_found = true;
								if (tx_euldap_div::is_in_onlygroups($server['matchgrps'], $arrGroups[$j]['title'])) {
									$gid .= ','.$arrGroups[$j]['uid'];
									$gname .= ','.$arrGroups[$j]['title'];
								}
							}
							$j++;
						}
						if (!$group_found && tx_euldap_div::is_in_onlygroups($server['matchgrps'], $department)) $newgroups[] = $department;
					}
					$k++;
				}
			} else {
				if (count($ldapres[$memberOf]) == 0) $memberOf = strtolower($memberOf);
				while ($k < count($ldapres[$memberOf])) {
					$department = $ldapres[$memberOf][$k];
					$equal = strpos($department, 'cn=');
					$comma = strpos($department, ',', $equal);
					$department = substr($department, $equal+3, $comma-$equal-3);
					if ($department) {
						$j = 0;
						$group_found = false;
						while (($j < sizeof($arrGroups)) && !($group_found)) {
							if (strtolower($arrGroups[$j]['title']) == strtolower($department)) {
								$group_found = true;
								if (tx_euldap_div::is_in_onlygroups($server['matchgrps'], $arrGroups[$j]['title'])) {
									$gid .= ','.$arrGroups[$j]['uid'];
									$gname .= ','.$arrGroups[$j]['title'];
								}
							}
							$j++;
						}
						if (!$group_found && tx_euldap_div::is_in_onlygroups($server['matchgrps'], $department)) $newgroups[] = $department;
					}
					$k++;
				}
			}
		} else {
			$department = $ldapbuildgroup;
			while (preg_match('/<([^>]*)>/', $department, $arrMatches)) {
				if (strtolower($arrMatches[1]) == 'ou') {
					$gid = '';
					$gname = '';
					$dn = $ldapres['dn'];
					$arrDN = explode(",", $dn);
					for ($jj=0; $jj < count($arrDN); $jj++) {
						$arrKeys = explode("=", $arrDN[$jj]);
						if (strtolower($arrKeys[1]) == strtolower($arrMatches[1])) {
							$found = $arrKeys[1];
							$tmpdepartment = str_replace('<'.$arrMatches[1].'>', $found, $department);
							$j = 0;
							$group_found = false;
							while (($j < count($arrGroups)) && !($group_found)) {
								if (strtolower($arrGroups[$j]['title']) == strtolower($tmpdepartment)) {
									$group_found = true;
									$gid.= ','.$arrGroups[$j]['uid'];
									$gname.= ','.$arrGroups[$j]['title'];
								}
								$j++;
							}
							(!$group_found)?$newgroups[]=$department:'';

						}
					}
					$department = $tmpdepartment;
				} elseif (is_array($ldapres[$arrMatches[1]])) {
					$gid = '';
					$gname = '';
					$jjj = 0;
					while(($jjj < count($ldapres[$arrMatches[1]])-1)){
						$department = $ldapbuildgroup;
						$found = $ldapres[$arrMatches[1]][$jjj];
						$department = str_replace('<'.$arrMatches[1].'>', $found, $department);
						$j = 0;
						$group_found = false;
						while (($j < sizeof($arrGroups)) && !($group_found)) {
							if (strtolower($arrGroups[$j]['title']) == strtolower($department)) {
								$group_found = true;
								if (tx_euldap_div::is_in_onlygroups($server['matchgrps'], $arrGroups[$j]['title'])) {
									$gid.= ','.$arrGroups[$j]['uid'];
									$gname.= ','.$arrGroups[$j]['title'];
								}
							}
							$j++;
						}
						if (!$group_found && tx_euldap_div::is_in_onlygroups($server['matchgrps'], $department)) $newgroups[] = $department;
						$jjj++;
					}
				} else {
					$gid = '';
					$gname = '';
					$department = $ldapbuildgroup;
					$found = $ldapres[$arrMatches[1]];
					$department = str_replace('<'.$arrMatches[1].'>', $found, $department);
					$j = 0;
					$group_found = false;
					while (($j < sizeof($arrGroups)) && !($group_found)) {
						if (strtolower($arrGroups[$j]['title']) == strtolower($department)) {
							$group_found = true;
							if (tx_euldap_div::is_in_onlygroups($server['matchgrps'], $arrGroups[$j]['title'])) {
								$gid.= ','.$arrGroups[$j]['uid'];
									$gname.= ','.$arrGroups[$j]['title'];
							}
						}
						$j++;
					}
					if (!$group_found && tx_euldap_div::is_in_onlygroups($server['matchgrps'], $department)) $newgroups[] = $department;
				}
			}
		}
		if ((is_array($newgroups)) && ($addnewgroups)) {
			reset($newgroups);
			$newgrouparray = tx_euldap_div::insert_newgrps($table, $newgroups, $match, $pid);
			if (is_array($newgrouparray)) {
				reset($newgrouparray);
				foreach ($newgrouparray as $newgrpid => $newgrpname) {
					if ($newgrpname != '') {
						$gid.=','.$newgrpid;
						$gname.=','.$newgrpname;
					}
				}
			}
		}
		if (($table == 'fe_users') && $fe_groups) $gid .= ','.$fe_groups;
		if (($table == 'be_users') && $be_groups) $gid .= ','.$be_groups;
		// cuts of leading ','
		if ($gid) $gid = substr($gid, 1);
		if ($gname) $gname = substr($gname, 1);
	}

	/**
	 * Insert new groups into the table (fe or be)
	 *
	 * @param	string		$table: be or fe groups table
	 * @param	array		$grps: groupnames of unmatched groupes
	 * @param	string		$match: matchstring; must match the beginning of string
	 * @param	integer		$pid: pageID for group
	 * @return	array		key:groupId of new group, value=groupname
	 */
	function insert_newgrps($table, $grps, $match, $pid) {
		if ($table == 'be_users') {
			$pid = 0;
			$table = 'be_groups';
		} else {
			$table = 'fe_groups';
		}
		foreach ($grps as $grp) {
			$qry = Array(
				'pid' => $pid,
				'tstamp' => time(),
				'title' => $grp,
				'description' => 'Inserted by eu_ldap '.time(),
				'eu_ldap' => '1'
			);
			//match condition at beginning of group
			$bolCreate = true;
			if ($match) $bolCreate = false;
			$onlygrouparray = explode(",", $match);
			foreach ($onlygrouparray as $value) {
				$value = strtolower(trim($value));
				$regExpr = str_replace("?", ".",
					str_replace("*", ".*", "/^".$value."$/"));
				if (preg_match($regExpr, strtolower($grp))) $bolCreate = true;
	       	}
			if ($bolCreate) {
				$rslt = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid, title',
					$table,
					"title = '".$grp."' AND hidden = 0 AND deleted = 0"
					);
				if ($rslt) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rslt);
				
				if(!$row) {
					$dbres = $GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $qry);
					$rslt = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid, title',
						$table,
						"title='".$grp."'"
						);
					if ($rslt) $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($rslt);
				}
				
				$return[$row['uid']] = $row['title'];
			}
		}
		return $return;
	}

	/**
	 * Updates typo3 users with data from ldap user object
	 *
	 * @param	array		$arrServers: information about ldap servers to query
	 * @param	array		$arrGroups: existing typo3 groups (fe/be)
	 * @param	string		$username: username of user to update
	 * @param	string		$user_table: fe_users or be_users
	 * @param	integer		$pid: pageID; necessary for automatic creation of groups
	 * @return	string		with information about updated user or FALSE if user does not exist in ldap
	 */
	function update_user($arrServers, $arrGroups, $username, $user_table, $pid=0) {
		$i = 0;
		$user_found = false;
		
		while (($i < count($arrServers)) && !($user_found)) {
			$ldapres = tx_euldap_div::search_ldap($arrServers[$i], $username);
			
			# print_r($ldapres);
			
			if ($ldapres['count'] == 1) {
				if ($pid == 0) $pid = $arrServers[$i]['feuser_pid'];
				// use update_single_user from here..
				$arrDisplay = tx_euldap_div::update_singleuser($arrServers[$i], $arrGroups, $ldapres[0], $user_table, $pid);
				$user_found = true;
				return $arrDisplay;
			}
			$i++;
		}
		if (!$user_found) return false;
	}
	
	/**
	 * updates single user (see above)
	 * does not return anything at all
	 *
	 * @param	array		$server: row with ldap-server settings
	 * @param	array		$arrGroups: array of groups (fe/be)
	 * @param	array		$user: user to be updated (ldap attribute array)
	 * @param	string		$user_table: fe_users or be_users
	 * @param	integer		$pid: pageID; necessary for automatic creation of groups
	 * @return	void		nothing at all..
	 */
	function update_singleuser($server, $arrGroups, $user, $user_table, $pid) {
		$ldapserver = $server['server'];
		$ldapname = $server['name'];
		$ldapusername = $server['username'];
		$ldapmail = $server['mail'];
		$ldapphone = $server['phone'];
		$ldapfax = $server['fax'];
		$ldapaddress = $server['address'];
		$ldapzip = $server['zip'];
		$ldapcity = $server['city'];
		$ldapcountry = $server['country'];
		$ldapwww = $server['www'];
		if ($this->importGroups) {
			$ldapbuildgroup = $server['build_group'];
			$use_memberOf = $server['memberof'];
		}
		$map_additional_fields = $server['map_additional_fields'];
		if ($use_memberOf) $use_memberOf = tx_euldap_div::use_memberof($server['servertype']);
		switch($server['servertype']) {
			case 0:
			case 1:
				if ($user['sAMAccountName']) {
					$username = $user['sAMAccountName'];
				} else {
					$username = $user['samaccountname'];
				}
				break;
			case 2:
			case 3:
				$username = $user[$ldapusername];
				break;
		}

		$name = $user[$ldapname];
		$email = $user[$ldapmail];
		
		if ($ldapbuildgroup || $use_memberOf) tx_euldap_div::assign_groups($server, $arrGroups, $user, $gid, $gname, $user_table, $pid);
		// preserve groups not imported by eu_ldap
		$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery ('uid', ($user_table=='fe_users'?'fe_groups':'be_groups'), 'eu_ldap = 0 AND uid IN (SELECT usergroup FROM '.$user_table." WHERE lower(username) = '".strtolower($GLOBALS['TYPO3_DB']->quoteStr($username, $user_table))."')");
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres)) {
			$gid .= ','.$row['uid'];
		}
		$gid = implode(',', array_unique(explode(',', $gid)));
		if ($user_table == 'fe_users') {
			$map_additional_fields =
				'address='.$GLOBALS['TYPO3_DB']->quoteStr($ldapaddress, $user_table)
				.',zip='.$GLOBALS['TYPO3_DB']->quoteStr($ldapzip, $user_table)
				.',city='.$GLOBALS['TYPO3_DB']->quoteStr($ldapcity, $user_table)
				.',country='.$GLOBALS['TYPO3_DB']->quoteStr($ldapcountry, $user_table)
				.',address='.$GLOBALS['TYPO3_DB']->quoteStr($ldapaddress, $user_table)
				.',telephone='.$GLOBALS['TYPO3_DB']->quoteStr($ldapphone, $user_table)
				.',fax='.$GLOBALS['TYPO3_DB']->quoteStr($ldapfax, $user_table)
				.',www='.$GLOBALS['TYPO3_DB']->quoteStr($ldapwww, $user_table)
				.($map_additional_fields?','.$map_additional_fields:'');
			$updateArray = array('tstamp' => time(),
				'name' => $GLOBALS['TYPO3_DB']->quoteStr($name, $user_table),
				'email' => $GLOBALS['TYPO3_DB']->quoteStr($email, $user_table)
			);
			if ($ldapbuildgroup || $use_memberOf) $updateArray['usergroup'] = $gid;
		} else {
			$updateArray = array('tstamp' => time(),
				'email' => $GLOBALS['TYPO3_DB']->quoteStr($email, $user_table),
				'realname' => $GLOBALS['TYPO3_DB']->quoteStr($name, $user_table)
			);
			if ($ldapbuildgroup || $use_memberOf) $updateArray['usergroup'] = $gid;
		}
		$map_additional_fields_up = tx_euldap_div::additional_fields($map_additional_fields, $user, $user_table);
		if (is_array($map_additional_fields_up)) $updateArray = t3lib_div::array_merge($updateArray, $map_additional_fields_up);
		
		/*
		$sql = $GLOBALS['TYPO3_DB']->UPDATEquery($user_table,"lower(username) = '".strtolower($GLOBALS['TYPO3_DB']->quoteStr($username, $user_table))."' AND pid=".$pid,$updateArray);
		debug($sql);
		*/
		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery($user_table,"lower(username) = '".strtolower($GLOBALS['TYPO3_DB']->quoteStr($username, $user_table))."' AND pid=".$pid,$updateArray);
		
		$arrDisplay['name'] = $name;
		$arrDisplay['gname'] = $gname;
		$arrDisplay['email'] = $email;
		$arrDisplay['ldapserver'] = $ldapserver;
		return $arrDisplay;
	}

	/**
	 * imports users from ldap-tree
	 *
	 * @param	array		$arrServer: array with server information
	 * @param	array		$arrGroups: array of typo3 groups
	 * @param	string		$user_table: fe_users or be_users
	 * @return	string		html content with results
	 */
	function import_users($arrServer, $arrGroups, $user_table) {
		$ldapres = tx_euldap_div::search_ldap($arrServer, '*');
		for ($l=0; $l<$ldapres['count']; $l++) {
			$content .= tx_euldap_div::import_singleuser($arrGroups, $ldapres[$l], $arrServer, $user_table, 1);
		}
		return $content;
	}

	/**
	 * imports a single user into table. Needed for automtic import after successfull login
	 *
	 * @param	array		$arrGroups: typo groups
	 * @param	array		$user: uset to be inserted
	 * @param	array		$server: server information (ldap)
	 * @param	integer		$pid: pid of user storage page
	 * @param	string		$user_table: fe_users or be_users
	 * @param	boolean		$return: should html-output be generated?
	 * @return	string		boolean if $return = false or html-table
	 */
	function import_singleuser($arrGroups, $user, $server, $user_table, $return=false) {
		$OK = false;
		$ldapserver = $server['server'];
		$ldapname = $server['name'];
		$ldapusername = $server['username'];
		$ldapmail = $server['mail'];
		$ldapphone = $server['phone'];
		$ldapfax = $server['fax'];
		$only_emailusers = $server['only_emailusers'];
		$ldapaddress = $server['address'];
		$ldapzip = $server['zip'];
		$ldapcity = $server['city'];
		$ldapcountry = $server['country'];
		$ldapwww = $server['www'];
		if ($this->importGroups) {
			$ldapbuildgroup = $server['build_group'];
			$use_memberOf = $server['memberof'];
		}
		$map_additional_fields = $server['map_additional_fields'];
		if ($use_memberOf) $use_memberOf = tx_euldap_div::use_memberof($server['servertype']);
		
		$pid = 0;
		if ($user_table == 'fe_users') $pid = ($server['feuser_pid']?$server['feuser_pid']:$server['pid']);
		
		
		switch($server['servertype']) {
			case 0:
			case 1:
				if ($user['sAMAccountName']) {
					$username = $user['sAMAccountName'];
				} else {
					$username = $user['samaccountname'];
				}
				break;
			case 2:
			case 3:
				$username = $user[$ldapusername];
				break;
		}
		$query = (($pid)?'pid ='.$pid.' AND ':'')."NOT deleted AND lower(username) = '".$GLOBALS['TYPO3_DB']->quoteStr(strtolower($username), $user_table)."'";
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('email', $user_table, $query);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		
		if (!is_array($row) && ($username != '')){
			$name = $user[$ldapname];
			$email = $user[$ldapmail];
			$telephone = $user[$ldapphone];
			
			if (($email) || !($only_emailusers)) {
				if ($ldapbuildgroup || $use_memberOf) tx_euldap_div::assign_groups($server, $arrGroups, $user, $gid, $gname, $user_table, $pid);
				$show = false;
				if ($server['matchgrps']) {
					if ($gname) $show = true;
				} else {
					$show = true;
				}
				
				if ($show) {
					$password = '';
					$content.= '<tr>
							<td>'.$username.'</td>
							<td>&nbsp;&nbsp;</td>
							<td>'.$name.'</td>
							<td>&nbsp;&nbsp;</td>
							<td>'.$gname.'</td>
							<td>&nbsp;&nbsp;</td>
							<td>'.$email.'</td>
							<td>&nbsp;&nbsp;</td>
							<td>'.$ldapserver.'</td>
						</tr>';
					srand ((double)microtime()*1000000);
					for ($l=1;$l<11;$l++) {
						$rand_num = round(rand(1, 26) + 97, 0);
						$password.= chr($rand_num);
					}
					$insValues=array('crdate' => time(),
						'tstamp' => time(),
						'pid' => $pid,
						'username' => str_replace("'", "''", $username),
						'email' => $email,
						'password' => $password,
						'tx_euldap_dn' => $user['dn']
					);
					if ($ldapbuildgroup || $use_memberOf) $insValues['usergroup'] = $gid;

					if ($user_table == 'fe_users') {
						$insValues['address'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapaddress], $user_table);
						$insValues['zip'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapzip], $user_table);
						$insValues['city'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapcity], $user_table);
						$insValues['country'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapcountry], $user_table);
						$insValues['www'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapwww], $user_table);
						$insValues['telephone'] = $GLOBALS['TYPO3_DB']->quoteStr($telephone, $user_table);
						$insValues['fax'] = $GLOBALS['TYPO3_DB']->quoteStr($user[$ldapfax], $user_table);
						$insValues['name'] = $GLOBALS['TYPO3_DB']->quoteStr($name);
					} else {
						$insValues['options'] = '3';
						$insValues['realname'] = $GLOBALS['TYPO3_DB']->quoteStr($name, $user_table);
						$insValues['fileoper_perms'] = '1';
					}
					$mapArray = tx_euldap_div::additional_fields($map_additional_fields, $user, $user_table);
					if (is_array($mapArray)) $insValues = t3lib_div::array_merge($insValues, $mapArray);
					
					$GLOBALS['TYPO3_DB']->exec_INSERTquery($user_table,$insValues);
				}
			}
		} elseif ($username !='') {
			tx_euldap_div::update_singleuser($server, $arrGroups, $user, $user_table, $pid);
		}
		
		return ($return)?$content:$OK;
	}

	/**
	 * deletes typo user if not found in ldap
	 *
	 * @param	array		$arrServers: all ldap-server information (1-n)
	 * @param	array		$row: all user information
	 * @param	string		$user_table: fe_users or be_users
	 * @param	[type]		$delete: ...
	 * @return	boolean		true if successfully deleted
	 */
	function delete_user($arrServers, $row, $user_table, $delete=true) {
		$i = 0;
		$user_found = 0;
		while ($i < sizeof($arrServers) && !$user_found) {
	       $ldapres = tx_euldap_div::search_ldap($arrServers[$i], $row['username']);
			// HJM 2004-01-16: von == auf >= ge�ndert, da es im NML-Tree mehrere Userobjecte gibt, die nicht anhand von
			//			Suchkriterien unterschieden werden k�nnen
			$is_onlygroup = 0;
			if ($ldapres['count'] >= 1) {
				if ($user_table == 'fe_users') {
					if ($row['pid'] == $arrServers[$i]['feuser_pid']) $user_found = 1;
				} else {
					 $user_found = 1;
				}
			}
			$i++;
		}
		if (!$user_found) {
			if ($delete) {
				$dbres = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					$user_table,
					'uid = '.$row['uid'].' AND deleted = 0',
					Array(
						'deleted' => '1'
					)
				);
			} else {
				$dbres = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
					$user_table,
					'uid = '.$row['uid'].' AND deleted = 0',
					Array(
						'disable' => '1'
					)
				);
			}
			return true;
		} else {
			return false;
		}
	}

		/**
 * checks username and password in ldap
 *
 * @param	array		$server_info: all ldap-server configuration needed (see table eu_ldapserver)
 * @param	string		$username: username to be checked in ldap
 * @param	string		$password: password to be checked
 * @return	array		ldap-user attributes, if found and authentificated
 */
	function checkNTUser ($server_info, $username, $password) {
	
		// convert character set local -> remote
		$this->remoteChar = $this->csObj->parse_charset($server_info['characterset']);
		$username = $this->csObj->conv($username, $this->localChar, $this->remoteChar);
		$password = $this->csObj->conv($password, $this->localChar, $this->remoteChar);
		
		$server = $server_info['server'];
		$ldapport = $server_info['port'];
		$domain = $server_info['domain'];
		$base_dn = $server_info['base_dn'];
		$cuser = $server_info['user'];
		$cpass = $server_info['password'];
		$servertype = $server_info['servertype'];
		$version = $server_info['version'];
		$strfilter = $server_info['filter'];
		$filter = str_replace('<search>', $username, $strfilter);

		if(!extension_loaded('ldap')) die('Your PHP version seems to lack LDAP support. Please install.');

		$ds = @ldap_connect($server, $ldapport);
		if ($this->conf['logLevel'] == 2) t3lib_div::devLog('try to connect: '.$server.':'.$ldapport, 'eu_ldap', 0);
		if ($ds) {
			if ($this->conf['logLevel'] == 2) t3lib_div::devLog('connect successful: '.$server.':'.$ldapport, 'eu_ldap', -1);
			if ($version == 3) ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			if ($this->conf['logLevel'] == 2) t3lib_div::devLog('server type: '.$servertype, 'eu_ldap', 0);
			if ($servertype > 1) {
				if ($this->conf['logLevel'] == 2) t3lib_div::devLog('try to bind: '.$cuser.' / '.$cpass, 'eu_ldap', 0);
				$r = @ldap_bind($ds, $cuser, $cpass);
				if ($r) {
					if ($this->conf['logLevel'] == 2) t3lib_div::devLog('bind successful', 'eu_ldap', -1);
					$dn = @ldap_search($ds, $base_dn, $filter);
					$dn = @ldap_get_entries($ds, $dn);
					$username = $dn[0]['dn'];
				} else {
					if ($this->conf['logLevel'] == 2) t3lib_div::devLog('bind failed', 'eu_ldap', 2);
					$username = null;
				}
			} elseif ($domain) {
				if ($servertype == 0) {
					$username = $domain."\\".$username;
				} else {
					$username = $username."@".$domain;
				}
			}
			
			if ($username && $password) {
				@ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
				if ($this->conf['logLevel'] == 2) t3lib_div::devLog('try to bind: '.$username.' / '.$password, 'eu_ldap', 0);
				$r = @ldap_bind($ds,$username,$password);
				if ($r) {
					if ($this->conf['logLevel'] == 2) t3lib_div::devLog('bind successful', 'eu_ldap', -1);
					
					$arrUser = tx_euldap_div::search($ds, $base_dn, $filter, array(), 'sub', true, LDAP_DEREF_NEVER, 1);
					$user = $arrUser[0];
					
					// convert character set remote -> local
					if (is_array($user)) $user = tx_euldap_div::convertArray($user, $this->remoteChar, $this->localChar);
					
					return $user;
				} else {
					if ($this->conf['logLevel'] == 2) t3lib_div::devLog('bind failed', 'eu_ldap', 2);
				}
			}
		}
	}
	
	function convertArray($arr, $char1, $char2) {
		while (list($k, $val) = each($arr)) {
			if (is_array($val)) {
				$arr[$k] = tx_euldap_div::convertArray($val, $char1, $char2);
			} else {
				$arr[$k] = $this->csObj->conv($val, $char1, $char2);
			}
		}
		return $arr;
	}
}
?>