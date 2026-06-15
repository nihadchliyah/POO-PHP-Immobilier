<?php

class BNPParibas implements PretBancaireInterface
{
    private float $tauxAnnuel = 3.50;
    private float $fraisDossier = 1500.0;

    public function getNomPrestataire(): string
    {
        return 'BNP Paribas';
    }

    public function getTauxAnnuel(): float
    {
        return $this->tauxAnnuel;
    }

    public function calculerMensualite(float $montant, int $dureeAns): float
    {
        $tauxMensuel = $this->tauxAnnuel / 100 / 12;
        $nbMois      = $dureeAns * 12;

        // Formule : M = P × [r(1+r)^n] / [(1+r)^n - 1]
        return $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $nbMois))
                        / (pow(1 + $tauxMensuel, $nbMois) - 1);
    }

    public function calculerCoutTotal(float $montant, int $dureeAns): float
    {
        return ($this->calculerMensualite($montant, $dureeAns) * $dureeAns * 12)
               + $this->fraisDossier;
    }

    public function obtenirDetails(float $montant, int $dureeAns): string
    {
        $mensualite = $this->calculerMensualite($montant, $dureeAns);
        $coutTotal  = $this->calculerCoutTotal($montant, $dureeAns);
        $interets   = $coutTotal - $montant - $this->fraisDossier;

        return sprintf(
            "[%s] Taux : %.2f%% | Mensualité : %.2f € | Intérêts : %.2f € | Frais dossier : %.2f € | Coût total : %.2f €",
            $this->getNomPrestataire(),
            $this->tauxAnnuel,
            $mensualite,
            $interets,
            $this->fraisDossier,
            $coutTotal
        );
    }
}
