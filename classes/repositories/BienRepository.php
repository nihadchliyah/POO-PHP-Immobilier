<?php

class BienRepository
{
    private array $biens = [];

    public function charger(array $donnees): void
    {
        foreach ($donnees as $data) {
            $this->biens[] = match($data['type']) {
                'appartement' => new Appartement(
                    $data['ville'], $data['prix'], $data['surface'],
                    $data['etage'], $data['ascenseur'], $data['typeAppartement']
                ),
                'maison' => new Maison(
                    $data['ville'], $data['prix'], $data['surface'],
                    $data['nbChambres'], $data['jardin'], $data['surfaceJardin'], $data['garage']
                ),
                'local' => new Local(
                    $data['ville'], $data['prix'], $data['surface'], $data['activite']
                ),
                default => throw new InvalidArgumentException("Type de bien inconnu : {$data['type']}"),
            };
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
