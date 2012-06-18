<?php
	require_once realpath(dirname(__FILE__) . '/WBL/Modules/Autoloader.php');
	$oAutoloader = new WBL_Modules_Autoloader();

	spl_autoload_register(array(
		$oAutoloader
			->addCoreOverride('oxutilsobject', 'WBL_Modules_UtilsObject')
			->setAutoloaderNamespaces(array('RS', 'WBL')),
		'includeClass'
	));
	unset($oAutoloader);