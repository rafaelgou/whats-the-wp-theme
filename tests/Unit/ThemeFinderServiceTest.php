<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ThemeFinderService;

class ThemeFinderServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function validSitesProvider()
    {
        return [
            ["https://www.consultparv.com/"],
            ["http://www.social-operator.com/"],
            ["http://elac.com/"],
            ["https://eringiles.com"],
            ["https://lumos.digital"],
            ["https://familylawgroup.com"],
            ["https://austinstoneworship.com"],
            ["https://thekingdomcenterchurch.com"],
            ["https://outwardboundcroatia.com"],
            ["https://smilesoftware.com"],
            ["https://journeymadison.com"],
            ["https://modernclimatecontrol.com"],
            ["https://archvista.com"],
            ["https://claudemirpereira.com.br"],
            ["https://gilmargoulart.com"],
            ["http://www.monidesign.com/"],
            // To review
            // ------------------------------------------------------
            // ["https://symplectic.co.uk"],
            // ["https://www.europlacer.com/"],
            // ["https://elvisiondigitalagency.com/landingpage/"],
            // ["https://fightthenewdrug.org/"],
            // ["http://www.davincitandems.com/"],
            // Autoptimize
            // ["https://visualimpactfitness.com"],
        ];
    }

    /**
     * @test
     * @dataProvider validSitesProvider
     */
    public function testValidSites($uri)
    {
        $service = new ThemeFinderService($uri, '127.0.0.1');
        $search = $service->search();
        $this->assertTrue($search->success);
    }

    public function invalidSitesProvider()
    {
        return [
            ["https://tech.rgou.net"],
        ];
    }

    /**
     * @test
     * @dataProvider invalidSitesProvider
     */
    public function testInvalidSites($uri)
    {
        $service = new ThemeFinderService($uri, '127.0.0.1');
        $search = $service->search();
        $this->assertFalse($search->success);
    }

    public function invalidSslSitesProvider()
    {
        return [
            ["https://westgatereservations.com"],
        ];
    }

    /**
     * @test
     * @dataProvider invalidSslSitesProvider
     */
    public function testInvalidSslSites($uri)
    {
        $this->expectExceptionMessage("SSL Certificate verify error.<br/>For security reasons, this site cannot be search.<br/>Sorry for that.");
        $this->expectExceptionCode(4001);
        $service = new ThemeFinderService($uri, '127.0.0.1');
        $service->search();
    }

    public function unreachableSitesProvider()
    {
        return [
            ["https://asdfghj.kl"],
            ["http://www.fleetkleenservice.com/"],
            ["https://hs.utah.gov"],
            ["https://navasotaenergy.com"],
        ];
    }

    /**
     * @test
     * @dataProvider unreachableSitesProvider
     */
    public function testUnreachableSites($uri)
    {
        $this->expectExceptionMessage("We cannot reach this site.<br/>Sorry for that.");
        $this->expectExceptionCode(4002);
        $service = new ThemeFinderService($uri, '127.0.0.1');
        $service->search();
    }

    public function haveMainThemeSitesProvider()
    {
        return [
            ["https://claudemirpereira.com.br"],
        ];
    }

    /**
     * @test
     * @dataProvider haveMainThemeSitesProvider
     */
    public function testHaveMainThemeSites($uri)
    {
        $service = new ThemeFinderService($uri, '127.0.0.1');
        $search = $service->search();
        $this->assertTrue(null !== $search->child_theme_id);
    }

    // /**
    //  * @todo How to simulate?
    //  * @return void
    //  */
    // public function noContentSitesProvider()
    // {
    //     return [
    //         // ["https://asdfghj.kl"],
    //     ];
    // }

    // /**
    //  * @test
    //  * @dataProvider noContentSitesProvider
    //  */
    // public function testNoContentSites($uri)
    // {
    //     $this->expectExceptionMessage("Cannot get site content.<br/>Sorry for that");
    //     $this->expectExceptionCode(4003);
    //     $service = new ThemeFinderService($uri, '127.0.0.1');
    //     $service->search();
    // }
}
