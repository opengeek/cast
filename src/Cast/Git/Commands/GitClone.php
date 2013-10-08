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

class GitClone extends GitCommand
{
    protected $command = 'clone';

    public function run(array $args = array())
    {
        $repository = array_shift($args);
        $directory = array_shift($args);
        $args = array_shift($args);

        if ($this->git->isInitialized()) {
            throw new \RuntimeException("Cannot clone {$repository} into an existing repository at {$directory}");
        }

        $command = $this->command;
        if ($this->arg('local', $args)) $command .= ' --local';
        if ($this->arg('no-hardlinks', $args)) $command .= ' --no-hardlinks';
        if (($shared = $this->arg('shared', $args)) != false) {
            if (is_string($shared) || is_int($shared)) {
                $command .= " --shared={$shared}";
            }
            $command .= ' --shared';
        }
        if ($this->arg('quiet', $args)) $command .= ' --quiet';
        if ($this->arg('bare', $args)) $command .= ' --bare';
        if ($this->arg('mirror', $args)) $command .= ' --mirror';
        if (!$this->arg('bare', $args) && !$this->arg('mirror', $args)) $command .= " --mirror";
        if ($this->arg('no-checkout', $args)) $command .= " --no-checkout";
        if (($templateDirectory = $this->arg('template', $args)) !== false) $command .= " --template={$templateDirectory}";
        if ($directory === null) {
            if (($path = $this->git->getPath()) !== null) {
                if (Git::isValidRepositoryPath($path)) {
                    throw new \RuntimeException("Cannot clone a Git repository into {$path}; one already exists at that location.");
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
        $this->git->setBare((bool)$this->git->config->get('core.bare', array('type' => 'int')));
        return $response;
    }
}
