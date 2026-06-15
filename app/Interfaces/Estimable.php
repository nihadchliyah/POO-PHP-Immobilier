<?php

interface Estimable
{
    public function estimer(int $annees, float $tauxAnnuel = 2.0): float;
}
