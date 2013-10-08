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

use Cast\Commands\CastCommand;
use Cast\Git\Git;

/**
 * The Cast command controller class.
 *
 * @package Cast
 */
class Cast
{
    const GIT_PATH = 'cast.git_path';
    const SERIALIZED_MODEL_PATH = 'cast.serialized_model_path';
    const SERIALIZED_MODEL_EXCLUDES = 'cast.serialized_model_excludes';

    /** @var \modX The MODX instance referenced by this Cast instance. */
    public $modx;
    /** @var Git A Git instance referenced by this Cast instance. */
    public $git;
    /** @var array An array of GitCommand classes loaded (on-demand). */
    protected $commands = array();
    /** @var array A cached array of config options. */
    protected $options = array();
    /** @var string The path where the serialized model objects are stored. */
    protected $serializedModelPath;
    /** @var array An array of classes to always exclude from serialization. */
    protected $serializedModelExcludes = array(
        'xPDOObject',
        'xPDOSimpleObject',
        'modAccess',
        'modAccessibleObject',
        'modAccessibleSimpleObject',
        'modActiveUser',
        'modDbRegisterQueue',
        'modDbRegisterTopic',
        'modDbRegisterMessage',
        'modManagerLog',
        'modPrincipal',
        'modSession',
    );

