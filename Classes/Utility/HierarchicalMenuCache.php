<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Michael Knoll <knoll@punkt.de>, punkt.de GmbH
*
*
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
 * Class implements an abstract class for x-classes modifying the rendering of hierarchical menus (hmenu).
 *
 * DOCUMENTATION
 * =============
 *
 * 1. Create an x-class by extending this class by a class called 'ux_tslib_content_HierarchicalMenu'. Inside this
 *    x-class, make sure to replace $CACHE_KEY with the name of the cache used in the caching framework and
 *    $CACHE_NAMESPACE with the namespace of your cache in the cache tag (e.g. necessary for memcached backend).
 *
 *
 * 2. Register this x-class in your ext_localconf:
 *
 *    $xclass = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('<YOUR_EXTENSION>') . '<PATH_TO_XCLASS>/ux_tslib_content_HierarchicalMenu.php';
 *    $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['tslib/content/class.tslib_content_hierarchicalmenu.php'] = $xclass;
 *
 *
 * 3. Add a caching framework configuration for the hierarchical menu caching inside ext_localconf.php
 *
 *    require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('<YOUR_EXTENSION>') . '<PATH_TO_XCLASS>/ux_tslib_content_HierarchicalMenu.php';
 *
 *    if (!is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ux_tslib_content_HierarchicalMenu::CACHE_NAMESPACE()])) {
 *         $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ux_tslib_content_HierarchicalMenu::CACHE_NAMESPACE()] = array(
 *     		'backend' => 't3lib_cache_backend_DbBackend',
 *     		'frontend' => 't3lib_cache_frontend_VariableFrontend',
 *       		'options' => array(
 *     			'cacheTable' => <TABLE_NAME_TO_BE_USED_AS_CACHE_TABLE>
 *     			'tagsTable' => <TABLE_NAME_TO_BE_USED_AS_CACHE_TAGS_TABLE>
 *     		)
 *     	);
 *    }
 *
 *   if ($confArr['cachingMode']=='normal') {
 *       $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearAllCache_additionalTables'][<TABLE_NAME_TO_BE_USED_AS_CACHE_TABLE>] = <TABLE_NAME_TO_BE_USED_AS_CACHE_TABLE>;
 *   }
 *
 *
 * 4. Add the following SQL snippet to your ext_tables.sql file
 *
 *   CREATE TABLE cf_ptonebrukerbase_hierarchicalmenu_cache (
 *        id integer(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 *        identifier VARCHAR(250) DEFAULT '' NOT NULL,
 *        crdate integer(11) UNSIGNED DEFAULT '0' NOT NULL,
 *        content mediumblob,
 *        lifetime integer(11) UNSIGNED DEFAULT '0' NOT NULL,
 *        PRIMARY KEY (id),
 *        KEY cache_id (identifier)
 *   ) ENGINE=InnoDB;
 *
 *
 *   CREATE TABLE cf_ptonebrukerbase_hierarchicalmenu_cache_tags (
 *        id integer(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 *        identifier VARCHAR(250) DEFAULT '' NOT NULL,
 *        tag VARCHAR(250) DEFAULT '' NOT NULL,
 *        PRIMARY KEY (id),
 *        KEY cache_id (identifier),
 *        KEY cache_tag (tag)
 *   ) ENGINE=InnoDB;
 *
 *
 * 5. Make sure to clear cache!!!
 *
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package Utility
 */
abstract class Tx_PtExtbase_Utility_HierarchicalMenuCache extends \TYPO3\CMS\Frontend\ContentObject\HierarchicalMenuContentObject {


	/**
	 * Cache entry identifier
	 *
	 * Set this in your extending class!
	 */
	protected static  $CACHE_KEY = NULL;



	/**
	 * Cache identifier for caching framework
	 *
	 * Set this in your extending class!
	 */
	protected static $CACHE_NAMESPACE = NULL;



	/**
	 * Set default lifetime of cache entries to 2h
	 *
	 * @var integer
	 */
	protected static $lifetime = 7200;



	/**
	 * @var t3lib_cache_backend_Backend
	 */
	private $cache;



	/**
	 * @var  t3lib_cache_Manager
	 */
	private $cacheManager;



	/**
	 * @var t3lib_cache_Factory
	 */
	private $cacheFactory;



	/**
	 * Returns the name of the cache to be used for hierarchical menu caching
	 *
	 * @throws Exception if $CACHE_KEY is not set in extending class
	 * @return string
	 */
	public static function CACHE_KEY() {
		if (static::$CACHE_KEY !== NULL) {
			return static::$CACHE_KEY;
		} else {
			throw new Exception('You have to set $CACHE_KEY as a property of your extending class!', 1370593165);
		}
	}



	/**
	 * Returns the namespace of the cache entries used for hierarchical menu caching
	 *
	 * @return string
	 * @throws Exception if $CACHE_NAMESPACE is not set in extending class
	 */
	public static function CACHE_NAMESPACE() {
		if (static::$CACHE_NAMESPACE !== NULL) {
			return static::$CACHE_NAMESPACE;
		} else {
			throw new Exception('You have to set $CACHE_NAMESPACE as a static property of your extending class!', 1370593166);
		}
	}



	/**
	 * Rendering the cObject, HMENU
	 *
	 * @param	array		Array of TypoScript properties
	 * @return	string		Output
	 */
	public function render($conf = array()) {
		$this->initializeCache();

		$cacheLifetime = static::$lifetime;

		// Check for different cache lifetime
		if (!empty($conf['cachingTtl']) && intval($conf['cachingTtl']) > 0) {
			$cacheLifetime = intval($conf['cachingTtl']);
		}

		// Check whether cached menu exists
		$cacheIdentifierHash = $this->createMenuCacheHashEntry($conf);
		$renderedMenu = $this->cache->get($cacheIdentifierHash);

		// Render and cache menu if no cache entry exists
		if ($renderedMenu === FALSE) {
			$renderedMenu = parent::render($conf);
			$this->cache->set($cacheIdentifierHash, $renderedMenu, array(), $cacheLifetime);
		}

		return $renderedMenu;

	}



	/**
	 * Creates a cache tag for the menu cache entries.
	 *
	 * @param array $conf TS configuration for hmenu
	 * @return string
	 */
	protected function createMenuCacheHashEntry($conf) {
		$cacheTagIngredients = array();

		// Get FE groups of currently logged in user for hash tag
		if (!empty($GLOBALS['TSFE']->fe_user->user['usergroup'])) {
			$feGroups = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']);
			sort($feGroups);
			$cacheTagIngredients[] = $feGroups;
		}

		$cacheTagIngredients[] = $_SERVER['SERVER_NAME'];
		$cacheTagIngredients[] = $conf;

		$cacheTagIngredients = $this->modifyCacheTagIngredients($cacheTagIngredients, $conf);

		// Merge hash tag
		$hashTag = static::$CACHE_KEY . md5(serialize($cacheTagIngredients)) . '_' . $GLOBALS['TSFE']->sys_language_uid;

		return $hashTag;

	}



	/**
	 * Template method for modifying the ingredients of the hash tag
	 * generated for the cache entry.
	 *
	 * @param array $cacheTagIngredients Array of default ingredients
	 * @param array $conf Configuration of currently rendered menu
	 * @return array The modified array
	 */
	protected function modifyCacheTagIngredients(array $cacheTagIngredients, $conf) {
		return $cacheTagIngredients;
	}



	private function initializeCache() {
		$this->cacheManager = $GLOBALS['typo3CacheManager'];
		$this->cacheFactory = $GLOBALS['typo3CacheFactory'];
		try {
			$this->cache = $this->cacheManager->getCache(static::$CACHE_NAMESPACE);
		} catch (\TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e) {
			$this->cache = $this->cacheFactory->create(
				static::$CACHE_NAMESPACE,
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][static::$CACHE_NAMESPACE]['frontend'],
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][static::$CACHE_NAMESPACE]['backend'],
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][static::$CACHE_NAMESPACE]['options']
			);
		}
	}

}
