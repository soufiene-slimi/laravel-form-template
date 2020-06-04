<?php

namespace SoufieneSlimi\LaravelFormTemplate\Tests;

use Orchestra\Testbench\TestCase;
use SoufieneSlimi\LaravelFormTemplate\LaravelFormTemplateServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [LaravelFormTemplateServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
