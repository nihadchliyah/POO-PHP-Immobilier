<?php

require_once __DIR__ . '/autoload.php';

BienFactory::enregistrerType('appartement', fn($data) => new Appartement(
    $data['ville'], (float)$data['prix'], (float)$data['surface'],
    (int)$data['etage'], (bool)($data['ascenseur'] ?? false), (string)($data['typeAppartement'] ?? "")
));

BienFactory::enregistrerType('maison', fn($data) => new Maison(
    $data['ville'], (float)$data['prix'], (float)$data['surface'],
    (int)$data['nbChambres'], (bool)($data['jardin'] ?? false), (float)($data['surfaceJardin'] ?? 0), (string)($data['garage'] ?? "")
));

BienFactory::enregistrerType('local', fn($data) => new Local(
    $data['ville'], (float)$data['prix'], (float)$data['surface'], (string)$data['activite']
));

header('Content-Type: text/plain; charset=utf-8');

function afficherDescriptionEstimee(Descriptible&Estimable $bien, int $annees = 5): string
{
    return sprintf(
        "%s | Valeur dans %d ans (2%%) : %s €",
        $bien->getDescription(),
        $annees,
        number_format($bien->estimer($annees, 2.0), 0, ',', ' ')
    );
}

$source = new JsonDataSource(__DIR__ . '/data/data.json');

$bienRepo = new BienRepository();
$bienRepo->charger($source);

$propRepo = new ProprietaireRepository();
$propRepo->charger($source, $bienRepo);

$catalogue = $bienRepo->tous();
$recherche = new RechercheService();

[$appart1, $appart2, $appart3, $maison1, $maison2, $maison3, $local1, $local2] = $catalogue;


echo "=== RÉFÉRENCES readonly ===" . PHP_EOL;
echo "Appart Lyon     : " . $appart1->reference . PHP_EOL;
echo "Appart Paris    : " . $appart2->reference . PHP_EOL;
echo "Maison Marseille: " . $maison1->reference . PHP_EOL;
echo "Local Paris     : " . $local1->reference  . PHP_EOL;


echo PHP_EOL . "=== APPARTEMENTS ===" . PHP_EOL;
echo $appart1->afficherInfos() . PHP_EOL;
echo $appart2->afficherInfos() . PHP_EOL;
echo $appart3->afficherInfos() . PHP_EOL;

echo PHP_EOL . "=== MAISONS ===" . PHP_EOL;
echo $maison1->afficherInfos() . PHP_EOL;
echo $maison2->afficherInfos() . PHP_EOL;
echo $maison3->afficherInfos() . PHP_EOL;

echo PHP_EOL . "=== LOCAUX COMMERCIAUX ===" . PHP_EOL;
echo $local1->afficherInfos() . PHP_EOL;
echo $local2->afficherInfos() . PHP_EOL;


echo PHP_EOL . "=== ENUM StatutBien ===" . PHP_EOL;

$appart1->louer(750);
$appart2->vendre(330000);
$local1->vendre(900000);

echo "Appart Lyon     → " . $appart1->afficherInfos() . PHP_EOL;
echo "Appart Paris    → " . $appart2->afficherInfos() . PHP_EOL;
echo "Appart Bordeaux → " . $appart3->afficherInfos() . PHP_EOL;
echo "Local Paris     → " . $local1->afficherInfos()  . PHP_EOL;


echo PHP_EOL . "=== INTERFACE Descriptible ===" . PHP_EOL;

foreach ($catalogue as $bien) {
    echo $bien->getDescription() . PHP_EOL;
}


echo PHP_EOL . "=== INTERFACE Estimable (valeur dans 10 ans à 3%/an) ===" . PHP_EOL;

$estimables = [
    ["Appart Lyon",      $appart1],
    ["Appart Paris",     $appart2],
    ["Maison Marseille", $maison1],
    ["Local Lyon",       $local2],
];

foreach ($estimables as [$label, $bien]) {
    echo sprintf(
        "%s → prix actuel : %s € → estimé dans 10 ans : %s €",
        $label,
        number_format($bien->getPrix(), 0, ',', ' '),
        number_format($bien->estimer(10, 3.0), 0, ',', ' ')
    ) . PHP_EOL;
}


echo PHP_EOL . "=== TESTS DE MODIFICATION ===" . PHP_EOL;

$appart3->setEtage(2);
$appart3->setAscenseur(true);
$appart3->setTypeAppartement("T1 bis");
echo $appart3->afficherInfos() . PHP_EOL;

echo PHP_EOL;
$maison3->setJardin(true);
$maison3->setSurfaceJardin(50);
$maison3->setGarage("Garage simple");
echo $maison3->afficherInfos() . PHP_EOL;


echo PHP_EOL . "=== CALCULS ===" . PHP_EOL;
echo "Prix au m² Lyon      : " . number_format($appart1->calculerPrixAuMetreCarre(), 2) . " €/m²" . PHP_EOL;
echo "Prix au m² Marseille : " . number_format($maison1->calculerPrixAuMetreCarre(), 2) . " €/m²" . PHP_EOL;
echo "Prix total Paris     : " . number_format($appart2->calculerPrixTotal(), 2) . " € (frais notaire inclus)" . PHP_EOL;
echo "Prix total Nantes    : " . number_format($maison2->calculerPrixTotal(), 2) . " € (frais notaire inclus)" . PHP_EOL;


echo PHP_EOL . "=== RENTABILITÉ ===" . PHP_EOL;

$biensAvecLoyer = [
    [$appart1, 750,  "Appart Lyon"],
    [$appart2, 1100, "Appart Paris"],
    [$appart3, 450,  "Appart Bordeaux"],
    [$maison1, 1800, "Maison Marseille"],
    [$maison2, 1100, "Maison Nantes"],
    [$maison3, 900,  "Maison Toulouse"],
];

