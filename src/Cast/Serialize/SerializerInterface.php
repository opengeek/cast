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

/**
 * Provides the API contract for a Cast Serializer implementation.
 *
 * Serializers do the work of serializing and unserializing data to and from
 * files that are tracked in a Git repository.
 *
 * @package Cast\Serialize
 */
interface SerializerInterface
{
    /**
     * Serialize a model to files that can be tracked by Git.
     *
     * @param null|array $model An optional serialization model.
     * @param array $options An array of options for the process.
     */
    public function serializeModel($model = null, array $options = array());

    /**
     * Serialize a model object to file.
     *
     * @param \xPDOObject $object The data to serialize.
     * @param array $options An array of options for the serialization process.
     *
     * @return int|bool The number of bytes written to file or false on failure.
     */
    public function serialize(\xPDOObject $object, array $options = array());

    /**
     * Unserialize a model from files tracked by Git.
     *
     * @param null|string $path An optional path for the serialized model; uses serializedModelPath if not set.
     * @param array $options An array of options for the process.
     */
    public function unserializeModel($path, array $options = array());

    /**
     * Unserialize a model object from file and save it into the database.
     *
     * @param string $path The path of the file to unserialize the object from.
     * @param array $options An array of options for the process.
     *
     * @throws \RuntimeException If unserialization fails to retrieve valid data.
     * @return bool TRUE if the object is saved to the database, FALSE if save fails.
     */
    public function unserialize($path, array $options = array());
}
