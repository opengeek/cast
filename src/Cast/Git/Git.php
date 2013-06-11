<?php
/*
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git;

/**
 * An API wrapper for executing Git commands on a Git repository.
 *
 * @package Cast\Git
 */
class Git
{
    /** @var string The path to the Git repository. */
    protected $path;
    /** @var bool Flag indicating if the repository is bare. */
    protected $bare;
    /** @var array A cached array of Git + Cast config data */
    protected $config;

    /**
     * Construct a new Git instance.
     *
     * @param string $path The path to a valid Git repository.
     * @param null|array $options An optional array of config options.
     */
    public function __construct($path, $options = null)
    {
        $this->path = $path;
        $this->config = $this->loadConfig($options);
        $this->bare = (bool)$this->getOption('core.bare', null, false);
    }

    /**
     * Get a config option for this Git instance.
     *
     * This includes Git global, user, and local config options, plus any
     * additional user-defined options for use in Cast.
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
        } elseif (is_array($this->config) && array_key_exists($key, $this->config)) {
            $value = $this->config[$key];
        } else {
            $value = $default;
        }
        return $value;
    }

    /**
     * Execute a Git command.
     *
     * @param string $command The complete command to execute.
     * @param null|array $options An optional config array.
     *
     * @throws \RuntimeException If the process could not be opened.
     * @return array An array containing the process result, stdout and stderr.
     */
    public function exec($command, $options = null)
    {
        $process = proc_open(
            $this->getOption('git_binary', $options, 'git') . ' ' . $command,
            array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w")
            ),
            $pipes,
            $this->path,
            $this->getOption('git_env', $options, null)
        );
        if (is_resource($process)) {
            try {
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $return = proc_close($process);
            } catch (\Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }
            return array($return, $output, $errors);
        }
        throw new \RuntimeException(sprintf('Could not execute command git %s', $command));
    }

    protected function loadConfig($options = null)
    {
        $config = array();
        $configResults = $this->exec("config --list", $options);
        $configLines = explode("\n", $configResults[1]);
        array_pop($configLines);
        foreach ($configLines as $configLine) {
            list($key, $value) = explode("=", $configLine, 2);
            $config[$key] = $value;
        }
        if (!is_array($options)) $options = array();
        return array_merge($config, $options);
    }
}
