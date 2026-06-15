<?php

class ProprietaireProfessionnel extends Proprietaire
{
    private string $siret;
    private string $raisonSociale;

    public function __construct(string $nom, string $email, string $siret, string $raisonSociale)
    {
        parent::__construct($nom, $email);
        $this->siret         = $siret;
        $this->raisonSociale = $raisonSociale;
    }

    public function getSiret(): string         { return $this->siret; }
    public function getRaisonSociale(): string  { return $this->raisonSociale; }

    public function afficherPortefeuille(): string
    {
        $lignes = [
            "Professionnel : {$this->raisonSociale} — {$this->getNom()} ({$this->id}) — SIRET : {$this->siret}"
        ];
        foreach ($this->getBiens() as $bien) {
            $lignes[] = "  • " . $bien->afficherInfos();
        }
        return implode(PHP_EOL, $lignes);
    }
}
