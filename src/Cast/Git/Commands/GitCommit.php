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

    public function run(array $args = array(), array $opts = array())
    {
        $files = array_shift($args);

        $command = $this->command;
        if ($this->arg('short', $opts)) {
            $command .= " --short";
            if ($this->arg('branch', $opts)) $command .= " --branch";
            if ($this->arg('null', $opts) || $this->arg('z', $opts)) $command .= " --null";
        }
        if ($this->arg('long', $opts)) {
            $command .= " --long";
        }
        if ($this->arg('dry-run', $opts)) {
            $command .= " --dry-run";
        }
        if ($this->arg('porcelain', $opts)) {
            $command .= " --porcelain";
            if ($this->arg('null', $opts) || $this->arg('z', $opts)) $command .= " --null";
        }
        if ($this->arg('all', $opts) || $this->arg('a', $opts)) {
            $command .= ' --all';
        } elseif ($this->arg('interactive', $opts) || $this->arg('patch', $opts) || $this->arg('p', $opts)) {
            throw new \RuntimeException("git interactive patch selection not supported by Cast");
        }
        if ($this->arg('verbose', $opts) || $this->arg('v', $opts)) $command .= ' --verbose';
        if ($this->arg('quiet', $opts) || $this->arg('q', $opts)) $command .= ' --quiet';
        if (($commit = $this->arg('reuse-message', $opts)) || ($commit = $this->arg('C', $opts))) {
            $command .= " -C {$commit}";
        }
        if (($commit = $this->arg('reedit-message', $opts)) || ($commit = $this->arg('c', $opts))) {
            throw new \RuntimeException('git commit -c {commit} not supported by Cast');
        }
        if (($commit = $this->arg('fixup', $opts))) {
            $command .= " --fixup={$commit}";
        }
        if (($commit = $this->arg('squash', $opts))) {
            $command .= " --squash={$commit}";
        }
        if (($mode = $this->arg('cleanup', $opts))) {
            $command .= " --cleanup={$mode}";
        }
        if ($this->arg('allow-empty', $opts)) $command .= " --allow-empty";
        if ($this->arg('reset-author', $opts)) $command .= " --reset-author";
        if (($msgFile = $this->arg('file', $opts)) || ($msgFile = $this->arg('F', $opts))) {
            $command .= " --file={$msgFile}";
        } elseif (($message = $this->arg('message', $opts)) || ($message = $this->arg('m', $opts))) {
            $command .= " --message={$message}";
        } elseif ($this->arg('allow-empty-message', $opts)) {
            $command .= " --allow-empty-message";
        } else {
            throw new \RuntimeException("git commit in Cast requires --file={file} or --message={message} options");
        }
        if ($this->arg('edit', $opts) || $this->arg('e', $opts)) {
            throw new \RuntimeException("git commit --edit not supported in Cast");
        }
        if ($this->arg('amend', $opts)) {
            if (!$this->arg('no-edit', $opts) && !$this->arg('message', $opts) && !$this->arg('m', $opts) &&
                !$this->arg('file', $opts) && !$this->arg('F', $opts) && !$this->arg('allow-empty-message', $opts)
            ) {
                throw new \RuntimeException("git commit --amend requires -m, -F, --allow-empty-message or --no-edit in Cast");
            }
        }
        if (($author = $this->arg('author', $opts))) {
            $command .= " --author={$author}";
        }
        if (($date = $this->arg('date', $opts))) {
            $command .= " --date={$date}";
        }
        if (($template = $this->arg('template', $opts)) || ($msgFile = $this->arg('t', $opts))) {
            $command .= " --template={$template}";
        }
        if ($this->arg('signoff', $opts) || $this->arg('s', $opts)) $command .= ' --signoff';
        if ($this->arg('no-verify', $opts) || $this->arg('n', $opts)) $command .= ' --no-verify';
        if ($this->arg('no-post-rewrite', $opts)) $command .= ' --no-post-rewrite';

        if (($mode = $this->arg('untracked-files', $opts)) || ($mode = $this->arg('u', $opts))) {
            $command .= " --untracked-files={$mode}";
        }
        if (($keyId = $this->arg('gpg-sign', $opts)) || ($keyId = $this->arg('S', $opts))) {
            $command .= " --gpg-sign={$keyId}";
        }

        if (!empty($files)) {
            if ($this->arg('include', $opts) || $this->arg('i', $opts)) $command .= ' --include';
            elseif ($this->arg('only', $opts) || $this->arg('o', $opts)) $command .= ' --only';

            if (!is_array($files)) $files = array($files);
            array_walk($files, function(&$value) {
                $value = escapeshellarg($value);
            });
            $command .= " -- " . implode(" ", $files);
        }

        return $this->exec($command);
    }
}
