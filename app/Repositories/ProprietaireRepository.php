<?php

class ProprietaireRepository
{
    private array $proprietaires = [];

    private ProprietaireFactoryRegistry $factory;

    public function __construct()
    {
        $this->factory = new ProprietaireFactoryRegistry();
        $this->factory->enregistrer('particulier',   new ProprietaireParticulierFactory());
        $this->factory->enregistrer('professionnel', new ProprietaireProfessionnelFactory());
    }

    public function charger(DataSourceInterface $source, BienRepository $bienRepo): void
    {
        $donnees      = $source->recupererDonnees();
        $propriosData = $donnees['proprietaires'] ?? [];

        foreach ($propriosData as $data) {
            $type   = $data['type'] ?? 'particulier';
            $proprio = $this->factory->creer($type, $data);

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
