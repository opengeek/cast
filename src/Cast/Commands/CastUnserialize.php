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

class CastUnserialize extends CastCommand
{
    protected $command = 'unserialize';

    public function run(array $args, array $opts)
    {
        $this->response = new CastResponse();

        $this->beforeRun($args, $opts);

        $command = $this->command;

        $this->cast->getSerializer()->unserializeModel();
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
