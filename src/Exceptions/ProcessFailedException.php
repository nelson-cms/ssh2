<?php
declare(strict_types=1);

namespace Nelson\Ssh2\Exceptions;

use Nelson\Ssh2\Process;
use InvalidArgumentException;
use RuntimeException;

class ProcessFailedException extends RuntimeException
{
	public function __construct(Process $process)
	{
		if (!$process->isExecuted()) {
			throw new InvalidArgumentException('The Process must be executed before it can be validated.');
		}

		if ($process->isSuccessful()) {
			throw new InvalidArgumentException('Expected a failed process, but the given process was successful.');
		}

		$error = sprintf('The command "%s" failed.'."\nError Message: %s\nWorking directory: %s",
			$process->getCmd(),
			$process->getError(),
			$process->getCwd(),
		);

		parent::__construct($error);
	}
}
