<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\GeneratorService;
use App\Core\Localization;

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
        $params = $this->request->getParams();
        $quantity = $params['quantity'] ?? null;
        if (isset($quantity) and $quantity >= 1) {
            $this->generator->run($quantity, $this->file);
        } else {
            echo $this->local->translate('gen.incorrect_quantity');
        }
    }
}
