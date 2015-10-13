<?php
namespace CakeOven\CakeMap\Test\TestCase\View\Helper;

use CakeOven\CakeMap\View\Helper\CakeMapHelper;

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
     * @test
     */
    public function constructor()
    {
        $this->assertNotNull($this->CakeMap);
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
    public function mapSuccess()
    {
        $mapJs = $this->CakeMap->map();

        // assert that the map canvas is in the HTML
        $expectedMapDiv = "<div id='map_canvas' style='width:600px; height:600px; style'></div>";
        $this->assertContains($expectedMapDiv, $mapJs);

        // assert that the map canvas is in the HTML
        $mapJs = $this->CakeMap->map([
            "id" => "map",
            "width" => "600px",
            "height" => "500px",
        ]);
        $expectedMapDiv = "<div id='map' style='width:600px; height:500px; style'></div>";
        $this->assertContains($expectedMapDiv, $mapJs);
    }
}
