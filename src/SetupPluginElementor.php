<?php

namespace WilokeCommandLine;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupPluginElementor extends CommonController
{
	protected        $commandName                             = 'make:plugin';
	protected        $commandDesc                             = 'Setup plugin with elementor';
	protected        $commandOptionNameSpace                  = 'namespace';
	protected        $commandAutoloadDir                      = 'autoloadDir';
	protected        $commandAutoloadDirDesc                  = 'Enter "App Directory Name" that you defined in the composer autoload. EG: src or app';
	protected        $commandOptionNameSpaceDesc              = 'Provide your Your Unit Test Namespace. EG: Wiloke';
	protected string $ShareRelativeDir                        = 'src/Share';
	protected string $ControllerRelativeDir                   = 'src/Controllers';
	protected string $TraitHandleAutoRenderSettingControllers = 'TraitHandleAutoRenderSettingControllers.php';
	protected string $ReadmeFilename                          = 'Readme.md';
	protected        $helperComponentDir                      = 'Helpers';
	protected        $skeletonComponentDir                    = 'Skeleton';
	protected        $skeletonRelativeDir                     = 'Illuminate/Skeleton';

	public function setOriginalRelativeDir()
	{
		$this->originalRelativeFileDir = '';
	}

	public function setRelativeComponentDir()
	{
		$this->relativeComponentDir = 'PluginElementor';
	}

	public function configure()
	{
		$this->setName($this->commandName)
			->setDescription($this->commandDesc)
			->addArgument(
				$this->commandAutoloadDir,
				InputArgument::OPTIONAL,
				$this->commandAutoloadDirDesc,
				$this->autoloadDir
			)
			->addOption(
				$this->commandOptionNameSpace,
				null,
				InputOption::VALUE_OPTIONAL,
				$this->commandOptionNameSpaceDesc
			);
	}

	public function copyPluginElementorFolder()
	{
		$getAbsFileDir=$this->getAbsFileDir();
		if (!$this->oFileSystem->exists($getAbsFileDir)) {
			$this->oFileSystem->mkdir($getAbsFileDir, 755);
		}
		$this->recursiveCopy($this->getRelativeComponentDir(), $getAbsFileDir);
	}

	public function execute(InputInterface $oInput, OutputInterface $oOutput)
	{
		$this->commonConfiguration($oInput, $oOutput);
		$this->setRelativeTargetFileDir();
		//$this->relativeComponentDir = dirname(dirname(__FILE__)) . '/components/' . $this->relativeComponentDir . '/';
		$this->autoloadDir = $oInput->getArgument($this->commandAutoloadDir);

		$this->copyPluginElementorFolder();
		$this->outputMsg();
	}
}