<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Common;
use Mygento\Jeeves\Generators\Crud;
use Mygento\Jeeves\IO\IOInterface;
use Symfony\Component\Filesystem\Filesystem;

class Generator extends Common
{
    protected $path;
    protected $io;
    private $converter;

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function setIO(IOInterface $io)
    {
        $this->io = $io;
    }

    protected function writeFile(string $path, string $content)
    {
        $fs = new Filesystem();
        $this->io->write(sprintf('Creating: <info>%s</info>.', $path));

        $fs->dumpFile($path, $content);
    }

    /**
     * Get Converter
     */
    protected function getConverter(): Crud\Common
    {
        if (null === $this->converter) {
            $this->converter = new Crud\Common();
        }

        return $this->converter;
    }
}
