<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;
use Tester\Environment;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

//		$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP

        $configurator->setDebugMode(true);
		$configurator->enableTracy($appDir . '/log');

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory($appDir . '/temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

		$configurator
			->addConfig($appDir . '/config/common.neon')
			->addConfig($appDir . '/config/local.neon');

		return $configurator;
	}


	public static function bootForTests(): Configurator
	{
		$configurator = self::boot();
		Environment::setup();
		return $configurator;
	}
}
