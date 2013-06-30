<?php
	/**
	 * Adapter fuer Singletons fuer OXID 4.7 und darunter.
	 * @category   Modules
	 * @package    \WBL_Modules
	 * @subpackage Helper
	 * @author     blange <code@wbl-konzept.de>
	 * @version    SVN: $Id$
	 */

	/**
	 * Adapter fuer Singletons fuer OXID 4.7 und darunter.
	 * @author     blange <code@wbl-konzept.de>
	 * @category   Modules
	 * @package    \WBL_Modules
	 * @subpackage Helper
	 * @version    SVN: $Id$
	 */
	class WBL_Modules_Helper_Singleton {
		/**
		 * Instance-Cache.
		 * @var array
		 */
		protected static $aInstances4WBLHelper = array();

		/**
		 * Ist die oxRegistry vorhanden?
		 * @var void|bool
		 */
		protected static $mWithRegistry4WBLHelper = null;

		/**
		 * Entfernt alle Klasseninstanzen aus dem Helper.
		 * @return void
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function clearClassCache() {
			self::$aInstances4WBLHelper = array();
		} // function

		/**
		 * Returnt die Klasse und speichert diese fuer spaeter zwischen.
		 * @param  string $sClassName Der Klassename.
		 * @param  string $sMethod    Singleton-Getter.
		 * @return object
		 * @throws \oxSystemComponentException
		 * @author blange <code@wbl-konzept.de>
		 *
		 */
		public static function get($sClassName, $sMethod = 'getInstance') {
			$mReturn = null;

			if (self::withRegistry()) {
				$mReturn = oxRegistry::get($sClassName);
			} // if
			else {
				if (!self::has($sClassName)) {
					$sCall = $sClassName;

					if ((version_compare(phpversion(), '5.3.0', '>=')) && ($sClassName[0] !== '\\')) {
						$sCall = '\\' . $sClassName;
					} // if

					self::set(
						$sClassName,
						method_exists($sCall, 'getInstance')
							? call_user_func_array($sCall . '::getInstance', array())
							: wblNew($sClassName)
					);
				} // if

				$mReturn = self::$aInstances4WBLHelper[strtolower($sClassName)];
			} // else

			return $mReturn;
		} // function

		/**
		 * Returnt die Config.
		 * @return \oxConfig
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function getConfig() {
			return self::withRegistry() ? oxRegistry::getConfig() : self::get('oxConfig');
		} // function

		/**
		 * Returnt die Sprache.
		 * @return \oxLang
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function getLang() {
			return self::withRegistry() ? oxRegistry::getLang() : self::get('oxLang');
		} // function

		/**
		 * Returnt die Session.
		 * @return \oxSession
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function getSession() {
			return self::withRegistry() ? oxRegistry::getSession() : self::get('oxSession');
		} // function

		/**
		 * Returnt die UtilsKlasse.
		 * @return \oxUtils
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function getUtils() {
			return self::withRegistry() ? oxRegistry::getUtils() : self::get('oxUtils');
		} // function

		/**
		 * Returnt die Keys der gecachten Klassen.
		 * @return array
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function getKeys() {
			return array_keys(self::$aInstances4WBLHelper);
		} // function

		/**
		 * Returnt true, falls das Objekt schon gesetzt wurde.
		 * @param  string $sClassName Der Klassenname.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function has($sClassName) {
			return isset(self::$aInstances4WBLHelper[strtolower($sClassName)]);
		} // function

		/**
		 * Loescht oder ergaenzt den Klassen-Cache.
		 * @param  string      $sClassName  Der Klassenname.
		 * @param  void|object $mInstance   Object oder null. Bei null wird der Cache resettet.
		 * @return void|object
		 * @author blange <code@wbl-konzept.de>
		 */
		public static function set($sClassName, $mInstance) {
			$mReturn    = null;
			$sClassName = strtolower($sClassName);

			if (!$mInstance) {
				unset(self::$aInstances4WBLHelper[$sClassName]);
			} // if
			else {
				$mReturn = self::$aInstances4WBLHelper[$sClassName] = $mInstance;
				unset($mInstance);
			} // else

			return $mReturn;
		} // function

		/**
		 * Returnt true, falls die Registry existiert.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		protected static function withRegistry() {
			if (self::$mWithRegistry4WBLHelper === null) {
				self::$mWithRegistry4WBLHelper = class_exists('oxRegistry', true);
			} // if

			return self::$mWithRegistry4WBLHelper;
		} // function
	} // class
