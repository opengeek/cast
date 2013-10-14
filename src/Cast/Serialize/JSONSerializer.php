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


class JSONSerializer extends AbstractSerializer
{
    protected $fileExtension = 'json';

    /**
     * Serialize a model object to file.
     *
     * @param \xPDOObject $object The data to serialize.
     * @param array $options An array of options for the serialization process.
     *
     * @return int|bool The number of bytes written to file or false on failure.
     */
    public function serialize(\xPDOObject $object, array $options = array())
    {
        $data = null;
        $path = $this->serializedModelPath;
        $segments = array();
        $segments[] = 'xPDOObject';
        $segments[] = $this->cast->modx->getTableClass($object->_class);
        $criteria = $pk = $object->getPrimaryKey();
        if (!is_array($pk)) $pk = array($pk);
        $segments[] = implode('-', $pk) . ".{$this->fileExtension}";
        $data = array(
            'class'    => $object->_class,
            'criteria' => $criteria,
            'object'   => $object->toArray('', true, false, true)
        );
        $path .= str_replace('\\', '/', implode('/', $segments));
        $beforeSerialize = isset($options['before_serialize_callback']) ? $options['before_serialize_callback'] : null;
        if ($beforeSerialize instanceof \Closure) {
            $beforeSerialize($this, $object, $data, $segments, $path, $options);
            unset($beforeSerialize);
        }
        $written = $this->cast->modx->getCacheManager()->writeFile($path, json_encode($data, version_compare(phpversion(), '5.4.0', '>=') ? JSON_PRETTY_PRINT : 0));
        $afterSerialize = isset($options['after_serialize_callback']) ? $options['after_serialize_callback'] : null;
        if ($afterSerialize instanceof \Closure) {
            $afterSerialize($this, $object, $options);
            unset($afterSerialize);
        }
        return $written;
    }

    /**
     * Unserialize a model object from file and save it into the database.
     *
     * @param string $path The path of the file to unserialize the object from.
     * @param array $options An array of options for the process.
     *
     * @throws SerializerException If unserialization fails to retrieve valid data.
     * @return bool TRUE if the object is saved to the database, FALSE if save fails.
     */
    public function unserialize($path, array $options = array())
    {
        if (is_readable($this->serializedModelPath . $path)) {
            $data = file_get_contents($this->serializedModelPath . $path);
            if (is_string($data)) {
                $payload = json_decode($data, true);
                $beforeUnserialize = isset($options['before_unserialize_callback']) ? $options['before_unserialize_callback'] : null;
                if ($beforeUnserialize instanceof \Closure) {
                    $beforeUnserialize($this, $path, $payload, $options);
                    unset($beforeUnserialize);
                }
                /** @var \xPDOObject $object */
                if (($object = $this->cast->modx->getObject($payload['class'], $payload['criteria'])) === null) {
                    $object = $this->cast->modx->newObject($payload['class']);
                    $object->fromArray($payload['object'], '', true, true);
                } else {
                    $object->fromArray($payload['object'], '', true, true);
                }
                $saved = $object->save();
                $afterUnserialize = isset($options['after_unserialize_callback']) ? $options['after_unserialize_callback'] : null;
                if ($afterUnserialize instanceof \Closure) {
                    $afterUnserialize($this, $path, $data, $object, $options);
                    unset($afterUnserialize);
                }
                return $saved;
            }
            throw new SerializerException($this, "Could not unserialize {$path} to the MODX database: no content");
        }
        throw new SerializerException($this, "Could not unserialize {$path} to the MODX database: file is not readable or does not exist");
    }
}
