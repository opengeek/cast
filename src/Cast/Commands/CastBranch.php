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


class CastBranch extends CastCommand
{
    protected $command = 'branch';

    protected $listOptions = array(
        'color',
        'no-color',
        'remotes', 'r',
        'all', 'a',
        'verbose', 'v',
        'merged',
        'no-merged',
        'contains'
    );

    protected $setOptions = array(
        'set-upstream',
        'unset-upstream',
        'set-upstream-to', 'u',
        'track',
        'no-track',
        'create-reflog', 'l',
        'force', 'f',
        'edit-description'
    );

    protected $moveOptions = array(
        'move', 'm',
        'M'
    );

    protected $deleteOptions = array(
        'delete', 'd',
        'D'
    );

    public function run(array $args = array())
    {
        $commit = array_shift($args);
        $pattern = array_shift($args);
        $args = array_shift($args);

        if (array_intersect($args, $this->setOptions)) {
            return $this->set($commit, $pattern, $args);
        } elseif (array_intersect($args, $this->moveOptions)) {
            return $this->move($commit, $pattern, $args);
        } elseif (array_intersect($args, $this->deleteOptions)) {
            return $this->delete($commit, $args);
        } elseif (array_intersect($args, $this->listOptions)) {
            return $this->get($commit, $pattern, $args);
        }
    }

    public function get($commit = null, $pattern = null, $args = null)
    {
        return $this->cast->git->{$this->command}->get($commit, $pattern, $args);
    }

    public function set($name, $startPoint = null, $args = null)
    {
        return $this->cast->git->{$this->command}->set($name, $startPoint, $args);
    }

    public function move($newBranch, $oldBranch = null, $args = null)
    {
        return $this->cast->git->{$this->command}->get($newBranch, $oldBranch, $args);
    }

    public function delete($name, $args = null)
    {
        return $this->cast->git->{$this->command}->get($name, $args);
    }
}
