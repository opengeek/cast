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

    public function run(array $args = array(), array $opts = array())
    {
        $branch = array_shift($args);
        $paths = array_shift($args);

        $usePaths = false;
        $separatePaths = false;
        $command = $this->command;
        if ($this->arg('quiet', $opts) || $this->arg('q', $opts)) $command .= " --quiet";
        if ($this->arg('detach', $opts)) {
            if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
            if ($this->arg('merge', $opts) || $this->arg('m', $opts)) $command .= " --merge";
            $command .= ' --detach';
        } elseif ($this->arg('b', $opts)) {
            if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
            if ($this->arg('merge', $opts) || $this->arg('m', $opts)) $command .= " --merge";
            $command .= " -b";
            $usePaths = true;
        } elseif ($this->arg('B', $opts)) {
            if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
            if ($this->arg('merge', $opts) || $this->arg('m', $opts)) $command .= " --merge";
            $command .= " -B";
            $usePaths = true;
        } elseif ($this->arg('orphan', $opts)) {
            if ($this->arg('force', $opts) || $this->arg('f', $opts)) $command .= ' --force';
            if ($this->arg('merge', $opts) || $this->arg('m', $opts)) $command .= " --merge";
            $command .= " --orphan";
            $usePaths = true;
        } elseif ($this->arg('ours', $opts)) {
            $command .= " --ours";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('theirs', $opts)) {
            $command .= " --theirs";
            $usePaths = true;
            $separatePaths = true;
        } elseif (($conflictStyle = $this->arg('conflict', $opts))) {
            $command .= " --conflict={$conflictStyle}";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('merge', $opts) || $this->arg('m', $opts)) {
            $command .= " --merge";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('force', $opts) || $this->arg('f', $opts)) {
            $command .= " --force";
            $usePaths = true;
            $separatePaths = true;
        } elseif ($this->arg('patch', $opts) || $this->arg('p', $opts)) {
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

        return $this->exec($command);
    }
}
