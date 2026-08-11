<?php

namespace Tests\Stubs;

use Tests\Stubs\SampleService;

class DependService
{
    public function __construct(
        public SampleService $service
    ){}
}