<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) 2013 Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast;


use Cast\Git\Git;

class Cast extends Commander
{
    const GIT_PATH = 'cast.git_path';

    /** @var \modX The MODX instance referenced by this Cast instance. */
    public $modx;
    /** @var Git A Git instance referenced by this Cast instance. */
    public $git;

    public function __construct(\modX &$modx, $options = null)
    {
        $this->modx =& $modx;
        if (is_array($options)) {
            $this->options = $options;
        }
        $gitPath = $this->getOption(self::GIT_PATH, null, $this->modx->getOption('base_path', null, MODX_BASE_PATH));
        $this->git = new Git($gitPath, $options);
    }

    /**
     * Get a config option for this Cast instance.
     *
     * @param string $key The key of the config option to get.
     * @param null|array $options An optional array of config key/value pairs.
     * @param mixed $default The default value to use if no option is found.
     *
     * @return mixed The value of the config option.
     */
    public function getOption($key, $options = null, $default = null)
    {
        if (is_array($options) && array_key_exists($key, $options)) {
            $value = $options[$key];
        } elseif (is_array($this->options) && array_key_exists($key, $this->options)) {
            $value = $this->options[$key];
        } else {
            $value = $default;
        }
        return $value;
    }

}
