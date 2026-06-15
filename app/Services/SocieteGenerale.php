<?php

class SocieteGenerale implements PretBancaireInterface
{
    private float $tauxAnnuel   = 3.80;
    private float $fraisDossier = 800.0;
    private float $remise       = 0.20; // remise si client premium (% du taux)

    private bool $clientPremium;

    public function __construct(bool $clientPremium = false)
    {
        $this->clientPremium = $clientPremium;
    }

    public function getNomPrestataire(): string
    {
        return 'Société Générale' . ($this->clientPremium ? ' (Client Premium)' : '');
    }

    public function getTauxAnnuel(): float
    {
        if ($this->clientPremium) {
            return $this->tauxAnnuel - $this->remise;
        }
        return $this->tauxAnnuel;
    }

    public function calculerMensualite(float $montant, int $dureeAns): float
    {
        $tauxMensuel = $this->getTauxAnnuel() / 100 / 12;
        $nbMois      = $dureeAns * 12;

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
        $remiseInfo = $this->clientPremium ? " (remise -%.2f%%)".sprintf("", $this->remise) : '';

        return sprintf(
            "[%s] Taux : %.2f%%%s | Mensualité : %.2f € | Intérêts : %.2f € | Frais dossier : %.2f € | Coût total : %.2f €",
            $this->getNomPrestataire(),
            $this->getTauxAnnuel(),
            $this->clientPremium ? ' (remise appliquée)' : '',
            $mensualite,
            $interets,
            $this->fraisDossier,
            $coutTotal
        );
    }
}
