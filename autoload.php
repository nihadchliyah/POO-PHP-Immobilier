<?php

spl_autoload_register(function (string $class): void {
    $map = [
        // Interfaces
        'Descriptible'                     => __DIR__ . '/app/Interfaces/Descriptible.php',
        'Estimable'                        => __DIR__ . '/app/Interfaces/Estimable.php',
        'Louable'                          => __DIR__ . '/app/Interfaces/Louable.php',
        'Vendable'                         => __DIR__ . '/app/Interfaces/Vendable.php',
        'ObserverInterface'                => __DIR__ . '/app/Interfaces/ObserverInterface.php',
        'ObservableInterface'              => __DIR__ . '/app/Interfaces/ObservableInterface.php',
        'PretBancaireInterface'            => __DIR__ . '/app/Interfaces/PretBancaireInterface.php',
        'ProprietaireFactoryInterface'     => __DIR__ . '/app/Interfaces/ProprietaireFactoryInterface.php',
        'DataSourceInterface'              => __DIR__ . '/app/Interfaces/DataSourceInterface.php',
        // Traits
        'LocationTrait'                    => __DIR__ . '/app/Traits/LocationTrait.php',
        'VenteTrait'                       => __DIR__ . '/app/Traits/VenteTrait.php',
        'EstimationTrait'                  => __DIR__ . '/app/Traits/EstimationTrait.php',
        'StatutTrait'                      => __DIR__ . '/app/Traits/StatutTrait.php',
        // Models
        'StatutBien'                       => __DIR__ . '/app/Models/StatutBien.php',
        'BienImmobilier'                   => __DIR__ . '/app/Models/BienImmobilier.php',
        'BienTransactionnel'               => __DIR__ . '/app/Models/BienTransactionnel.php',
        'Appartement'                      => __DIR__ . '/app/Models/Appartement.php',
        'Maison'                           => __DIR__ . '/app/Models/Maison.php',
        'Local'                            => __DIR__ . '/app/Models/Local.php',
        'Proprietaire'                     => __DIR__ . '/app/Models/Proprietaire.php',
        'ProprietaireParticulier'          => __DIR__ . '/app/Models/ProprietaireParticulier.php',
        'ProprietaireProfessionnel'        => __DIR__ . '/app/Models/ProprietaireProfessionnel.php',
        // Repositories
        'BienRepository'                   => __DIR__ . '/app/Repositories/BienRepository.php',
        'ProprietaireRepository'           => __DIR__ . '/app/Repositories/ProprietaireRepository.php',
        // Services
        'RechercheService'                 => __DIR__ . '/app/Services/RechercheService.php',
        'BienFactory'                      => __DIR__ . '/app/Services/BienFactory.php',
        'JsonDataSource'                   => __DIR__ . '/app/Services/JsonDataSource.php',
        'DatabaseConnection'               => __DIR__ . '/app/Services/DatabaseConnection.php',
        'NotificationMailObserver'         => __DIR__ . '/app/Services/NotificationMailObserver.php',
        'HistoriqueObserver'               => __DIR__ . '/app/Services/HistoriqueObserver.php',
        'BNPParibas'                       => __DIR__ . '/app/Services/BNPParibas.php',
        'CreditAgricole'                   => __DIR__ . '/app/Services/CreditAgricole.php',
        'SocieteGenerale'                  => __DIR__ . '/app/Services/SocieteGenerale.php',
        'LCL'                              => __DIR__ . '/app/Services/LCL.php',
        'SimulateurPret'                   => __DIR__ . '/app/Services/SimulateurPret.php',
        'ProprietaireParticulierFactory'   => __DIR__ . '/app/Services/ProprietaireParticulierFactory.php',
        'ProprietaireProfessionnelFactory' => __DIR__ . '/app/Services/ProprietaireProfessionnelFactory.php',
        'ProprietaireFactoryRegistry'      => __DIR__ . '/app/Services/ProprietaireFactoryRegistry.php',
        // Controllers
        'BienController'                   => __DIR__ . '/app/Controllers/BienController.php',
        'ProprietaireController'           => __DIR__ . '/app/Controllers/ProprietaireController.php',
        'PatternController'                => __DIR__ . '/app/Controllers/PatternController.php',
        'AnnonceController'                => __DIR__ . '/app/Controllers/AnnonceController.php',
    ];

    if (isset($map[$class])) {
        require $map[$class];
    }
});
