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
        if ($this->opt('abort', $opts)) {
            $command .= " --abort";
        } else {
            if ($this->opt('edit', $opts) || $this->opt('e', $opts)) {
                throw new \RuntimeException("git merge --edit not supported in Cast");
            } elseif ($this->opt('no-edit', $opts)) $command .= ' --no-edit';
            if ($this->opt('commit', $opts)) $command .= ' --commit';
            elseif ($this->opt('no-commit', $opts)) $command .= ' --no-commit';

            if ($this->opt('ff', $opts)) $command .= ' --ff';
            elseif ($this->opt('no-ff', $opts)) $command .= ' --no-ff'; elseif ($this->opt('ff-only', $opts)) $command .= ' --ff-only';

            if (($n = $this->opt('log', $opts))) {
                if ($n === true || (integer)$n === 0) $command .= ' --log';
                else $command .= " --log={$n}";
            }

            if ($this->opt('stat', $opts)) $command .= ' --stat';
            elseif ($this->opt('no-stat', $opts)) $command .= ' --no-stat'; elseif ($this->opt('n', $opts)) $command .= ' -n';

            if ($this->opt('squash', $opts)) $command .= ' --squash';
            elseif ($this->opt('no-squash', $opts)) $command .= ' --no-squash';

            if (($strategy = $this->opt('strategy', $opts))) {
                $command .= " --strategy={$strategy}";
                $this->setStrategyOptions($opts, $command);
            } elseif (($strategy = $this->opt('s', $opts))) {
                $command .= " -s {$strategy}";
                $this->setStrategyOptions($opts, $command);
            }

            if ($this->opt('verify-signatures', $opts)) $command .= ' --verify-signatures';
            elseif ($this->opt('no-verify-signatures', $opts)) $command .= ' --no-verify-signatures';

            if ($this->opt('summary', $opts)) $command .= ' --summary';
            elseif ($this->opt('no-summary', $opts)) $command .= ' --no-summary';

            if ($this->opt('quiet', $opts)) $command .= ' --quiet';
            elseif ($this->opt('q', $opts)) $command .= ' -q';

            if ($this->opt('verbose', $opts)) $command .= ' --verbose';
            elseif ($this->opt('v', $opts)) $command .= ' -v';

            if ($this->opt('progress', $opts)) $command .= ' --progress';
            elseif ($this->opt('no-progress', $opts)) $command .= ' --no-progress';

            if (($msg = $this->opt('m', $opts))) $command .= " -m \"{$msg}\"";

            if ($this->opt('rerere-autoupdate', $opts)) $command .= ' --rerere-autoupdate';
            elseif ($this->opt('no-rerere-autoupdate', $opts)) $command .= ' --no-rerere-autoupdate';

            if (!empty($commit)) {
                if (!is_array($commit)) $commit = array($commit);
                $command .= " -- " . implode(" ", $commit);
            }
        }

        return $this->exec($command);
    }

    protected function setStrategyOptions($opts, &$command)
    {
        if (($option = $this->opt('strategy-option', $opts))) $command .= " --strategy-option={$option}";
        elseif (($option = $this->opt('X', $opts))) $command .= " -X {$option}";
    }
}
