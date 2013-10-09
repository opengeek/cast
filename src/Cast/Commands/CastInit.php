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

class CastInit extends CastCommand
{
    protected $command = 'init';

    public function beforeRun(array $args = array(), array $opts = array())
    {
        $directory = isset($args[0]) ? $args[0] : '';
        if (empty($directory)) {
            $directory = $this->cast->git->getPath();
        } else {
            $this->cast->git->setPath($directory);
        }

        if ($this->cast->git->isInitialized()) throw new \RuntimeException('Cannot reinitialize an existing git repository at ' . $directory);

        if ($this->cast->git->getOption('core.bare', null, false)) {
            throw new \RuntimeException('Cast does not currently support bare repositories');
        }

        if ($this->shouldSerialize($opts)) {
            $this->cast->getSerializer()->serializeModel();
        }
    }
}
