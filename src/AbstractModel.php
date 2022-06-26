<?php

/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenAPI\Runtime;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

/**
 * Class AbstractModel
 *
 * @package OpenApi\Model
 */
abstract class AbstractModel implements ModelInterface
{
    protected static PropertyInfoExtractor $PropertyInfoExtractor;
    /**
     * @var array Cache for reflection results
     */
    protected static array $reflectionCache = [];
    /**
     * TRUE means this object is a simple object contains one basic value, such as intger, string, datetime
     */
    protected bool $isRawObject = false;
    /**
     * @var mixed
     */
    protected $rawData;

    /**
     * @param  mixed  $data
     */
    public function __construct($data = null)
    {
        if (!isset(static::$PropertyInfoExtractor)) {
            static::$PropertyInfoExtractor = $this->initializePropertyInfoExtractor();
        }

        if (is_string($data)) {
            $data = \json_decode($data, true) ?: $data;
        }

        $this->exchangeArray($data);
    }

    /**
     * @param  mixed  $data
     *
     * @return static
     */
    public function exchangeArray($data): ModelInterface
    {
        if ($this->isRawObject) {
            $this->rawData = $data;

            return $this;
        } else {
            foreach ((array)$data as $index => $value) {
                if (property_exists($this, $index)) {
                    $this->parseProperty($index, $value);
                }
            }
        }

        return $this;
    }

    public function getArrayCopy(): array
    {
        $arrayCopy = [];
        if ($this->isRawObject) {
            $arrayCopy = $this->rawData;
        } else {
            $properties = self::$PropertyInfoExtractor->getProperties(get_class($this));
            if ($properties) {
                foreach ($properties as $property) {
                    if (property_exists($this, $property)) {
                        $propertyValue = $this->getProperty($this->$property);
                        if (null !== $propertyValue) {
                            $propertyName = $property;
                            if (str_starts_with($propertyName, '_')) {
                                $propertyName = str_replace('_', '$', $propertyName);
                            }
                            $arrayCopy[$propertyName] = $propertyValue;
                        }
                    }
                }
            }
        }

        return $arrayCopy;
    }

    public function isRawObject(): bool
    {
        return $this->isRawObject;
    }

    public function toJson(): string
    {
        return \json_encode($this->getArrayCopy());
    }

    /**
     * @param  string  $index
     * @param  mixed   $value
     *
     * @return ModelInterface
     */
    protected function parseProperty(string $index, $value): ModelInterface
    {
        $propertyTypes = $this->getPropertyTypes($this, $index);

        $countValue = 0;
        if ($propertyTypes && isset($value)) {
            if (is_array($value)) {
                $countValue = count($value);
            } elseif (is_string($value) || is_integer($value) || is_object($value)) {
                $countValue = 1;
            }
        }

        if (0 < $countValue) {
            foreach ($propertyTypes as $PropertyType) {
                if ($PropertyType->isCollection()) {
                    $values = [];
                    // symfony/property-info < v6
                    if (method_exists($PropertyType, 'getCollectionValueType')) {
                        // @codeCoverageIgnoreStart
                        if (($className = $PropertyType->getCollectionValueType()->getClassName())) {
                            foreach ((array)$value as $valueItem) {
                                /** @var ModelInterface $propertyValue */
                                $PropertyValue = new $className($valueItem);
                                if ($PropertyValue instanceof ModelInterface && !$PropertyValue->isRawObject()) {
                                    $values[] = $PropertyValue;
                                } else {
                                    $values[] = $valueItem;
                                }
                            }
                        }
                        // @codeCoverageIgnoreEnd
                    }

                    // symfony/property-info >= v6
                    if (method_exists($PropertyType, 'getCollectionValueTypes')) {
                        if (($className = $PropertyType->getCollectionValueTypes()[0]->getClassName())) {
                            foreach ((array)$value as $valueItem) {
                                /** @var ModelInterface $propertyValue */
                                $PropertyValue = new $className($valueItem);
                                if ($PropertyValue instanceof ModelInterface && !$PropertyValue->isRawObject()) {
                                    $values[] = $PropertyValue;
                                } else {
                                    $values[] = $valueItem;
                                }
                            }
                        }
                    }

                    if (1 <= count($values)) {
                        $value = $values;
                        break;
                    }
                } elseif ($PropertyType->getClassName()) {
                    $className = $PropertyType->getClassName();
                    /** @var ModelInterface $propertyValue */
                    $PropertyValue = new $className($value);
                    if (!($PropertyValue instanceof ModelInterface && $PropertyValue->isRawObject())) {
                        $value = new $className($value);
                    }
                }
            }
        }
        $this->$index = $value;


        return $this;
    }

    protected function getPropertyTypes(ModelInterface $object, string $index): array
    {
        $objectClass = get_class($object);

        if (
            array_key_exists($objectClass, static::$reflectionCache) &&
            array_key_exists($index, static::$reflectionCache[$objectClass])
        ) {
            $propertyTypes = static::$reflectionCache[$objectClass][$index];
        } else {
            $propertyTypes = static::$PropertyInfoExtractor->getTypes($objectClass, $index) ?? [];

            static::$reflectionCache[$objectClass][$index] = $propertyTypes;
        }

        return $propertyTypes;
    }

    /**
     * @param  mixed  $theProperty
     *
     * @return mixed
     */
    protected function getProperty($theProperty)
    {
        $arrayCopy = null;

        if ($theProperty instanceof ModelInterface) {
            $arrayCopy = $theProperty->getArrayCopy();
        } elseif (is_array($theProperty)) {
            foreach ($theProperty as $key => $property) {
                $arrayCopy[$key] = $this->getProperty($property);
            }
        } else {
            $arrayCopy = $theProperty;
        }

        return $arrayCopy;
    }

    private function initializePropertyInfoExtractor(): PropertyInfoExtractor
    {
        $ReflectionExtractor = new ReflectionExtractor();
        $PhpDocExtractor     = new PhpDocExtractor();

        return new PropertyInfoExtractor(
            [$ReflectionExtractor],
            [$PhpDocExtractor, $ReflectionExtractor],
            [$PhpDocExtractor],
            [$ReflectionExtractor]
        );
    }
}
