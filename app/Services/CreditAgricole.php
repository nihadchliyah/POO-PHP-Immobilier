<?php

class CreditAgricole implements PretBancaireInterface
{
    private float $tauxAnnuel   = 3.20;
    private float $fraisDossier = 1200.0;
    private float $assurance    = 0.10; // % du montant par an

    public function getNomPrestataire(): string
    {
        return 'Crédit Agricole';
    }

    public function getTauxAnnuel(): float
    {
        return $this->tauxAnnuel;
    }

    public function calculerMensualite(float $montant, int $dureeAns): float
    {
        $tauxMensuel      = $this->tauxAnnuel / 100 / 12;
        $tauxAssuranceMois = $this->assurance / 100 / 12;
        $nbMois           = $dureeAns * 12;

        $mensualiteCredit = $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $nbMois))
         / (pow(1 + $tauxMensuel, $nbMois) - 1);

        // Le Crédit Agricole inclut l'assurance dans la mensualité
        $mensualiteAssurance = $montant * $tauxAssuranceMois;

        return $mensualiteCredit + $mensualiteAssurance;
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
            "[%s] Taux : %.2f%% + assurance %.2f%% | Mensualité : %.2f € | Intérêts + assurance : %.2f € | Frais dossier : %.2f € | Coût total : %.2f €",
            $this->getNomPrestataire(),
            $this->tauxAnnuel,
            $this->assurance,
            $mensualite,
            $interets,
            $this->fraisDossier,
            $coutTotal
        );
    }
}
