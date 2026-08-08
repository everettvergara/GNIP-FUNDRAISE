<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OptimizedImage extends Component
{
    public ?string $webpSrc;

    public function __construct(
        public string $src,
        public string $alt = '',
        public string $class = '',
        public bool $lazy = false,
        public ?int $width = null,
        public ?int $height = null,
    ) {
        $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $this->src);
        $this->webpSrc = $webpPath !== null && file_exists(public_path($webpPath))
            ? asset($webpPath)
            : null;
    }

    public function render(): View|Closure|string
    {
        return view('components.optimized-image');
    }
}
