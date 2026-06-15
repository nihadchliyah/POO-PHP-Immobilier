<?php

class NotificationMailObserver implements ObserverInterface
{
    private string $destinataire;

    public function __construct(string $destinataire = 'agence@immobilier.fr')
    {
        $this->destinataire = $destinataire;
    }

    public function update(string $event, array $data): void
    {
        $message = match ($event) {
            'louer'   => sprintf(
                "Le bien %s (%s) vient d'être loué pour %.0f €/mois.",
                $data['reference'], $data['ville'], $data['loyer']
            ),
            'resilier' => sprintf(
                "Le bail du bien %s (%s) a été résilié.",
                $data['reference'], $data['ville']
            ),
            'vendre'  => sprintf(
                "Le bien %s (%s) a été vendu pour %s €.",
                $data['reference'], $data['ville'], number_format($data['prix'], 0, ',', ' ')
            ),
            default   => sprintf("Changement de statut pour le bien %s.", $data['reference']),
        };

        echo sprintf("[MAIL → %s] %s", $this->destinataire, $message) . PHP_EOL;
    }
}
