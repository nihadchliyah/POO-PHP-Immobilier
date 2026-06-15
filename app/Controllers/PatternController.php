<?php

class PatternController
{
    public function index(): void
    {
        // Singleton
        $db1          = DatabaseConnection::getInstance();
        $db2          = DatabaseConnection::getInstance();
        $memeInstance = ($db1 === $db2);

        // Observer
        $historiqueObserver = new HistoriqueObserver();
        $proprio            = new Proprietaire("Alice Martin", "alice@immo.fr");
        $appartObs          = new Appartement("Bordeaux", 150000, 50, 2, true, "T2");
        $proprio->ajouterBien($appartObs);
        $appartObs->attach($historiqueObserver);
        $msgLouer   = $appartObs->louer(600);
        $msgResilier = $appartObs->resilier();
        $msgVendre  = $appartObs->vendre(160000);
        $historique = $historiqueObserver->getHistorique();

        // Strategy
        $montant    = 200000;
        $dureeAns   = 25;
        $simulateur = new SimulateurPret(new BNPParibas());
        $banques    = [
            new BNPParibas(),
            new CreditAgricole(),
            new SocieteGenerale(),
            new SocieteGenerale(clientPremium: true),
            new LCL(),
        ];

        // Factory
        $registry = new ProprietaireFactoryRegistry();
        $registry->enregistrer('particulier',   new ProprietaireParticulierFactory());
        $registry->enregistrer('professionnel', new ProprietaireProfessionnelFactory());
        $alice = $registry->creer('particulier',   ['nom' => 'Alice Martin', 'email' => 'alice@immo.fr', 'telephone' => '06 12 34 56 78']);
        $bob   = $registry->creer('professionnel', ['nom' => 'Bob Dupont',   'email' => 'bob@immo.fr',   'siret' => '123 456 789 00012', 'raisonSociale' => 'Dupont Immobilier SARL']);

        require __DIR__ . '/../Views/patterns/index.php';
    }
}
