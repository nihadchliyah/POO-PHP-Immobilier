<?php

trait LocationTrait
{
    private bool  $loue         = false;
    private float $loyerMensuel = 0;

    public function louer(float $loyerMensuel): string
    {
        if (method_exists($this, 'isVendu') && $this->isVendu()) {
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
}
