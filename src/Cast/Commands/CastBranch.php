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

use Cast\Response\CastResponse;

class CastBranch extends CastCommand
{
    protected $command = 'branch';

    protected $listOptions = array(
        'color',
        'no-color',
        'remotes', 'r',
        'all', 'a',
        'verbose', 'v', 'vv',
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

    public function run(array $args = array(), array $opts = array())
    {
        $commit = array_shift($args);
        $pattern = array_shift($args);

        if (array_intersect(array_keys($opts), $this->setOptions)) {
            $results = $this->set($commit, $pattern, $opts);
        } elseif (array_intersect(array_keys($opts), $this->moveOptions)) {
            $results = $this->move($commit, $pattern, $opts);
        } elseif (array_intersect(array_keys($opts), $this->deleteOptions)) {
            $results = $this->delete($commit, $opts);
        } else {
            $results = $this->get($commit, $pattern, $opts);
        }
        return new CastResponse($results);
    }

    public function get($commit = null, $pattern = null, $opts = null)
    {
        return $this->cast->git->{$this->command}->get($commit, $pattern, $opts);
    }

    public function set($name, $startPoint = null, $opts = null)
    {
        return $this->cast->git->{$this->command}->set($name, $startPoint, $opts);
    }

    public function move($newBranch, $oldBranch = null, $opts = null)
    {
        return $this->cast->git->{$this->command}->move($newBranch, $oldBranch, $opts);
    }

    public function delete($name, $opts = null)
    {
        return $this->cast->git->{$this->command}->delete($name, $opts);
    }
}
