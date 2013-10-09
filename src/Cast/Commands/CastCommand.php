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
use Cast\Response\CastResponse;

/**
 * An abstract Cast command class.
 *
 * @package Cast\Commands
 */
abstract class CastCommand
{
    const SERIALIZE = 'cast.serialize';

    /** @var Cast */
    public $cast;

    /** @var string The command string for this CastCommand */
    protected $command;
    /** @var CastResponse */
    protected $response;

    /**
     * Get an instance of this CastCommand.
     *
     * @param Cast &$cast
     */
    public function __construct(&$cast)
    {
        $this->cast = & $cast;
    }

    /**
     * Run the GitCommand wrapped by this CastCommand.
     *
     * @param array $args An array of arguments for the command.
     * @param array $opts An array of options for the command.
     *
     * @throws \RuntimeException If an unrecoverable error occurs running the GitCommand.
     * @return CastResponse The result of the GitCommand wrapped in a CastResponse object.
     */
    public function run(array $args = array(), array $opts = array())
    {
        $this->response = new CastResponse();
        $this->beforeRun($args, $opts);
        $this->response->fromResult($this->cast->git->{$this->command}->run($args, $opts));
        $this->afterRun($args, $opts);
        return $this->response;
    }

    /**
     * Override this method to implement logic before the GitCommand::run() method is invoked.
     *
     * @param array $args An array of arguments for the command.
     * @param array $opts An array of options for the command.
     */
    public function beforeRun(array $args = array(), array $opts = array())
    {
    }

    /**
     * Override this method to implement logic after the GitCommand::run() method is invoked.
     *
     * @param array $args An array of arguments for the command.
     * @param array $opts An array of options for the command.
     */
    public function afterRun(array $args = array(), array $opts = array())
    {
    }

    /**
     * Get an option value by key for this command.
     *
     * @param string $key The option key.
     * @param array $opts An array of options to search in.
     * @param mixed $default The default value to return if not found.
     *
     * @return mixed The option value or the default if not found.
     */
    public function opt($key, $opts, $default = false)
    {
        $value = $default;
        if (is_array($opts) && array_key_exists($key, $opts)) {
            $value = $opts[$key];
        }
        return $value;
    }

    /**
     * Determines if Cast should (un-)serialize the model data.
     *
     * @param array $opts An array of command options.
     *
     * @return bool true if
     */
    protected function shouldSerialize(array $opts = array()) {
        $shouldSerialize = ((integer)$this->cast->getOption(Cast::SERIALIZER_MODE, $opts, 0) < 1);
        $serializeOpt = $this->opt(CastCommand::SERIALIZE, $opts, null);
        if ($serializeOpt !== null) {
            $shouldSerialize = (bool)$serializeOpt;
        }
        return $shouldSerialize;
    }
}
