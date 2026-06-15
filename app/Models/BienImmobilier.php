<?php

abstract class BienImmobilier
{
    public readonly string $reference;

    private static int $compteur = 0;

    private string $ville;
    private float  $prix;
    private float  $surface;

    public function __construct(string $ville, float $prix, float $surface)
    {
        self::$compteur++;
        $this->reference = 'REF-' . str_pad(self::$compteur, 4, '0', STR_PAD_LEFT);

        $this->ville   = $ville;
        $this->setPrix($prix);
        $this->surface = $surface;
    }

    public function getVille(): string  { return $this->ville; }
    public function getPrix(): float    { return $this->prix; }
    public function getSurface(): float { return $this->surface; }

    public function setVille(string $ville): void { $this->ville = $ville; }

    public function setPrix(float $prix): void
    {
        if ($prix <= 0) {
            throw new InvalidArgumentException("Le prix doit être positif.");
        }
        $this->prix = $prix;
    }

    public function setSurface(float $surface): void { $this->surface = $surface; }

    protected function getInfosCommunes(): string
    {
        return sprintf(
            "[%s] Situé à %s | Surface : %.1f m² | Prix : %.2f € | Prix/m² : %.2f €",
            $this->reference,
            $this->ville,
            $this->surface,
            $this->prix,
            $this->calculerPrixAuMetreCarre()
        );
    }

    public function calculerPrixAuMetreCarre(): float
    {
        return $this->prix / $this->surface;
    }

    public function calculerPrixTotal(float $tauxNotaire = 7.5): float
    {
        return $this->prix * (1 + $tauxNotaire / 100);
    }

    public function calculerRentabilite(float $loyerMensuel): float
    {
        return (($loyerMensuel * 12) / $this->prix) * 100;
    }

    abstract public function afficherInfos(): string;
}
