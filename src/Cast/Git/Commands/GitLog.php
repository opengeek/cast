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

class GitLog extends GitCommand
{
    protected $command = 'log';

    public function run(array $args = array())
    {
        $revisionRange = array_shift($args);
        $paths = array_shift($args);
        $args = array_shift($args);

        $command = $this->command;
        if (($maxCount = (integer)$this->arg('max-count', $args)) !== 0) $command .= " --max-count={$maxCount}";
        if ($this->arg('follow', $args)) $command .= " --follow";
        if ($this->arg('no-decorate', $args)) {
            $command .= " --no-decorate";
        } elseif (($decoration = $this->arg('decorate', $args)) !== false) {
            if (in_array($decoration, array('short', 'full', 'no'))) {
                $command .= " --decorate={$decoration}";
            } else {
                $command .= " --decorate";
            }
        }
        if ($this->arg('source', $args)) $command .= " --source";
        if ($this->arg('use-mailmap', $args)) $command .= " --use-mailmap";
        if ($this->arg('full-diff', $args)) $command .= " --full-diff";
        if ($this->arg('log-size', $args)) $command .= " --log-size";
        if (is_string($revisionRange) && !empty($revisionRange)) $command .= " {$revisionRange}";
        if (!empty($paths)) {
            if (!is_array($paths)) $paths = array($paths);
            $command .= " -- " . implode(" ", $paths);
        }

        return $this->git->exec($command);
    }
}
