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
use Symfony\Component\PropertyInfo\Type;

/**
 * Class AbstractModel
 *
 * @package OpenApi\Model
 */
abstract class AbstractModel implements ModelInterface
{
    /**
     * @var PropertyInfoExtractor|null
     */
    protected static ?PropertyInfoExtractor $PropertyInfoExtractor;
    /**
     * @var array Cache for reflection results
     */
    protected static array $reflectionCache = [];
    /**
     * TRUE means this object is a simple object contains one basic value, such as intger, string, datetime
     *
     * @var boolean
     */
    protected bool $isRawObject = false;
    /**
     * @var mixed
     */
    protected $rawData;

    /**
     * AbstractModel constructor.
     *
     * @param  \StdClass|array!string $data
     */
    public function __construct($data = null)
    {
        if (!isset(static::$PropertyInfoExtractor)) {
            $ReflectionExtractor = new ReflectionExtractor();
            $PhpDocExtractor     = new PhpDocExtractor();

            self::$PropertyInfoExtractor = new PropertyInfoExtractor(
                [$ReflectionExtractor],
                [$PhpDocExtractor, $ReflectionExtractor],
                [$PhpDocExtractor],
                [$ReflectionExtractor]
            );
        }


        if (is_string($data)) {
            $data = \json_decode($data, true) ?: $data;
        }

        $this->exchangeArray($data);
    }

    /**
     * @param $data
     *
     * @return self
     */
    public function exchangeArray($data): AbstractModel
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

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        $arrayCopy = [];
        if ($this->isRawObject) {
            $arrayCopy = $this->rawData;
        } else {
            $properties = self::$PropertyInfoExtractor->getProperties(get_class($this));
            foreach ($properties as $property) {
                $propertyValue = $this->getProperty($this->$property);
                if (property_exists($this, $property) && null !== $propertyValue) {
                    $propertyName = $property;
                    if (0 === strpos($propertyName, '_')) {
                        $propertyName = str_replace('_', '$', $propertyName);
                    }
                    $arrayCopy[$propertyName] = $propertyValue;
                }
            }
        }

        return $arrayCopy;
    }

    /**
     * @return bool
     */
    public function isRawObject(): bool
    {
        return $this->isRawObject;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return \json_encode($this->getArrayCopy());
    }

    /**
     * @param $index
     * @param $value
     *
     * @return $this
     */
    protected function parseProperty($index, $value): AbstractModel
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
                    if (true == ($className = $PropertyType->getCollectionValueType()->getClassName())) {
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

    /**
     * @param $object
     * @param $index
     *
     * @return Type[]|null
     */
    protected function getPropertyTypes(ModelInterface $object, $index): ?array
    {
        $objectClass = get_class($object);

        if (array_key_exists($objectClass, static::$reflectionCache) &&
            array_key_exists($index, static::$reflectionCache[$objectClass])) {
            $propertyTypes = static::$reflectionCache[$objectClass][$index];
        } else {
            $propertyTypes = static::$PropertyInfoExtractor->getTypes($objectClass, $index);

            static::$reflectionCache[$objectClass][$index] = $propertyTypes;
        }

        return $propertyTypes;
    }

    /**
     * @param $theProperty
     *
     * @return mixed|ModelInterface
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
}
