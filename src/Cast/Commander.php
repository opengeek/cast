<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast;


class Commander
{
    /** @var array A cached array of config options. */
    protected $options = array();
    /** @var array An array of GitCommand classes loaded (on-demand). */
    protected $commands = array();

    /**
     * Get a config option for this object.
     *
     * @param string $key The key of the config option to get.
     * @param null|array $options An optional array of config key/value pairs.
     * @param mixed $default The default value to use if no option is found.
     *
     * @return mixed The value of the config option.
     */
    public function getOption($key, $options = null, $default = null)
    {
        if (is_array($options) && array_key_exists($key, $options)) {
            $value = $options[$key];
        } elseif (is_array($this->options) && array_key_exists($key, $this->options)) {
            $value = $this->options[$key];
        } else {
            $value = $default;
        }
        return $value;
    }

    public function __call($name, $arguments)
    {
        if (!array_key_exists($name, $this->commands)) {
            $commandClass = $this->_commandClass($name);
            if (class_exists($commandClass)) {
                $this->commands[$name] = new $commandClass($this);
                return call_user_func_array(array($this->commands[$name], 'run'), array($arguments));
            }
            throw new \BadMethodCallException(sprintf('The Git GitCommand class %s does not exist', ucfirst($name)));
        }
        return call_user_func_array(array($this->commands[$name], 'run'), array($arguments));
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->commands)) {
            $commandClass = $this->_commandClass($name);
            if (class_exists($commandClass)) {
                $this->commands[$name] = new $commandClass($this);
                return $this->commands[$name];
            }
            throw new \InvalidArgumentException(sprintf('The Git GitCommand class %s does not exist', ucfirst($name)));
        }
        return $this->commands[$name];
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->commands);
    }

    protected function _commandClass($name)
    {
        $className = ucfirst($name);
        return __NAMESPACE__ . "\\Commands\\{$className}";
    }
}
