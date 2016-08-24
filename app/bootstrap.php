<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(array('78.108.107.248',
								  '78.108.107.249',
								  '78.108.107.250', 
								  '78.108.107.251', 
								  '78.108.107.252', 
								  '78.108.107.253', 
								  '78.108.107.254', 
								  '78.108.107.255',
								  '94.112.79.49',
								  '94.112.79.165', 
								  '77.236.208.61',
								  '94.112.79.19',
								  '95.82.174.130',
								  '89.103.182.50',
								  '89.103.183.73',
								  '212.20.99.253',
								  '89.103.182.236',
								  '78.45.37.34')
								 ); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
