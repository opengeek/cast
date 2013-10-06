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

class GitAdd extends GitCommand
{
    protected $command = 'add';

    public function run(array $args = array())
    {
        $pathSpec = array_shift($args);
        $args = array_shift($args);

        if ($pathSpec)
        if ((is_string($pathSpec) && $pathSpec === '') || (is_array($pathSpec)) && empty($pathSpec)) {
            $pathSpec = '.';
        }
        if (!is_array($pathSpec)) {
            $pathSpec = array($pathSpec);
        }
        $paths = implode(" ", $pathSpec);

        $command = $this->command;
        if ($this->arg('interactive', $args) || $this->arg('i', $args) || $this->arg('patch', $args) || $this->arg('p', $args)) {
            throw new \RuntimeException("git interactive patch selection not supported by Cast");
        }
        if ($this->arg('edit', $args) || $this->arg('e', $args)) {
            throw new \RuntimeException("git interactive patch editing not supported by Cast");
        }
        if ($this->arg('dry-run', $args) || $this->arg('n', $args)) {
            $command .= ' --dry-run';
            if ($this->arg('ignore-missing', $args)) $command .= ' --ignore-missing';
        }
        if ($this->arg('verbose', $args)) $command .= ' --verbose';
        elseif ($this->arg('v', $args)) $command .= ' -v';
        if ($this->arg('force', $args)) $command .= ' --force';
        elseif ($this->arg('f', $args)) $command .= ' -f';
        if ($this->arg('update', $args) || $this->arg('u', $args)) {
            $command .= ' --update';
        } elseif ($this->arg('all', $args) || $this->arg('A', $args) || $this->arg('no-ignore-removal', $args)) {
            $command .= ' --all';
        } elseif ($this->arg('no-all', $args) || $this->arg('ignore-removal', $args)) {
            $command .= ' --no-all';
        }
        if ($this->arg('intent-to-add', $args)) $command .= ' --intent-to-add';
        elseif ($this->arg('N', $args)) $command .= ' -N';
        if ($this->arg('refresh', $args)) $command .= ' --refresh';
        if ($this->arg('ignore-errors', $args)) $command .= ' --ignore-errors';
        if ($paths === '.') $command .= " {$paths}";
        else $command .= " -- {$paths}";

        return $this->exec($command);
    }
}
