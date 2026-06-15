<?php

class ProprietaireFactoryRegistry
{
    private array $factories = [];

    public function enregistrer(string $type, ProprietaireFactoryInterface $factory): void
    {
        $this->factories[strtolower($type)] = $factory;
    }

    public function creer(string $type, array $data): Proprietaire
    {
        $type = strtolower($type);

        if (!isset($this->factories[$type])) {
            throw new InvalidArgumentException(
                "Type de propriétaire inconnu : '{$type}'. Types disponibles : "
                . implode(', ', array_keys($this->factories))
            );
        }

        return $this->factories[$type]->creer($data);
    }
}
