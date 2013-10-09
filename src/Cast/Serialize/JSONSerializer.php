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
        return $this->cast->modx->getCacheManager()->writeFile($path, json_encode($data, version_compare(phpversion(), '5.4.0', '>=') ? JSON_PRETTY_PRINT : 0));
    }

    /**
     * Unserialize a model object from file and save it into the database.
     *
     * @param string $path The path of the file to unserialize the object from.
     * @param array $options An array of options for the process.
     *
     * @throws \RuntimeException If unserialization fails to retrieve valid data.
     * @return bool TRUE if the object is saved to the database, FALSE if save fails.
     */
    public function unserialize($path, array $options = array())
    {
        if (is_readable($this->serializedModelPath . $path)) {
            $data = file_get_contents($this->serializedModelPath . $path);
            if (is_string($data)) {
                $payload = json_decode($data, true);
                /** @var \xPDOObject $object */
                if (($object = $this->cast->modx->getObject($payload['class'], $payload['criteria'])) === null) {
                    $object = $this->cast->modx->newObject($payload['class']);
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
}
