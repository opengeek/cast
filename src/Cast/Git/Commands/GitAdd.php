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

    public function run(array $args = array(), array $opts = array())
    {
        $pathSpec = array_shift($args);

        if ($pathSpec !== null) {
            if ((is_string($pathSpec) && $pathSpec === '') || (is_array($pathSpec)) && empty($pathSpec)) {
                $pathSpec = '.';
            }
            if (!is_array($pathSpec)) {
                $pathSpec = array($pathSpec);
            }
            $paths = implode(" ", $pathSpec);
        } else {
            $paths = '';
        }

        $command = $this->command;
        if ($this->arg('interactive', $opts) || $this->arg('i', $opts) || $this->arg('patch', $opts) || $this->arg('p', $opts)) {
            throw new \RuntimeException("git interactive patch selection not supported by Cast");
        }
        if ($this->arg('edit', $opts) || $this->arg('e', $opts)) {
            throw new \RuntimeException("git interactive patch editing not supported by Cast");
        }
        if ($this->arg('dry-run', $opts) || $this->arg('n', $opts)) {
            $command .= ' --dry-run';
            if ($this->arg('ignore-missing', $opts)) $command .= ' --ignore-missing';
        }
        if ($this->arg('verbose', $opts)) $command .= ' --verbose';
        elseif ($this->arg('v', $opts)) $command .= ' -v';
        if ($this->arg('force', $opts)) $command .= ' --force';
        elseif ($this->arg('f', $opts)) $command .= ' -f';
        if ($this->arg('update', $opts) || $this->arg('u', $opts)) {
            $command .= ' --update';
        } elseif ($this->arg('all', $opts) || $this->arg('A', $opts) || $this->arg('no-ignore-removal', $opts)) {
            $command .= ' --all';
        } elseif ($this->arg('no-all', $opts) || $this->arg('ignore-removal', $opts)) {
            $command .= ' --no-all';
        }
        if ($this->arg('intent-to-add', $opts)) $command .= ' --intent-to-add';
        elseif ($this->arg('N', $opts)) $command .= ' -N';
        if ($this->arg('refresh', $opts)) $command .= ' --refresh';
        if ($this->arg('ignore-errors', $opts)) $command .= ' --ignore-errors';
        if ($paths === '.') $command .= " {$paths}";
        elseif (!empty($paths)) $command .= " -- {$paths}";

        return $this->exec($command);
    }
}
