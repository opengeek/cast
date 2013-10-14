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


class PHPSerializer extends AbstractSerializer
{
    protected $fileExtension = 'model';

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
        $written = $this->cast->modx->getCacheManager()->writeFile($path, "<?php return " . var_export($data, true) . ";\n");
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
        $absPath = $this->serializedModelPath . $path;
        if (is_readable($absPath)) {
            $data = include $absPath;
            if (is_array($data) && isset($data['class']) && isset($data['object'])) {
                $beforeUnserialize = isset($options['before_unserialize_callback']) ? $options['before_unserialize_callback'] : null;
                if ($beforeUnserialize instanceof \Closure) {
                    $beforeUnserialize($this, $path, $data, $options);
                    unset($beforeUnserialize);
                }
                /** @var \xPDOObject $object */
                if (($object = $this->cast->modx->getObject($data['class'], $data['criteria'])) === null) {
                    $object = $this->cast->modx->newObject($data['class']);
                    $object->fromArray($data['object'], '', true, true);
                } else {
                    $object->fromArray($data['object'], '', true, true);
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
