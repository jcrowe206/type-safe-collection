<?php

namespace JCrowe\TypeSafeCollection;


use Illuminate\Support\Collection;
use InvalidArgumentException;

class TypeSafeCollection extends Collection {


    /**
     * If set to true, no exception will be thrown
     * but invalid elements will not be added
     *
     * @var bool
     */
    protected $ignoreInvalidElements = false;

    /**
     * @var array
     */
    protected $allowedClasses;


    /**
     * Validates the provided array against the allowed classes property
     * and constructs the Collection object
     *
     * @param array $elements
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function __construct(array $elements = [])
    {
        $this->assertValidAllowedTypesAreSet();

        if ($this->shouldIgnoreInvalidElements()) {

            $elements = $this->getValidElements($elements);

        } else {

            $this->assertElementsAreValidTypes($elements);
        }

        parent::__construct($elements);
    }


    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function put($key, $value)
    {
        if ($this->isValidElement($value)) {

            parent::put($key, $value);

        } else {

            $this->handleInvalidElement($value);
        }
    }


    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function prepend($value)
    {
        if ($this->isValidElement($value)) {

            parent::prepend($value);

        } else {

            $this->handleInvalidElement($value);
        }

    }



    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function push($value)
    {

        if ($this->isValidElement($value)) {

            parent::push($value);

        } else {

            $this->handleInvalidElement($value);
        }
    }


    /**
     * @param $element
     * @return bool
     */
    protected function isInValidElement($element)
    {
        return !$this->isValidElement($element);
    }


    /**
     * @param $element
     * @return bool
     */
    protected function isValidElement($element)
    {
        foreach ($this->allowedClasses as $allowedClass) {

            if ($element instanceof $allowedClass) {

                return $this->passesInternalCheck($element);
            }
        }

        return false;
    }


    /**
     * @param array $elements
     * @throws \InvalidArgumentException
     */
    private function assertElementsAreValidTypes(array $elements)
    {
        foreach ($elements as $element) {

            $this->assertIsValidElement($element);
        }
    }


    /**
     * @param array $elements
     * @return array
     */
    private function getValidElements(array $elements)
    {
        $isAssociativeArray = array_keys($elements) !== range(0, count($elements) - 1);

        foreach ($elements as $key => $element) {

            if ($this->isInvalidElement($element)) {

                unset($elements[$key]);
            }
        }

        return $isAssociativeArray ? $elements : array_values($elements);
    }

    /**
     * Assert that the provided $element can be added to the collection
     *
     * @param $element
     * @throws InvalidArgumentException
     */
    private function assertIsValidElement($element)
    {
        if ($this->isInvalidElement($element)) {

            $this->handleInvalidElement($element);

            return false;
        }

        return true;
    }


    /**
     * @param $object
     * @return bool
     */
    private function passesInternalCheck($object)
    {
        if (method_exists($this, 'onAddNewElement')) {

            return $this->onAddNewElement($object);
        }

        return true;
    }


    /**
     * @throws \Exception
     */
    private function assertValidAllowedTypesAreSet()
    {
        if ($this->allowedClasses === null || !is_array($this->allowedClasses) || count($this->allowedClasses) === 0) {

            throw new \Exception("TypeSafeCollection requires that \$allowedClasses is set and is a not empty array. None provided in " . get_class($this));
        }
    }


    /**
     * @param $element
     * @throws InvalidArgumentException
     */
    private function handleInvalidElement($element)
    {

        if ($this->shouldIgnoreInvalidElements()) {

            return false;
        }

        $type = gettype($element) === 'object' ? get_class($element) : gettype($element);

        $message = get_class($this) . ' only accepts elements of types ' . implode(',', $this->allowedClasses) . '. ' . $type . ' provided.';
        
        throw new InvalidArgumentException($message);
    }


    /**
     * @return bool
     */
    private function shouldIgnoreInvalidElements()
    {
        return $this->ignoreInvalidElements;
    }


}