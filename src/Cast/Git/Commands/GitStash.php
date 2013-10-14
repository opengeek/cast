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
                if ($this->opt('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->opt('q', $opts)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'pop':
            case 'apply':
                if ($this->opt('index', $opts)) $command .= ' --index';
                if ($this->opt('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->opt('q', $opts)) $command .= ' -q';
                if (is_string($stash) && $stash !== '') $command .= " {$stash}";
                break;

            case 'branch':
                $branch = $this->opt('branch', $opts);
                if (empty($branch) && $branch !== '0') throw new GitCommandException($this, 'git stash branch requires a branch argument in Cast');
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
                if ($this->opt('quiet', $opts)) $command .= ' --quiet';
                elseif ($this->opt('q', $opts)) $command .= ' -q';
                if ($this->opt('patch', $opts) || $this->opt('p', $opts)) throw new GitCommandException($this, 'Cast does not support git interactive patch mode');
                if ($this->opt('keep-index', $opts)) $command .= ' --keep-index';
                elseif ($this->opt('no-keep-index', $opts)) $command .= ' --no-keep-index';
                if ($this->opt('include-untracked', $opts)) $command .= ' --include-untracked';
                elseif ($this->opt('u', $opts)) $command .= ' -u';
                if ($this->opt('all', $opts)) $command .= ' --all';
                elseif ($this->opt('a', $opts)) $command .= ' -a';
                if (($msg = $this->opt('message', $opts))) $command .= " \"{$msg}\"";
                elseif (is_string($stash) && $stash !== '') $command .= " \"{$stash}\"";
                break;
        }

        return $this->git->exec($command);
    }
}
