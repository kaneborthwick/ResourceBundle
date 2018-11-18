<?php

namespace ResourceBundle\Hydrator;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as BaseDoctrineObjectHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\Inflector;
use Traversable;

/**
 *
 */
class DoctrineObjectHydrator extends BaseDoctrineObjectHydrator
{

    public function hydrateValue($name, $value, $data = null)
    {
        $value = \Zend\Hydrator\AbstractHydrator::hydrateValue($name, $value, $data);

        if (is_null($value) && method_exists($this->metadata, 'isNullable')) {
            return null;
        }

        return $this->handleTypeConversions($value, $this->metadata->getTypeOfField($name));
    }

    protected function toMany($object, $collectionName, $target, $values)
    {

        $metadata = $this->objectManager->getClassMetadata(ltrim($target, '\\'));
        $target = $metadata->name;
        $identifier = $metadata->getIdentifier();

        if (!is_array($values) && !$values instanceof Traversable) {
            $values = (array) $values;
        }

        $collection = [];

        // If the collection contains identifiers, fetch the objects from database
        foreach ($values as $value) {
            if ($value instanceof $target) {
                // assumes modifications have already taken place in object
                $collection[] = $value;
                continue;
            } elseif (empty($value)) {
                // assumes no id and retrieves new $target
                $collection[] = $this->find($value, $target);
                continue;
            }

            $find = [];
            if (is_array($identifier)) {
                foreach ($identifier as $field) {
                    switch (gettype($value)) {
                        case 'object':
                            $getter = 'get' . Inflector::classify($field);

                            if (is_callable([$value, $getter])) {
                                $find[$field] = $value->$getter();
                            } elseif (property_exists($value, $field)) {
                                $find[$field] = $value->$field;
                            }
                            break;
                        case 'array':
                            if (array_key_exists($field, $value) && $value[$field] != null) {
                                $find[$field] = $value[$field];
                                // removed to allow crud api to set id from client
                                //unset($value[$field]); // removed identifier from persistable data
                            }
                            break;
                        default:
                            $find[$field] = $value;
                            break;
                    }
                }
            }

            if (!empty($find) && $found = $this->find($find, $target)) {
                $collection[] = (is_array($value)) ? $this->hydrate($value, $found) : $found;
            } else {
                $targetNetadata = $this->objectManager->getClassMetadata($target);
                $collection[] = (is_array($value)) ? $this->hydrate($value, new $target) : new $target;
            }
        }

        $collection = array_filter(
            $collection,
            function ($item) {
                return null !== $item;
            }
        );

        // Set the object so that the strategy can extract the Collection from it

        /** @var \DoctrineModule\Stdlib\Hydrator\Strategy\AbstractCollectionStrategy $collectionStrategy */
        $collectionStrategy = $this->getStrategy($collectionName);
        $collectionStrategy->setObject($object);

        // We could directly call hydrate method from the strategy, but if people want to override
        // hydrateValue function, they can do it and do their own stuff
        $this->hydrateValue($collectionName, $collection, $values);
    }
}
