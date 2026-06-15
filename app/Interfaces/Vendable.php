<?php

interface Vendable
{
    public function vendre(float $prixVente): string;
    public function isVendu(): bool;
    public function getPrixVente(): float;
}
