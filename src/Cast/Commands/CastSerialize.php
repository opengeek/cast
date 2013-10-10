<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Commands;


use Cast\Cast;
use Cast\Response\CastResponse;

class CastSerialize extends CastCommand
{
    protected $command = 'serialize';

    public function run(array $args, array $opts)
    {
        $this->response = new CastResponse();

        $this->beforeRun($args, $opts);

        $command = $this->command;

        $path = array_shift($args);
        if ($path !== null) {
            $paths = array();
            $path = trim($path, "'");
            if (is_readable($path)) {
                $paths[] = $path;
            }
            while (($path = array_shift($args)) !== null) {
                $path = trim($path, "'");
                if (is_readable($path)) {
                    $paths[] = $path;
                }
            }

            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $this->cast->getSerializer()->serializeModel($path);
                    $command .= " {$path}";
                } elseif (is_file($path)) {
                    $this->cast->getSerializer()->unserialize(substr($path, strlen($this->cast->getOption(Cast::SERIALIZED_MODEL_PATH, null, '.model/'))));
                    $command .= " {$path}";
                }
            }
        } else {
            $this->cast->getSerializer()->serializeModel();
        }

        $this->afterRun($args, $opts);

        return $this->response;
    }
}
