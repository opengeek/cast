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

    public function run(array $args = array(), array $opts = array())
    {
        $repository = array_shift($args);
        $directory = array_shift($args);

        if ($this->git->isInitialized()) {
            throw new \RuntimeException("Cannot clone {$repository} into an existing repository at {$directory}");
        }

        $command = $this->command;
        if ($this->opt('local', $opts)) $command .= ' --local';
        if ($this->opt('no-hardlinks', $opts)) $command .= ' --no-hardlinks';
        if (($shared = $this->opt('shared', $opts)) != false) {
            if (is_string($shared) || is_int($shared)) {
                $command .= " --shared={$shared}";
            }
            $command .= ' --shared';
        }
        if ($this->opt('quiet', $opts)) $command .= ' --quiet';
        if ($this->opt('bare', $opts)) $command .= ' --bare';
        if ($this->opt('mirror', $opts)) $command .= ' --mirror';
        if (!$this->opt('bare', $opts) && !$this->opt('mirror', $opts)) $command .= " --mirror";
        if ($this->opt('no-checkout', $opts)) $command .= " --no-checkout";
        if (($templateDirectory = $this->opt('template', $opts)) !== false) $command .= " --template={$templateDirectory}";
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
