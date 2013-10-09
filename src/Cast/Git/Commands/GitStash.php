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

    public function run(array $args = array(), array $opts = array())
    {
        $subCommand = array_shift($args);
        $stash = array_shift($args);

        $command = $this->command;

        if (!empty($subCommand)) $command .= " {$subCommand}";
        switch ($subCommand) {
            case 'list':
                break;

            case 'show':
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'drop':
                if ($this->arg('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->arg('q', $opts)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'pop':
            case 'apply':
                if ($this->arg('index', $opts)) $command .= ' --index';
                if ($this->arg('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->arg('q', $opts)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'branch':
                $branch = $this->arg('branch', $opts);
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
                if ($this->arg('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->arg('q', $opts)) $command .= ' -q';
                if ($this->arg('patch', $opts) || $this->arg('p', $opts)) throw new \RuntimeException('Cast does not support git interactive patch mode');
                if ($this->arg('keep-index', $opts)) $command .= ' --keep-index';
                elseif ($this->arg('no-keep-index', $opts)) $command .= ' --no-keep-index';
                if ($this->arg('include-untracked', $opts)) $command .= ' --include-untracked';
                elseif ($this->arg('u', $opts)) $command .= ' -u';
                if ($this->arg('all', $opts)) $command .= ' --all';
                elseif ($this->arg('a', $opts)) $command .= ' -a';
                if (($msg = $this->arg('message', $opts))) $command .= " \"{$msg}\"";
                elseif (is_string($stash) && $stash !== '') $command .= " \"{$stash}\"";
                break;
        }

        return $this->git->exec($command);
    }
}
