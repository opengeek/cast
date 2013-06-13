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


class GitCheckout extends GitCommand
{
    protected $command = 'checkout';

    public function run(array $args = array())
    {
        $branch = array_shift($args);
        $paths = array_shift($args);
        $args = array_shift($args);

        $usePaths = false;
        $separatePaths = false;
        $command = $this->command;
        if ($this->arg('quiet', $args) || $this->arg('q', $args)) $command .= " --quiet";
        if ($this->arg('detach', $args)) {
            if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
            if ($this->arg('merge', $args) || $this->arg('m', $args)) $command .= " --merge";
            $command .= ' --detach';
        } elseif ($this->arg('b', $args)) {
            if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
            if ($this->arg('merge', $args) || $this->arg('m', $args)) $command .= " --merge";
            $command .= " -b";
            $usePaths = true;
        } elseif ($this->arg('B', $args)) {
            if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
            if ($this->arg('merge', $args) || $this->arg('m', $args)) $command .= " --merge";
            $command .= " -B";
            $usePaths = true;
        } elseif ($this->arg('orphan', $args)) {
            if ($this->arg('force', $args) || $this->arg('f', $args)) $command .= ' --force';
            if ($this->arg('merge', $args) || $this->arg('m', $args)) $command .= " --merge";
            $command .= " --orphan";
            $usePaths = true;
        } elseif ($this->arg('ours', $args)) {
            $command .= " --ours";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('theirs', $args)) {
            $command .= " --theirs";
            $usePaths = true;
            $separatePaths = true;
        } elseif (($conflictStyle = $this->arg('conflict', $args))) {
            $command .= " --conflict={$conflictStyle}";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('merge', $args) || $this->arg('m', $args)) {
            $command .= " --merge";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('force', $args) || $this->arg('f', $args)) {
            $command .= " --force";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('patch', $args) || $this->arg('p', $args)) {
            $command .= " --patch";
            $usePaths = true;
            $separatePaths = true;
        }
        if (!empty($branch)) {
            $command .= " {$branch}";
        }
        if ($usePaths && !empty($paths)) {
            if (!is_array($paths)) $paths = array($paths);
            $command .= ($separatePaths ? " -- " : " ") . implode(" ", $paths);
        }

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }
}
