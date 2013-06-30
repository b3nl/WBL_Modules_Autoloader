<?php
	require_once realpath(dirname(__FILE__) . '/WBL/Modules/Autoloader.php');
	$oWBLAutoloader = new WBL_Modules_Autoloader();

	spl_autoload_register(
		array(
			$oWBLAutoloader
				->setAutoloaderNamespaces(array('WBL'))
				->setFileEndings(array('.php')),
			'includeClass'
		)
	);
	unset($oWBLAutoloader);
