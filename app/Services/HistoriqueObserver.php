<?php

class HistoriqueObserver implements ObserverInterface
{
    private array $historique = [];

    public function update(string $event, array $data): void
    {
        $this->historique[] = [
            'date'      => date('d/m/Y H:i:s'),
            'evenement' => $event,
            'reference' => $data['reference'],
            'details'   => $data,
        ];

        echo sprintf(
            "[HISTORIQUE] %s | %s | Bien %s — %s",
            date('d/m/Y H:i:s'),
            strtoupper($event),
            $data['reference'],
            $data['ville']
        ) . PHP_EOL;
    }

    public function getHistorique(): array
    {
        return $this->historique;
    }

    public function afficherHistorique(): string
    {
        if (empty($this->historique)) {
            return "Aucun événement enregistré.";
        }

        $lignes = ["--- Historique des changements de statut ---"];
        foreach ($this->historique as $entree) {
            $lignes[] = sprintf(
                "  [%s] %-10s → Bien %s (%s)",
                $entree['date'],
                strtoupper($entree['evenement']),
                $entree['reference'],
                $entree['details']['ville']
            );
        }
        return implode(PHP_EOL, $lignes);
    }
}