foreach ($biensAvecLoyer as [$bien, $loyer, $label]) {
    echo sprintf(
        "%s → loyer %d €/mois → rentabilité brute : %.2f %%",
        $label, $loyer, $bien->calculerRentabilite($loyer)
    ) . PHP_EOL;
}


echo PHP_EOL . "=== INTERFACE Louable ===" . PHP_EOL;

$appart3b = new Appartement("Bordeaux", 95000, 30, 1, false, "Studio");
echo $appart3b->louer(350)      . PHP_EOL;
echo $appart3b->resilier()      . PHP_EOL;
echo $appart3b->afficherInfos() . PHP_EOL;

echo PHP_EOL . "=== INTERFACE Vendable ===" . PHP_EOL;

$maisonTest = new Maison("Nantes", 280000, 90, 3, true, 80, "Garage simple");
echo $maisonTest->louer(1100)    . PHP_EOL;
echo $maisonTest->vendre(300000) . PHP_EOL;


echo PHP_EOL . "=== OVERRIDE : calculerPrixAuMetreCarre() dans Appartement (bonus étage) ===" . PHP_EOL;

$rdc    = new Appartement("Lyon", 180000, 45, 0);
$etage3 = new Appartement("Lyon", 180000, 45, 3);
$etage7 = new Appartement("Lyon", 180000, 45, 7);

echo sprintf("RDC     → %.2f €/m²", $rdc->calculerPrixAuMetreCarre())    . PHP_EOL;
echo sprintf("Étage 3 → %.2f €/m²", $etage3->calculerPrixAuMetreCarre()) . PHP_EOL;
echo sprintf("Étage 7 → %.2f €/m²", $etage7->calculerPrixAuMetreCarre()) . PHP_EOL;

echo PHP_EOL . "=== OVERRIDE : calculerRentabilite() dans Maison ===" . PHP_EOL;

$maisonAvecJardin = new Maison("Nantes", 280000, 90, 3, true, 80, "Garage simple");
$maisonSansJardin = new Maison("Nantes", 280000, 90, 3, false, 0, "");
echo sprintf("Avec jardin + garage  → %.2f %%", $maisonAvecJardin->calculerRentabilite(1100)) . PHP_EOL;
echo sprintf("Sans jardin ni garage → %.2f %%", $maisonSansJardin->calculerRentabilite(1100)) . PHP_EOL;

echo PHP_EOL . "=== OVERRIDE : calculerPrixTotal() dans Local (TVA 20%) ===" . PHP_EOL;

$localTVA   = new Local("Paris", 850000, 200, "Restaurant");
$appartTest = new Appartement("Paris", 850000, 200, 5);
echo sprintf("Local commercial (TVA 20%%)  : %s €", number_format($localTVA->calculerPrixTotal(), 2, ',', ' '))  . PHP_EOL;
echo sprintf("Appartement (notaire 7.5%%) : %s €", number_format($appartTest->calculerPrixTotal(), 2, ',', ' ')) . PHP_EOL;


echo PHP_EOL . "=== RECHERCHE — Union types (string|int) + Nullable (?string) ===" . PHP_EOL;

$refCible = $appart2->reference;
$trouve   = $recherche->chercher($refCible, $catalogue);
echo "Par référence '{$refCible}'   : " . ($trouve ? $trouve->afficherInfos() : "Non trouvé") . PHP_EOL;

$trouve = $recherche->chercher("Lyon", $catalogue);
echo "Par ville 'Lyon'            : " . ($trouve ? $trouve->afficherInfos() : "Non trouvé") . PHP_EOL;

$trouve = $recherche->chercher(4, $catalogue);
echo "Par index 4                 : " . ($trouve ? $trouve->afficherInfos() : "Non trouvé") . PHP_EOL;

$trouve = $recherche->chercher("Marseille", $catalogue, "Marseille");
echo "Ville 'Marseille' (filtrée) : " . ($trouve ? $trouve->afficherInfos() : "Non trouvé") . PHP_EOL;

$trouve = $recherche->chercher("Strasbourg", $catalogue);
echo "Ville inconnue 'Strasbourg' : " . ($trouve ? $trouve->afficherInfos() : "Non trouvé") . PHP_EOL;


echo PHP_EOL . "=== INTERSECTION TYPES (Descriptible & Estimable) ===" . PHP_EOL;

foreach ([$appart1, $maison2, $local2] as $bien) {
    echo afficherDescriptionEstimee($bien, 5) . PHP_EOL;
}


echo PHP_EOL . "=== PROPRIETAIRES ===" . PHP_EOL;

foreach ($propRepo->tous() as $proprio) {
    echo $proprio->afficherPortefeuille() . PHP_EOL . PHP_EOL;
}


echo PHP_EOL . "=== CONTRÔLES SUR LES PRIX ===" . PHP_EOL;

try {
    new Appartement("Lyon", -5000, 40, 2);
} catch (InvalidArgumentException $e) {
    echo "Prix négatif    → " . $e->getMessage() . PHP_EOL;
}

try {
    new Appartement("Lyon", 0, 40, 2);
} catch (InvalidArgumentException $e) {
    echo "Prix à zéro     → " . $e->getMessage() . PHP_EOL;
}

try {
    $appart3->setPrix(-1000);
} catch (InvalidArgumentException $e) {
    echo "Setter invalide → " . $e->getMessage() . PHP_EOL;
}

$appart3->setPrix(185000);
echo "Nouveau prix Bordeaux : " . number_format($appart3->getPrix(), 2) . " €" . PHP_EOL;
