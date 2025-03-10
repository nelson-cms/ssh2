<?php
declare(strict_types=1);

namespace NelsonCms\Ssh2;

final readonly class Output
{
	public function __construct(
		private string $command,
		private string $stdIo,
		private string $stdError,
	)
	{
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
