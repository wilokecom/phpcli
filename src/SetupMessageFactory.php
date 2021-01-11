<?php


namespace WilokeCommandLine;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SetupMessageFactory extends CommonController
{
	protected $commandName = 'make:message-factory';
	protected $commandDesc = 'Setup Message Factory';

	protected $commandAutoloadDir     = 'autoloadDir';
	protected $commandAutoloadDirDesc = 'Enter "App Directory Name" that you defined in the composer autoload. EG: src or app';

	protected $commandOptionNameSpace     = 'namespace';
	protected $commandOptionNameSpaceDesc = 'Provide your Your Unit Test Namespace. EG: Wiloke';

	protected $componentsDir = 'components/Message';

	/**
	 * @var Filesystem
	 */
	private $oFileSystem;

	/**
	 * @var mixed
	 */
	private $originalFileNames
		= ['AbstractMessage.php', 'AjaxMessage.php', 'MessageFactory.php', 'NormalMessage.php',
		   'RestMessage.php', 'ShortcodeMessage.php'];

	/**
	 * @var mixed
	 */
	private $autoloadDir = 'app';

	public function setFileDir()
	{
		$this->fileDir = 'Illuminate/Message';
	}

	protected function configure()
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

	/**
	 * @throws \Exception
	 */
	private function createPostSkeletonComponent()
	{
		if ($this->namespace) {
			$this->namespace = $this->generateNamespace();
		}

		foreach ($this->originalFileNames as $fileName) {
			$this->content = file_get_contents($this->componentsDir . $fileName);

			if (empty($this->content)) {
				throw new \Exception('We could not get ' . $fileName .
					' content. Please re-check read permission');
			}

			$this->replaceNamespace();

			$this->autoloadDir = trim($this->autoloadDir, '/') . '/';
			$fileDirectory = './' . $this->autoloadDir . $this->fileDir;

			if (!$this->oFileSystem->exists($fileDirectory)) {
				$this->oFileSystem->mkdir($fileDirectory);
			}

			$this->oFileSystem->dumpFile($fileDirectory . '/' . $fileName, $this->content);
		}
	}

	/**
	 * @param InputInterface $oInput
	 * @param OutputInterface $oOutput
	 * @return int|null
	 */
	protected function execute(InputInterface $oInput, OutputInterface $oOutput): ?int
	{
		$this->setFileDir();
		$this->componentsDir = dirname(dirname(__FILE__)) . '/' . $this->componentsDir . '/';
		$this->autoloadDir = $oInput->getArgument($this->commandAutoloadDir);
		$this->oFileSystem = new Filesystem();

		if (!$this->oFileSystem->exists($this->autoloadDir)) {
			$oOutput->writeln('The auto-load directory does not exists', OutputInterface::VERBOSITY_NORMAL);

			return false;
		} else {
			$this->namespace = $oInput->getOption($this->commandOptionNameSpace);

			try {
				$this->createPostSkeletonComponent();
				$oOutput->writeln('Wiloke PHPUNIT has been setup successfully');
			}
			catch (\Exception $oE) {
				$oOutput->writeln($oE->getMessage(), OutputInterface::VERBOSITY_NORMAL);
			}
		}

		return true;
	}
}