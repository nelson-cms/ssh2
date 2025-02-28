<?php
declare(strict_types=1);

namespace NelsonCms\Ssh2;

use Exception;
use NelsonCms\Ssh2\Exceptions\AuthenticationFailedException;
use NelsonCms\Ssh2\Exceptions\ConnectionFailedException;

final class Connection
{
	/** @var resource */
	private $connection = null;

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
		?string $passphrase = null
	)
	{
		$this->passphrase = $passphrase;
		$this->port = $port;
		$this->privateKeyPath = $privateKeyPath;
		$this->publicKeyPath = $publicKeyPath;
		$this->user = $user;
		$this->host = $host;
	}


	/**
	 * @return resource
	 * @throws Exception
	 */
	public function getConnection()
	{
		if ($this->connection === null) {
			$this->connect();
		}

		return $this->connection;
	}


	public function connect(): void
	{
		$connection = ssh2_connect($this->host, $this->port);

		if ($connection === false) {
			throw new ConnectionFailedException('Connection failed');
		}

		$this->connection = $connection;

		$result = ssh2_auth_pubkey_file(
			$this->connection,
			$this->user,
			$this->publicKeyPath,
			$this->privateKeyPath,
			$this->passphrase ?? '',
		);

		if ($result === false) {
			throw new AuthenticationFailedException('Authentication failed');
		}
	}


	public function __destruct()
	{
		if ($this->connection !== null) {
			ssh2_disconnect($this->connection);
		}
	}
}
