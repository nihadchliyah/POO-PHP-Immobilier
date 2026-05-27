<?php

abstract class BienTransactionnel extends BienImmobilier implements Louable, Vendable, Estimable
{
    private bool  $loue         = false;
    private float $loyerMensuel = 0;

    private bool  $vendu        = false;
    private float $prixVente    = 0;

    public function louer(float $loyerMensuel): string
    {
        if ($this->vendu) {
            return "Impossible : le bien de {$this->getVille()} est déjà vendu.";
        }
        if ($this->loue) {
            return "Le bien de {$this->getVille()} est déjà loué.";
        }
        $this->loue         = true;
        $this->loyerMensuel = $loyerMensuel;
        return "Bien de {$this->getVille()} loué pour {$loyerMensuel} €/mois.";
    }

    public function resilier(): string
    {
        if (!$this->loue) {
            return "Le bien de {$this->getVille()} n'est pas loué.";
        }
        $this->loue         = false;
        $this->loyerMensuel = 0;
        return "Bail résilié pour le bien de {$this->getVille()}.";
    }

    public function isLoue(): bool           { return $this->loue; }
    public function getLoyerMensuel(): float  { return $this->loyerMensuel; }

    public function vendre(float $prixVente): string
    {
        if ($this->vendu) {
            return "Le bien de {$this->getVille()} est déjà vendu.";
        }
        if ($this->loue) {
            return "Impossible : le bien de {$this->getVille()} est en cours de location.";
        }
        $this->vendu     = true;
        $this->prixVente = $prixVente;
        return "Bien de {$this->getVille()} vendu pour {$prixVente} €.";
    }

    public function isVendu(): bool       { return $this->vendu; }
    public function getPrixVente(): float  { return $this->prixVente; }

    public function estimer(int $annees, float $tauxAnnuel = 2.0): float
    {
        return $this->getPrix() * pow(1 + $tauxAnnuel / 100, $annees);
    }

    protected function getStatut(): StatutBien
    {
        return match(true) {
            $this->vendu => StatutBien::Vendu,
            $this->loue  => StatutBien::Loue,
            default      => StatutBien::Disponible,
        };
    }
}
