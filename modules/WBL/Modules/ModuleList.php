<?php
	/**
	 * ./modules/WBL/Modules/ModuleList.php
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxModule
	 * @version SVN: $Id$
	 */

	/**
	 * Extension of the oxModuleList.
	 *
	 * Without a Module, the Autoloader can not be activated in the backend and there are problems
	 * with modules and "no" module dir.
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxModule
	 * @version SVN: $Id$
	 */
	class WBL_Modules_ModuleList extends WBL_Modules_ModuleList_parent {
		/**
		 * The used autoloader.
		 * @var WBL_Modules_Autoloader|void|bool
		 */
		protected $mWBLAutoloader = false;

		/**
		 * (non-PHPdoc)
		 * @see http/core/oxModuleList::getDeletedExtensions()
		 */
		public function getDeletedExtensions() {
			$aDeletes = parent::getDeletedExtensions();

			if ($aDeletes && $this->getWBLAutoloader()) {
				$aDeletes = $this->removeWBLModules($aDeletes);
			} // if

			return $aDeletes;
		} // function

		/**
		 * Adds the WBL-Autoloader-Modules to the list.
		 * @author blange <code@wbl-konzept.de>
		 * @return void
		 */
		public function getDisabledModuleClasses() {
			$aParent = parent::getDisabledModuleClasses();

			if (!$oAutoloader = $this->getWBLAutoloader()) {
				return $aParent;
			} // if

			if (!(($aDisabledModules = $this->getDisabledModules()) && is_array($aDisabledModules))) {
				return $aParent;
			} // if

			$aDisabledModuleClasses = array();
			$aModules               = $this->getAllModules();

			foreach ($aDisabledModules as $sId) {
				foreach ($aModules as $sClass => $aModuleClasses) {
					foreach ($aModuleClasses as $sModuleClass) {
						/*
						 * Module classes of the autoloader do not start with the dir, so the oxid comparison
						 * with the dir ist not working. But the Modules of the autoloader must start
						 * with the id, so use it with the autoloader.
						 */
						if (((strpos($sModuleClass, $sId) === 0) || (strpos($sModuleClass, "\\{$sId}") === 0)) &&
							($oAutoloader->isIncludeAllowed($sModuleClass)))
						{
							$aDisabledModuleClasses[] = $sModuleClass;
						} // if
					} // foreach
				} // foreach
			} // foreach

			return array_unique(array_merge($aParent, $aDisabledModuleClasses));
		} // function

		/**
		 * Returns the Autoloader-Instance when its registered or null.
		 * @author blange <code@wbl-konzept.de>
		 * @return WBL_Modules_Autoloader|void
		 */
		public function getWBLAutoloader() {
			if ($this->mWBLAutoloader !== false) {
				return $this->mWBLAutoloader;
			} // if

			$this->mWBLAutoloader = null;

			foreach (spl_autoload_functions() as $mCall) {
				if (!is_array($mCall)) {
					continue;
				} // if

				if (($mObject = reset($mCall)) instanceof WBL_Modules_Autoloader)
				{
					$this->setWBLAutoloader($mObject);
					break; // makes the codecoverage more easy
				} // if
			} // foreach

			return $this->mWBLAutoloader;
		} // function

		/**
		 * Removes the files which are loaded through the autoloader classes.
		 * @author blange <code@wbl-konzept.de>
		 * @param array $aModules
		 * @return array
		 */
		protected function removeWBLModules(array $aModules) {
			if (!(($oAutoloader = $this->getWBLAutoloader()) &&
				($oAutoloader->getAutoloaderNamespaces())))
			{
				return $aModules;
			} // if

			$aAllFiltered = array();

			foreach ($aModules as $sOXIDClass => $aClassModules) {
				$aFiltered = array_filter($aClassModules, array($this, 'removeWBLModulesArrayFilter'));

				if ($aFiltered) {
					$aAllFiltered[$sOXIDClass] = $aFiltered;
				} // if
			} // foreach

			return $aAllFiltered;
		} // function

		/**
		 * Callback for the array_filter-Call in self::removeWBLModules.
		 * @author blange <code@wbl-konzept.de>
		 * @param string $sClassName
		 * @return bool
		 */
		protected function removeWBLModulesArrayFilter($sClassName) {
			$oAutoloader = $this->getWBLAutoloader();
			$aNamespaces = $oAutoloader->getAutoloaderNamespaces();

			foreach ($aNamespaces as $sNamespace) {
				if ((strpos($sClassName, $sNamespace) === 0) && ($oAutoloader->getFilePath($sClassName))) {
					return false;
				} // if
			} // if

			return true;
		} // function

		/**
		 * Sets the used autoloader.
		 * @author blange <code@wbl-konzept.de>
		 * @param WBL_Modules_Autoloader $oAutoloader
		 * @return WBL_Modules_ModuleList
		 */
		public function setWBLAutoloader(WBL_Modules_Autoloader $oAutoloader) {
			$this->mWBLAutoloader = $oAutoloader;
			unset($oAutoloader);

			return $this;
		} // function
	} // class