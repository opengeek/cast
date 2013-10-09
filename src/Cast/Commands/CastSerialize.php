<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Commands;


use Cast\Response\CastResponse;

class CastSerialize extends CastCommand
{
    protected $command = 'serialize';

    public function run(array $args, array $opts)
    {
        $this->response = new CastResponse();

        $this->beforeRun($args, $opts);

        $command = $this->command;

        $this->cast->getSerializer()->serializeModel();
        $this->response->fromResult(
            array(
                0,
                "",
                "",
                $command,
                array()
            )
        );

        $this->afterRun($args, $opts);

        return $this->response;
    }
}
