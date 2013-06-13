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

    public function run(array $args = array())
    {
        $commit = array_shift($args);
        $args = array_shift($args);

        return $this->get($commit, $args);
    }

    public function get($commit = null, $args = null)
    {
        $command = $this->command;
        if ($this->arg('all', $args)) {
            $command .= ' --all';
        } elseif ($this->arg('remotes', $args)) {
            $command .= ' --remotes';
        }
        if ($this->arg('verbose', $args)) $command .= ' --verbose';
        if ($this->arg('force', $args)) $command .= ' --force';
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
        if (($pattern = $this->arg('pattern', $args))) {
            if (!is_array($pattern)) $pattern = array($pattern);
            $command .= " " . implode(' ', $pattern);
        }

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    public function create($name, $startPoint = null, $args = null)
    {

    }

    public function delete($name, $args = null)
    {

    }
}
