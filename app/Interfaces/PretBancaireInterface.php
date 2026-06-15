<?php

interface PretBancaireInterface
{
    public function getNomPrestataire(): string;

    public function getTauxAnnuel(): float;

    public function calculerMensualite(float $montant, int $dureeAns): float;

    public function calculerCoutTotal(float $montant, int $dureeAns): float;

    public function obtenirDetails(float $montant, int $dureeAns): string;
}
