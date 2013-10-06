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

class GitCommit extends GitCommand
{
    protected $command = 'commit';

    public function run(array $args = array())
    {
        $files = array_shift($args);
        $args = array_shift($args);

        $command = $this->command;
        if ($this->arg('short', $args)) {
            $command .= " --short";
            if ($this->arg('branch', $args)) $command .= " --branch";
            if ($this->arg('null', $args) || $this->arg('z', $args)) $command .= " --null";
        }
        if ($this->arg('long', $args)) {
            $command .= " --long";
        }
        if ($this->arg('dry-run', $args)) {
            $command .= " --dry-run";
        }
        if ($this->arg('porcelain', $args)) {
            $command .= " --porcelain";
            if ($this->arg('null', $args) || $this->arg('z', $args)) $command .= " --null";
        }
        if ($this->arg('all', $args) || $this->arg('a', $args)) {
            $command .= ' --all';
        } elseif ($this->arg('interactive', $args) || $this->arg('patch', $args) || $this->arg('p', $args)) {
            throw new \RuntimeException("git interactive patch selection not supported by Cast");
        }
        if ($this->arg('verbose', $args) || $this->arg('v', $args)) $command .= ' --verbose';
        if ($this->arg('quiet', $args) || $this->arg('q', $args)) $command .= ' --quiet';
        if (($commit = $this->arg('reuse-message', $args)) || ($commit = $this->arg('C', $args))) {
            $command .= " -C {$commit}";
        }
        if (($commit = $this->arg('reedit-message', $args)) || ($commit = $this->arg('c', $args))) {
            throw new \RuntimeException('git commit -c {commit} not supported by Cast');
        }
        if (($commit = $this->arg('fixup', $args))) {
            $command .= " --fixup={$commit}";
        }
        if (($commit = $this->arg('squash', $args))) {
            $command .= " --squash={$commit}";
        }
        if (($mode = $this->arg('cleanup', $args))) {
            $command .= " --cleanup={$mode}";
        }
        if ($this->arg('allow-empty', $args)) $command .= " --allow-empty";
        if ($this->arg('reset-author', $args)) $command .= " --reset-author";
        if (($msgFile = $this->arg('file', $args)) || ($msgFile = $this->arg('F', $args))) {
            $command .= " --file={$msgFile}";
        } elseif (($message = $this->arg('message', $args)) || ($message = $this->arg('m', $args))) {
            $command .= " --message={$message}";
        } elseif ($this->arg('allow-empty-message', $args)) {
            $command .= " --allow-empty-message";
        } else {
            throw new \RuntimeException("git commit in Cast requires --file={file} or --message={message} options");
        }
        if ($this->arg('edit', $args) || $this->arg('e', $args)) {
            throw new \RuntimeException("git commit --edit not supported in Cast");
        }
        if ($this->arg('amend', $args)) {
            if (!$this->arg('no-edit', $args) && !$this->arg('message', $args) && !$this->arg('m', $args) &&
                !$this->arg('file', $args) && !$this->arg('F', $args) && !$this->arg('allow-empty-message', $args)
            ) {
                throw new \RuntimeException("git commit --amend requires -m, -F, --allow-empty-message or --no-edit in Cast");
            }
        }
        if (($author = $this->arg('author', $args))) {
            $command .= " --author={$author}";
        }
        if (($date = $this->arg('date', $args))) {
            $command .= " --date={$date}";
        }
        if (($template = $this->arg('template', $args)) || ($msgFile = $this->arg('t', $args))) {
            $command .= " --template={$template}";
        }
        if ($this->arg('signoff', $args) || $this->arg('s', $args)) $command .= ' --signoff';
        if ($this->arg('no-verify', $args) || $this->arg('n', $args)) $command .= ' --no-verify';
        if ($this->arg('no-post-rewrite', $args)) $command .= ' --no-post-rewrite';

        if (($mode = $this->arg('untracked-files', $args)) || ($mode = $this->arg('u', $args))) {
            $command .= " --untracked-files={$mode}";
        }
        if (($keyId = $this->arg('gpg-sign', $args)) || ($keyId = $this->arg('S', $args))) {
            $command .= " --gpg-sign={$keyId}";
        }

        if (!empty($files)) {
            if ($this->arg('include', $args) || $this->arg('i', $args)) $command .= ' --include';
            elseif ($this->arg('only', $args) || $this->arg('o', $args)) $command .= ' --only';

            if (!is_array($files)) $files = array($files);
            array_walk($files, function(&$value) {
                $value = escapeshellarg($value);
            });
            $command .= " -- " . implode(" ", $files);
        }

        return $this->exec($command);
    }
}
