<?php
use lib\struct\Struct;
use lib\struct\collection\Collection;

/**
 * Test the lib\struct\collection\Collection class
 * @author Joshua Ross <joshualross@gmail.com>
 */
class CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Return an array of structs
     * param
     * @return type
     */
    public function structDataProvider()
    {
        return array(
        	array(
        	    array(
        	        'foo' => new StructMock(array('foo' => true)),
        	        'bar' => new StructMock(array('bar' => true)),
        	        'baz' => new StructMock(array('baz' => true)),
                ),
            ),
        );
    }

    /**
     * Test offset get
     * @test
     * @param array $structs
     * @dataProvider structDataProvider
     */
    public function constructWithInitialValues($structs)
    {
        $collection = new Collection($structs);
        foreach ($structs as $key => $struct)
            $this->assertTrue($collection->offsetExists($key));
    }

    /**
     * ArrayAccess
     * @test
     * @param array $structs
     * @dataProvider structDataProvider
     */
    public function arrayAccess($structs)
    {
        $collection = new Collection();
        foreach ($structs as $key => $struct)
        {
            $collection[$key] = $struct;
            $this->assertEquals($struct, $collection[$key]);
        }
    }

    /**
     * Test count
     * @test
     * @param array $structs
     * @dataProvider structDataProvider
     */
    public function countMatches($structs)
    {
        $collection = new Collection($structs);
        $this->assertEquals(count($structs), $collection->count());
    }

    /**
     * Test toJSON - importantly, make sure we only json encode the rows
     * @test param
     * @param array $structs
     * @dataProvider structDataProvider
     */
    public function toJSON($structs)
    {
        $expected = json_encode($structs);
        $collection = new Collection($structs);
        $this->assertEquals($expected, $collection->toJSON());
    }
}

/**
 * A Mock of a struct, for testing
 * @author Joshua Ross <joshualross@gmail.com>
 */
class StructMock extends Struct
{
    public $foo = false;
    public $bar = false;
    public $baz = false;
}
