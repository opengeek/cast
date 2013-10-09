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

        if ($this->opt('cached', $opts)) $command .= ' --cached';
        elseif ($this->opt('staged', $opts)) $command .= ' --staged';
        if ($this->opt('patch', $opts)) $command .= ' --patch';
        elseif ($this->opt('p', $opts)) $command .= ' -p'; elseif ($this->opt('u', $opts)) $command .= ' -u';

        if (($lines = $this->opt('U', $opts))) $command .= " -U{$lines}";
        elseif (($lines = $this->opt('unified', $opts))) $command .= " --unified={$lines}";

        if ($this->opt('raw', $opts)) $command .= ' --raw';
        if ($this->opt('patch-with-raw', $opts)) $command .= ' --patch-with-raw';

        if (($diffAlgorithm = $this->opt('diff-algorithm', $opts))) {
            $command .= " --diff-algorithm={$diffAlgorithm}";
        } elseif ($this->opt('minimal', $opts)) $command .= ' --minimal'; elseif ($this->opt('patience', $opts)) $command .= ' --patience'; elseif ($this->opt('histogram', $opts)) $command .= ' --histogram';

        if (($stat = $this->opt('stat', $opts))) {
            if ($stat === true) $command .= " --stat";
            else $command .= " --stat={$stat}";
        } elseif ($this->opt('numstat', $opts)) $command .= " --numstat"; elseif ($this->opt('shortstat', $opts)) $command .= " --shortstat";
        if (($dirStat = $this->opt('dirstat', $opts))) {
            if ($dirStat === true) $command .= " --dirstat";
            else $command .= " --dirstat={$dirStat}";
        }

        if ($this->opt('summary', $opts)) $command .= ' --summary';
        if ($this->opt('patch-with-stat', $opts)) $command .= ' --patch-with-stat';
        if ($this->opt('z', $opts)) $command .= ' -z';
        if ($this->opt('name-only', $opts)) $command .= ' --name-only';
        if ($this->opt('name-status', $opts)) $command .= ' --name-status';
        if (($submodule = $this->opt('submodule', $opts))) {
            if ($submodule === true) $command .= ' --submodule';
            else $command .= " --submodule={$submodule}";
        }

        if (($when = $this->opt('color', $opts))) {
            if ($when === true) $command .= " --color";
            else $command .= " --color={$when}";
        } elseif ($this->opt('no-color', $opts)) {
            $command .= " --no-color";
        }
        if (($mode = $this->opt('word-diff', $opts))) {
            if ($mode === true) $command .= " --word-diff";
            else $command .= " --word-diff={$mode}";
        }
        if (($regex = $this->opt('word-diff-regex', $opts)) && is_string($mode) && $mode !== '') {
            $command .= " --word-diff={$regex}";
        }
        if (($regex = $this->opt('color-words', $opts))) {
            if ($regex === true) $command .= " --color-words";
            else $command .= " --color-words={$regex}";
        }

        if ($this->opt('no-renames', $opts)) $command .= ' --no-renames';
        if ($this->opt('check', $opts)) $command .= ' --check';
        if ($this->opt('full-index', $opts)) $command .= ' --full-index';
        if ($this->opt('binary', $opts)) $command .= ' --binary';

        if (($n = $this->opt('abbrev', $opts))) {
            if ($n === true) $command .= ' --abbrev';
            else {
                $n = (integer)$n;
                $command .= " --abbrev={$n}";
            }
        }
        if (($n = $this->opt('break-rewrites', $opts))) {
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
        } elseif (($n = $this->opt('B', $opts))) {
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
        if (($n = $this->opt('find-renames', $opts))) {
            if ($n === true) $command .= ' --find-renames';
            else $command .= " --find-renames={$n}";
        } elseif (($n = $this->opt('M', $opts))) {
            if ($n === true) $command .= ' -M';
            else $command .= " -M{$n}";
        }
        if (($n = $this->opt('find-copies', $opts))) {
            if ($n === true) $command .= ' --find-copies';
            else $command .= " --find-copies={$n}";
        } elseif (($n = $this->opt('C', $opts))) {
            if ($n === true) $command .= ' -C';
            else $command .= " -C{$n}";
        }
        if ($this->opt('find-copies-harder', $opts)) $command .= ' --find-copies-harder';
        if ($this->opt('irreversible-delete', $opts)) $command .= ' --irreversible-delete';
        elseif ($this->opt('D', $opts)) $command .= ' -D';
        if (($num = (integer)$this->opt('l', $opts))) $command .= " -l{$num}";
        if (($filter = $this->opt('diff-filter', $opts))) {
            if ($filter === true) $command .= ' --diff-filter';
            else $command .= " --diff-filter={$filter}";
        }
        if (($string = $this->opt('S', $opts)) && is_string($string)) $command .= " -S{$string}";
        if (($regex = $this->opt('G', $opts)) && is_string($regex)) $command .= " -G{$regex}";
        if ($this->opt('pickaxe-all', $opts)) $command .= ' --pickaxe-all';
        if ($this->opt('pickaxe-regex', $opts)) $command .= ' --pickaxe-regex';
        if (($orderFile = $this->opt('O', $opts)) && is_string($orderFile)) $command .= " -O{$orderFile}";

        if ($this->opt('R', $opts)) $command .= ' -R';
        if (($path = $this->opt('relative', $opts))) {
            if ($path === true) $command .= ' --relative';
            else $command .= " --relative={$path}";
        }

        if ($this->opt('text', $opts)) $command .= ' --text';
        elseif ($this->opt('a', $opts)) $command .= ' -a';
        if ($this->opt('ignore-space-at-eol', $opts)) $command .= ' --ignore-space-at-eol';
        if ($this->opt('ignore-space-change', $opts)) $command .= ' --ignore-space-change';
        elseif ($this->opt('b', $opts)) $command .= ' -b';
        if ($this->opt('ignore-all-space', $opts)) $command .= ' --ignore-all-space';
        elseif ($this->opt('w', $opts)) $command .= ' -w';
        if (($lines = (integer)$this->opt('inter-hunk-context', $opts))) $command .= " --inter-hunk-context={$lines}";
        if ($this->opt('function-context', $opts)) $command .= ' --function-context';
        elseif ($this->opt('W', $opts)) $command .= ' -W';

        if ($this->opt('exit-code', $opts)) $command .= ' --exit-code';
        if ($this->opt('quiet', $opts)) $command .= ' --quiet';

        $command .= ' --no-ext-diff';

        if (($when = $this->opt('ignore-submodules', $opts))) {
            if ($when === true) $command .= " --ignore-submodules";
            else $command .= " --ignore-submodules={$when}";
        }
        if ($this->opt('textconv', $opts)) {
            $command .= " --textconv";
        } elseif ($this->opt('no-textconv', $opts)) {
            $command .= " --no-textconv";
        }

        if (($prefix = $this->opt('src-prefix', $opts))) $command .= " --src-prefix={$prefix}";
        if (($prefix = $this->opt('dst-prefix', $opts))) $command .= " --dst-prefix={$prefix}";
        if ($this->opt('no-prefix', $opts)) $command .= ' --no-prefix';

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
