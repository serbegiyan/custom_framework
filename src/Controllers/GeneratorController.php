<?php

namespace App\Controllers;

use App\Core\Localization;
use App\Core\Request;
use App\Services\GeneratorService;

class GeneratorController
{
    public function __construct(
        public Request $request,
        public GeneratorService $generator,
        public Localization $local,
    ) {
    }
    public string $file;

    public function generate(): void
    {
        $this->file = __DIR__ . '/../../storage/data.csv';
        $quantity = $this->request->getInt('quantity');

        if ($quantity >= 1) {
            $this->generator->run($quantity, $this->file);
        } else {
            echo $this->local->translate('gen.incorrect_quantity');
        }
    }
}
