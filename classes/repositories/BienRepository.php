<?php

class BienRepository
{
    private array $biens = [];

    public function charger(DataSourceInterface $source): void
    {
        $donnees = $source->recupererDonnees();
        $biensData = $donnees['biens'] ?? [];
        
        foreach ($biensData as $data) {
            $this->biens[] = BienFactory::creerDepuisTableau($data);
        }
    }

    public function tous(): array
    {
        return $this->biens;
    }

    public function get(int $index): ?BienImmobilier
    {
        return $this->biens[$index] ?? null;
    }
}
