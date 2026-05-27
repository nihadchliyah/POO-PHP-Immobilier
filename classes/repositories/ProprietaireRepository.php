<?php

class ProprietaireRepository
{
    private array $proprietaires = [];

    public function charger(array $donnees, BienRepository $bienRepo): void
    {
        foreach ($donnees as $data) {
            $proprio = new Proprietaire($data['nom'], $data['email']);
            foreach ($data['biens'] as $index) {
                $bien = $bienRepo->get($index);
                if ($bien !== null) {
                    $proprio->ajouterBien($bien);
                }
            }
            $this->proprietaires[] = $proprio;
        }
    }

    public function tous(): array
    {
        return $this->proprietaires;
    }
}
