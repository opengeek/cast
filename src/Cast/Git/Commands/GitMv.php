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

class GitMv extends GitCommand
{
    protected $command = 'mv';

    public function run(array $args = array(), array $opts = array())
    {
        $source = array_shift($args);
        $destination = array_shift($args);

        if ((!is_array($source) || !is_string($source)) || $source === '' || !is_string($destination) || $destination === '') {
            throw new \InvalidArgumentException("git mv requires at least one source and a destination argument");
        }

        $command = $this->command;
        if ($this->opt('dry-run', $opts)) $command .= ' --dry-run';
        elseif ($this->opt('n', $opts)) $command .= ' -n';
        if ($this->opt('force', $opts)) $command .= ' --force';
        elseif ($this->opt('f', $opts)) $command .= ' -f';
        if ($this->opt('verbose', $opts)) $command .= ' --verbose';
        elseif ($this->opt('v', $opts)) $command .= ' -v';
        if ($this->opt('k', $opts)) $command .= ' -k';

        if (is_array($source)) $source = implode(" ", $source);
        $command .= " {$source}";
        $command .= " {$destination}";

        return $this->exec($command);
    }
}
