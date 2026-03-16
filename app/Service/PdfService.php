<?php

namespace App\Service;

use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class PdfService
{
    /**
     * Génère un PDF à partir d'une vue Blade en utilisant Browsershoot.
     */
    public function generateFromView(string $viewPath, array $data): string
    {
        $html = View::make($viewPath, $data)->render();

        return Browsershot::html($html)
            ->setNodeBinary(config('browsershot.node_binary_path'))
            ->setNpmBinary(config('browsershot.npm_binary_path'))
            ->format('A4')
            ->showBackground()
            ->noSandbox()
            ->setOption('args', ['--disable-web-security'])
            ->margins(10, 10, 10, 10)
            ->waitUntilNetworkIdle()
            ->pdf();
    }
}
