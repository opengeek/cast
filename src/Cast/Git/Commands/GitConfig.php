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

class GitConfig extends GitCommand
{
    protected $command = 'config';

    public function run(array $args = array(), array $opts = array())
    {
        $arg1 = array_shift($args);
        $arg2 = array_shift($args);
        $arg3 = array_shift($args);

        if (!$this->arg('global', $opts) && !$this->arg('system', $opts) && !$this->git->isInitialized()) {
            throw new \BadMethodCallException();
        }

        $command = $this->command;
        if ($this->arg('global', $opts)) $command .= " --global";
        elseif ($this->arg('system', $opts)) $command .= " --system"; elseif ($this->arg('local', $opts)) $command .= " --local"; elseif (($file = $this->arg('file', $opts)) || ($file = $this->arg('f', $opts))) {
            $command .= " --f {$file}";
        }

        if ($this->arg('get', $opts)) {
            $this->_setReadArguments($command, $opts);
        } elseif ($this->arg('get-all', $opts)) {
            $this->_setReadArguments($command, $opts);
        } elseif ($this->arg('get-regexp', $opts)) {
            $this->_setReadArguments($command, $opts);
        } elseif ($this->arg('list', $opts)) {
            $this->_setReadArguments($command, $opts);
        } elseif ($this->arg('add', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->arg('replace-all', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
            if (is_string($arg3) && $arg3 !== '') $command .= " {$arg3}";
        } elseif ($this->arg('unset', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->arg('unset-all', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset-all requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->arg('rename-section', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty old_name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty new_name argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->arg('remove-section', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --remove-section requires a non-empty name argument');
            }
            $command .= " {$arg1}";
        } else {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
            if (is_string($arg3) && $arg3 !== '') $command .= " {$arg3}";
        }

        return $this->exec($command);
    }

    protected function _setReadArguments(&$command, $opts)
    {
        if (!$this->arg('list', $opts)) {
            $this->_setTypeArgument($command, $opts);
        }
        if ($this->arg('null', $opts)) $command .= " --null";
        elseif ($this->arg('z', $opts)) $command .= " -z";

        if ($this->arg('includes', $opts)) $command .= " --includes";
        elseif ($this->arg('no-includes', $opts)) $command .= " --no-includes";
    }

    protected function _setTypeArgument(&$command, $opts)
    {
        if ($this->arg('bool', $opts)) $command .= " --bool";
        elseif ($this->arg('int', $opts)) $command .= " --int"; elseif ($this->arg('bool-or-int', $opts)) $command .= " --bool-or-int";
    }
}
