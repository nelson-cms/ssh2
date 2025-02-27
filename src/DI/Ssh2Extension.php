<?php

declare(strict_types=1);

namespace NelsonCms\Ssh2\DI;

use NelsonCms\Ssh2\Connection;
use Nette\DI\CompilerExtension;

final class Ssh2Extension extends CompilerExtension
{

	/** @var string */
	private $host;

	/** @var string */
	private $user;

	/** @var string */
	private $publicKeyPath;

	/** @var string */
	private $privateKeyPath;

	/** @var int */
	private $port = 22;

	/** @var string|null */
	private $passphrase = null;


	public function __construct(
		string $host,
		string $user,
		string $publicKeyPath,
		string $privateKeyPath,
		int $port = 22,
		?string $passphrase = null,
	)
	{
		$this->passphrase = $passphrase;
		$this->port = $port;
		$this->privateKeyPath = $privateKeyPath;
		$this->publicKeyPath = $publicKeyPath;
		$this->user = $user;
		$this->host = $host;
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
