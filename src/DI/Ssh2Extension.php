<?php

declare(strict_types=1);

namespace Nelson\Ssh2\DI;

use Nelson\Ssh2\VO\Connection;
use Nette\DI\CompilerExtension;

final class Ssh2Extension extends CompilerExtension
{

	public function __construct(
		private readonly string $host,
		private readonly string $user,
		private readonly string $publicKeyPath,
		private readonly string $privateKeyPath,
		private readonly int $port = 22,
		private readonly ?string $passphrase = null,
	)
	{
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('connection'))
			->setFactory(Connection::class)
			->setAutowired(false)
			->setArguments([
				'host' => $this->host,
				'user' => $this->user,
				'publicKeyPath' => $this->publicKeyPath,
				'privateKeyPath' => $this->privateKeyPath,
				'port' => $this->port,
				'passphrase' => $this->passphrase,
			]);
	}
}
