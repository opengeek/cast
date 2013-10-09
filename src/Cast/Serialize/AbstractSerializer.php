<?php
/**
 * This file is part of the cast package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cast\Serialize;


use Cast\Cast;

abstract class AbstractSerializer implements SerializerInterface
{
    /** @var Cast */
    public $cast;

    /** @var string */
    protected $serializedModelPath;
    /** @var array An array of classes to always exclude from serialization. */
    protected $defaultModelExcludes = array(
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
     * Get a Serializer instance.
     *
     * @param Cast $cast
     */
    public function __construct(Cast &$cast)
    {
        $this->cast = &$cast;
        $this->serializedModelPath = $this->cast->git->getPath() . '/' . $this->cast->getOption(Cast::SERIALIZED_MODEL_PATH, null, '.model/');
    }

    /**
     * Get the default template defining model serialization.
     *
     * @param array $options An array of options for the process.
     *
     * @return array An array of model classes and attributes defining their serialization.
     */
    public function getDefaultModel(array $options = array())
    {
        $excludes = array_merge(
            $this->defaultModelExcludes,
            $this->cast->getOption(Cast::SERIALIZED_MODEL_EXCLUDES, $options, array())
        );
        $classes = array_diff($this->cast->modx->getDescendants('xPDOObject'), $excludes);
        $model = array();
        foreach ($classes as $class) {
            if (in_array('class_key', array_keys($this->cast->modx->getFields($class)))) {
                $criteria['class_key'] = $class;
            } else {
                $criteria[] = "1=1";
            }
            $model[$class] = $criteria;
        }
        return $model;
    }

    /**
     * Serialize a model to files that can be tracked by Git.
     *
     * @param null|array $model An optional serialization model.
     * @param array $options An array of options for the process.
     */
    public function serializeModel($model = null, array $options = array())
    {
        if ($model === null) $model = $this->getDefaultModel($options);
        foreach ($model as $class => $criteria) {
            $iterator = $this->cast->modx->getIterator($class, $criteria, false);
            foreach ($iterator as $object) {
                $this->serialize($object, $options);
            }
        }
    }

    /**
     * Unserialize a model from files tracked by Git.
     *
     * @param null|string $path An optional path for the serialized model; uses serializedModelPath if not set.
     * @param array $options An array of options for the process.
     */
    public function unserializeModel($path = null, array $options = array())
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
     * @param \xPDOObject $object The data to serialize.
     * @param array $options An array of options for the serialization process.
     *
     * @return int|bool The number of bytes written to file or false on failure.
     */
    abstract public function serialize(\xPDOObject $object, array $options = array());

    /**
     * Unserialize a model object from file and save it into the database.
     *
     * @param string $path The path of the file to unserialize the object from.
     * @param array $options An array of options for the process.
     *
     * @throws \RuntimeException If unserialization fails to retrieve valid data.
     * @return bool TRUE if the object is saved to the database, FALSE if save fails.
     */
    abstract public function unserialize($path, array $options = array());
}
