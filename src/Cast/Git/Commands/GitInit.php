<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git\Commands;

use Cast\Git\Git;

class GitInit extends GitCommand
{
    protected $command = 'init';

    public function run(array $args = array(), array $opts = array())
    {
        $directory = array_shift($args);

        if ($this->git->isInitialized()) {
            throw new \RuntimeException("Cannot re-initialize an existing repository at {$directory}");
        }

        $command = $this->command;
        if ($this->arg('quiet', $opts)) $command .= ' --quiet';
        if ($this->arg('bare', $opts)) $command .= ' --bare';
        if (($templateDirectory = $this->arg('template', $opts)) !== false) $command .= " --template={$templateDirectory}";
        if ($directory === null) {
            if (($path = $this->git->getPath()) !== null) {
                if (Git::isValidRepositoryPath($path)) {
                    throw new \RuntimeException("Cannot initialize a Git repository at {$path}; one already exists at that location.");
                }
                $directory = $path;
            } else {
                $directory = ".";
            }
        }
        $command .= " {$directory}";

        $response = $this->git->exec($command);

        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        $this->git->setPath($directory);
        $this->git->setInitialized();
        $this->git->setBare((bool)$this->git->config('core.bare', null, null, array('type' => 'int')));
        return $response;
    }
}
