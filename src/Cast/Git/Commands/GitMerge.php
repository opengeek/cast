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

class GitMerge extends GitCommand
{
    protected $command = 'merge';

    public function run(array $args = array(), array $opts = array())
    {
        $commit = array_shift($args);

        $command = $this->command;
        if ($this->arg('abort', $opts)) {
            $command .= " --abort";
        } else {
            if ($this->arg('edit', $opts) || $this->arg('e', $opts)) {
                throw new \RuntimeException("git merge --edit not supported in Cast");
            } elseif ($this->arg('no-edit', $opts)) $command .= ' --no-edit';
            if ($this->arg('commit', $opts)) $command .= ' --commit';
            elseif ($this->arg('no-commit', $opts)) $command .= ' --no-commit';

            if ($this->arg('ff', $opts)) $command .= ' --ff';
            elseif ($this->arg('no-ff', $opts)) $command .= ' --no-ff'; elseif ($this->arg('ff-only', $opts)) $command .= ' --ff-only';

            if (($n = $this->arg('log', $opts))) {
                if ($n === true || (integer)$n === 0) $command .= ' --log';
                else $command .= " --log={$n}";
            }

            if ($this->arg('stat', $opts)) $command .= ' --stat';
            elseif ($this->arg('no-stat', $opts)) $command .= ' --no-stat'; elseif ($this->arg('n', $opts)) $command .= ' -n';

            if ($this->arg('squash', $opts)) $command .= ' --squash';
            elseif ($this->arg('no-squash', $opts)) $command .= ' --no-squash';

            if (($strategy = $this->arg('strategy', $opts))) {
                $command .= " --strategy={$strategy}";
                $this->setStrategyOptions($opts, $command);
            } elseif (($strategy = $this->arg('s', $opts))) {
                $command .= " -s {$strategy}";
                $this->setStrategyOptions($opts, $command);
            }

            if ($this->arg('verify-signatures', $opts)) $command .= ' --verify-signatures';
            elseif ($this->arg('no-verify-signatures', $opts)) $command .= ' --no-verify-signatures';

            if ($this->arg('summary', $opts)) $command .= ' --summary';
            elseif ($this->arg('no-summary', $opts)) $command .= ' --no-summary';

            if ($this->arg('quiet', $opts)) $command .= ' --quiet';
            elseif ($this->arg('q', $opts)) $command .= ' -q';

            if ($this->arg('verbose', $opts)) $command .= ' --verbose';
            elseif ($this->arg('v', $opts)) $command .= ' -v';

            if ($this->arg('progress', $opts)) $command .= ' --progress';
            elseif ($this->arg('no-progress', $opts)) $command .= ' --no-progress';

            if (($msg = $this->arg('m', $opts))) $command .= " -m \"{$msg}\"";

            if ($this->arg('rerere-autoupdate', $opts)) $command .= ' --rerere-autoupdate';
            elseif ($this->arg('no-rerere-autoupdate', $opts)) $command .= ' --no-rerere-autoupdate';

            if (!empty($commit)) {
                if (!is_array($commit)) $commit = array($commit);
                $command .= " -- " . implode(" ", $commit);
            }
        }

        return $this->exec($command);
    }

    protected function setStrategyOptions($opts, &$command)
    {
        if (($option = $this->arg('strategy-option', $opts))) $command .= " --strategy-option={$option}";
        elseif (($option = $this->arg('X', $opts))) $command .= " -X {$option}";
    }
}
