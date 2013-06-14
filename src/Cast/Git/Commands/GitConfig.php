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

    public function run(array $args = array())
    {
        $arg1 = array_shift($args);
        $arg2 = array_shift($args);
        $arg3 = array_shift($args);

        if (!$this->arg('global', $args) && !$this->arg('system', $args) && !$this->git->isInitialized()) {
            throw new \BadMethodCallException();
        }

        $command = $this->command;
        if ($this->arg('global', $args)) $command .= " --global";
        elseif ($this->arg('system', $args)) $command .= " --system";
        elseif ($this->arg('local', $args)) $command .= " --local";
        elseif (($file = $this->arg('file', $args)) || ($file = $this->arg('f', $args))) {
            $command .= " --f {$file}";
        }

        if ($this->arg('get', $args)) {
            $this->_setReadArguments($command, $args);
        } elseif ($this->arg('get-all', $args)) {
            $this->_setReadArguments($command, $args);
        } elseif ($this->arg('get-regexp', $args)) {
            $this->_setReadArguments($command, $args);
        } elseif ($this->arg('list', $args)) {
            $this->_setReadArguments($command, $args);
        } elseif ($this->arg('add', $args)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->arg('replace-all', $args)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
            if (is_string($arg3) && $arg3 !== '') $command .= " {$arg3}";
        } elseif ($this->arg('unset', $args)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->arg('unset-all', $args)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset-all requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->arg('rename-section', $args)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty old_name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty new_name argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->arg('remove-section', $args)) {
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

        $response = $this->git->exec($command);
        if ($response[0] !== 0 && !empty($response[2])) {
            throw new \RuntimeException($response[2]);
        }
        return $response[1];
    }

    protected function _setReadArguments(&$command, $args)
    {
        if (!$this->arg('list', $args)) {
            $this->_setTypeArgument($command, $args);
        }
        if ($this->arg('null', $args)) $command .= " --null";
        elseif ($this->arg('z', $args)) $command .= " -z";

        if ($this->arg('includes', $args)) $command .= " --includes";
        elseif ($this->arg('no-includes', $args)) $command .= " --no-includes";
    }

    protected function _setTypeArgument(&$command, $args)
    {
        if ($this->arg('bool', $args)) $command .= " --bool";
        elseif ($this->arg('int', $args)) $command .= " --int";
        elseif ($this->arg('bool-or-int', $args)) $command .= " --bool-or-int";
    }
}
