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

use Cast\Git\Git;

class GitStatus extends GitCommand
{
    protected $command = 'status';

    public function run(array $args = array(), array $opts = array())
    {
        $command = $this->command;
        if ($this->opt('short', $opts)) $command .= ' --short';
        if ($this->opt('long', $opts)) $command .= ' --long';
        if ($this->opt('branch', $opts)) $command .= ' --branch';
        if ($this->opt('ignored', $opts)) $command .= ' --ignored';
        if (($when = $this->opt('ignore-submodules', $opts)) != false) {
            if ($when === true) {
                $command .= ' --ignore-submodules';
            } else {
                $command .= ' --ignore-submodules=' . $when;
            }
        }
        if ($this->opt('ignored', $opts)) $command .= ' --ignored';
        if ($this->opt('porcelain', $opts)) $command .= ' --porcelain';
        if ($this->opt('x', $opts)) $command .= ' -x';

        return $this->exec($command);
    }
}
