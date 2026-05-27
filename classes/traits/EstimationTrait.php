<?php

trait EstimationTrait
{
    public function estimer(int $annees, float $tauxAnnuel = 2.0): float
    {
        return $this->getPrix() * pow(1 + $tauxAnnuel / 100, $annees);
    }
}
