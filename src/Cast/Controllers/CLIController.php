<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Controllers;


use Cast\Cast;

class CLIController implements ControllerInterface
{
    protected $cast;
    protected $script;
    protected $command;
    protected $arguments = array();
    protected $options = array();

    /**
     * Handle a request and provide a response.
     *
     * @param Cast &$cast A reference to a valid Cast instance.
     * @param array $args An array of arguments for the request.
     *
     * @return ControllerResponse A Cast ControllerResponse instance.
     */
    public function handle(Cast &$cast, array $args)
    {
        $this->cast = &$cast;
        $this->parseArgs($args);
        $results = $this->cast->{$this->command}->run($this->arguments, $this->options);
        $response = new ControllerResponse($this, $results);
        return $response;
    }

    /**
     * Get the current or last executed Cast command.
     *
     * @return string The command most recently handled or being handled.
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get all of the arguments provided to the controller request.
     *
     * @return array An array of arguments for the request.
     */
    public function getArguments()
    {
        reset($this->arguments);
        return $this->arguments;
    }

    /**
     * Get a specific argument provided to the controller request by index.
     *
     * @param integer $idx The 0-based index of the argument to get.
     *
     * @throws \OutOfBoundsException If no argument exists for the specified index.
     * @return string The argument at the specified argument index.
     */
    public function getArgument($idx)
    {
        if (!isset($this->arguments[$idx])) {
            throw new \OutOfBoundsException("No argument exists at index {$idx}");
        }
        return $this->arguments[$idx];
    }

    /**
     * Get all of the options provided to the controller request.
     *
     * @return array An array of options for the request.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get a specific option value provided to the controller request by key.
     *
     * @param string $key The long or short key identifying the option.
     *
     * @throws \OutOfBoundsException If no value exists for the specified option key.
     * @return mixed The option value for the specified key.
     */
    public function getOption($key)
    {
        if (!isset($this->options[$key])) {
            throw new \OutOfBoundsException("No option exists with key {$key}");
        }
        return $this->options[$key];
    }

    private function parseArgs($args)
    {
        $this->script = array_shift($args);
        $this->command = array_shift($args);
        while ($arg = next($args)) {
            if (strpos($arg, '-') === 0) {
                $this->addOption($arg);
            } else {
                $this->arguments[] = $arg;
            }
        }
    }

    private function addOption($option)
    {
        $option = ltrim($option, '-');
        if (strpos($option, '=') > 0) {
            $exploded = explode('=', $option, 2);
            $this->options[$exploded[0]] = $exploded[1];
        } else {
            $this->options[$option] = true;
        }
    }
}
