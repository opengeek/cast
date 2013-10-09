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

    public function run(array $args = array(), array $opts = array())
    {
        $revisionRange = array_shift($args);
        $paths = array_shift($args);

        $command = $this->command;
        if (($maxCount = (integer)$this->opt('max-count', $opts)) !== 0) $command .= " --max-count={$maxCount}";
        if ($this->opt('follow', $opts)) $command .= " --follow";
        if ($this->opt('no-decorate', $opts)) {
            $command .= " --no-decorate";
        } elseif (($decoration = $this->opt('decorate', $opts)) !== false) {
            if (in_array($decoration, array('short', 'full', 'no'))) {
                $command .= " --decorate={$decoration}";
            } else {
                $command .= " --decorate";
            }
        }
        if ($this->opt('source', $opts)) $command .= " --source";
        if ($this->opt('use-mailmap', $opts)) $command .= " --use-mailmap";
        if ($this->opt('full-diff', $opts)) $command .= " --full-diff";
        if ($this->opt('log-size', $opts)) $command .= " --log-size";
        if (is_string($revisionRange) && !empty($revisionRange)) $command .= " {$revisionRange}";
        if (!empty($paths)) {
            if (!is_array($paths)) $paths = array($paths);
            $command .= " -- " . implode(" ", $paths);
        }

        return $this->git->exec($command);
    }
}
