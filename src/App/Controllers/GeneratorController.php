<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Services\GeneratorService;
use App\Services\Localization;
use Core\JsonResponse;
use Core\Request;

class GeneratorController
{
    public string $file;

    public function __construct(
        public Request $request,
        public GeneratorService $generator,
        public Localization $local,
    ) {
        $this->file = __DIR__ . '/../../storage/data.csv';
    }

    public function generate(): JsonResponse
    {
        $quantity = $this->request->getInt('quantity', 0);

        if ($quantity < 1) {
            throw new ValidationException($this->local->translate('gen.incorrect_quantity'));
        }
        $this->generator->run($quantity, $this->file);
        return new JsonResponse(['message' => $this->local->translate('created')], 200);
    }
}
