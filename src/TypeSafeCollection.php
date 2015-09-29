<?php

namespace JCrowe\TypeSafeCollection;


use Illuminate\Support\Collection;
use InvalidArgumentException;

class TypeSafeCollection extends Collection {



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

        $this->assertElementsAreValidTypes($elements);

        parent::__construct($elements);
    }


    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function put($key, $value)
    {
        $this->assertIsValidElement($value);

        parent::put($key, $value);
    }


    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function prepend($value)
    {
        $this->assertIsValidElement($value);

        parent::prepend($value);
    }



    /**
     * Type protection
     *
     * @param mixed $value
     */
    public function push($value)
    {
        $this->assertIsValidElement($value);

        parent::push($value);
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
     * Assert that the provided $element can be added to the collection
     *
     * @param $element
     * @throws InvalidArgumentException
     */
    private function assertIsValidElement($element)
    {
        foreach ($this->allowedClasses as $allowedClass) {

            if ($element instanceof $allowedClass && $this->passesInternalCheck($element)) {

                return true;
            }
        }

        $type = gettype($element) === 'object' ? get_class($element) : gettype($element);

        if (!$this->passesInternalCheck($element)) {

            throw new InvalidArgumentException('Invalid argument supplied to ' . get_class($this) . '.');
        }

        throw new InvalidArgumentException(

            get_class($this) . ' only accepts elements of types ' . implode(',', $this->allowedClasses) . '. ' . $type . ' provided.'
        );
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


}