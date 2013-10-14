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
        if ($this->opt('short', $opts)) {
            $command .= " --short";
            if ($this->opt('branch', $opts)) $command .= " --branch";
            if ($this->opt('null', $opts) || $this->opt('z', $opts)) $command .= " --null";
        }
        if ($this->opt('long', $opts)) {
            $command .= " --long";
        }
        if ($this->opt('dry-run', $opts)) {
            $command .= " --dry-run";
        }
        if ($this->opt('porcelain', $opts)) {
            $command .= " --porcelain";
            if ($this->opt('null', $opts) || $this->opt('z', $opts)) $command .= " --null";
        }
        if ($this->opt('all', $opts) || $this->opt('a', $opts)) {
            $command .= ' --all';
        } elseif ($this->opt('interactive', $opts) || $this->opt('patch', $opts) || $this->opt('p', $opts)) {
            throw new GitCommandException($this, "git interactive patch selection not supported by Cast");
        }
        if ($this->opt('verbose', $opts) || $this->opt('v', $opts)) $command .= ' --verbose';
        if ($this->opt('quiet', $opts) || $this->opt('q', $opts)) $command .= ' --quiet';
        if (($commit = $this->opt('reuse-message', $opts)) || ($commit = $this->opt('C', $opts))) {
            $command .= " -C {$commit}";
        }
        if (($commit = $this->opt('reedit-message', $opts)) || ($commit = $this->opt('c', $opts))) {
            throw new GitCommandException($this, 'git commit -c {commit} not supported by Cast');
        }
        if (($commit = $this->opt('fixup', $opts))) {
            $command .= " --fixup={$commit}";
        }
        if (($commit = $this->opt('squash', $opts))) {
            $command .= " --squash={$commit}";
        }
        if (($mode = $this->opt('cleanup', $opts))) {
            $command .= " --cleanup={$mode}";
        }
        if ($this->opt('allow-empty', $opts)) $command .= " --allow-empty";
        if ($this->opt('reset-author', $opts)) $command .= " --reset-author";
        if (($msgFile = $this->opt('file', $opts)) || ($msgFile = $this->opt('F', $opts))) {
            $command .= " --file={$msgFile}";
        } elseif (($message = $this->opt('message', $opts)) || ($message = $this->opt('m', $opts))) {
            $command .= " --message={$message}";
        } elseif ($this->opt('allow-empty-message', $opts)) {
            $command .= " --allow-empty-message";
        } else {
            throw new GitCommandException($this, "git commit in Cast requires --file={file} or --message={message} options");
        }
        if ($this->opt('edit', $opts) || $this->opt('e', $opts)) {
            throw new GitCommandException($this, "git commit --edit not supported in Cast");
        }
        if ($this->opt('amend', $opts)) {
            if (!$this->opt('no-edit', $opts) && !$this->opt('message', $opts) && !$this->opt('m', $opts) &&
                !$this->opt('file', $opts) && !$this->opt('F', $opts) && !$this->opt('allow-empty-message', $opts)
            ) {
                throw new GitCommandException($this, "git commit --amend requires -m, -F, --allow-empty-message or --no-edit in Cast");
            }
        }
        if (($author = $this->opt('author', $opts))) {
            $command .= " --author={$author}";
        }
        if (($date = $this->opt('date', $opts))) {
            $command .= " --date={$date}";
        }
        if (($template = $this->opt('template', $opts)) || ($msgFile = $this->opt('t', $opts))) {
            $command .= " --template={$template}";
        }
        if ($this->opt('signoff', $opts) || $this->opt('s', $opts)) $command .= ' --signoff';
        if ($this->opt('no-verify', $opts) || $this->opt('n', $opts)) $command .= ' --no-verify';
        if ($this->opt('no-post-rewrite', $opts)) $command .= ' --no-post-rewrite';

        if (($mode = $this->opt('untracked-files', $opts)) || ($mode = $this->opt('u', $opts))) {
            $command .= " --untracked-files={$mode}";
        }
        if (($keyId = $this->opt('gpg-sign', $opts)) || ($keyId = $this->opt('S', $opts))) {
            $command .= " --gpg-sign={$keyId}";
        }

        if (!empty($files)) {
            if ($this->opt('include', $opts) || $this->opt('i', $opts)) $command .= ' --include';
            elseif ($this->opt('only', $opts) || $this->opt('o', $opts)) $command .= ' --only';

            if (!is_array($files)) $files = array($files);
            array_walk($files, function(&$value) {
                $value = escapeshellarg($value);
            });
            $command .= " -- " . implode(" ", $files);
        }

        return $this->exec($command);
    }
}
