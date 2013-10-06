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

class GitRm extends GitCommand
{
    protected $command = 'rm';

    public function run(array $args = array())
    {
        $pathSpec = array_shift($args);
        $args = array_shift($args);

        if ($pathSpec === null || (is_string($pathSpec) && $pathSpec === '') || (!is_string($pathSpec) && !is_array($pathSpec))) {
            throw new \InvalidArgumentException("git rm requires at least one file argument");
        }
        if (!is_array($pathSpec)) {
            $pathSpec = array($pathSpec);
        }
        $paths = implode(" ", $pathSpec);

        $command = $this->command;
        if ($this->arg('dry-run', $args)) $command .= ' --dry-run';
        elseif ($this->arg('n', $args)) $command .= ' -n';
        if ($this->arg('force', $args)) $command .= ' --force';
        elseif ($this->arg('f', $args)) $command .= ' -f';
        if ($this->arg('quiet', $args)) $command .= ' --quiet';
        elseif ($this->arg('q', $args)) $command .= ' -q';
        if ($this->arg('r', $args)) $command .= ' -r';
        if ($this->arg('cached', $args)) $command .= ' --cached';
        if ($this->arg('ignore-unmatch', $args)) $command .= ' --ignore-unmatch';
        $command .= " -- {$paths}";

        return $this->exec($command);
    }
}
