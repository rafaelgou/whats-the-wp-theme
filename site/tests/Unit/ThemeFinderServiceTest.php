<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ThemeFinderService;

class ThemeFinderServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testBasic()
    {
        $uris = [
            // "https://www.consultparv.com/",
            // "https://tech.rgou.net",
            // "http://www.social-operator.com/",
            // "http://www.fleetkleenservice.com/",
            // "https://hs.utah.gov",
            // "https://symplectic.co.uk",
            // "https://eringiles.com",
            // "https://visualimpactfitness.com",
            // "https://westgatereservations.com",
            // "https://lumos.digital",
            // "https://familylawgroup.com",
            // "https://austinstoneworship.com",
            // "https://thekingdomcenterchurch.com",
            // "https://outwardboundcroatia.com",
            // "https://smilesoftware.com",
            // "https://journeymadison.com",
            // "https://modernclimatecontrol.com",
            // "https://archvista.com",
            "https://centigo.se",
            "https://navasotaenergy.com",
            "https://claudemirpereira.com.br",
            "https://gilmargoulart.com",
        ];

        foreach ($uris as $uri) {
            $service = new ThemeFinderService($uri, '127.0.0.1');
            $search = $service->search();
            $result = strpos($search->uri, $uri) === 0;
            $this->assertTrue($result);
        }
    }
}
