<?php

class RechercheService
{
    public function chercher(string|int $critere, array $biens, ?string $filtreVille = null): ?BienImmobilier
    {
        $filtre = fn(BienImmobilier $b) => $filtreVille === null || $b->getVille() === $filtreVille;

        return match(gettype($critere)) {
            'integer' => $biens[$critere] ?? null,
            'string'  => array_values(array_filter(
                $biens,
                fn(BienImmobilier $b) => $filtre($b)
                    && ($b->reference === $critere || stripos($b->getVille(), $critere) !== false)
            ))[0] ?? null,
            default => null,
        };
    }
}
