<?php
declare(strict_types=1);

namespace NelsonCms\Ssh2;

final class Output
{
	/** @var string */
	private $command;

	/** @var string */
	private $stdIo;

	/** @var string */
	private $stdError;


	public function __construct(
		string $command,
		string $stdIo,
		string $stdError
	)
	{
		$this->stdError = $stdError;
		$this->stdIo = $stdIo;
		$this->command = $command;
	}


	public function getCommand(): string
	{
		return $this->command;
	}


	public function getStdIo(): string
	{
		return $this->stdIo;
	}


	public function getStdError(): string
	{
		return $this->stdError;
	}


	public function hasError(): bool
	{
		return $this->stdError !== '';
	}
}
