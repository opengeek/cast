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

    public function run(array $args = array(), array $opts = array())
    {
        $arg1 = array_shift($args);
        $arg2 = array_shift($args);
        $arg3 = array_shift($args);

        $command = $this->command;

        if ($this->arg('cached', $opts)) $command .= ' --cached';
        elseif ($this->arg('staged', $opts)) $command .= ' --staged';
        if ($this->arg('patch', $opts)) $command .= ' --patch';
        elseif ($this->arg('p', $opts)) $command .= ' -p'; elseif ($this->arg('u', $opts)) $command .= ' -u';

        if (($lines = $this->arg('U', $opts))) $command .= " -U{$lines}";
        elseif (($lines = $this->arg('unified', $opts))) $command .= " --unified={$lines}";

        if ($this->arg('raw', $opts)) $command .= ' --raw';
        if ($this->arg('patch-with-raw', $opts)) $command .= ' --patch-with-raw';

        if (($diffAlgorithm = $this->arg('diff-algorithm', $opts))) {
            $command .= " --diff-algorithm={$diffAlgorithm}";
        } elseif ($this->arg('minimal', $opts)) $command .= ' --minimal'; elseif ($this->arg('patience', $opts)) $command .= ' --patience'; elseif ($this->arg('histogram', $opts)) $command .= ' --histogram';

        if (($stat = $this->arg('stat', $opts))) {
            if ($stat === true) $command .= " --stat";
            else $command .= " --stat={$stat}";
        } elseif ($this->arg('numstat', $opts)) $command .= " --numstat"; elseif ($this->arg('shortstat', $opts)) $command .= " --shortstat";
        if (($dirStat = $this->arg('dirstat', $opts))) {
            if ($dirStat === true) $command .= " --dirstat";
            else $command .= " --dirstat={$dirStat}";
        }

        if ($this->arg('summary', $opts)) $command .= ' --summary';
        if ($this->arg('patch-with-stat', $opts)) $command .= ' --patch-with-stat';
        if ($this->arg('z', $opts)) $command .= ' -z';
        if ($this->arg('name-only', $opts)) $command .= ' --name-only';
        if ($this->arg('name-status', $opts)) $command .= ' --name-status';
        if (($submodule = $this->arg('submodule', $opts))) {
            if ($submodule === true) $command .= ' --submodule';
            else $command .= " --submodule={$submodule}";
        }

        if (($when = $this->arg('color', $opts))) {
            if ($when === true) $command .= " --color";
            else $command .= " --color={$when}";
        } elseif ($this->arg('no-color', $opts)) {
            $command .= " --no-color";
        }
        if (($mode = $this->arg('word-diff', $opts))) {
            if ($mode === true) $command .= " --word-diff";
            else $command .= " --word-diff={$mode}";
        }
        if (($regex = $this->arg('word-diff-regex', $opts)) && is_string($mode) && $mode !== '') {
            $command .= " --word-diff={$regex}";
        }
        if (($regex = $this->arg('color-words', $opts))) {
            if ($regex === true) $command .= " --color-words";
            else $command .= " --color-words={$regex}";
        }

        if ($this->arg('no-renames', $opts)) $command .= ' --no-renames';
        if ($this->arg('check', $opts)) $command .= ' --check';
        if ($this->arg('full-index', $opts)) $command .= ' --full-index';
        if ($this->arg('binary', $opts)) $command .= ' --binary';

        if (($n = $this->arg('abbrev', $opts))) {
            if ($n === true) $command .= ' --abbrev';
            else {
                $n = (integer)$n;
                $command .= " --abbrev={$n}";
            }
        }
        if (($n = $this->arg('break-rewrites', $opts))) {
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
        } elseif (($n = $this->arg('B', $opts))) {
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
        if (($n = $this->arg('find-renames', $opts))) {
            if ($n === true) $command .= ' --find-renames';
            else $command .= " --find-renames={$n}";
        } elseif (($n = $this->arg('M', $opts))) {
            if ($n === true) $command .= ' -M';
            else $command .= " -M{$n}";
        }
        if (($n = $this->arg('find-copies', $opts))) {
            if ($n === true) $command .= ' --find-copies';
            else $command .= " --find-copies={$n}";
        } elseif (($n = $this->arg('C', $opts))) {
            if ($n === true) $command .= ' -C';
            else $command .= " -C{$n}";
        }
        if ($this->arg('find-copies-harder', $opts)) $command .= ' --find-copies-harder';
        if ($this->arg('irreversible-delete', $opts)) $command .= ' --irreversible-delete';
        elseif ($this->arg('D', $opts)) $command .= ' -D';
        if (($num = (integer)$this->arg('l', $opts))) $command .= " -l{$num}";
        if (($filter = $this->arg('diff-filter', $opts))) {
            if ($filter === true) $command .= ' --diff-filter';
            else $command .= " --diff-filter={$filter}";
        }
        if (($string = $this->arg('S', $opts)) && is_string($string)) $command .= " -S{$string}";
        if (($regex = $this->arg('G', $opts)) && is_string($regex)) $command .= " -G{$regex}";
        if ($this->arg('pickaxe-all', $opts)) $command .= ' --pickaxe-all';
        if ($this->arg('pickaxe-regex', $opts)) $command .= ' --pickaxe-regex';
        if (($orderFile = $this->arg('O', $opts)) && is_string($orderFile)) $command .= " -O{$orderFile}";

        if ($this->arg('R', $opts)) $command .= ' -R';
        if (($path = $this->arg('relative', $opts))) {
            if ($path === true) $command .= ' --relative';
            else $command .= " --relative={$path}";
        }

        if ($this->arg('text', $opts)) $command .= ' --text';
        elseif ($this->arg('a', $opts)) $command .= ' -a';
        if ($this->arg('ignore-space-at-eol', $opts)) $command .= ' --ignore-space-at-eol';
        if ($this->arg('ignore-space-change', $opts)) $command .= ' --ignore-space-change';
        elseif ($this->arg('b', $opts)) $command .= ' -b';
        if ($this->arg('ignore-all-space', $opts)) $command .= ' --ignore-all-space';
        elseif ($this->arg('w', $opts)) $command .= ' -w';
        if (($lines = (integer)$this->arg('inter-hunk-context', $opts))) $command .= " --inter-hunk-context={$lines}";
        if ($this->arg('function-context', $opts)) $command .= ' --function-context';
        elseif ($this->arg('W', $opts)) $command .= ' -W';

        if ($this->arg('exit-code', $opts)) $command .= ' --exit-code';
        if ($this->arg('quiet', $opts)) $command .= ' --quiet';

        $command .= ' --no-ext-diff';

        if (($when = $this->arg('ignore-submodules', $opts))) {
            if ($when === true) $command .= " --ignore-submodules";
            else $command .= " --ignore-submodules={$when}";
        }
        if ($this->arg('textconv', $opts)) {
            $command .= " --textconv";
        } elseif ($this->arg('no-textconv', $opts)) {
            $command .= " --no-textconv";
        }

        if (($prefix = $this->arg('src-prefix', $opts))) $command .= " --src-prefix={$prefix}";
        if (($prefix = $this->arg('dst-prefix', $opts))) $command .= " --dst-prefix={$prefix}";
        if ($this->arg('no-prefix', $opts)) $command .= ' --no-prefix';

        if (is_string($arg1) && $arg1 !== '') $command .= " {$arg1}";
        if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        if ((is_string($arg3) && $arg3 !== '') || (is_array($arg3) && !empty($arg3))) {
            if (!is_array($arg3)) $arg3 = array($arg3);
            $paths = implode(" ", $arg3);
            $command .= " -- {$paths}";
        }

        return $this->exec($command);
    }
}
