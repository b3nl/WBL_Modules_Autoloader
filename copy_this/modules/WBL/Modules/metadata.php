<?php
	/**
	 * Module-Metadata for the WBL Autoloader.
	 * @author blange <code@wbl-konzept.de>
	 * @category modules
	 * @package WBL_Modules
	 * @subpackage oxAutoload
	 * @version SVN: $Id$
	 */
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

	if (version_compare(current(explode('_', oxConfig::getInstance()->getVersion())), '4.7.0', '>='))
	{
		$aWBLAutoloaderClasses['oxsession']     = 'WBL_Modules_Session';
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