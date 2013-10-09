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

    public function run(array $args = array(), array $opts = array())
    {
        $pathSpec = array_shift($args);

        if ($pathSpec === null || (is_string($pathSpec) && $pathSpec === '') || (!is_string($pathSpec) && !is_array($pathSpec))) {
            throw new \InvalidArgumentException("git rm requires at least one file argument");
        }
        if (!is_array($pathSpec)) {
            $pathSpec = array($pathSpec);
        }
        $paths = implode(" ", $pathSpec);

        $command = $this->command;
        if ($this->arg('dry-run', $opts)) $command .= ' --dry-run';
        elseif ($this->arg('n', $opts)) $command .= ' -n';
        if ($this->arg('force', $opts)) $command .= ' --force';
        elseif ($this->arg('f', $opts)) $command .= ' -f';
        if ($this->arg('quiet', $opts)) $command .= ' --quiet';
        elseif ($this->arg('q', $opts)) $command .= ' -q';
        if ($this->arg('r', $opts)) $command .= ' -r';
        if ($this->arg('cached', $opts)) $command .= ' --cached';
        if ($this->arg('ignore-unmatch', $opts)) $command .= ' --ignore-unmatch';
        $command .= " -- {$paths}";

        return $this->exec($command);
    }
}
