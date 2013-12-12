<?php
	error_reporting(0);
	
	include_once 'app/data/functions.php';
	include_once 'app/Core.php';
	include_once 'app/Configurator.php';
	include_once 'app/data/FormListeners.php';
	
	$Core = new Core();
	$Configurator = new Configurator();
	
	$Configurator->setThemeDir("./app/theme/");
	
	$Core->getSereverData();
	
	$Configurator->paganation();
	$Configurator->user_panel();
	$Configurator->user_login();
	$Configurator->user_register();
	
	$Configurator->registerCom("{activate_script}", "");
	
	$Configurator->user_activation();
	
	$Configurator->registerCom("{title}", "eLyz Minecraft");
	$Configurator->registerCom("{weblink}", rf_webLink());
	$Configurator->registerCom("{cssdir}", rf_styleDir());
	$Configurator->registerCom("{jsdir}", rf_jsDir());
	$Configurator->registerCom("{styleImagesDir}", rf_styleImagesDir());
	
	$Configurator->registerCom("{ServerStatus}", $Core->serverStatus());
	$Configurator->registerCom("{ServerMotd}", $Core->getServerMotd());
	$Configurator->registerCom("{ServerIP}", $Core->ServerIP);
	$Configurator->registerCom("{ServerPort}", $Core->ServerPort);
	$Configurator->registerCom("{ServerOnlinePlayers}", $Core->getServerOnlinePlayers());
	$Configurator->registerCom("{ServerMaxPlayers}", $Core->getServerMaxPlayers());
	
	$Configurator->run();