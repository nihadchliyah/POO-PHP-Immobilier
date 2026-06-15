<?php

class ProprietaireParticulierFactory implements ProprietaireFactoryInterface
{
    public function creer(array $data): Proprietaire
    {
        $this->valider($data);

        return new ProprietaireParticulier(
            nom:       $data['nom'],
            email:     $data['email'],
            telephone: $data['telephone'] ?? ''
        );
    }

    private function valider(array $data): void
    {
        foreach (['nom', 'email'] as $champ) {
            if (empty($data[$champ])) {
                throw new InvalidArgumentException(
                    "ProprietaireParticulierFactory : le champ '{$champ}' est obligatoire."
                );
            }
        }
    }
}
