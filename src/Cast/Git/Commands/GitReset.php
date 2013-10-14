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

class GitReset extends GitCommand
{
    protected $command = 'reset';

    public function run(array $args = array(), array $opts = array())
    {
        $treeish = array_shift($args);
        $pathSpec = array_shift($args);

        if (!is_string($treeish) || $treeish === '') {
            $treeish = 'HEAD';
        }

        $paths = '';
        if (is_string($pathSpec) || is_array($pathSpec)) {
            if (!is_array($pathSpec)) {
                $pathSpec = array($pathSpec);
            }
            $paths = implode(" ", $pathSpec);
        }

        $command = $this->command;
        if ($this->opt('patch', $opts) || $this->opt('p', $opts)) {
            throw new GitCommandException($this, "git interactive patch mode not supported in Cast");
        }
        if ($this->opt('q', $opts)) $command .= ' --q';
        elseif ($this->opt('quiet', $opts)) $command .= ' --quiet';

        $ignorePaths = true;
        if ($this->opt('soft', $opts)) {
            $command .= ' --soft';
        } elseif ($this->opt('mixed', $opts)) {
            $command .= ' --mixed';
        } elseif ($this->opt('hard', $opts)) {
            $command .= ' --hard';
        } elseif ($this->opt('merge', $opts)) {
            $command .= ' --merge';
        } elseif ($this->opt('keep', $opts)) {
            $command .= ' --keep';
        } else {
            $ignorePaths = false;
        }

        $command .= " {$treeish}";

        if (!$ignorePaths && (!empty($paths) || $paths === '0')) {
            $command .= " -- {$paths}";
        }

        return $this->exec($command);
    }
}
