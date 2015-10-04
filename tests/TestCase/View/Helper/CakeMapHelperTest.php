<?php
namespace CakeMap\Test\TestCase\View\Helper;

use CakeMap\View\Helper\CakeMapHelper;

/**
 * Class CakeMapHelperTest
 */
class CakeMapHelperTest extends \PHPUnit_Framework_TestCase
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
     *
     */
    public function testConstructor()
    {
        $this->assertNotNull($this->CakeMap);
        $this->assertInstanceOf("CakeMapHelper", $this->CakeMap);
    }

    /**
     *
     */
    public function testMap()
    {
        $mapJs = $this->CakeMap->map();

        // assert that the map canvas is in the HTML
        $expectedMapDiv = "<div id='map_canvas' style='width:800px; height:800px; style'></div>";
        $this->assertTrue(strpos($mapJs, $expectedMapDiv) !== false);

        // assert that the map canvas is in the HTML
        $mapJs = $this->CakeMap->map([
            "id" => "new_map_canvas",
            "width" => "600px",
            "height" => "500px",
        ]);
        $expectedMapDiv = "<div id='new_map_canvas' style='width:600px; height:500px; style'></div>";
        $this->assertTrue(strpos($mapJs, $expectedMapDiv) !== false);
    }
}
