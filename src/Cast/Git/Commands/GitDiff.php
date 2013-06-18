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

class GitDiff extends GitCommand
{
    protected $command = 'diff';

    public function run(array $args = array())
    {
        $arg1 = array_shift($args);
        $arg2 = array_shift($args);
        $arg3 = array_shift($args);
        $args = array_shift($args);

        $command = $this->command;

        if ($this->arg('cached', $args)) $command .= ' --cached';
        elseif ($this->arg('staged', $args)) $command .= ' --staged';
        if ($this->arg('patch', $args)) $command .= ' --patch';
        elseif ($this->arg('p', $args)) $command .= ' -p';
        elseif ($this->arg('u', $args)) $command .= ' -u';

        if (($lines = $this->arg('U', $args))) $command .= " -U{$lines}";
        elseif (($lines = $this->arg('unified', $args))) $command .= " --unified={$lines}";

        if ($this->arg('raw', $args)) $command .= ' --raw';
        if ($this->arg('patch-with-raw', $args)) $command .= ' --patch-with-raw';

        if (($diffAlgorithm = $this->arg('diff-algorithm', $args))) {
            $command .= " --diff-algorithm={$diffAlgorithm}";
        } elseif ($this->arg('minimal', $args)) $command .= ' --minimal';
        elseif ($this->arg('patience', $args)) $command .= ' --patience';
        elseif ($this->arg('histogram', $args)) $command .= ' --histogram';

        if (($stat = $this->arg('stat', $args))) {
            if ($stat === true) $command .= " --stat";
            else $command .= " --stat={$stat}";
        } elseif ($this->arg('numstat', $args)) $command .= " --numstat";
        elseif ($this->arg('shortstat', $args)) $command .= " --shortstat";
        if (($dirStat = $this->arg('dirstat', $args))) {
            if ($dirStat === true) $command .= " --dirstat";
            else $command .= " --dirstat={$dirStat}";
        }

        if ($this->arg('summary', $args)) $command .= ' --summary';
        if ($this->arg('patch-with-stat', $args)) $command .= ' --patch-with-stat';
        if ($this->arg('z', $args)) $command .= ' -z';
        if ($this->arg('name-only', $args)) $command .= ' --name-only';
        if ($this->arg('name-status', $args)) $command .= ' --name-status';
        if (($submodule = $this->arg('submodule', $args))) {
            if ($submodule === true) $command .= ' --submodule';
            else $command .= " --submodule={$submodule}";
        }

        if (($when = $this->arg('color', $args))) {
            if ($when === true) $command .= " --color";
            else $command .= " --color={$when}";
        } elseif ($this->arg('no-color', $args)) {
            $command .= " --no-color";
        }
        if (($mode = $this->arg('word-diff', $args))) {
            if ($mode === true) $command .= " --word-diff";
            else $command .= " --word-diff={$mode}";
        }
        if (($regex = $this->arg('word-diff-regex', $args)) && is_string($mode) && $mode !== '') {
            $command .= " --word-diff={$regex}";
        }
        if (($regex = $this->arg('color-words', $args))) {
            if ($regex === true) $command .= " --color-words";
            else $command .= " --color-words={$regex}";
        }

        if ($this->arg('no-renames', $args)) $command .= ' --no-renames';
        if ($this->arg('check', $args)) $command .= ' --check';
        if ($this->arg('full-index', $args)) $command .= ' --full-index';
        if ($this->arg('binary', $args)) $command .= ' --binary';

        if (($n = $this->arg('abbrev', $args))) {
            if ($n === true) $command .= ' --abbrev';
            else {
                $n = (integer)$n;
                $command .= " --abbrev={$n}";
            }
        }
        if (($n = $this->arg('break-rewrites', $args))) {
            if ($n === true) $command .= ' --break-rewrites';
            else {
                $exploded = explode('/', $n, 2);
                if (count($exploded) === 2) {
                    $n = $exploded[0];
                    $m = $exploded[1];
                    $command .= " --break-rewrites={$n}/{$m}";
                } else {
                    $command .= " --break-rewrites={$n}";
                }
            }
        } elseif (($n = $this->arg('B', $args))) {
            if ($n === true) $command .= ' -B';
            else {
                $exploded = explode('/', $n, 2);
                if (count($exploded) === 2 && strpos($n, '/') > 0) {
                    $n = $exploded[0];
                    $m = $exploded[1];
                    $command .= " -B{$n}/{$m}";
                } else {
                    $command .= " -B{$n}";
                }
            }
        }
        if (($n = $this->arg('find-renames', $args))) {
            if ($n === true) $command .= ' --find-renames';
            else $command .= " --find-renames={$n}";
        } elseif (($n = $this->arg('M', $args))) {
            if ($n === true) $command .= ' -M';
            else $command .= " -M{$n}";
        }
        if (($n = $this->arg('find-copies', $args))) {
            if ($n === true) $command .= ' --find-copies';
            else $command .= " --find-copies={$n}";
        } elseif (($n = $this->arg('C', $args))) {
            if ($n === true) $command .= ' -C';
            else $command .= " -C{$n}";
        }
        if ($this->arg('find-copies-harder', $args)) $command .= ' --find-copies-harder';
        if ($this->arg('irreversible-delete', $args)) $command .= ' --irreversible-delete';
        elseif ($this->arg('D', $args)) $command .= ' -D';
        if (($num = (integer)$this->arg('l', $args))) $command .= " -l{$num}";
        if (($filter = $this->arg('diff-filter', $args))) {
            if ($filter === true) $command .= ' --diff-filter';
            else $command .= " --diff-filter={$filter}";
        }
        if (($string = $this->arg('S', $args)) && is_string($string)) $command .= " -S{$string}";
        if (($regex = $this->arg('G', $args)) && is_string($regex)) $command .= " -G{$regex}";
        if ($this->arg('pickaxe-all', $args)) $command .= ' --pickaxe-all';
        if ($this->arg('pickaxe-regex', $args)) $command .= ' --pickaxe-regex';
        if (($orderFile = $this->arg('O', $args)) && is_string($orderFile)) $command .= " -O{$orderFile}";

        if ($this->arg('R', $args)) $command .= ' -R';
        if (($path = $this->arg('relative', $args))) {
            if ($path === true) $command .= ' --relative';
            else $command .= " --relative={$path}";
        }

        if ($this->arg('text', $args)) $command .= ' --text';
        elseif ($this->arg('a', $args)) $command .= ' -a';
        if ($this->arg('ignore-space-at-eol', $args)) $command .= ' --ignore-space-at-eol';
        if ($this->arg('ignore-space-change', $args)) $command .= ' --ignore-space-change';
        elseif ($this->arg('b', $args)) $command .= ' -b';
        if ($this->arg('ignore-all-space', $args)) $command .= ' --ignore-all-space';
        elseif ($this->arg('w', $args)) $command .= ' -w';
        if (($lines = (integer)$this->arg('inter-hunk-context', $args))) $command .= " --inter-hunk-context={$lines}";
        if ($this->arg('function-context', $args)) $command .= ' --function-context';
        elseif ($this->arg('W', $args)) $command .= ' -W';

        if ($this->arg('exit-code', $args)) $command .= ' --exit-code';
        if ($this->arg('quiet', $args)) $command .= ' --quiet';

        $command .= ' --no-ext-diff';

        if (($when = $this->arg('ignore-submodules', $args))) {
            if ($when === true) $command .= " --ignore-submodules";
            else $command .= " --ignore-submodules={$when}";
        }
        if ($this->arg('textconv', $args)) {
            $command .= " --textconv";
        } elseif ($this->arg('no-textconv', $args)) {
            $command .= " --no-textconv";
        }

        if (($prefix = $this->arg('src-prefix', $args))) $command .= " --src-prefix={$prefix}";
        if (($prefix = $this->arg('dst-prefix', $args))) $command .= " --dst-prefix={$prefix}";
        if ($this->arg('no-prefix', $args)) $command .= ' --no-prefix';

        if (is_string($arg1) && $arg1 !== '') $command .= " {$arg1}";
        if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        if ((is_string($arg3) && $arg3 !== '') || (is_array($arg3) && !empty($arg3))) {
            if (!is_array($arg3)) $arg3 = array($arg3);
            $paths = implode(" ", $arg3);
            $command .= " -- {$paths}";
        }

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }
}
