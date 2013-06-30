<?php
	/**
	 * Module-Metadata for the WBL Autoloader.
	 * @author blange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxAutoload
	 * @version SVN: $Id$
	 */
	$sSysVersionForLoader  = current(explode('_', WBL_Modules_Helper_Singleton::getConfig()->getVersion()));
	$sMetadataVersion      = '1.0';
	$aWBLAutoloaderClasses = array(
		'oxmodulelist' => 'WBL_Modules_ModuleList'
	);

	$aWBLAutoloaderFiles = array(
		'WBL_Modules_Helper_Singleton' => 'WBL/Modules/Helper/Singleton.php',
		'WBL_Modules_Object_Abstract'  => 'WBL/Modules/Object/Abstract.php',
		'WBL_Modules_Object_Interface' => 'WBL/Modules/Object/Interface.php',
		'WBL_Modules_Session'          => 'WBL/Modules/Session.php',
		'WBL_Modules_UtilsObject'      => 'WBL/Modules/UtilsObject.php'
	);

	// Fix for the session object, that can not provide a basket object.
	if ((version_compare($sSysVersionForLoader, '4.7.0', '>=')) &&
		((version_compare($sSysVersionForLoader, '4.7.6', '<')) || (version_compare($sSysVersionForLoader, '5.0.6', '<'))))
	{
		$aWBLAutoloaderClasses['oxsession']  = 'WBL_Modules_Session';
	} // if

	if ((version_compare($sSysVersionForLoader, '4.7.0', '>='))) {
		$aWBLAutoloaderClasses['oxutilsobject'] = 'WBL_Modules_UtilsObject';
	} // if

	foreach ($aWBLAutoloaderClasses as $sClass) {
		// OXID needs the slash
		$aWBLAutoloaderFiles[$sClass] = str_replace('_', '/', $sClass) . '.php';
	} // foreach

	$aModule = array(
		'author'       => 'WBL Konzept',
		'description'  => array(
			'de' => 'Spezieller Autoloader um ZEND Framework Strukturen zu erlauben',
			'en' => 'Special Autoloader to allow ZEND Framework structures'
		),
		'email'        => 'code@wbl-konzept.de',
		'extend'       => $aWBLAutoloaderClasses,
		'files'        => $aWBLAutoloaderFiles,
		'id'           => 'WBL_Modules',
		'title'        => 'WBL Modules Autoloader',
		'thumbnail'    => 'wbl_logo.jpg',
		'url'          => 'http://wbl-konzept.de',
		'version'      => '1.0.0'
	);