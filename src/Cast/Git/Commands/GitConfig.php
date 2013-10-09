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

        if (!$this->opt('global', $opts) && !$this->opt('system', $opts) && !$this->git->isInitialized()) {
            throw new \BadMethodCallException();
        }

        $command = $this->command;
        if ($this->opt('global', $opts)) $command .= " --global";
        elseif ($this->opt('system', $opts)) $command .= " --system"; elseif ($this->opt('local', $opts)) $command .= " --local"; elseif (($file = $this->opt('file', $opts)) || ($file = $this->opt('f', $opts))) {
            $command .= " --f {$file}";
        }

        if ($this->opt('get', $opts)) {
            $command .= " --get";
            $command = $this->_setReadArguments($command, $opts);
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config requires a non-empty name argument');
            }
            $command .= " {$arg1}";
        } elseif ($this->opt('get-all', $opts)) {
            $command .= " --get-all";
            $command = $this->_setReadArguments($command, $opts);
        } elseif ($this->opt('get-regexp', $opts)) {
            $command .= " --get-regexp";
            $command = $this->_setReadArguments($command, $opts);
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --get-regexp requires a non-empty name argument');
            }
            $command .= " {$arg1}";
        } elseif ($this->opt('list', $opts)) {
            $command .= " --list";
            $command = $this->_setReadArguments($command, $opts);
        } elseif ($this->opt('add', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --add requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->opt('replace-all', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --replace-all requires a non-empty value argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
            if (is_string($arg3) && $arg3 !== '') $command .= " {$arg3}";
        } elseif ($this->opt('unset', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->opt('unset-all', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --unset-all requires a non-empty name argument');
            }
            $command .= " {$arg1}";
            if (is_string($arg2) && $arg2 !== '') $command .= " {$arg2}";
        } elseif ($this->opt('rename-section', $opts)) {
            if (!is_string($arg1) || $arg1 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty old_name argument');
            }
            if (!is_string($arg2) || $arg2 === '') {
                throw new \InvalidArgumentException('git config --rename-section requires a non-empty new_name argument');
            }
            $command .= " {$arg1}";
            $command .= " {$arg2}";
        } elseif ($this->opt('remove-section', $opts)) {
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

    protected function _setReadArguments($command, $opts)
    {
        if (!$this->opt('list', $opts)) {
            $command = $this->_setTypeArgument($command, $opts);
        }
        if ($this->opt('null', $opts)) $command .= " --null";
        elseif ($this->opt('z', $opts)) $command .= " -z";

        if ($this->opt('includes', $opts)) $command .= " --includes";
        elseif ($this->opt('no-includes', $opts)) $command .= " --no-includes";

        return $command;
    }

    protected function _setTypeArgument($command, $opts)
    {
        if ($this->opt('bool', $opts)) $command .= " --bool";
        elseif ($this->opt('int', $opts)) $command .= " --int"; elseif ($this->opt('bool-or-int', $opts)) $command .= " --bool-or-int";

        return $command;
    }
}
