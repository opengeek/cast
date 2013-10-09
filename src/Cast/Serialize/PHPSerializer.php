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
        $segments[] = $object->_class;
        switch ($object->_class) {
            case 'modResource':
//                break;
            case 'modCategory':
//                break;
            default:
                $criteria = $pk = $object->getPrimaryKey();
                if (!is_array($pk)) $pk = array($pk);
                $segments[] = implode('-', $pk) . ".{$this->fileExtension}";
        }
        $data = array(
            'class'    => $object->_class,
            'criteria' => $criteria,
            'object'   => $object->toArray('', true, false, true)
        );
        $path .= str_replace('\\', '/', implode('/', $segments));
        return $this->cast->modx->getCacheManager()->writeFile($path, "<?php return " . var_export($data, true) . ";\n");
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
        $absPath = $this->serializedModelPath . $path;
        if (is_readable($absPath)) {
            $data = include $absPath;
            if (is_array($data) && isset($data['class']) && isset($data['object'])) {
                /** @var \xPDOObject $object */
                if (($object = $this->cast->modx->getObject($data['class'], $data['criteria'])) === null) {
                    $object = $this->cast->modx->newObject($data['class']);
                    $object->fromArray($data['object'], '', true, true);
                } else {
                    $object->fromArray($data['object'], '', true, true);
                }
                return $object->save();
            }
            throw new \RuntimeException("Could not unserialize {$path} to the MODX database: no content");
        }
        throw new \RuntimeException("Could not unserialize {$path} to the MODX database: file is not readable or does not exist");
    }
}
