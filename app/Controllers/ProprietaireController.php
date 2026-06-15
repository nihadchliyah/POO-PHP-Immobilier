<?php

class ProprietaireController
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

    // ── Lecture ──────────────────────────────────────────────────────────────

    public function index(): void
    {
        $recherche = trim($_GET['q'] ?? '');

        if ($recherche !== '') {
            $stmt = $this->pdo->prepare("
                SELECT * FROM proprietaires
                WHERE nom LIKE :q OR email LIKE :q OR raison_sociale LIKE :q
                ORDER BY nom
            ");
            $stmt->execute([':q' => '%' . $recherche . '%']);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM proprietaires ORDER BY nom");
        }

        $proprietaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proprietaires as &$proprio) {
            $stmt = $this->pdo->prepare("
                SELECT b.reference, b.ville, b.prix, b.type
                FROM biens b
                JOIN proprietaires_biens pb ON b.id = pb.bien_id
                WHERE pb.proprietaire_id = ?
            ");
            $stmt->execute([$proprio['id']]);
            $proprio['biens'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($proprio);

        require __DIR__ . '/../Views/proprietaires/index.php';
    }

    // ── Création ─────────────────────────────────────────────────────────────

    public function create(): void
    {
        // Seuls les biens sans propriétaire sont proposés
        $biens = $this->pdo->query("
            SELECT id, reference, ville, type FROM biens
            WHERE id NOT IN (SELECT bien_id FROM proprietaires_biens)
            ORDER BY ville
        ")->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        require __DIR__ . '/../Views/proprietaires/create.php';
    }

    public function store(): void
    {
        $type          = $_POST['type']          ?? 'particulier';
        $nom           = trim($_POST['nom']       ?? '');
        $email         = trim($_POST['email']     ?? '');
        $telephone     = trim($_POST['telephone'] ?? '');
        $raisonSociale = trim($_POST['raisonSociale'] ?? '');
        $siret         = trim($_POST['siret']     ?? '');

        // ── Validation ───────────────────────────────────────────────────────
        $errors = [];

        if ($nom === '') {
            $errors[] = "Le nom est obligatoire.";
        }
        if ($email === '') {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } else {
            $check = $this->pdo->prepare("SELECT id FROM proprietaires WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = "Cet email est déjà utilisé par un autre propriétaire.";
            }
        }
        if ($type === 'particulier' && $telephone === '') {
            $errors[] = "Le téléphone est obligatoire pour un particulier.";
        }
        if ($type === 'professionnel' && $raisonSociale === '') {
            $errors[] = "La raison sociale est obligatoire pour un professionnel.";
        }
        if ($type === 'professionnel' && $siret === '') {
            $errors[] = "Le SIRET est obligatoire pour un professionnel.";
        }

        if (!empty($errors)) {
            $biens = $this->pdo->query("
                SELECT id, reference, ville, type FROM biens
                WHERE id NOT IN (SELECT bien_id FROM proprietaires_biens)
                ORDER BY ville
            ")->fetchAll(PDO::FETCH_ASSOC);
            require __DIR__ . '/../Views/proprietaires/create.php';
            return;
        }

        // ── Insertion ────────────────────────────────────────────────────────
        $stmt = $this->pdo->prepare("
            INSERT INTO proprietaires (nom, email, type, telephone, siret, raison_sociale)
            VALUES (:nom, :email, :type, :telephone, :siret, :raison_sociale)
        ");
        $stmt->execute([
            ':nom'            => $nom,
            ':email'          => $email,
            ':type'           => $type,
            ':telephone'      => $telephone ?: null,
            ':siret'          => $siret ?: null,
            ':raison_sociale' => $raisonSociale ?: null,
        ]);

        $proprioId = (int)$this->pdo->lastInsertId();

        // Lier uniquement les biens sans propriétaire
        foreach ($_POST['biens'] ?? [] as $bienId) {
            $bienId = (int)$bienId;
            $libre  = $this->pdo->prepare("SELECT bien_id FROM proprietaires_biens WHERE bien_id = ?");
            $libre->execute([$bienId]);
            if (!$libre->fetch()) {
                $this->pdo->prepare("
                    INSERT INTO proprietaires_biens (proprietaire_id, bien_id) VALUES (?, ?)
                ")->execute([$proprioId, $bienId]);
            }
        }

        header('Location: ?page=proprietaires');
        exit;
    }

    // ── Modification ─────────────────────────────────────────────────────────

    public function edit(?int $id): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM proprietaires WHERE id = ?");
        $stmt->execute([$id]);
        $proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$proprietaire) {
            header('Location: ?page=proprietaires');
            exit;
        }

        // Biens déjà liés à CE propriétaire
        $stmtBiens = $this->pdo->prepare("
            SELECT b.id FROM biens b
            JOIN proprietaires_biens pb ON b.id = pb.bien_id
            WHERE pb.proprietaire_id = ?
        ");
        $stmtBiens->execute([$id]);
        $biensSelectionnes = array_column($stmtBiens->fetchAll(PDO::FETCH_ASSOC), 'id');

        // Biens disponibles = sans propriétaire OU déjà liés à CE propriétaire
        $stmtDispos = $this->pdo->prepare("
            SELECT id, reference, ville, type FROM biens
            WHERE id NOT IN (
                SELECT bien_id FROM proprietaires_biens WHERE proprietaire_id != ?
            )
            ORDER BY ville
        ");
        $stmtDispos->execute([$id]);
        $biens = $stmtDispos->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        require __DIR__ . '/../Views/proprietaires/edit.php';
    }

    public function update(?int $id): void
    {
        $type          = $_POST['type']          ?? 'particulier';
        $nom           = trim($_POST['nom']       ?? '');
        $email         = trim($_POST['email']     ?? '');
        $telephone     = trim($_POST['telephone'] ?? '');
        $raisonSociale = trim($_POST['raisonSociale'] ?? '');
        $siret         = trim($_POST['siret']     ?? '');

        // ── Validation ───────────────────────────────────────────────────────
        $errors = [];

        if ($nom === '') {
            $errors[] = "Le nom est obligatoire.";
        }
        if ($email === '') {
            $errors[] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide.";
        } else {
            $check = $this->pdo->prepare("SELECT id FROM proprietaires WHERE email = ? AND id != ?");
            $check->execute([$email, $id]);
            if ($check->fetch()) {
                $errors[] = "Cet email est déjà utilisé par un autre propriétaire.";
            }
        }
        if ($type === 'particulier' && $telephone === '') {
            $errors[] = "Le téléphone est obligatoire pour un particulier.";
        }
        if ($type === 'professionnel' && $raisonSociale === '') {
            $errors[] = "La raison sociale est obligatoire pour un professionnel.";
        }
        if ($type === 'professionnel' && $siret === '') {
            $errors[] = "Le SIRET est obligatoire pour un professionnel.";
        }

        if (!empty($errors)) {
            // Re-charger les données nécessaires à la vue edit
            $stmt = $this->pdo->prepare("SELECT * FROM proprietaires WHERE id = ?");
            $stmt->execute([$id]);
            $proprietaire = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmtBiens = $this->pdo->prepare("
                SELECT b.id FROM biens b
                JOIN proprietaires_biens pb ON b.id = pb.bien_id
                WHERE pb.proprietaire_id = ?
            ");
            $stmtBiens->execute([$id]);
            $biensSelectionnes = array_column($stmtBiens->fetchAll(PDO::FETCH_ASSOC), 'id');

            $stmtDispos = $this->pdo->prepare("
                SELECT id, reference, ville, type FROM biens
                WHERE id NOT IN (
                    SELECT bien_id FROM proprietaires_biens WHERE proprietaire_id != ?
                )
                ORDER BY ville
            ");
            $stmtDispos->execute([$id]);
            $biens = $stmtDispos->fetchAll(PDO::FETCH_ASSOC);

            require __DIR__ . '/../Views/proprietaires/edit.php';
            return;
        }

        // ── Mise à jour ───────────────────────────────────────────────────────
        $stmt = $this->pdo->prepare("
            UPDATE proprietaires
            SET nom = :nom, email = :email, type = :type,
                telephone = :telephone, siret = :siret, raison_sociale = :raison_sociale
            WHERE id = :id
        ");
        $stmt->execute([
            ':nom'            => $nom,
            ':email'          => $email,
            ':type'           => $type,
            ':telephone'      => $telephone ?: null,
            ':siret'          => $siret ?: null,
            ':raison_sociale' => $raisonSociale ?: null,
            ':id'             => $id,
        ]);

        // Recalcule les biens liés — respecte la règle 1 bien = 1 propriétaire
        $this->pdo->prepare("DELETE FROM proprietaires_biens WHERE proprietaire_id = ?")->execute([$id]);
        foreach ($_POST['biens'] ?? [] as $bienId) {
            $bienId = (int)$bienId;
            $libre  = $this->pdo->prepare("SELECT bien_id FROM proprietaires_biens WHERE bien_id = ?");
            $libre->execute([$bienId]);
            if (!$libre->fetch()) {
                $this->pdo->prepare("
                    INSERT INTO proprietaires_biens (proprietaire_id, bien_id) VALUES (?, ?)
                ")->execute([$id, $bienId]);
            }
        }

        header('Location: ?page=proprietaires');
        exit;
    }

    // ── Suppression ──────────────────────────────────────────────────────────

    public function delete(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pdo->prepare("DELETE FROM proprietaires WHERE id = ?")->execute([$id]);
        }
        header('Location: ?page=proprietaires');
        exit;
    }
}
