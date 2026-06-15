<?php

class BienController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function dispatch(string $action, ?int $id): void
    {
        match ($action) {
            'create' => $this->create(),
            'store'  => $this->store(),
            'edit'   => $this->edit($id),
            'update' => $this->update($id),
            'delete' => $this->delete($id),
            default  => $this->index(),
        };
    }

    public function index(): void
    {
        $recherche = trim($_GET['q'] ?? '');

        // Stats globales (toujours sur tous les biens)
        $statsRow = $this->pdo->query("
            SELECT
                COUNT(*) as total,
                SUM(statut='disponible') as disponible,
                SUM(statut='loue') as loue,
                SUM(statut='vendu') as vendu
            FROM biens
        ")->fetch(PDO::FETCH_ASSOC);
        $stats = $statsRow ?: ['total' => 0, 'disponible' => 0, 'loue' => 0, 'vendu' => 0];

        if ($recherche !== '') {
            $stmt = $this->pdo->prepare("
                SELECT b.*,
                       IFNULL((SELECT COUNT(*) FROM annonces a WHERE a.bien_id = b.id AND a.statut = 'active'), 0) as nb_annonces
                FROM biens b
                WHERE b.ville LIKE :q OR b.reference LIKE :q OR b.type LIKE :q
                ORDER BY b.type, b.ville
            ");
            $stmt->execute([':q' => '%' . $recherche . '%']);
        } else {
            $stmt = $this->pdo->query("
                SELECT b.*,
                       IFNULL((SELECT COUNT(*) FROM annonces a WHERE a.bien_id = b.id AND a.statut = 'active'), 0) as nb_annonces
                FROM biens b
                ORDER BY b.type, b.ville
            ");
        }

        $biens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../Views/biens/index.php';
    }


    public function create(): void
    {
        require __DIR__ . '/../Views/biens/create.php';
    }

    public function store(): void
    {
        $type    = $_POST['type']    ?? 'appartement';
        $ville   = trim($_POST['ville']   ?? '');
        $prix    = (float)($_POST['prix']    ?? 0);
        $surface = (float)($_POST['surface'] ?? 0);
        $statut  = $_POST['statut']  ?? 'disponible';

        // ── Validation ───────────────────────────────────────────────────────
        $errors = [];
        if ($ville === '')  $errors[] = "La ville est obligatoire.";
        if ($prix <= 0)     $errors[] = "Le prix doit être supérieur à 0.";
        if ($surface <= 0)  $errors[] = "La surface doit être supérieure à 0.";
        if ($type === 'local' && trim($_POST['activite'] ?? '') === '') {
            $errors[] = "L'activité est obligatoire pour un local commercial.";
        }

        if (!empty($errors)) {
            require __DIR__ . '/../Views/biens/create.php';
            return;
        }

        $count     = (int)$this->pdo->query("SELECT COUNT(*) FROM biens")->fetchColumn();
        $reference = 'REF-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $details = match ($type) {
            'appartement' => json_encode([
                'etage'           => (int)($_POST['etage'] ?? 0),
                'ascenseur'       => isset($_POST['ascenseur']),
                'typeAppartement' => $_POST['typeAppartement'] ?? '',
            ]),
            'maison' => json_encode([
                'nbChambres'    => (int)($_POST['nbChambres'] ?? 0),
                'jardin'        => isset($_POST['jardin']),
                'surfaceJardin' => (float)($_POST['surfaceJardin'] ?? 0),
                'garage'        => $_POST['garage'] ?? '',
            ]),
            'local' => json_encode([
                'activite' => $_POST['activite'] ?? '',
            ]),
            default => '{}',
        };

        $stmt = $this->pdo->prepare("
            INSERT INTO biens (reference, type, ville, prix, surface, statut, details)
            VALUES (:reference, :type, :ville, :prix, :surface, :statut, :details)
        ");
        $stmt->execute([
            ':reference' => $reference,
            ':type'      => $type,
            ':ville'     => $ville,
            ':prix'      => $prix,
            ':surface'   => $surface,
            ':statut'    => $statut,
            ':details'   => $details,
        ]);

        header('Location: ?page=biens');
        exit;
    }

    public function edit(?int $id): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM biens WHERE id = ?");
        $stmt->execute([$id]);
        $bien = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bien) {
            header('Location: ?page=biens');
            exit;
        }

        $bien['details'] = json_decode($bien['details'] ?? '{}', true) ?? [];
        require __DIR__ . '/../Views/biens/edit.php';
    }

    public function update(?int $id): void
    {
        $type    = $_POST['type']    ?? 'appartement';
        $ville   = trim($_POST['ville']   ?? '');
        $prix    = (float)($_POST['prix']    ?? 0);
        $surface = (float)($_POST['surface'] ?? 0);
        $statut  = $_POST['statut']  ?? 'disponible';

        // ── Validation ───────────────────────────────────────────────────────
        $errors = [];
        if ($ville === '')  $errors[] = "La ville est obligatoire.";
        if ($prix <= 0)     $errors[] = "Le prix doit être supérieur à 0.";
        if ($surface <= 0)  $errors[] = "La surface doit être supérieure à 0.";
        if ($type === 'local' && trim($_POST['activite'] ?? '') === '') {
            $errors[] = "L'activité est obligatoire pour un local commercial.";
        }

        if (!empty($errors)) {
            $stmt = $this->pdo->prepare("SELECT * FROM biens WHERE id = ?");
            $stmt->execute([$id]);
            $bien = $stmt->fetch(PDO::FETCH_ASSOC);
            $bien['details'] = json_decode($bien['details'] ?? '{}', true) ?? [];
            require __DIR__ . '/../Views/biens/edit.php';
            return;
        }

        $details = match ($type) {
            'appartement' => json_encode([
                'etage'           => (int)($_POST['etage'] ?? 0),
                'ascenseur'       => isset($_POST['ascenseur']),
                'typeAppartement' => $_POST['typeAppartement'] ?? '',
            ]),
            'maison' => json_encode([
                'nbChambres'    => (int)($_POST['nbChambres'] ?? 0),
                'jardin'        => isset($_POST['jardin']),
                'surfaceJardin' => (float)($_POST['surfaceJardin'] ?? 0),
                'garage'        => $_POST['garage'] ?? '',
            ]),
            'local' => json_encode([
                'activite' => $_POST['activite'] ?? '',
            ]),
            default => '{}',
        };

        $stmt = $this->pdo->prepare("
            UPDATE biens
            SET type = :type, ville = :ville, prix = :prix,
                surface = :surface, statut = :statut, details = :details
            WHERE id = :id
        ");
        $stmt->execute([
            ':type'    => $type,
            ':ville'   => $ville,
            ':prix'    => $prix,
            ':surface' => $surface,
            ':statut'  => $statut,
            ':details' => $details,
            ':id'      => $id,
        ]);

        header('Location: ?page=biens');
        exit;
    }

    public function delete(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stmt = $this->pdo->prepare("DELETE FROM biens WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: ?page=biens');
        exit;
    }
}
