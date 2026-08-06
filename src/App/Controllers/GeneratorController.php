<?php

namespace App\Controllers;

use Core\JsonResponse;
use App\Services\Localization;
use Core\Request;
use App\Exceptions\ValidationException;
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

    public function generate(): JsonResponse
    {
        $this->file = __DIR__ . '/../../storage/data.csv';
        $quantity = $this->request->getInt('quantity', 0);

        if ($quantity < 1) {
            throw new ValidationException($this->local->translate('gen.incorrect_quantity'));
        }
        $this->generator->run($quantity, $this->file);
        return new JsonResponse(['message' => $this->local->translate('created')], 200);
    }
}
