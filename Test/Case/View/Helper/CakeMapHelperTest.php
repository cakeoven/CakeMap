<?php

/**
 * Class GoogleMapHelperTest
 */
class CakeMapHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CakeMapHelper
     */
    protected $CakeMap;

    /**
     *
     */
    protected function setUp()
    {
        $this->CakeMap = new CakeMapHelper();
    }

    /**
     * @test
     */
    public function getVersion()
    {
        $this->assertNotEmpty($this->CakeMap->getVersion());
    }

    /**
     * @test
     */
    public function testConstructor()
    {
        $this->assertNotNull($this->CakeMap);
        $this->assertInstanceOf("CakeMapHelper", $this->CakeMap);
    }

    /**
     * @test
     */
    public function testMap()
    {
        $map = $this->CakeMap->map();

        // assert that the map canvas is in the HTML
        $expectedMapDiv = "<div id='map_canvas' style='width:600px; height:600px; style'></div>";
        $this->assertContains($map, $expectedMapDiv);

        // assert that the map canvas is in the HTML
        $map = $this->CakeMap->map([
            "id" => "new_map_canvas",
            "width" => "600px",
            "height" => "500px",
        ]);
        $expectedMapDiv = "<div id='new_map_canvas' style='width:600px; height:500px; style'></div>";
        $this->assertContains($map, $expectedMapDiv);
    }
}
