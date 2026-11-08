<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Les tests n'ont pas besoin des styles compilés : ils passent même sans « npm run build »
        $this->withoutVite();
    }
}
