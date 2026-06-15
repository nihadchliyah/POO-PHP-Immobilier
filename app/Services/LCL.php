<?php

class LCL implements PretBancaireInterface
{
    private float $tauxAnnuel    = 3.00;
    private float $fraisDossier  = 1000.0;
    private int   $dureeMaxAns   = 20; // LCL limite à 20 ans

    public function getNomPrestataire(): string
    {
        return 'LCL';
    }

    public function getTauxAnnuel(): float
    {
        return $this->tauxAnnuel;
    }

    public function calculerMensualite(float $montant, int $dureeAns): float
    {
        $dureeAns    = min($dureeAns, $this->dureeMaxAns);
        $tauxMensuel = $this->tauxAnnuel / 100 / 12;
        $nbMois      = $dureeAns * 12;

        return $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $nbMois))
                        / (pow(1 + $tauxMensuel, $nbMois) - 1);
    }

    public function calculerCoutTotal(float $montant, int $dureeAns): float
    {
        $dureeEffective = min($dureeAns, $this->dureeMaxAns);

        return ($this->calculerMensualite($montant, $dureeEffective) * $dureeEffective * 12)
               + $this->fraisDossier;
    }

    public function obtenirDetails(float $montant, int $dureeAns): string
    {
        $dureeEffective = min($dureeAns, $this->dureeMaxAns);
        $mensualite     = $this->calculerMensualite($montant, $dureeEffective);
        $coutTotal      = $this->calculerCoutTotal($montant, $dureeEffective);
        $interets       = $coutTotal - $montant - $this->fraisDossier;
        $avertissement  = $dureeAns > $this->dureeMaxAns
            ? sprintf(' ⚠ Durée limitée à %d ans', $this->dureeMaxAns)
            : '';

        return sprintf(
            "[%s] Taux : %.2f%% | Durée : %d ans%s | Mensualité : %.2f € | Intérêts : %.2f € | Frais dossier : %.2f € | Coût total : %.2f €",
            $this->getNomPrestataire(),
            $this->tauxAnnuel,
            $dureeEffective,
            $avertissement,
            $mensualite,
            $interets,
            $this->fraisDossier,
            $coutTotal
        );
    }
}
