<?php

class ProprietaireProfessionnelFactory implements ProprietaireFactoryInterface
{
    public function creer(array $data): Proprietaire
    {
        $this->valider($data);

        return new ProprietaireProfessionnel(
            nom:           $data['nom'],
            email:         $data['email'],
            siret:         $data['siret']         ?? '',
            raisonSociale: $data['raisonSociale'] ?? $data['nom']
        );
    }

    private function valider(array $data): void
    {
        foreach (['nom', 'email', 'siret'] as $champ) {
            if (empty($data[$champ])) {
                throw new InvalidArgumentException(
                    "ProprietaireProfessionnelFactory : le champ '{$champ}' est obligatoire."
                );
            }
        }
    }
}
