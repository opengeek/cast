<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Commands;

use Cast\Cast;
use Cast\Response\CastResponse;

/**
 * An abstract Cast command class.
 *
 * @package Cast\Commands
 */
abstract class CastCommand
{
    /** @var Cast */
    public $cast;

    /** @var string The command string for this CastCommand */
    protected $command;
    protected $response;

    /**
     * Get an instance of this CastCommand.
     *
     * @param Cast &$cast
     */
    public function __construct(&$cast)
    {
        $this->cast = & $cast;
    }

    /**
     * Run the GitCommand wrapped by this CastCommand.
     *
     * @param array $args An array of arguments.
     *
     * @throws \RuntimeException If an error occurs executing the GitCommand::run() method.
     * @return CastResponse The result of the GitCommand wrapped in a CastResponse object.
     */
    public function run(array $args = array())
    {
        return new CastResponse($this->cast->git->{$this->command}->run($args));
    }

    /**
     * Get an argument value by key for this command.
     *
     * @param string $key The argument key.
     * @param array $args An array of arguments to search in.
     * @param mixed $default The default value to return if not found.
     *
     * @return mixed The argument value or the default if not found.
     */
    public function arg($key, $args, $default = false)
    {
        $value = $default;
        if (is_array($args) && array_key_exists($key, $args)) {
            $value = $args[$key];
        }
        return $value;
    }
}