    /**
     * Construct a new instance of Cast
     *
     * @param \modX &$modx A reference to a modX instance to work with.
     * @param array $options An array of options for the Cast instance.
     */
    public function __construct(\modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->options = $options;
        $gitPath = $this->getOption(self::GIT_PATH, null, $this->modx->getOption('base_path', null, MODX_BASE_PATH));
        $this->git = new Git($gitPath, $options);
        $this->serializedModelPath = $gitPath . $this->getOption(Cast::SERIALIZED_MODEL_PATH, null, '.model/');
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

    /**
     * Get the default serialization profile.
     *
     * @param array $excludes An array of classes to exclude from data serialization.
     *
     * @return array An array of classes and criteria for data serialization.
     */
    public function defaultProfile(array $excludes = array())
    {
        $excludes = array_merge(
            $this->serializedModelExcludes,
            $this->getOption(Cast::SERIALIZED_MODEL_EXCLUDES, null, array()),
            $excludes
        );
        $classes = array_diff($this->modx->getDescendants('xPDOObject'), $excludes);
        $profile = array();
        foreach ($classes as $class) {
            if (in_array('class_key', array_keys($this->modx->getFields($class)))) {
                $criteria['class_key'] = $class;
            } else {
                $criteria[] = "1=1";
            }
            $profile[$class] = $criteria;
        }
        return $profile;
    }

    /**
     * Serialize a model to files that can be tracked by Git.
     *
     * @param null|array $profile An optional serialization profile.
     * @param null|string $path An optional path for serialization; uses serializedModelPath if not set.
     */
    public function serializeModel($profile = null, $path = null)
    {
        if ($profile === null) $profile = $this->defaultProfile();
        foreach ($profile as $class => $criteria) {
            $iterator = $this->modx->getIterator($class, $criteria, false);
            foreach ($iterator as $object) {
                $this->serialize($object, $path);
            }
        }
    }

    /**
     * Unserialize a model from files tracked by Git.
     *
     * @param null|string $path An optional path for the serialized model; uses serializedModelPath if not set.
     */
    public function unserializeModel($path = null)
    {
        if ($path === null) $path = $this->serializedModelPath;
        $directory = new \DirectoryIterator($path);
        /** @var \SplFileInfo $file */
        foreach ($directory as $file) {
            if (in_array($file->getFilename(), array('.', '..', '.DS_Store'))) continue;
            $relPath = substr($file->getPathname(), strlen($this->serializedModelPath));
            if ($file->isFile() && $file->getExtension() === 'json') $this->unserialize($relPath);
            if ($file->isDir()) $this->unserializeModel($this->serializedModelPath . $relPath);
        }
    }

    /**
     * Serialize a model object to file.
     *
     * @param \xPDOObject $object The object to serialize.
     * @param null|string $path An optional path for serialization; uses serializedModelPath if not set.
     *
     * @return int|bool The number of bytes written to file or false on failure.
     */
    public function serialize(\xPDOObject $object, $path = null)
    {
        $data = null;
        if ($path === null) $path = $this->serializedModelPath;
        $segments = array();
        $segments[] = 'xPDOObject';
        $segments[] = $object->_class;
        switch ($object->_class) {
            case 'modResource':
//                break;
            case 'modCategory':
//                break;
            default:
                $criteria = $pk = $object->getPrimaryKey();
                if (!is_array($pk)) $pk = array($pk);
                $segments[] = implode('-', $pk) . '.json';
        }
        $data = array(
            'class'    => $object->_class,
            'criteria' => $criteria,
            'object'   => $object->toArray('', true, false, true)
        );
        $path .= str_replace('\\', '/', implode('/', $segments));
        return $this->modx->getCacheManager()->writeFile($path, json_encode($data, version_compare(phpversion(), '5.4.0', '>=') ? JSON_PRETTY_PRINT : 0));
    }

    /**
     * Unserialize a model object from file and save it into the database.
     *
     * @param string $path The path of the file to unserialize the object from.
     *
     * @throws \RuntimeException If unserialization fails to retrieve valid data.
     * @return bool TRUE if the object is saved to the database, FALSE if save fails.
     */
    public function unserialize($path)
    {
        if (is_readable($this->serializedModelPath . $path)) {
            $data = file_get_contents($this->serializedModelPath . $path);
            if (is_string($data)) {
                $payload = json_decode($data, true);
                /** @var \xPDOObject $object */
                if (($object = $this->modx->getObject($payload['class'], $payload['criteria'])) === null) {
                    $object = $this->modx->newObject($payload['class']);
                    $object->fromArray($payload['object'], '', true, true);
                } else {
                    $object->fromArray($payload['object'], '', true, true);
                }
                return $object->save();
            }
            throw new \RuntimeException("Could not unserialize {$path} to the MODX database: no content");
        }
        throw new \RuntimeException("Could not unserialize {$path} to the MODX database: file is not readable or does not exist");
    }

    /**
     * Return the fully qualified Cast Command class for a command.
     *
     * @param string $name The name of the command.
     *
     * @return string The fully qualified Cast Command class.
     */
    public function commandClass($name)
    {
        $namespace = explode('\\', __NAMESPACE__);
        $prefix = array_pop($namespace);
        $className = $prefix . ucfirst($name);
        return __NAMESPACE__ . "\\Commands\\{$className}";
    }

    /**
     * Magically load, instantiate and run() a Cast Command Class
     *
     * @param string $name The command to run.
     * @param array $arguments The arguments to pass to the command.
     *
     * @throws \BadMethodCallException If no CastCommand class exists for the specified name.
     * @return mixed The results of the command.
     */
    public function __call($name, $arguments)
    {
        if (!array_key_exists($name, $this->commands)) {
            $commandClass = $this->commandClass($name);
            if (class_exists($commandClass)) {
                $this->commands[$name] = new $commandClass($this);
                return call_user_func_array(array($this->commands[$name], 'run'), array($arguments));
            }
            throw new \BadMethodCallException(sprintf('The Cast Command class %s does not exist', $commandClass));
        }
        return call_user_func_array(array($this->commands[$name], 'run'), array($arguments));
    }

    /**
     * Magically load and instantiate a Cast Command Class
     *
     * @param string $name The command to load.
     *
     * @throws \InvalidArgumentException If no CastCommand class exists for the specified name.
     * @return CastCommand The CastCommand class for the specified command.
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->commands)) {
            $commandClass = $this->commandClass($name);
            if (class_exists($commandClass)) {
                $this->commands[$name] = new $commandClass($this);
                return $this->commands[$name];
            }
            throw new \InvalidArgumentException(sprintf('The Cast Command class %s does not exist', $commandClass));
        }
        return $this->commands[$name];
    }

    /**
     * Test if a CastCommand class exists for the specified name.
     *
     * @param string $name The command to test.
     *
     * @return bool TRUE if the CastCommand class exists for the specified name, FALSE otherwise.
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->commands);
    }
}
