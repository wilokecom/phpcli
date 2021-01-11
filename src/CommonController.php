<?php


namespace WilokeCommandLine;


use Symfony\Component\Console\Command\Command;

abstract class CommonController extends Command
{
	protected $namespace;
	protected $content;
	protected $fileDir;

	public abstract function setFileDir();

	/**
	 * @var mixed
	 */
	protected $namespacePlaceholder = 'WilokeTest';

	protected function generateNamespace(): string
	{
		return $this->namespace . '\\' . str_replace('/', '\\', $this->fileDir);
	}

	protected function replaceNamespace()
	{
		if ($this->namespace) {
			$this->content = str_replace(
				[
					$this->namespacePlaceholder,
					'#namespace'
				],
				[
					$this->namespace,
					'namespace'
				],
				$this->content
			);
		}

		return $this->content;
	}
}
