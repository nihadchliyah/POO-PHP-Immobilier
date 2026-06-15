<?php

trait VenteTrait
{
    private bool  $vendu        = false;
    private float $prixVente    = 0;

    public function vendre(float $prixVente): string
    {
        if ($this->vendu) {
            return "Le bien de {$this->getVille()} est déjà vendu.";
        }
        if (method_exists($this, 'isLoue') && $this->isLoue()) {
            return "Impossible : le bien de {$this->getVille()} est en cours de location.";
        }
        $this->vendu     = true;
        $this->prixVente = $prixVente;
        return "Bien de {$this->getVille()} vendu pour {$prixVente} €.";
    }

    public function isVendu(): bool       { return $this->vendu; }
    public function getPrixVente(): float  { return $this->prixVente; }
}
