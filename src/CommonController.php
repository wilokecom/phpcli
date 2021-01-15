<?php


namespace WilokeCommandLine;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

abstract class CommonController extends Command
{
	protected $namespace;
	protected $content;
	protected $relativeTargetFileDir;
	protected $componentsDir;
	protected $msg            = 'Congrats! The component has been created successfully';
	protected $helperMsg      = 'The setup has been cancelled due to the component is already existed';
	protected $helperQuestion = 'The component has been setup already, Do you want to override it?';

	protected $oInput;
	protected $oOutput;

	/**
	 * @var string $autoloadDir
	 */
	protected $autoloadDir = 'app';

	/**
	 * @var Filesystem
	 */
	protected $oFileSystem;
	protected $originalNamespace;
	protected $originalRelativeFileDir;

	public abstract function setOriginalRelativeDir();

	public function setRelativeTargetFileDir($dir = ''): CommonController
	{
		if (empty($dir)) {
			$this->relativeTargetFileDir = $this->originalRelativeFileDir;
		} else {
			$this->relativeTargetFileDir = $dir;
		}

		return $this;
	}

	public function commonConfiguration($oInput, $oOutput): CommonController
	{
		$this->oInput = $oInput;
		$this->oOutput = $oOutput;
		$this->setOriginalRelativeDir();
		$this->setRelativeTargetFileDir();
		$this->oFileSystem = new Filesystem();

		return $this;
	}

	protected function outputMsg($status = 'success')
	{
		switch ($status) {
			case 'warning':
				$this->oOutput->writeln('<fg=yellow;options=bold>' . $this->msg . '</>');
				break;
			case 'danger':
				$this->oOutput->writeln('<fg=red;options=bold>' . $this->msg . '</>');
				break;
			default:
				$this->oOutput->writeln('<fg=green;options=bold>' . $this->msg . '</>');
				break;
		}
	}

	protected function isContinue($msg = '', $question = ''): bool
	{
		$question = empty($question) ? $this->helperQuestion : $question;

		$oHelper = $this->getHelper('question');
		$question .= ' yes|no:';
		$isConfirmed = new ConfirmationQuestion($question, false);

		if (!$oHelper->ask($this->oInput, $this->oOutput, $isConfirmed)) {
			$msg = empty($msg) ? $this->helperMsg : $msg;
			$this->msg = $msg;
			return false;
		}

		return true;
	}

	/**
	 * @var mixed
	 */
	protected $namespacePlaceholder = 'WilokeTest';

	public function trailingslashit($dir)
	{
		if (function_exists('trailingslashit')) {
			return trailingslashit($dir);
		}

		return rtrim($dir, '/') . '/';
	}

	public function untrailingslashit($dir)
	{
		if (function_exists('untrailingslashit')) {
			return untrailingslashit($dir);
		}

		return rtrim($dir, '/');
	}

	public function getAutoloadDir(): string
	{
		$this->autoloadDir = './' . trim($this->autoloadDir, '/') . '/';

		return $this->autoloadDir;
	}

	public function getAbsFileDir(): string
	{
		return $this->getAutoloadDir() . $this->relativeTargetFileDir;
	}

	protected function generateNamespace(): string
	{
		if (empty($this->originalNamespace)) {
			return '';
		}

		$this->namespace = $this->originalNamespace . '\\' . str_replace('/', '\\', $this->relativeTargetFileDir);

		return $this->namespace;
	}

	protected function getComponentsDir($componentDir = ''): string
	{
		return dirname(dirname(__FILE__)) . '/components/' . ($componentDir ? $componentDir : $this->componentsDir) . '/';
	}

	protected function dummyFile($fileDir, $target, $namespace = ''): bool
	{
		if (empty($namespace)) {
			$namespace = $this->generateNamespace();
		}

		$aPasteFileDir = explode('/', $fileDir);
		$filename = end($aPasteFileDir);
		$target = $this->trailingslashit($target);

		if (!empty($this->originalNamespace)) {
			$this->content = file_get_contents($fileDir);
			$this->replaceNamespace($namespace);
			$this->oFileSystem->dumpFile($target . $filename, $this->content);
		} else {
			$this->oFileSystem->copy($fileDir, $target . $filename);
		}

		return true;
	}

	public function recursiveCopy($originalDir, $targetDir, $namespace = '')
	{
		$originalDir = $this->trailingslashit($originalDir);
		$targetDir = $this->trailingslashit($targetDir);
		$aFiles = glob($originalDir . '*');

		if (empty($namespace)) {
			$namespace = $this->generateNamespace();
		}

		foreach ($aFiles as $fileDir) {
			$aPasteFileDir = explode('/', $fileDir);
			$file = end($aPasteFileDir);

			if (is_file($fileDir)) {
				$this->dummyFile($fileDir, $targetDir, $namespace);
			} else {
				$folder = $file;
				if (!$this->oFileSystem->exists($targetDir . $folder)) {
					$this->oFileSystem->mkdir($targetDir . $folder);
				}

				if (!empty($namespace)) {
					$namespace .= '\\' . $folder;
				}

				return array_map([$this, 'recursiveCopy'], [$originalDir . $folder], [$targetDir . $folder],
					[$namespace]);
			}
		}
	}

	protected function replaceNamespace($namespace = '')
	{
		$namespace = empty($namespace) ? $this->namespace : $namespace;
		if ($namespace) {
			$this->content = str_replace(
				[
					$this->namespacePlaceholder,
					'#namespace',
					'WilokeOriginalNamespace',
					'#use'
				],
				[
					$namespace,
					'namespace',
					$this->originalNamespace,
					'use'
				],
				$this->content
			);
		}

		return $this->content;
	}
}
