<?php

class Local extends BienImmobilier implements Vendable, Estimable, Descriptible
{
    use VenteTrait;

    private string     $activite;
    private StatutBien $statut    = StatutBien::Disponible;

    public function __construct(string $ville, float $prix, float $surface, string $activite)
    {
        parent::__construct($ville, $prix, $surface);
        $this->activite = $activite;
    }

    public function getActivite(): string { return $this->activite; }

    public function calculerPrixTotal(float $tauxNotaire = 7.5): float
    {
        return $this->getPrix() * 1.20;
    }

    public function estimer(int $annees, float $tauxAnnuel = 2.0): float
    {
        return $this->getPrix() * pow(1 + $tauxAnnuel / 100, $annees);
    }

    public function getDescription(): string
    {
        return sprintf(
            "Local commercial (%s) de %.1f m² à %s",
            $this->activite,
            $this->getSurface(),
            $this->getVille()
        );
    }

    public function afficherInfos(): string
    {
        return sprintf(
            "[Local] %s | Activité : %s | Statut : %s",
            parent::getInfosCommunes(),
            $this->activite,
            $this->statut->value
        );
    }
}
