<?php

class Appartement extends BienTransactionnel implements Descriptible
{
    private int    $etage;
    private bool   $ascenseur;
    private string $typeAppartement;

    public function __construct(
        string $ville,
        float  $prix,
        float  $surface,
        int    $etage,
        bool   $ascenseur       = false,
        string $typeAppartement = ""
    ) {
        parent::__construct($ville, $prix, $surface);
        $this->etage           = $etage;
        $this->ascenseur       = $ascenseur;
        $this->typeAppartement = $typeAppartement;
    }

    public function getEtage(): int              { return $this->etage; }
    public function hasAscenseur(): bool          { return $this->ascenseur; }
    public function getTypeAppartement(): string   { return $this->typeAppartement; }

    public function setEtage(int $etage): void           { $this->etage = $etage; }
    public function setAscenseur(bool $ascenseur): void   { $this->ascenseur = $ascenseur; }
    public function setTypeAppartement(string $t): void   { $this->typeAppartement = $t; }

    public function louer(float $loyerMensuel): string
    {
        $loyerMinimum = $this->getSurface() * 10;
        if ($loyerMensuel < $loyerMinimum) {
            return sprintf(
                "Loyer refusé : minimum %.0f €/mois pour %.0f m² (10 €/m²).",
                $loyerMinimum,
                $this->getSurface()
            );
        }
        return parent::louer($loyerMensuel);
    }

    public function calculerPrixAuMetreCarre(): float
    {
        $base  = parent::calculerPrixAuMetreCarre();
        $bonus = $base * ($this->etage * 0.01);
        return $base + $bonus;
    }

    public function getDescription(): string
    {
        return sprintf(
            "Appartement %s — étage %d — %s à %s",
            $this->typeAppartement,
            $this->etage,
            $this->ascenseur ? "avec ascenseur" : "sans ascenseur",
            $this->getVille()
        );
    }

    public function afficherInfos(): string
    {
        return sprintf(
            "[Appartement] %s | Étage : %d | Ascenseur : %s | Type : %s | Statut : %s",
            parent::getInfosCommunes(),
            $this->etage,
            $this->ascenseur ? "Oui" : "Non",
            $this->typeAppartement,
            $this->getStatut()->value
        );
    }
}
