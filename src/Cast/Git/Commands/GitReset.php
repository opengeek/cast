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

    public function run(array $args = array())
    {
        $treeish = array_shift($args);
        $pathSpec = array_shift($args);
        $args = array_shift($args);

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
        if ($this->arg('patch', $args) || $this->arg('p', $args)) {
            throw new \RuntimeException("git interactive patch mode not supported in Cast");
        }
        if ($this->arg('q', $args)) $command .= ' --q';
        elseif ($this->arg('quiet', $args)) $command .= ' --quiet';

        $ignorePaths = true;
        if ($this->arg('soft', $args)) {
            $command .= ' --soft';
        } elseif ($this->arg('mixed', $args)) {
            $command .= ' --mixed';
        } elseif ($this->arg('hard', $args)) {
            $command .= ' --hard';
        } elseif ($this->arg('merge', $args)) {
            $command .= ' --merge';
        } elseif ($this->arg('keep', $args)) {
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
