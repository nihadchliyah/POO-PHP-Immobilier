<?php

spl_autoload_register(function (string $class): void {
    $map = [
        'Descriptible'           => __DIR__ . '/classes/interfaces/Descriptible.php',
        'Estimable'              => __DIR__ . '/classes/interfaces/Estimable.php',
        'Louable'                => __DIR__ . '/classes/interfaces/Louable.php',
        'Vendable'               => __DIR__ . '/classes/interfaces/Vendable.php',
        'StatutBien'             => __DIR__ . '/classes/entities/StatutBien.php',
        'BienImmobilier'         => __DIR__ . '/classes/entities/BienImmobilier.php',
        'BienTransactionnel'     => __DIR__ . '/classes/entities/BienTransactionnel.php',
        'Appartement'            => __DIR__ . '/classes/entities/Appartement.php',
        'Maison'                 => __DIR__ . '/classes/entities/Maison.php',
        'Local'                  => __DIR__ . '/classes/entities/Local.php',
        'Proprietaire'           => __DIR__ . '/classes/entities/Proprietaire.php',
        'BienRepository'         => __DIR__ . '/classes/repositories/BienRepository.php',
        'ProprietaireRepository' => __DIR__ . '/classes/repositories/ProprietaireRepository.php',
        'RechercheService'       => __DIR__ . '/classes/services/RechercheService.php',
        'BienFactory'            => __DIR__ . '/classes/services/BienFactory.php',
        'DataSourceInterface'    => __DIR__ . '/classes/interfaces/DataSourceInterface.php',
        'JsonDataSource'         => __DIR__ . '/classes/services/JsonDataSource.php',
        'LocationTrait'          => __DIR__ . '/classes/traits/LocationTrait.php',
        'VenteTrait'             => __DIR__ . '/classes/traits/VenteTrait.php',
        'EstimationTrait'        => __DIR__ . '/classes/traits/EstimationTrait.php',
        'StatutTrait'            => __DIR__ . '/classes/traits/StatutTrait.php',
    ];

    if (isset($map[$class])) {
        require $map[$class];
    }
});
