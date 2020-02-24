<?php

/*                                                                        *
 * This script originally belongs to the TYPO3 Flow framework and was 	  *
 * back-ported to pt_extbase									          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This converter transforms arrays to simple objects (POPO) by setting properties.
 *
 * This converter will only be used on target types that are not entities or value objects (for those the
 * PersistentObjectConverter is used).
 *
 * The target type can be overridden in the source by setting the __type key to the desired value.
 *
 * The converter will return an instance of the target type with all properties given in the source array set to
 * the respective values. For the mechanics used to set the values see ObjectAccess::setProperty().
 *
 * @api
 * @Flow\Scope("singleton")
 */
class Tx_PtExtbase_Extbase_Property_TypeConverter_ObjectConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter
{
    /**
     * @var integer
     */
    const CONFIGURATION_TARGET_TYPE = 3;

    /**
     * @var integer
     */
    const CONFIGURATION_OVERRIDE_TARGET_TYPE_ALLOWED = 4;

    /**
     * @var array
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = 'object';

    /**
     * @var integer
     */
    protected $priority = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Reflection\ReflectionService
     */
    protected $reflectionService;


    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Reflection\ReflectionService $reflectionService
     */
    public function injectReflectionService(\TYPO3\CMS\Extbase\Reflection\ReflectionService $reflectionService): void
    {
        $this->reflectionService = $reflectionService;
    }

    /**
     * Only convert non-persistent types
     *
     * @param mixed $source
     * @param string $targetType
     * @return boolean
     */
    public function canConvertFrom($source, $targetType)
    {
        $isValueObject = is_subclass_of($targetType, '\TYPO3\CMS\Extbase\DomainObject\AbstractValueObject');
        $isEntity = is_subclass_of($targetType, '\TYPO3\CMS\Extbase\DomainObject\AbstractEntity');
        return !($isEntity || $isValueObject);
    }

    /**
     * Convert all properties in the source array
     *
     * @param mixed $source
     * @return array
     */
    public function getSourceChildPropertiesToBeConverted($source)
    {
        if (isset($source['__type'])) {
            unset($source['__type']);
        }
        return $source;
    }

    /**
     * The type of a property is determined by the reflection service.
     *
     * @param string $targetType
     * @param string $propertyName
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     * @return string
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException
     */
    public function getTypeOfChildProperty($targetType, $propertyName, \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration)
    {
        $configuredTargetType = $configuration->getConfigurationFor($propertyName)->getConfigurationValue('Tx_PtExtbase_Extbase_Property_TypeConverter_ObjectConverter', self::CONFIGURATION_TARGET_TYPE);
        if ($configuredTargetType !== null) {
            return $configuredTargetType;
        }

        if (method_exists($targetType, \TYPO3\CMS\Extbase\Reflection\ObjectAccess::buildSetterMethodName($propertyName))) {
            $methodParameters = $this->reflectionService->getMethodParameters($targetType, \TYPO3\CMS\Extbase\Reflection\ObjectAccess::buildSetterMethodName($propertyName));
            $methodParameter = current($methodParameters);
            if (!isset($methodParameter['type'])) {
                throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException('Setter for property "' . $propertyName . '" had no type hint or documentation in target object of type "' . $targetType . '".', 1303379158);
            } else {
                return $methodParameter['type'];
            }
        } else {
            $methodParameters = $this->reflectionService->getMethodParameters($targetType, '__construct');
            if (isset($methodParameters[$propertyName]) && isset($methodParameters[$propertyName]['type'])) {
                return $methodParameters[$propertyName]['type'];
            } else {
                throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException('Property "' . $propertyName . '" had no setter or constructor argument in target object of type "' . $targetType . '".', 1303379126);
            }
        }
    }

