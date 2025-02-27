<?php
declare(strict_types=1);

namespace NelsonCms\Ssh2;

use LogicException;

final class Process
{
	/** @var string */
	private $commandLine;

	/** @var string */
	private $cmd;

	/** @var Output */
	private $output;

	/** @var bool */
	private $isExecuted = false;

	/** @var Connection */
	private $connection;

	/** @var string|null */
	private $cwd = null;


	/**
	 * @param Connection $connection
	 * @param string|array<int|string, string> $cmd
	 * @param string|null $cwd
	 */
	public function __construct(
		Connection $connection,
		$cmd,
		?string $cwd = null
	)
	{
		$this->cwd = $cwd;
		$this->connection = $connection;
		$this->cmd = is_array($cmd) ? implode(' ', $cmd) : $cmd;
		$this->commandLine = $this->cwd !== null ? 'cd ' . $this->cwd . ' && ' . $this->cmd : $this->cmd;
	}


	public function run(): void
	{
		if ($this->isExecuted) {
			throw new LogicException('Process has already been executed');
		}

		$stream = ssh2_exec($this->connection->getConnection(), $this->commandLine);

		if ($stream === false) {
			throw new LogicException('Unable to execute command');
		}

		$stdErrorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		$stdIoStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);

		// It should not return false, because the stream is already created, but here we are
		if ($stdErrorStream === false || $stdIoStream === false) {
			throw new LogicException('Unable to fetch stream');
		}

		stream_set_blocking($stdErrorStream, true);
		stream_set_blocking($stdIoStream, true);

		$this->output = new Output(
			$this->commandLine,
			(string) stream_get_contents($stdIoStream),
			(string) stream_get_contents($stdErrorStream),
		);

		fclose($stream);
		$this->isExecuted = true;
	}


	public function getOutput(): string
	{

		return $this->output->getStdIo();
	}


	public function getError(): string
	{
		return $this->output->getStdError();
	}


	public function getCmd(): string
	{
		return $this->cmd;
	}


	public function getCwd(): ?string
	{
		return $this->cwd;
	}


	public function isSuccessful(): bool
	{
		return $this->isExecuted && isset($this->output) && !$this->output->hasError();
	}


	public function isExecuted(): bool
	{
		return $this->isExecuted;
	}

}
