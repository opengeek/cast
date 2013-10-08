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


class CastResponse
{
    protected $result;
    protected $output;
    protected $errors;

    public function __construct(array $response)
    {
        $this->result = (integer)$response[0];
        $this->output = $response[1];
        $this->errors = $response[2];
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function wasSuccessful()
    {
        return $this->result === 0;
    }

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

    public function __toString()
    {
        $output[] = "Command completed with code " . (string)$this->result;
        $output[] = rtrim($this->errors, "\n");
        $output[] = rtrim($this->output, "\n");
        return implode("\n", $output) . "\n";
    }
}
