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

/**
 * Defines the API contract for a Cast request Controller class.
 *
 * @package Cast\Controllers
 */
interface ControllerInterface
{
    /**
     * Handle a request and provide a response.
     *
     * @param Cast &$cast A reference to a valid Cast instance.
     * @param array $args An array of arguments for the request.
     *
     * @return ControllerResponse A Cast ControllerResponse instance.
     */
    public function handle(Cast &$cast, array $args);

    /**
     * Get the current or last executed Cast command.
     *
     * @return string The command most recently handled or being handled.
     */
    public function getCommand();

    /**
     * Get all of the arguments provided to the controller request.
     *
     * @return array An array of arguments for the request.
     */
    public function getArguments();

    /**
     * Get a specific argument provided to the controller request by index.
     *
     * @param integer $idx The 0-based index of the argument to get.
     *
     * @throws \OutOfBoundsException If no argument exists for the specified index.
     * @return string The argument at the specified argument index.
     */
    public function getArgument($idx);

    /**
     * Get all of the options provided to the controller request.
     *
     * @return array An array of options for the request.
     */
    public function getOptions();

    /**
     * Get a specific option value provided to the controller request by key.
     *
     * @param string $key The long or short key identifying the option.
     *
     * @throws \OutOfBoundsException If no value exists for the specified option key.
     * @return mixed The option value for the specified key.
     */
    public function getOption($key);
}
