<?php

namespace WilokeCommandLine;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupSlackPostMessage extends CommonController
{
	protected $commandName          = 'make:slack-message';
	protected $commandDesc          = 'Setup Slack Post Message';
	protected $relativeComponentDir = 'Slack';

	protected $commandOptionNameSpace     = 'namespace';
	protected $commandOptionNameSpaceDesc = 'Provide your Your Unit Test Namespace. EG: Wiloke';

	protected $originalFilename = 'PostMessage.php';

	/**
	 * @var mixed
	 */
	private $filename;

	public function setRelativeComponentDir()
	{
		$this->relativeComponentDir = 'Slack';
	}

	public function setOriginalRelativeDir()
	{
		$this->originalRelativeFileDir = 'Illuminate/Slack';
	}

	public function configure()
	{
		$this->setName($this->commandName)
			->setDescription($this->commandDesc)
			->addArgument(
				$this->commonClassName,
				InputArgument::REQUIRED,
				$this->commonClassNameDesc
			)
			->addOption(
				$this->commandOptionNameSpace,
				null,
				InputOption::VALUE_OPTIONAL,
				$this->commandOptionNameSpaceDesc
			);
	}

	protected function createShortcode(): bool
	{
		if (!$this->oFileSystem->exists($this->getAbsFileDir())) {
			$this->oFileSystem->mkdir($this->getAbsFileDir());
		}

		if ($this->oFileSystem->exists($this->trailingslashit($this->getAbsFileDir()) . $this->filename)) {
			if (!$this->isContinue()) {
				return true;
			}
		}

		$this->dummyFile(
			$this->getRelativeComponentDir() . $this->originalFilename,
			$this->trailingslashit(
				$this->relativeTargetFileDir
			),
			$this->originalNamespace . '\\Controllers\\' . $this->relativeComponentDir,
			$this->filename
		);


		return true;
	}


	public function execute(InputInterface $oInput, OutputInterface $oOutput)
	{
		$this->commonConfiguration($oInput, $oOutput);
		$this->className = $oInput->getArgument($this->commonClassName);
		$this->filename = $this->className . '.php';

		$this->createShortcode();
		$this->outputMsg();
	}
}
