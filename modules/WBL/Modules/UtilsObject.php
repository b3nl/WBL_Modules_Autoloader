<?php
	/**
	 * ./modules/WBL/UtilsObject.php
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxUtilsObject
	 * @version SVN: $Id$
	 */

	/**
	 * oxUtilsObject for special autoloader handling.
	 * @author Bjoern Simon Lange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxUtilsObject
	 * @version SVN: $Id$
	 */
	class WBL_Modules_UtilsObject extends WBL_Modules_UtilsObject_parent {
		/**
		 * The used autoloader.
		 * @var WBL_Modules_Autoloader|void|bool
		 */
		protected $mWBLAutoloader = false;

		/**
		 * Returns the module chain without disabled modules.
		 * @author blange <code@wbl-konzept.de>
		 * @param array $aClassChain
		 * @return array
		 */
		protected function _getActiveModuleChain($aClassChain) {
			if (!($aClassChain && $this->withAutoloader() &&
				($aDisabledModules = $this->getConfig()->getConfigParam('aDisabledModules')) &&
				(is_array($aDisabledModules)))) {
				return parent::_getActiveModuleChain($aClassChain);
			} // if

			$oAutoloader = $this->getWBLAutoloader();

			// Remove the deactive WBL-Autoloader-Modules.
			foreach ($aDisabledModules as $sModuleId) {
				foreach ($aClassChain as $iRound => $sModule) {
					/*
					 * Module classes of the autoloader do not start with the dir, so the oxid comparison
					 * with the dir ist not working. But the Modules of the autoloader must start
					 * with the id, so use it with the autoloader.
					 */
					if (($oAutoloader->isIncludeAllowed($sModule)) &&
						((strpos($sModule, $sModuleId . '_') === 0) || (strpos($sModule, $sModuleId . '\\') === 0)))
					{
						unset($aClassChain[$iRound]);
					} // if
				} // foreach
			} // foreach

			if ($aClassChain) {
				$aUsedChain = $aClassChain;

				// Modules of the wbl autoloader should be not checked with the normal logic.
				foreach ($aClassChain as $iRound => $sModule) {
					if ($oAutoloader->isIncludeAllowed($sModule)) {
						unset($aClassChain[$iRound]);
					} // if
				} // foreach

				// No change of the keys.
				return $aUsedChain + parent::_getActiveModuleChain($aClassChain);
			} // if

			return array();
		} // function

		/**
		 * Returns the Autoloader-Instance when its registered or null.
		 * @author blange <code@wbl-konzept.de>
		 * @return WBL_Modules_Autoloader|void
		 */
		public function getWBLAutoloader() {
			if ($this->mWBLAutoloader === false) {
				$this->mWBLAutoloader = null;

				foreach (spl_autoload_functions() as $mCall) {
					if ((is_array($mCall)) && ($mObject = reset($mCall)) instanceof WBL_Modules_Autoloader) {
						$this->setWBLAutoloader($mObject);
						break; // makes the codecoverage more easy
					} // if
				} // foreach
			} // if

			return $this->mWBLAutoloader;
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

		/**
		 * Is an autoloader used?
		 * @author blange <code@wbl-konzept.de>
		 * @return bool
		 */
		protected function withAutoloader() {
			return ($oAutoloader = $this->getWBLAutoloader()) && $oAutoloader->getAutoloaderNamespaces();
		} // function
	} // class