    /**
     * Convert an object from $source to an object.
     *
     * @param mixed $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     * @return object the target type
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidPropertyMappingConfigurationException
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration = null)
    {
        $object = $this->buildObject($convertedChildProperties, $targetType);
        foreach ($convertedChildProperties as $propertyName => $propertyValue) {
            $result = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::setProperty($object, $propertyName, $propertyValue);
            if ($result === false) {
                $exceptionMessage = sprintf(
                    'Property "%s" having a value of type "%s" could not be set in target object of type "%s". Make sure that the property is accessible properly, for example via an appropriate setter method.',
                    $propertyName,
                    (is_object($propertyValue) ? get_class($propertyValue) : gettype($propertyValue)),
                    $targetType
                );
                throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException($exceptionMessage, 1304538165);
            }
        }

        return $object;
    }

    /**
     * Determines the target type based on the source's (optional) __type key.
     *
     * @param mixed $source
     * @param string $originalTargetType
     * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
     * @return string
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidPropertyMappingConfigurationException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException
     * @throws \InvalidArgumentException
     */
    public function getTargetTypeForSource($source, $originalTargetType, \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration = null)
    {
        $targetType = $originalTargetType;

        if (is_array($source) && array_key_exists('__type', $source)) {
            $targetType = $source['__type'];

            if ($configuration === null) {
                throw new \InvalidArgumentException('A property mapping configuration must be given, not NULL.', 1326277369);
            }
            if ($configuration->getConfigurationValue('TYPO3\Flow\Property\TypeConverter\ObjectConverter', self::CONFIGURATION_OVERRIDE_TARGET_TYPE_ALLOWED) !== true) {
                throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidPropertyMappingConfigurationException('Override of target type not allowed. To enable this, you need to set the PropertyMappingConfiguration Value "CONFIGURATION_OVERRIDE_TARGET_TYPE_ALLOWED" to TRUE.', 1317050430);
            }

            // FIXME: The following check and the checkInheritanceChainWithoutIsA() method should be removed if we raise the PHP requirement to 5.3.9 or higher
            if (version_compare(phpversion(), '5.3.8', '>')) {
                if ($targetType !== $originalTargetType && is_a($targetType, $originalTargetType, true) === false) {
                    throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException('The given type "' . $targetType . '" is not a subtype of "' . $originalTargetType . '".', 1317048056);
                }
            } else {
                $targetType = $this->checkInheritanceChainWithoutIsA($targetType, $originalTargetType);
            }
        }

        return $targetType;
    }

    /**
     * Builds a new instance of $objectType with the given $possibleConstructorArgumentValues.
     * If constructor argument values are missing from the given array the method looks for a
     * default value in the constructor signature.
     *
     * Furthermore, the constructor arguments are removed from $possibleConstructorArgumentValues
     *
     * @param array &$possibleConstructorArgumentValues
     * @param string $objectType
     * @return object The created instance
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException if a required constructor argument is missing
     */
    protected function buildObject(array &$possibleConstructorArgumentValues, $objectType)
    {
        $className = $objectType;
        if (method_exists($className, '__construct')) {
            $constructorSignature = $this->reflectionService->getMethodParameters($className, '__construct');
            $constructorArguments = [];
            foreach ($constructorSignature as $constructorArgumentName => $constructorArgumentInformation) {
                if (array_key_exists($constructorArgumentName, $possibleConstructorArgumentValues)) {
                    $constructorArguments[] = $possibleConstructorArgumentValues[$constructorArgumentName];
                    unset($possibleConstructorArgumentValues[$constructorArgumentName]);
                } elseif ($constructorArgumentInformation['optional'] === true) {
                    $constructorArguments[] = $constructorArgumentInformation['defaultValue'];
                } else {
                    throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidTargetException('Missing constructor argument "' . $constructorArgumentName . '" for object of type "' . $objectType . '".', 1268734872);
                }
            }
            $classReflection = new \ReflectionClass($className);
            return $classReflection->newInstanceArgs($constructorArguments);
        } else {
            return new $className();
        }
    }

    /**
     * This is a replacement for the functionality provided by is_a() with 3 parameters which is only available from
     * PHP 5.3.9. It can be removed if the TYPO3.Flow PHP version requirement is raised to 5.3.9 or above.
     *
     * @param string $targetType
     * @param string $originalTargetType
     * @return string
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException
     */
    protected function checkInheritanceChainWithoutIsA($targetType, $originalTargetType)
    {
        $targetTypeToCompare = $targetType;
        do {
            if ($targetTypeToCompare === $originalTargetType) {
                return $targetType;
            }
        } while ($targetTypeToCompare = get_parent_class($targetTypeToCompare));

        throw new  \TYPO3\CMS\Extbase\Property\Exception\InvalidDataTypeException('The given type "' . $targetType . '" is not a subtype of "' . $originalTargetType . '".', 1360928582);
    }
}
