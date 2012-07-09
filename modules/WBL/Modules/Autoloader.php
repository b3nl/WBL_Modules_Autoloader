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
		 * Adapter for overcoming the strtolower in the oxNew-core.
		 * @author blange <code@wbl-konzept.de>
		 * @param string $sFullClassName Der full qualified class name
		 * @return object
		 * @throws oxSystemComponentException If the class is missing.
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
		 * Cache key for caching the found class paths.
		 * @var string
		 */
		const FILE_CACHE_KEY = 'aEosNeoAutoloaderFilePaths';

		/**
		 * Lazy Loading for the path cache.
		 * @var void|array
		 */
		protected $mFilePaths = null;

		/**
		 * Helping array to override some core classes.
		 * @var array
		 */
		protected $aCoreOverrides = array();

		/**
		 * The possible file endings of a class/Interface-file (FIFO).
		 * @var array
		 */
		protected $aFileEndings = array();

		/**
		 * The used namespaces.
		 * @var array
		 */
		private $aNamespaces = array();

		/**
		 * Should the paths be cached in a file.
		 * @var bool
		 */
		protected $bWithFileCache = true;

		/**
		 * The base dir for including the files.
		 * @var string
		 */
		protected $sBaseDir = '';

		/**
		 * Standard file ending.
		 * @var string
		 */
		const DEFAULT_FILE_ENDING = '.php';

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
		 * Adds an entry to the file cache.
		 * @param  string $sClass The full qualified class name.
		 * @param  string $sPath  the full path to the class.
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
		 * Returns the used namespaces
		 * @return array
		 * @author blange <code@wbl-konzept.de>
		 */
		public function getAutoloaderNamespaces() {
			return $this->aNamespaces;
		} // function

		/**
		 * Returnt the base dir for the includes.
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
		 * Returns the class names for overriding some core classes.
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
			 * foreach und file_exists ist as fast as a glob-call for the different
			 * file endings.
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
		 * Returns the cached class paths.
		 * @return array
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function getCachedClassPaths() {
			if ($this->mFilePaths === null) {
				$this->mFilePaths = array();

				if (($this->withFileCaching()) &&
					($mTemp = oxUtils::getInstance()->fromPhpFileCache(self::FILE_CACHE_KEY)) &&
					(is_array($mTemp)))
				{
					$this->mFilePaths = $mTemp;
				} // if
			} // if

			return $this->mFilePaths;
		} // function

		/**
		 * Returns the used file endings (FIFO).
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
			$aOverrides = array_change_key_case($this->getCoreOverrides(), CASE_LOWER);

			if (!($aOverrides && array_key_exists($sClass = strtolower($sClass), $aOverrides))) {
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
		 * Includes the called class if possible.
		 * @author blange <code@wbl-konzept.de>
		 * @param  string $sClass The full qualified class name.
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
		 * Tries to get the path from the caching and includes the file if possible.
		 * @param  string $sClass The full qualified class name.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		protected function includeClassFromCache($sClass) {
			return $this->withFileCaching() && ($aPaths = $this->getCachedClassPaths()) &&
				array_key_exists($sClass, $aPaths) && (bool) require_once $aPaths[$sClass];
		} // function

		/**
		 * Is the autoloader allowed to include the class?
		 * @param  string $sClass The class or interface name.
		 * @return bool
		 * @author blange <code@wbl-konzept.de>
		 */
		public function isIncludeAllowed($sClass) {
			return ($aNamespaces = $this->getAutoloaderNamespaces()) &&
				// Ueberspringe *_parent-Classes
				(strpos($sClass, '_parent') === false) &&
				 /*
				  * Skips the class, if the namespaces does not match. The check does not allow
				  * relative paths as well (or any other file include calling class_exists()),
				  * as long as you say so
				  */
				(preg_match('/^(\\\\)?(' . implode('|', $aNamespaces) . ')(_|\\\\)/', $sClass));
		} // function

		/**
		 * Sets the namespaces allowed for this autoloader.
		 * @param  array $aNames
		 * @return WBL_Modules_Autoloader
		 * @author blange <code@wbl-konzept.de>
		 */
		public function setAutoloaderNamespaces(array $aNames) {
			$this->aNamespaces = $aNames;

			return $this;
		} // function

		/**
		 * Sets the base dir for the includes.
		 * @author blange <code@wbl-konzept.de>
		 * @param  string $sDir The path to an existing dir.
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
		 * Sets the fileendings (FIFO) matching this autoloader
		 * @param  array $aEndings
		 * @return WBL_Modules_Autoloader
		 * @author blange <code@wbl-konzept.de>
		 */
		public function setFileEndings(array $aEndings) {
			$this->aFileEndings = $aEndings;

			return $this;
		} // function

		/**
		 * Should the found paths be cached in a file.
		 * @param  bool $bNewState the new state.
		 * @return bool The old state.
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