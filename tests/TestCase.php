<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesAdmins;

abstract class TestCase extends BaseTestCase
{
    use CreatesAdmins;
}
