<?php

namespace Tests\Unit;

use App\Models\Hearing;
use PHPUnit\Framework\TestCase;

class HearingDisplayTitleTest extends TestCase
{
    public function testDevelopmentHearingUsesStreetAddressForDisplay()
    {
        $hearing = new Hearing([
            'type' => 'development',
            'street_address' => '456 Updated Avenue',
            'title' => 'Old Title Value',
        ]);

        $this->assertSame('456 Updated Avenue', $hearing->display_title);
    }

    public function testPolicyHearingPrefersTitleForDisplay()
    {
        $hearing = new Hearing([
            'type' => 'policy',
            'title' => 'Policy Update',
            'street_address' => '789 Elm Street',
        ]);

        $this->assertSame('Policy Update', $hearing->display_title);
    }

    public function testPolicyHearingFallsBackToStreetAddress()
    {
        $hearing = new Hearing([
            'type' => 'policy',
            'title' => null,
            'street_address' => '1010 Pine Road',
        ]);

        $this->assertSame('1010 Pine Road', $hearing->display_title);
    }
}
