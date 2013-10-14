<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Serialize;


use Cast\CastException;

class SerializerException extends CastException
{
    protected $serializer;

    public function __construct(SerializerInterface &$serializer, $message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->serializer = &$serializer;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }
}
