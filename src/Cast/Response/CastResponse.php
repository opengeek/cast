<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Response;

use Cast\CastException;

/**
 * Represents a response returned from a CastCommand being run().
 *
 * @package Cast\Response
 */
class CastResponse
{
    /** @var string The command that was constructed and executed */
    protected $command;
    /** @var array An array of options */
    protected $options;
    /** @var int The result code from the execution of a Git command. */
    protected $result;
    /** @var string The output produced during execution of the CastCommand. */
    protected $output;
    /** @var string Errors produced during execution of the CastCommand. */
    protected $errors;

    private $_initialized = false;

    /**
     * Construct a new CastResponse instance.
     *
     * @param array $response An array returned from GitCommand::exec() containing an execution
     * code, any output, and any errors produced by the process.
     */
    public function __construct(array $response = array())
    {
        if (!empty($response) && count($response) === 5) {
            $this->result = (integer)$response[0];
            $this->output = $response[1];
            $this->errors = $response[2];
            $this->command = $response[3];
            $this->options = $response[4];
            $this->_initialized = true;
        } else {
            $this->result = -1;
            $this->output = '';
            $this->errors = '';
            $this->command = '';
            $this->options = array();
        }
    }

    /**
     * Get the result code produced by the CastCommand being executed.
     *
     * @return int The result code produced by execution of a Git command.
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result = 0)
    {
        $this->result = (integer)$result;
    }

    /**
     * Get the output produced by the CastCommand being executed.
     *
     * @return string The output produced by the CastCommand being executed.
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function addOutput($output)
    {
        $this->output .= trim($output, "\n") . "\n";
    }

    /**
     * Get the errors produced by the CastCommand being executed.
     *
     * @return string Any errors produced by the CastCommand being executed.
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function addErrors($errors)
    {
        $this->errors .= trim($errors, "\n") . "\n";
    }

    /**
     * Indicates if the CastCommand execution produced any errors.
     *
     * @return bool true if the error output is empty, false if it is not.
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Indicates if the CastCommand execution was successful.
     *
     * @return bool true if the execution was successful, false otherwise.
     */
    public function wasSuccessful()
    {
        return $this->result === 0;
    }

    /**
     * Get the command string executed by Git.
     *
     * @return string The command string executed by Git
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get the array of options passed to the Git command execution.
     *
     * @return array An array of options used when executing the command.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get a JSON representation of this response.
     *
     * @param int $options Options passed through to json_encode().
     *
     * @return string A JSON string representation of this response.
     */
    public function toJSON($options = 0)
    {
        return json_encode(
            array(
                'result' => $this->result,
                'output' => $this->output,
                'errors' => $this->errors
            ),
            $options
        );
    }

    /**
     * Update an existing response from a GitCommand::run() result.
     *
     * @param array $result An array result from a GitCommand::run() method call.
     *
     * @throws CastException If the result is not an array with 5 elements
     * or the response has already been initialized with a GitCommand::run() result.
     */
    public function fromResult($result)
    {
        if ($this->_initialized) {
            throw new CastException("this response has already been initialized from a GitCommand::run() method");
        }
        if (!is_array($result) || count($result) !== 5) {
            throw new CastException("invalid result provided to CastResponse");
        }
        $this->setResult($result[0]);
        $this->addOutput($result[1]);
        $this->addErrors($result[2]);
        $this->command = $result[3];
        $this->options = $result[4];
        $this->_initialized = true;
    }

    /**
     * Return a human-readable string representation of this response.
     *
     * @return string A human-readable string representation of this response.
     */
    public function __toString()
    {
        $output[] = rtrim($this->errors, "\n");
        $output[] = rtrim($this->output, "\n");
        return implode("\n", $output) . "\n";
    }
}
