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
            $paths = '.';
        }

        $command = $this->command;
        if ($this->opt('interactive', $opts) || $this->opt('i', $opts) || $this->opt('patch', $opts) || $this->opt('p', $opts)) {
            throw new GitCommandException($this, "git interactive patch selection not supported by Cast");
        }
        if ($this->opt('edit', $opts) || $this->opt('e', $opts)) {
            throw new GitCommandException($this, "git interactive patch editing not supported by Cast");
        }
        if ($this->opt('dry-run', $opts) || $this->opt('n', $opts)) {
            $command .= ' --dry-run';
            if ($this->opt('ignore-missing', $opts)) $command .= ' --ignore-missing';
        }
        if ($this->opt('verbose', $opts)) $command .= ' --verbose';
        elseif ($this->opt('v', $opts)) $command .= ' -v';
        if ($this->opt('force', $opts)) $command .= ' --force';
        elseif ($this->opt('f', $opts)) $command .= ' -f';
        if ($this->opt('update', $opts) || $this->opt('u', $opts)) {
            $command .= ' --update';
        } elseif ($this->opt('all', $opts) || $this->opt('A', $opts) || $this->opt('no-ignore-removal', $opts)) {
            $command .= ' --all';
        } elseif ($this->opt('no-all', $opts) || $this->opt('ignore-removal', $opts)) {
            $command .= ' --no-all';
        }
        if ($this->opt('intent-to-add', $opts)) $command .= ' --intent-to-add';
        elseif ($this->opt('N', $opts)) $command .= ' -N';
        if ($this->opt('refresh', $opts)) $command .= ' --refresh';
        if ($this->opt('ignore-errors', $opts)) $command .= ' --ignore-errors';
        if ($paths === '.') $command .= " {$paths}";
        elseif (!empty($paths)) $command .= " -- {$paths}";

        return $this->exec($command);
    }
}
