<?php namespace Illuminate\Session;

use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;

class FileSessionHandler implements \SessionHandlerInterface {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The path where sessions should be stored.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new file driven handler instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $path
	 * @return void
	 */
	public function __construct(Filesystem $files, $path)
	{
		$this->path = $path;
		$this->files = $files;
	}

	/**
	 * {@inheritDoc}
	 */
	public function open($savePath, $sessionName): bool
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function close(): bool
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function read($sessionId): string
	{
		if ($this->files->exists($path = $this->path.'/'.$sessionId))
		{
			return $this->files->get($path);
		}

		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function write($sessionId, $data): bool
	{
		return $this->files->put($this->path.'/'.$sessionId, $data, true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function destroy($sessionId): bool
	{
		return $this->files->delete($this->path.'/'.$sessionId);
	}

	/**
	 * {@inheritDoc}
	 */
	public function gc($lifetime): int|false
	{
		$files = Finder::create()
					->in($this->path)
					->files()
					->ignoreDotFiles(true)
					->date('<= now - '.$lifetime.' seconds');

		foreach ($files as $file)
		{
			$this->files->delete($file->getRealPath());
		}

		return true;
	}

}
