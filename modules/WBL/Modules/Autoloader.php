<?php
	/**
	 * ./modules/WBL/Modules/Autoloader.php
	 * @author blange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @version $id$
	 */

	if (!function_exists('wblNew')) {
		/**
		 * Adapter um das strtolower im OXID-Kern auszugleichen.
		 * @author blange <code@wbl-konzept.de>
		 * @param string $sFullClassName Der vollqualifizierte Klassenname.
		 * @return object
		 * @throws oxSystemComponentException Falls die Klasse nicht gefunden wurde.
		 */
		function wblNew($sFullClassName) {
			if (!class_exists($sFullClassName, false)) {
				spl_autoload_call($sFullClassName);
			} // if

			return call_user_func_array('oxNew', func_get_args());
		} // function
	} // if

	/**
	 * Autoloader for module classes.
	 * @author blange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @version $id$
	 */
	class WBL_Modules_Autoloader {
		/**
		 * Die Standard-Dateiendung.
		 * @var string
		 */
		const DEFAULT_FILE_ENDING = '.php';

		/**
		 * Mit diesem Dateicache-Key speichert OXID im Autoloader die Pfade.
		 * @var string
		 */
		const FILE_CACHE_KEY = 'aEosNeoAutoloaderFilePaths';

		/**
		 * Lazy Loading fuer den Pfad-Cache von OXID.
		 * @var void|array
		 */
		protected $mFilePaths = null;

		/**
		 * Helping array to override some core classes.
		 * @var array
		 */
		protected $aCoreOverrides = array();

		/**
		 * Die moeglichen Dateiendungen einer Klassen/Interface-Datei (FIFO).
		 * @var array
		 */
		protected $aFileEndings = array();

		/**
		 * Die Namespaces die gecheckt werden sollen.
		 * @var array
		 */
		private $aNamespaces = array();

		/**
		 * Sollen die Pfade in einer Datei gecacht werden?
		 * @var bool
		 */
		protected $bWithFileCache = true;

		/**
		 * Der Basis-Ordner fuer die Includes.
		 * @var string
		 */
		protected $sBaseDir = '';

		/**
		 * Adds a module overwriting the core (fifo).
		 * @author blange <code@wbl-konzept.de>
		 * @param string $sCore
		 * @param string $sModule
		 * @return WBL_Modules_Autoloader
		 */
		public function addCoreOverride($sCore, $sModule) {
			if (!array_key_exists($sCore, $this->aCoreOverrides)) {
				$this->aCoreOverrides[$sCore] = array();
			} // if

			$this->aCoreOverrides[$sCore][] = $sModule;

			return $this;
		} // function

		/**
		 * Fuegt einen Eintrag zum OXID-Cache fuer die Klasssenpfade hinzu.
		 * @param  string $sClass Der vollqualifizierte Klassenname.
		 * @param  string $sPath  Der volle Klassenpfad.
		 * @return WBL_Modules_Autoloader
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function addToFileCache($sClass, $sPath) {
			$this->mFilePaths[$sClass] = $sPath;

			if ($this->withFileCaching()) {
				oxUtils::getInstance()->toPhpFileCache(
					self::FILE_CACHE_KEY,
					array_merge(
						$this->getCachedClassPaths(),
						array($sClass => $sPath)
					)
				);
			} // if

			return $this;
		} // function

		/**
		 * Returnt die Namespaces die inkludiert werden sollen.
		 * @return array
		 * @author blange <code@wbl-konzept.de>
		 */
		public function getAutoloaderNamespaces() {
			return $this->aNamespaces;
		} // function

		/**
		 * Returnt den Basis-Ordner fuer die Includes.
		 * @author blange <code@wbl-konzept.de>
		 * @return string
		 */
		protected function getBaseDir() {
			if (!$this->sBaseDir) {
				$this->setBaseDir(realpath(DIRNAME(__FILE__) . '/../..') . DIRECTORY_SEPARATOR);
			} // if

			return $this->sBaseDir;
		} // function

		/**
		 * Returns the class names for overriden some core classes.
		 * @author blange <code@wbl-konzept.de>
		 * @return array
		 */
		protected function getCoreOverrides() {
			return $this->aCoreOverrides;
		} // function

		/**
		 * Returns the file path of the class or an empty string.
		 * @author blange <code@wbl-konzept.de>
		 * @param  string $sClass The full qualified class name.
		 * @return string
		 */
		public function getFilePath($sClass) {
			startProfile($sMethod = __METHOD__);

			$aEndings   = $this->getFileEndings();
			$sBaseDir   = $this->getBaseDir();
			$sClassPart = trim(str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $sClass));

			/*
			 * foreach und file_exists ist genauso schnell wie ein glob-Aufruf fuer unterschiedliche
			 * Dateienden.
			 */
			foreach ($aEndings as $sEnding) {
				if (is_readable($sPath = $sBaseDir . $sClassPart . $sEnding)) {
					$this->addToFileCache($sClass, $sPath);
					stopProfile($sMethod);

					return $sPath;
				} // if
			} // foreach

			return '';
		} // function

		/**
		 * Returnt die von OXID gecachten Dateipfade fuer Klassen.
		 * @return array
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function getCachedClassPaths() {
			if ($this->mFilePaths !== null) {
				return $this->mFilePaths;
			} // if

			$this->mFilePaths = array();

			if (($this->withFileCaching()) &&
				($mTemp = oxUtils::getInstance()->fromPhpFileCache(self::FILE_CACHE_KEY)) &&
				(is_array($mTemp)))
			{
				$this->mFilePaths = $mTemp;
			} // if

			return $this->mFilePaths;
		} // function

		/**
		 * Returnt die moeglichen Dateiendungen (FIFO).
		 * @return array
		 * @author b.lange <code@wbl-konzept.de>
		 */
		protected function getFileEndings() {
			if (!$this->aFileEndings) {
				$this->aFileEndings[] = self::DEFAULT_FILE_ENDING;
			} // if

			return $this->aFileEndings;
		} // function

		/**
		 * Iterates through the override array, sets the aModules-Config temporarily and includes the core class itself.
		 * @param string $sClass
		 *
		 */
		protected function handleCoreOverrides($sClass) {
			if (!$aOverrides = array_change_key_case($this->getCoreOverrides(), CASE_LOWER)) {
				return false;
			} // if

			if (!array_key_exists($sClass = strtolower($sClass), $aOverrides)) {
				return false;
			} // if

			$oConfig = oxConfig::getInstance();
			$oConfig->setConfigParam(
				'aModules',
				array_merge(
					array($sClass => implode('&', $aModules = $aOverrides[$sClass])),
					$aOldValue = (array) $oConfig->getConfigParam('aModules')
				)
			);
			oxAutoload($sClass);

			foreach ($aModules as $sModuleClass) {
				$this->includeClass($sModuleClass, false);
			} // foreach

			$oConfig->setConfigParam('aModules', $aOldValue);
			unset($oConfig);

			return true;
		} // function

		/**
		 * Inkludiert die angeforderte Klasse wenn moeglich.
		 * @author blange <code@wbl-konzept.de>
		 * @param  string $sClass Der vollqualifizierte Klassenname.
		 * @param  bool   $bWithFileCache Should the caching be temporarily be disabled?
		 * @return bool
		 */
		public function includeClass($sClass, $bWithFileCache = true) {
			if ($this->handleCoreOverrides($sClass)) {
				return true;
			} // if

			if (!$this->isIncludeAllowed($sClass)) {
				return false;
			} // if

			if ($bWithFileCache && $this->includeClassFromCache($sClass)) {
				return true;
			} // if

			return ($sPath = $this->getFilePath($sClass)) ? (bool) require_once $sPath : false;
		} // function

		/**
		 * Liest den Pfad-Cache von OXID aus und inkludiert falls moeglich die entsprechende Datei.
		 * @param  string $sClass Der vollqualifizierte Klassenname.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function includeClassFromCache($sClass) {
			if (!$this->withFileCaching()) {
				return false;
			} // if

			if (!$aPaths = $this->getCachedClassPaths()) {
				return false;
			} // if

			return array_key_exists($sClass, $aPaths)
				? (bool) require_once $aPaths[$sClass]
				: false;
		} // function

		/**
		 * Darf der Autoloader fuer diese Datei verwendete werden?
		 * @param  string $sClass Der volle Klassenname.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		public function isIncludeAllowed($sClass) {
			return ($aNamespaces = $this->getAutoloaderNamespaces()) &&
				// Ueberspringe *_parent-Classes
				(strpos($sClass, '_parent') === false) &&
				/*
				 * Ueberspringe die Klasse, wenn der Namespace nicht stimmt. Normalerweise fuehrt
				 * diese Kontrolle auch dazu, dass z.B. keine relativen Pfade erlaubt sind, auszer
				 * der Admin konfiguriert den Admin-Loader entsprechend.
				 */
				(preg_match('/^(\\\\)?(' . implode('|', $aNamespaces) . ')(_|\\\\)/', $sClass));
		} // function

		/**
		 * Laedt die Namespaces, die mit diesem Autoloader beachtet werden sollen.
		 * @param  array $aNames Die Namespaces die beachtet werden sollen.
		 * @return WBL_Modules_Autoloader
		 * @author blange <code@wbl-konzept.de>
		 */
		public function setAutoloaderNamespaces(array $aNames) {
			$this->aNamespaces = $aNames;

			return $this;
		} // function

		/**
		 * Wechselt den Basis-Ordner fuer die Includes.
		 * @author blange <code@wbl-konzept.de>
		 * @param  string $sDir Der entsprechende Ordner, Existenz wird nicht kontrolliert.
		 * @return WBL_Modules_Autoloader
		 */
		public function setBaseDir($sDir) {
			$this->sBaseDir = realpath($sDir) . DIRECTORY_SEPARATOR;

			return $this;
		} // function

		/**
		 * Setting class names for oxid core classes which could be only overwritten here.
		 * @author blange <code@wbl-konzept.de>
		 * @param array $aOverrides
		 * @return WBL_Modules_Autoloader
		 */
		public function setCoreOverrides(array $aOverrides) {
			$this->aCoreOverrides = $aOverrides;

			return $this;
		} // function

		/**
		 * Setzt die Dateiendungen die (FIFO) beachten werden sollen.
		 * @param  array $aEndings Die moeglichen Dateiendungen.
		 * @return WBL_Modules_Autoloader
		 * @author blange <code@wbl-konzept.de>
		 */
		public function setFileEndings(array $aEndings) {
			$this->aFileEndings = $aEndings;

			return $this;
		} // function

		/**
		 * Sollen die Pfade in einer Datei gecacht werden?
		 * @param  bool $bNewState Der neue Status.
		 * @return bool Der alte Status.
		 * @author blange <code@wbl-konzept.de>
		 */
		public function withFileCaching($bNewState = false) {
			$bOldState = $this->bWithFileCache;

			if (func_num_args()) {
				$this->bWithFileCache = $bNewState;
			} // if

			return $bOldState;
		} // function
	} // class