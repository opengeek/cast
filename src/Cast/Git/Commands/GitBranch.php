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

    public function run(array $args = array())
    {
        $commit = array_shift($args);
        $pattern = array_shift($args);
        $args = array_shift($args);

        if (array_intersect(array_keys($args), $this->setOptions)) {
            return $this->set($commit, $pattern, $args);
        } elseif (array_intersect(array_keys($args), $this->moveOptions)) {
            return $this->move($commit, $pattern, $args);
        } elseif (array_intersect(array_keys($args), $this->deleteOptions)) {
            return $this->delete($commit, $args);
        } elseif (array_intersect(array_keys($args), $this->listOptions)) {
            return $this->get($commit, $pattern, $args);
        }
    }

    public function get($commit = null, $pattern = null, $args = null)
    {
        $command = $this->command;
        if ($this->arg('list', $args)) $command .= " --list";
        if ($this->arg('all', $args) || $this->arg('a', $args)) {
            $command .= ' --all';
        } elseif ($this->arg('remotes', $args) || $this->arg('r', $args)) {
            $command .= ' --remotes';
        }
        if ($this->arg('verbose', $args) || $this->arg('v', $args) || $this->arg('vv', $args)) $command .= ' --verbose';
        if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
        if ($this->arg('merged', $args)) {
            $command .= ' --merged';
        } elseif ($this->arg('no-merged', $args)) {
            $command .= ' --no-merged';
        } elseif ($this->arg('contains', $args)) {
            $command .= ' --contains';
        }
        if (!empty($commit)) {
            $command .= " {$commit}";
        }
        if (!empty($pattern)) {
            if (!is_array($pattern)) $pattern = array($pattern);
            $command .= " " . implode(' ', $pattern);
        }

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    public function set($name, $startPoint = null, $args = null)
    {
        $command = $this->command;
        if (($upstream = $this->arg('set-upstream-to', $args)) !== false || ($upstream = $this->arg('u', $args))) {
            $command .= " --set-upstream-to={$upstream}";
        } elseif ($this->arg('set-upstream', $args)) {
            $command .= " --set-upstream";
        } elseif ($this->arg('track', $args)) {
            $command .= " --track";
        } elseif ($this->arg('no-track', $args)) {
            $command .= " --no-track";
        }
        if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= " --force";
        if ($this->arg('create-reflog', $args) || $this->arg('l', $args)) $command .= " -l";
        $command .= " {$name}";
        if (!empty($startPoint)) $command .= " {$startPoint}";

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    public function move($newBranch, $oldBranch = null, $args = null)
    {
        $command = $this->command;
        if ($this->arg('m', $args) || $this->arg('move', $args)) {
            $command .= " --move";
        } elseif ($this->arg('M', $args)) {
            $command .= " -M";
        }
        if (!empty($oldBranch)) $command .= " {$oldBranch}";
        $command .= " {$newBranch}";

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    public function delete($name, $args = null)
    {
        $command = $this->command;
        if ($this->arg('d', $args) || $this->arg('delete', $args)) {
            $command .= " --delete";
        } elseif ($this->arg('D', $args)) {
            $command .= " -D";
        }
        if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
        $command .= " {$name}";

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }
}
