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

class GitStash extends GitCommand
{
    protected $command = 'stash';

    public function run(array $args = array())
    {
        $subCommand = array_shift($args);
        $stash = array_shift($args);
        $args = array_shift($args);

        $command = $this->command;

        if (!empty($subCommand)) $command .= " {$subCommand}";
        switch ($subCommand) {
            case 'list':
                break;

            case 'show':
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'drop':
                if ($this->arg('quiet', $args)) $command .= ' --quiet';
                elseif ($this->arg('q', $args)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'pop':
            case 'apply':
                if ($this->arg('index', $args)) $command .= ' --index';
                if ($this->arg('quiet', $args)) $command .= ' --quiet';
                elseif ($this->arg('q', $args)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'branch':
                $branch = $this->arg('branch', $args);
                if (empty($branch) && $branch !== '0') throw new \RuntimeException('git stash branch requires a branch argument in Cast');
                $command .= " {$branch}";
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'clear':
                break;

            case 'create':
                break;

            case 'save':
            case '':
            case null:

            default:
                if ($this->arg('quiet', $args)) $command .= ' --quiet';
                elseif ($this->arg('q', $args)) $command .= ' -q';
                if ($this->arg('patch', $args) || $this->arg('p', $args)) throw new \RuntimeException('Cast does not support git interactive patch mode');
                if ($this->arg('keep-index', $args)) $command .= ' --keep-index';
                elseif ($this->arg('no-keep-index', $args)) $command .= ' --no-keep-index';
                if ($this->arg('include-untracked', $args)) $command .= ' --include-untracked';
                elseif ($this->arg('u', $args)) $command .= ' -u';
                if ($this->arg('all', $args)) $command .= ' --all';
                elseif ($this->arg('a', $args)) $command .= ' -a';
                if (($msg = $this->arg('message', $args))) $command .= " \"{$msg}\"";
                elseif (is_string($stash) && $stash !== '') $command .= " \"{$stash}\"";
                break;
        }

        return $this->git->exec($command);
    }
}
