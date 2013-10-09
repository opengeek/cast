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

use Cast\Cast;
use Cast\Response\CastResponse;

class CastInit extends CastCommand
{
    protected $command = 'init';

    public function run(array $args = array(), array $opts = array())
    {
        $directory = array_shift($args);
        if (empty($directory)) {
            $directory = $this->cast->git->getPath();
        } else {
            $this->cast->git->setPath($directory);
        }

        if ($this->cast->git->isInitialized()) throw new \RuntimeException('Cannot reinitialize an existing git repository at ' . $directory);

        if ($this->cast->git->getOption('core.bare', null, false)) {
            throw new \RuntimeException('Cast does not currently support bare repositories');
        }

        $this->cast->getSerializer()->serializeModel();

        return new CastResponse($this->cast->git->{$this->command}->run($args, $opts));
    }
}
