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

/**
 * An abstract representation of a Git command.
 *
 * @package Cast\Git\Commands
 */
abstract class GitCommand
{
    /** @var Git */
    public $git;

    /** @var string The Git command represented by this class */
    protected $command;

    /**
     * Run the Git command.
     *
     * @param array $args An array of arguments for the command.
     *
     * @param array $opts
     *
     * @return mixed The results of the command.
     */
    abstract public function run(array $args = array(), array $opts = array());

    public function __construct(&$git)
    {
        $this->git = & $git;
    }

    /**
     *
     * @param $key
     * @param $opts
     * @param bool $default
     *
     * @return bool|string|mixed
     */
    public function opt($key, $opts, $default = false)
    {
        $value = $default;
        if (is_array($opts) && array_key_exists($key, $opts)) {
            $value = $opts[$key];
        }
        return $value;
    }

    /**
     * Execute a Git command string and return an appropriate response.
     *
     * @param string $command The complete Git CLI command string to execute.
     *
     * @throws \RuntimeException If an error occurs executing the command.
     * @return array The stdout or stderr response from the Git command as appropriate.
     */
    public function exec($command)
    {
        return $this->git->exec($command);
    }
}
