<?php

namespace App\Core;

class View
{
    private string $path;

    public function __construct()
    {
        $this->path = __DIR__ . '/../../views/';
    }

    private function renderFile(string $template, array $data = []): string
    {
        extract($data);

        ob_start();

        require $this->path . $template . '.php';

        $result = ob_get_clean();

        if ($result === false) {
            throw new \RuntimeException("Failed to render template: {$template}");
        }

        return $result;
    }

    public function render(string $template, array $data = []): string
    {
        $content = $this->renderFile($template, $data);

        return $this->renderFile('layouts/main', [
            'content' => $content,
        ]);
    }
}
