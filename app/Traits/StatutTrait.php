<?php

trait StatutTrait
{
    protected function getStatut(): StatutBien
    {
        $vendu = property_exists($this, 'vendu') && $this->vendu;
        $loue  = property_exists($this, 'loue') && $this->loue;

        if (property_exists($this, 'statut') && $this->statut instanceof StatutBien) {
            return $this->statut;
        }

        return match(true) {
            $vendu  => StatutBien::Vendu,
            $loue   => StatutBien::Loue,
            default => StatutBien::Disponible,
        };
    }
}
