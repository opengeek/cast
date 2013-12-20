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
use Cast\Git\GitException;

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
     * Run the Git command represented by this class.
     *
     * @param array $args An array of arguments for the command.
     * @param array $opts An array of options for the command.
     *
     * @throws GitCommandException If an error occurs running the command.
     * @return array The results of the command.
     */
    abstract public function run(array $args = array(), array $opts = array());

    /**
     * Construct a new GitCommand instance.
     *
     * @param Git &$git A reference to a Git repository instance.
     */
    public function __construct(&$git)
    {
        $this->git = & $git;
    }

    /**
     * Get an option value from an array of parsed options.
     *
     * @param string $key The option key to lookup a value for.
     * @param array $opts An array of options to lookup the key in.
     * @param bool $default The default value to return if the key is not found.
     *
     * @return bool|string|mixed The option value, or the default if not found.
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
     * @throws GitCommandException If an error occurs executing the git command.
     * @return array The formatted response from the Git command.
     */
    public function exec($command)
    {
        try {
            return $this->git->exec($command);
        } catch (GitException $e) {
            throw new GitCommandException($this, $e->getMessage(), $e->getCode(), $e);
        }
    }
}
