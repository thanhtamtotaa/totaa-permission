<?php

namespace Totaa\TotaaPermission\Tests;

use Orchestra\Testbench\TestCase;
use Totaa\TotaaPermission\TotaaPermissionServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [TotaaPermissionServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
