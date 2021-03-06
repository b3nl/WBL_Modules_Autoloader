<?php
    /**
     * ./modules/WBL/UtilsObject.php
     * @author     Bjoern Simon Lange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_Modules
     * @subpackage oxUtilsObject
     * @version    SVN: $Id$
     */

    eval('class WBL_Modules_UtilsObject_Extender extends ' .
        (class_exists('oxRegistry', false) ? 'WBL_Modules_UtilsObject_parent' : 'oxUtilsObject') .
        ' { }');

    /**
     * oxUtilsObject for special autoloader handling.
     * @author     Bjoern Simon Lange <code@wbl-konzept.de>
     * @category   modules
     * @package    WBL_Modules
     * @subpackage oxUtilsObject
     * @version    SVN: $Id$
     */
    class WBL_Modules_UtilsObject extends WBL_Modules_UtilsObject_Extender
    {
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
        protected function _getActiveModuleChain($aClassChain)
        {
            if (!($aClassChain && $this->withAutoloader() &&
                /*
                 * There is no config getter in oxUtilsObject since 4.7.0
                 * ($aDisabledModules = $this->getConfig()->getConfigParam('aDisabledModules')) &&
                 */
                class_exists('oxConfig', false) &&
                ($aDisabledModules = WBL_Modules_Helper_Singleton::getConfig()->getConfigParam('aDisabledModules')) &&
                (is_array($aDisabledModules))))
            {
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
                    if (($oAutoloader->isIncludeAllowed($sModule)) && // This classes are handled thr oxid.
                        ((strpos($sModule, $sModuleId . '_') === 0) || (strpos($sModule, $sModuleId . '\\') === 0))
                    ) {
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
                    else {
                        // Non-Autoloader-Files should be no part of the used array.
                        unset($aUsedChain[$iRound]);
                    } // else
                } // foreach

                $aOverall = $aUsedChain + parent::_getActiveModuleChain($aClassChain);
                ksort($aOverall);
                return $aOverall;
            } // if

            return array();
        } // function

        /**
         * Returns the Autoloader-Instance when its registered or null.
         * @author blange <code@wbl-konzept.de>
         * @return WBL_Modules_Autoloader|void
         */
        public function getWBLAutoloader()
        {
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
         * (non-PHPdoc)
         *
         * Removes the "hacked" module config.
         * @see http/core/oxUtilsObject::oxNew()
         */
        public function oxNew($sClassName)
        {
            $aArgs = func_get_args();

            $oObject = call_user_func_array(
                version_compare(phpversion(), '5.3.0', '>=') ? 'parent::oxNew' : array('parent', 'oxNew'),
                $aArgs
            );

            if ((!class_exists('oxRegistry', false)) && (class_exists('oxconfig', false))) {
                $this->removeCoreOverridesFromConfig($sClassName);
            } // if

            return $oObject;
        } // function

        /**
         * Removes the core overrides from the module class chain.
         * @author blange <code@wbl-konzept.de>
         * @param string $sClassName
         * @return void
         */
        protected function removeCoreOverridesFromConfig($sClassName)
        {
            $oLoader    = $this->getWBLAutoloader();
            $sClassName = strtolower($sClassName);

            if ($oLoader && (array_key_exists($sClassName, $aOverrides = $this->getWBLAutoloader()->getCoreOverrides())))
            {
                // oxconfig is loaded directly, so no need to check a special class name.
                $aModules       = WBL_Modules_Helper_Singleton::getConfig()->getConfigParam('aModules');
                $sCoreOverrides = $aOverrides[$sClassName];

                if (array_key_exists(strtolower($sClassName), $aModules)) {
                    $aModules[$sClassName] = str_replace($sCoreOverrides, '', $aModules[$sClassName]);

                    WBL_Modules_Helper_Singleton::getConfig()->setConfigParam('aModules', array_filter($aModules));
                } // if
            } // if
        } // function

        /**
         * Sets the used autoloader.
         * @author blange <code@wbl-konzept.de>
         * @param WBL_Modules_Autoloader $oAutoloader
         * @return WBL_Modules_ModuleList
         */
        public function setWBLAutoloader(WBL_Modules_Autoloader $oAutoloader)
        {
            $this->mWBLAutoloader = $oAutoloader;
            unset($oAutoloader);

            return $this;
        } // function

        /**
         * Is an autoloader used?
         * @author blange <code@wbl-konzept.de>
         * @return bool
         */
        protected function withAutoloader()
        {
            return ($oAutoloader = $this->getWBLAutoloader()) && $oAutoloader->getAutoloaderNamespaces();
        } // function
    } // class