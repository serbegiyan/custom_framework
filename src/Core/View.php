<?php

namespace Core;

class View
{
    private string $path;

    public function __construct()
    {
        $this->path = __DIR__ . '/../../views/';
    }
    /**
    * @param string $template
     * @param array<string, mixed> $data
     * @return string
    */
    private function renderFile(string $template, array $data = []): string
    {
        $fullPath = $this->path . $template . '.php';

        if (!file_exists($fullPath)) {
            throw new \InvalidArgumentException("Template file not found: {$template}.php");
        }

        extract($data);

        ob_start();

        require $this->path . $template . '.php';

        $result = ob_get_clean();

        return $result;
    }

    /**
     * @param string $template
     * @param array<string, mixed> $data
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        $content = $this->renderFile($template, $data);

        return $this->renderFile('layouts/main', [
            'content' => $content,
        ]);
    }
}
