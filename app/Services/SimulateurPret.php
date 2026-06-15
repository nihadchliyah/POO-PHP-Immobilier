<?php

class SimulateurPret
{
    private PretBancaireInterface $prestataire;

    public function __construct(PretBancaireInterface $prestataire)
    {
        $this->prestataire = $prestataire;
    }

    public function setPrestataire(PretBancaireInterface $prestataire): void
    {
        $this->prestataire = $prestataire;
    }

    public function getPrestataire(): PretBancaireInterface
    {
        return $this->prestataire;
    }

    public function simuler(float $montant, int $dureeAns): string
    {
        return $this->prestataire->obtenirDetails($montant, $dureeAns);
    }

    public function comparerTous(float $montant, int $dureeAns, array $prestataires): string
    {
        $resultats = [];
        $meilleur  = null;
        $coutMin   = PHP_FLOAT_MAX;

        foreach ($prestataires as $prestataire) {
            $cout = $prestataire->calculerCoutTotal($montant, $dureeAns);
            $resultats[] = $prestataire->obtenirDetails($montant, $dureeAns);

            if ($cout < $coutMin) {
                $coutMin  = $cout;
                $meilleur = $prestataire->getNomPrestataire();
            }
        }

        $resultats[] = sprintf("\n=> Meilleure offre : %s (coût total : %.2f €)", $meilleur, $coutMin);

        return implode("\n", $resultats);
    }
}
