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
        if ($this->arg('list', $opts)) $command .= " --list";
        if ($this->arg('all', $opts) || $this->arg('a', $opts)) {
            $command .= ' --all';
        } elseif ($this->arg('remotes', $opts) || $this->arg('r', $opts)) {
            $command .= ' --remotes';
        }
        if ($this->arg('verbose', $opts) || $this->arg('v', $opts) || $this->arg('vv', $opts)) $command .= ' --verbose';
        if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
        if ($this->arg('merged', $opts)) {
            $command .= ' --merged';
        } elseif ($this->arg('no-merged', $opts)) {
            $command .= ' --no-merged';
        } elseif ($this->arg('contains', $opts)) {
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
        if (($upstream = $this->arg('set-upstream-to', $opts)) !== false || ($upstream = $this->arg('u', $opts))) {
            $command .= " --set-upstream-to={$upstream}";
        } elseif ($this->arg('set-upstream', $opts)) {
            $command .= " --set-upstream";
        } elseif ($this->arg('track', $opts)) {
            $command .= " --track";
        } elseif ($this->arg('no-track', $opts)) {
            $command .= " --no-track";
        }
        if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= " --force";
        if ($this->arg('create-reflog', $opts) || $this->arg('l', $opts)) $command .= " -l";
        $command .= " {$name}";
        if (!empty($startPoint)) $command .= " {$startPoint}";

        return $this->exec($command);
    }

    public function move($newBranch, $oldBranch = null, $opts = null)
    {
        $command = $this->command;
        if ($this->arg('m', $opts) || $this->arg('move', $opts)) {
            $command .= " --move";
        } elseif ($this->arg('M', $opts)) {
            $command .= " -M";
        }
        if (!empty($oldBranch)) $command .= " {$oldBranch}";
        $command .= " {$newBranch}";

        return $this->exec($command);
    }

    public function delete($name, $opts = null)
    {
        $command = $this->command;
        if ($this->arg('d', $opts) || $this->arg('delete', $opts)) {
            $command .= " --delete";
        } elseif ($this->arg('D', $opts)) {
            $command .= " -D";
        }
        if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
        $command .= " {$name}";

        return $this->exec($command);
    }
}
