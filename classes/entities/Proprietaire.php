<?php

class Proprietaire
{
    public readonly string $id;

    private string $nom;
    private string $email;

    private array $biens = [];

    public function __construct(string $nom, string $email)
    {
        $this->id    = 'PROP-' . strtoupper(substr(md5($nom . $email . microtime()), 0, 6));
        $this->nom   = $nom;
        $this->email = $email;
    }

    public function getId(): string    { return $this->id; }
    public function getNom(): string   { return $this->nom; }
    public function getEmail(): string { return $this->email; }

    public function ajouterBien(BienImmobilier $bien): void
    {
        $this->biens[] = $bien;
    }

    public function getBiens(): array { return $this->biens; }

    public function typeBien(BienImmobilier $bien): string
    {
        return match(true) {
            $bien instanceof Appartement => 'Appartement',
            $bien instanceof Maison      => 'Maison',
            $bien instanceof Local       => 'Local commercial',
            default                      => 'Bien immobilier',
        };
    }

    public function afficherPortefeuille(): string
    {
        $lignes = ["Propriétaire : {$this->nom} ({$this->id}) — {$this->email}"];
        foreach ($this->biens as $bien) {
            $lignes[] = "  • " . $bien->afficherInfos();
        }
        return implode(PHP_EOL, $lignes);
    }
}
