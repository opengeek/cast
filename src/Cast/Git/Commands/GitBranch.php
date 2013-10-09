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

class GitBranch extends GitCommand
{
    protected $command = 'branch';

    protected $listOptions = array(
        'color',
        'no-color',
        'remotes', 'r',
        'all', 'a',
        'verbose', 'v',
        'merged',
        'no-merged',
        'contains'
    );

    protected $setOptions = array(
        'set-upstream',
        'unset-upstream',
        'set-upstream-to', 'u',
        'track',
        'no-track',
        'create-reflog', 'l',
        'force', 'f',
        'edit-description'
    );

    protected $moveOptions = array(
        'move', 'm',
        'M'
    );

    protected $deleteOptions = array(
        'delete', 'd',
        'D'
    );

    public function run(array $args = array(), array $opts = array())
    {
        $commit = array_shift($args);
        $pattern = array_shift($args);

        if (array_intersect(array_keys($opts), $this->setOptions)) {
            return $this->set($commit, $pattern, $opts);
        } elseif (array_intersect(array_keys($opts), $this->moveOptions)) {
            return $this->move($commit, $pattern, $opts);
        } elseif (array_intersect(array_keys($opts), $this->deleteOptions)) {
            return $this->delete($commit, $opts);
        } elseif (array_intersect(array_keys($opts), $this->listOptions)) {
            return $this->get($commit, $pattern, $opts);
        }
    }

    public function get($commit = null, $pattern = null, $opts = null)
    {
        $command = $this->command;
        if ($this->opt('list', $opts)) $command .= " --list";
        if ($this->opt('all', $opts) || $this->opt('a', $opts)) {
            $command .= ' --all';
        } elseif ($this->opt('remotes', $opts) || $this->opt('r', $opts)) {
            $command .= ' --remotes';
        }
        if ($this->opt('verbose', $opts) || $this->opt('v', $opts) || $this->opt('vv', $opts)) $command .= ' --verbose';
        if ($this->opt('force', $opts) || $this->opt('f', $opts)) $command .= ' --force';
        if ($this->opt('merged', $opts)) {
            $command .= ' --merged';
        } elseif ($this->opt('no-merged', $opts)) {
            $command .= ' --no-merged';
        } elseif ($this->opt('contains', $opts)) {
            $command .= ' --contains';
        }
        if (!empty($commit)) {
            $command .= " {$commit}";
        }
        if (!empty($pattern)) {
            if (!is_array($pattern)) $pattern = array($pattern);
            $command .= " " . implode(' ', $pattern);
        }

        return $this->exec($command);
    }

    public function set($name, $startPoint = null, $opts = null)
    {
        $command = $this->command;
        if (($upstream = $this->opt('set-upstream-to', $opts)) !== false || ($upstream = $this->opt('u', $opts))) {
            $command .= " --set-upstream-to={$upstream}";
        } elseif ($this->opt('set-upstream', $opts)) {
            $command .= " --set-upstream";
        } elseif ($this->opt('track', $opts)) {
            $command .= " --track";
        } elseif ($this->opt('no-track', $opts)) {
            $command .= " --no-track";
        }
        if ($this->opt('force', $opts) || $this->opt('f', $opts)) $command .= " --force";
        if ($this->opt('create-reflog', $opts) || $this->opt('l', $opts)) $command .= " -l";
        $command .= " {$name}";
        if (!empty($startPoint)) $command .= " {$startPoint}";

        return $this->exec($command);
    }

    public function move($newBranch, $oldBranch = null, $opts = null)
    {
        $command = $this->command;
        if ($this->opt('m', $opts) || $this->opt('move', $opts)) {
            $command .= " --move";
        } elseif ($this->opt('M', $opts)) {
            $command .= " -M";
        }
        if (!empty($oldBranch)) $command .= " {$oldBranch}";
        $command .= " {$newBranch}";

        return $this->exec($command);
    }

    public function delete($name, $opts = null)
    {
        $command = $this->command;
        if ($this->opt('d', $opts) || $this->opt('delete', $opts)) {
            $command .= " --delete";
        } elseif ($this->opt('D', $opts)) {
            $command .= " -D";
        }
        if ($this->opt('force', $opts) || $this->opt('f', $opts)) $command .= ' --force';
        $command .= " {$name}";

        return $this->exec($command);
    }
}
