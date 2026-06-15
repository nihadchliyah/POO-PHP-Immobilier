<?php

class Proprietaire implements ObserverInterface
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

        if ($bien instanceof ObservableInterface) {
            $bien->attach($this);
        }
    }


    public function update(string $event, array $data): void
    {
        $message = match ($event) {
            'louer'   => sprintf(
                "Votre bien %s (%s) a été loué pour %.0f €/mois.",
                $data['reference'], $data['ville'], $data['loyer']
            ),
            'resilier' => sprintf(
                "Le bail de votre bien %s (%s) a été résilié. Il est à nouveau disponible.",
                $data['reference'], $data['ville']
            ),
            'vendre'  => sprintf(
                "Votre bien %s (%s) a été vendu pour %s €.",
                $data['reference'], $data['ville'], number_format($data['prix'], 0, ',', ' ')
            ),
            default   => sprintf("Changement de statut sur votre bien %s.", $data['reference']),
        };

        echo sprintf(
            "[NOTIF PROPRIÉTAIRE → %s (%s)] %s",
            $this->nom,
            $this->email,
            $message
        ) . PHP_EOL;
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
