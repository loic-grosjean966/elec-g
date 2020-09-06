<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('excerpt', [$this, 'excerpt']),
        ];
    }

    /**
     * Filtre qui retournera la chaîne de texte donnée tronquée à $maxSize caractères avec "." concaténé derrière. Si trop petite le filtre retourne juste la chaîne sans y toucher
     */
    public function excerpt(string $value, int $maxSize) : string
    {
        return mb_strlen($value) > $maxSize ? substr($value, 0, $maxSize) . '.' : $value;
    }
}
