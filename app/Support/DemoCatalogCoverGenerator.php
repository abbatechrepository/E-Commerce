<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoCatalogCoverGenerator
{
    public function ensure(Product $product, bool $forceDemoReplacement = false): ProductImage
    {
        $product->loadMissing('artist', 'genre', 'images');

        $existingDemoImage = $product->images
            ->first(fn (ProductImage $image) => str_starts_with($image->image_path, 'products/demo/'));

        $shouldGenerate = $forceDemoReplacement
            || $product->images->isEmpty()
            || ($existingDemoImage && $product->images->count() === 1);

        if (! $shouldGenerate) {
            return $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        }

        $path = sprintf(
            'products/demo/%s-%d.svg',
            Str::slug($product->album_title ?: $product->name ?: 'album'),
            $product->id
        );

        Storage::disk('public')->put($path, $this->renderSvg($product));

        if ($existingDemoImage) {
            if ($existingDemoImage->image_path !== $path && Storage::disk('public')->exists($existingDemoImage->image_path)) {
                Storage::disk('public')->delete($existingDemoImage->image_path);
            }

            $existingDemoImage->update([
                'image_path' => $path,
                'alt_text' => 'Capa do album '.$product->album_title,
                'position' => 1,
                'is_primary' => true,
            ]);

            return $existingDemoImage->refresh();
        }

        $product->images()->update(['is_primary' => false]);

        return $product->images()->create([
            'image_path' => $path,
            'alt_text' => 'Capa do album '.$product->album_title,
            'position' => 1,
            'is_primary' => true,
        ]);
    }

    private function renderSvg(Product $product): string
    {
        $seed = abs(crc32($product->slug ?: $product->album_title ?: (string) $product->id));
        $palette = $this->palette($seed);

        $album = $this->escape($this->truncate($product->album_title ?: $product->name ?: 'Disco Raro', 34));
        $artist = $this->escape($this->truncate($product->artist?->name ?: 'Artista Desconhecido', 28));
        $genre = $this->escape(Str::upper($this->truncate($product->genre?->name ?: 'CATALOGO', 18)));
        $format = $this->escape(Str::upper($this->truncate($product->media_format ?: 'LP', 10)));
        $condition = $this->escape($this->truncate($product->disc_condition ?: 'Muito bom', 20));
        $price = 'R$ '.number_format((float) $product->effective_price, 2, ',', '.');
        $rarity = $product->is_rare ? 'RARO' : 'CATALOGO';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200" role="img" aria-label="{$album}">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$palette['start']}"/>
      <stop offset="55%" stop-color="{$palette['middle']}"/>
      <stop offset="100%" stop-color="{$palette['end']}"/>
    </linearGradient>
    <radialGradient id="shine" cx="30%" cy="20%" r="70%">
      <stop offset="0%" stop-color="rgba(255,255,255,0.28)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
    </radialGradient>
  </defs>
  <rect width="1200" height="1200" fill="url(#bg)"/>
  <circle cx="860" cy="360" r="270" fill="rgba(17,24,39,0.20)"/>
  <circle cx="860" cy="360" r="190" fill="rgba(17,24,39,0.24)"/>
  <circle cx="860" cy="360" r="110" fill="rgba(255,255,255,0.10)"/>
  <circle cx="860" cy="360" r="16" fill="#f8fafc"/>
  <rect x="68" y="68" width="1064" height="1064" rx="44" fill="url(#shine)"/>
  <rect x="96" y="96" width="388" height="72" rx="36" fill="rgba(15,23,42,0.25)"/>
  <text x="290" y="142" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="30" font-weight="700" letter-spacing="6" fill="#f8fafc">{$genre}</text>
  <text x="96" y="680" font-family="Georgia, 'Times New Roman', serif" font-size="98" font-weight="700" fill="#f8fafc">{$album}</text>
  <text x="96" y="770" font-family="Arial, Helvetica, sans-serif" font-size="46" font-weight="600" fill="rgba(248,250,252,0.92)">{$artist}</text>
  <text x="96" y="860" font-family="Arial, Helvetica, sans-serif" font-size="28" font-weight="700" letter-spacing="4" fill="rgba(248,250,252,0.75)">{$format} · {$condition}</text>
  <rect x="96" y="930" width="270" height="88" rx="28" fill="rgba(15,23,42,0.30)"/>
  <text x="132" y="987" font-family="Arial, Helvetica, sans-serif" font-size="42" font-weight="700" fill="#f8fafc">{$price}</text>
  <rect x="962" y="950" width="142" height="54" rx="27" fill="rgba(255,255,255,0.16)"/>
  <text x="1033" y="985" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="24" font-weight="700" letter-spacing="3" fill="#f8fafc">{$rarity}</text>
</svg>
SVG;
    }

    private function palette(int $seed): array
    {
        $palettes = [
            ['#6b1f1f', '#b45309', '#f59e0b'],
            ['#1d3557', '#457b9d', '#a8dadc'],
            ['#283618', '#606c38', '#dda15e'],
            ['#3d0c11', '#7f1d1d', '#ef4444'],
            ['#2d1e2f', '#6d597a', '#e56b6f'],
            ['#14213d', '#fca311', '#e5e5e5'],
            ['#264653', '#2a9d8f', '#e9c46a'],
            ['#1b4332', '#2d6a4f', '#95d5b2'],
        ];

        [$start, $middle, $end] = $palettes[$seed % count($palettes)];

        return compact('start', 'middle', 'end');
    }

    private function truncate(string $value, int $limit): string
    {
        return Str::limit(trim($value), $limit, '...');
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
