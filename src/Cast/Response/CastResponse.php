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

/**
 * Represents a response returned from a CastCommand being run().
 *
 * @package Cast\Response
 */
class CastResponse
{
    /** @var int The result code from the execution of a Git command. */
    protected $result;
    /** @var string The output produced during execution of the CastCommand. */
    protected $output;
    /** @var string Errors produced during execution of the CastCommand. */
    protected $errors;

    /**
     * Construct a new CastResponse instance.
     *
     * @param array $response An array returned from GitCommand::exec() containing an execution
     * code, any output, and any errors produced by the process.
     */
    public function __construct(array $response)
    {
        $this->result = (integer)$response[0];
        $this->output = $response[1];
        $this->errors = $response[2];
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

    /**
     * Get the output produced by the CastCommand being executed.
     *
     * @return string The output produced by the CastCommand being executed.
     */
    public function getOutput()
    {
        return $this->output;
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
     * Return a human-readable string representation of this response.
     *
     * @return string A human-readable string representation of this response.
     */
    public function __toString()
    {
        $output[] = "Command completed with code " . (string)$this->result;
        $output[] = rtrim($this->errors, "\n");
        $output[] = rtrim($this->output, "\n");
        return implode("\n", $output) . "\n";
    }
}
