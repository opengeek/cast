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

class CastReset extends CastCommand
{
    protected $command = 'reset';

    public function afterRun(array $args = array(), array $opts = array())
    {
        if ($this->opt('hard', $opts) && $this->shouldSerialize($opts)) {
            $this->cast->getSerializer()->unserializeModel();
        }
    }
}
