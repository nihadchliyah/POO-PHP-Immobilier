<?php

class ProprietaireParticulier extends Proprietaire
{
    private string $telephone;

    public function __construct(string $nom, string $email, string $telephone)
    {
        parent::__construct($nom, $email);
        $this->telephone = $telephone;
    }

    public function getTelephone(): string { return $this->telephone; }

    public function afficherPortefeuille(): string
    {
        $lignes = [
            "Particulier : {$this->getNom()} ({$this->id}) — {$this->getEmail()} — {$this->telephone}"
        ];
        foreach ($this->getBiens() as $bien) {
            $lignes[] = "  • " . $bien->afficherInfos();
        }
        return implode(PHP_EOL, $lignes);
    }
}
