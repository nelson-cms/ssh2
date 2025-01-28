<?php
declare(strict_types=1);

namespace Nelson\Ssh2;

use Exception;
use Nelson\Ssh2\Exceptions\AuthenticationFailedException;
use Nelson\Ssh2\Exceptions\ConnectionFailedException;

final class Connection
{

	/** @var resource */
	private $connection = null;

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
