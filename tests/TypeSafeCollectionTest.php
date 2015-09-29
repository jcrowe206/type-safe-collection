<?php

namespace JCrowe\TypeSafeCollection\Tests;

use InvalidArgumentException;
use JCrowe\TypeSafeCollection\TypeSafeCollection;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase as TestCase;

class TypeSafeCollectionTest extends TestCase {


    /**
     * @expectedException \Exception
     */
    public function testNoAllowedClassThrowsException()
    {
        new CollectionTestMockNoRestrictions();
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testWrongClassThrowsException()
    {
        new CollectionTestMockWithRestrictions([
            new CollectionTestAllowedObject1(),
            new CollectionTestAllowedObject2(),
            new \stdClass(),
            new CollectionTestAllowedObject3(),
        ]);
    }


    /**
     * No exception thrown for valid objects
     */
    public function testAddingCorrectClassDoesNotThrowException()
    {
        new CollectionTestMockWithRestrictions([
            new CollectionTestAllowedObject1(),
            new CollectionTestAllowedObject2(),
            new CollectionTestAllowedObject3(),
        ]);

        static::assertTrue(true);
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testAdditionalUserProvidedChecks()
    {
        new CollectionTestMockWithUserProvidedCheck([
            new CollectionTestAllowedObject1(),
            new CollectionTestAllowedObject2(),
            new CollectionTestAllowedObject3(),
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddingInvalidElementThrowsException()
    {
        $collection = $this->getCollection();

        $collection->push(new \stdClass());
    }



    public function testPushingValidElementDoesNotThrowException()
    {
        $collection = $this->getCollection();
        $count = $collection->count();

        $collection->push(new CollectionTestAllowedObject1());

        static::assertEquals($count + 1, $collection->count());
    }


    public function testPuttingValidElementDoesNotThrowException()
    {
        $collection = $this->getCollection();

        $collection->put('foo', new CollectionTestAllowedObject1());

        static::assertNotEmpty($collection->get('foo'));
    }


    private function getCollection()
    {
        return new CollectionTestMockWithRestrictions([
            new CollectionTestAllowedObject1(),
            new CollectionTestAllowedObject2(),
            new CollectionTestAllowedObject3(),
        ]);
    }

}

/* ---------------------------------------------------------
 *  Mock classes below
 * ---------------------------------------------------------
 */
class CollectionTestMockNoRestrictions extends TypeSafeCollection {}


class CollectionTestMockWithRestrictions extends TypeSafeCollection {

    protected $allowedClasses = [
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject1',
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject2',
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject3'
    ];
}

class CollectionTestMockWithUserProvidedCheck extends TypeSafeCollection {

    protected $allowedClasses = [
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject1',
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject2',
        'JCrowe\TypeSafeCollection\Tests\CollectionTestAllowedObject3'
    ];

    protected function onAddNewElement($element)
    {
        if (!method_exists($element, 'setName')) {

            return false;
        }
    }
}

trait CollectionTestNamer {

    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName() {
        return !empty($this->name) ? $this->name : get_class($this);
    }
}

class CollectionTestAllowedObject1 {
    use CollectionTestNamer;
};

class CollectionTestAllowedObject2 {
    use CollectionTestNamer;
};
class CollectionTestAllowedObject3 {};