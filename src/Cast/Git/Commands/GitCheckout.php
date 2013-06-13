<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Git\Commands;


class GitCheckout
{
    protected $command = 'checkout';

    public function run(array $args = array())
    {
        $branch = array_shift($args);
        $args = array_shift($args);


    }
}
