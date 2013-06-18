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

    public function run(array $args = array())
    {
        $commit = array_shift($args);
        $args = array_shift($args);

        $command = $this->command;
        if ($this->arg('abort', $args)) {
            $command .= " --abort";
        } else {
            if ($this->arg('edit', $args) || $this->arg('e', $args)) {
                throw new \RuntimeException("git merge --edit not supported in Cast");
            } elseif ($this->arg('no-edit', $args)) $command .= ' --no-edit';
            if ($this->arg('commit', $args)) $command .= ' --commit';
            elseif ($this->arg('no-commit', $args)) $command .= ' --no-commit';

            if ($this->arg('ff', $args)) $command .= ' --ff';
            elseif ($this->arg('no-ff', $args)) $command .= ' --no-ff';
            elseif ($this->arg('ff-only', $args)) $command .= ' --ff-only';

            if (($n = $this->arg('log', $args))) {
                if ($n === true || (integer)$n === 0) $command .= ' --log';
                else $command .= " --log={$n}";
            }

            if ($this->arg('stat', $args)) $command .= ' --stat';
            elseif ($this->arg('no-stat', $args)) $command .= ' --no-stat';
            elseif ($this->arg('n', $args)) $command .= ' -n';

            if ($this->arg('squash', $args)) $command .= ' --squash';
            elseif ($this->arg('no-squash', $args)) $command .= ' --no-squash';

            if (($strategy = $this->arg('strategy', $args))) {
                $command .= " --strategy={$strategy}";
                $this->setStrategyOptions($args, $command);
            }
            elseif (($strategy = $this->arg('s', $args))) {
                $command .= " -s {$strategy}";
                $this->setStrategyOptions($args, $command);
            }

            if ($this->arg('verify-signatures', $args)) $command .= ' --verify-signatures';
            elseif ($this->arg('no-verify-signatures', $args)) $command .= ' --no-verify-signatures';

            if ($this->arg('summary', $args)) $command .= ' --summary';
            elseif ($this->arg('no-summary', $args)) $command .= ' --no-summary';

            if ($this->arg('quiet', $args)) $command .= ' --quiet';
            elseif ($this->arg('q', $args)) $command .= ' -q';

            if ($this->arg('verbose', $args)) $command .= ' --verbose';
            elseif ($this->arg('v', $args)) $command .= ' -v';

            if ($this->arg('progress', $args)) $command .= ' --progress';
            elseif ($this->arg('no-progress', $args)) $command .= ' --no-progress';

            if (($msg = $this->arg('m', $args))) $command .= " -m \"{$msg}\"";

            if ($this->arg('rerere-autoupdate', $args)) $command .= ' --rerere-autoupdate';
            elseif ($this->arg('no-rerere-autoupdate', $args)) $command .= ' --no-rerere-autoupdate';

            if (!empty($commit)) {
                if (!is_array($commit)) $commit = array($commit);
                $command .= " -- " . implode(" ", $commit);
            }
        }

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    protected function setStrategyOptions($args, &$command)
    {
        if (($option = $this->arg('strategy-option', $args))) $command .= " --strategy-option={$option}";
        elseif (($option = $this->arg('X', $args))) $command .= " -X {$option}";
    }
}
