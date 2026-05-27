<?php

class Maison extends BienImmobilier implements Louable, Vendable, Estimable, Descriptible
{
    use LocationTrait, VenteTrait, EstimationTrait, StatutTrait;

    private int    $nbChambres;
    private bool   $jardin;
    private float  $surfaceJardin;
    private string $garage;

    public function __construct(
        string $ville,
        float  $prix,
        float  $surface,
        int    $nbChambres,
        bool   $jardin        = false,
        float  $surfaceJardin = 0,
        string $garage        = ""
    ) {
        parent::__construct($ville, $prix, $surface);
        $this->nbChambres    = $nbChambres;
        $this->jardin        = $jardin;
        $this->surfaceJardin = $surfaceJardin;
        $this->garage        = $garage;
    }

    public function getNbChambres(): int      { return $this->nbChambres; }
    public function hasJardin(): bool          { return $this->jardin; }
    public function getSurfaceJardin(): float  { return $this->surfaceJardin; }
    public function getGarage(): string        { return $this->garage; }

    public function setNbChambres(int $n): void       { $this->nbChambres = $n; }
    public function setJardin(bool $j): void           { $this->jardin = $j; }
    public function setSurfaceJardin(float $s): void   { $this->surfaceJardin = $s; }
    public function setGarage(string $g): void         { $this->garage = $g; }

    public function calculerRentabilite(float $loyerMensuel): float
    {
        $chargesJardin = $this->jardin ? $this->surfaceJardin * 2 : 0;
        $chargesGarage = !empty($this->garage) ? 300 : 0;
        $revenuNet     = ($loyerMensuel * 12) - $chargesJardin - $chargesGarage;
        return ($revenuNet / $this->getPrix()) * 100;
    }

    public function getDescription(): string
    {
        return sprintf(
            "Maison %d chambres%s à %s",
            $this->nbChambres,
            $this->jardin ? " avec jardin de {$this->surfaceJardin} m²" : "",
            $this->getVille()
        );
    }

    public function afficherInfos(): string
    {
        $jardinLabel = $this->jardin
            ? sprintf("Oui (%.1f m²)", $this->surfaceJardin)
            : "Non";

        return sprintf(
            "[Maison] %s | Chambres : %d | Jardin : %s | Garage : %s | Statut : %s",
            parent::getInfosCommunes(),
            $this->nbChambres,
            $jardinLabel,
            $this->garage ?: "Aucun",
            $this->getStatut()->value
        );
    }
}
