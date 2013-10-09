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

class CastStatus extends CastCommand
{
    protected $command = 'status';

    public function beforeRun(array $args = array(), array $opts = array())
    {
        if ($this->shouldSerialize($opts)) {
            $this->cast->getSerializer()->serializeModel();
        }
    }
}
