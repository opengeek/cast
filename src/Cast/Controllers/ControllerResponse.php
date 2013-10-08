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

use Cast\Response\CastResponse;

/**
 * A controller wrapper for a CastResponse.
 *
 * @package Cast\Controllers
 */
class ControllerResponse
{
    /** @var string The CastCommand string. */
    protected $command;
    /** @var array An array of arguments from the CastCommand. */
    protected $arguments;
    /** @var array An array of options from the CastCommand. */
    protected $options;
    /** @var \Cast\Response\CastResponse The CastResponse object this object wraps. */
    protected $response;

    /**
     * Construct a new ControllerResponse instance
     *
     * @param ControllerInterface $controller The Controller building this response.
     * @param CastResponse &$response A reference to the CastResponse to be wrapped by this instance.
     */
    public function __construct(ControllerInterface $controller, CastResponse &$response)
    {
        $this->command = $controller->getCommand();
        $this->arguments = $controller->getArguments();
        $this->options = $controller->getOptions();
        $this->response = &$response;
    }

    /**
     * Indicates if the CastCommand completed successfully.
     *
     * @return bool true if the response is a successful one.
     */
    public function wasSuccessful()
    {
        return $this->response->wasSuccessful();
    }

    /**
     * The code returned by the execution of the actual Git command.
     *
     * @return integer The code returned by execution of a Git command.
     */
    public function getCode()
    {
        return $this->response->getResult();
    }

    /**
     * Get the output from execution of the CastCommand.
     *
     * @return string The output from the CastCommand.
     */
    public function getOutput()
    {
        return $this->response->getOutput();
    }

    /**
     * Error output from execution of the CastCommand.
     *
     * @return string Error output from the CastCommand.
     */
    public function getErrors()
    {
        return $this->response->getErrors();
    }

    /**
     * Indicates if any errors were produced by the execution of the GitCommand.
     *
     * @return bool true if the error output from the CastCommand is not empty.
     */
    public function hasErrors()
    {
        return $this->response->hasErrors();
    }

    /**
     * Return a human-readable string representation of the response.
     *
     * @return string A string representation of the complete response.
     */
    public function __toString()
    {
        return $this->response->__toString();
    }
}
