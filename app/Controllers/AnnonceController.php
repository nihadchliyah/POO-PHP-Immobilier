<?php

class AnnonceController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function dispatch(string $action, ?int $id): void
    {
        match ($action) {
            'show'   => $this->show($id),
            'create' => $this->create(),
            'store'  => $this->store(),
            'edit'   => $this->edit($id),
            'update' => $this->update($id),
            'delete' => $this->delete($id),
            default  => $this->index(),
        };
    }

    public function show(?int $id): void
    {
        if (!$id) { header('Location: ?page=annonces'); exit; }

        $stmt = $this->pdo->prepare("
            SELECT a.*,
                   b.reference, b.type as bien_type, b.ville, b.prix as prix_bien,
                   b.surface, b.statut as statut_bien, b.details, b.created_at as bien_created_at
            FROM annonces a
            JOIN biens b ON a.bien_id = b.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$annonce) { header('Location: ?page=annonces'); exit; }

        // Propriétaire du bien
        $stmtProprio = $this->pdo->prepare("
            SELECT p.* FROM proprietaires p
            JOIN proprietaires_biens pb ON p.id = pb.proprietaire_id
            WHERE pb.bien_id = ?
        ");
        $stmtProprio->execute([$annonce['bien_id']]);
        $proprietaire = $stmtProprio->fetch(PDO::FETCH_ASSOC) ?: null;

        // Autres annonces du même bien
        $stmtRelated = $this->pdo->prepare("
            SELECT a.id, a.titre, a.type_transaction, a.prix_demande, a.statut
            FROM annonces a
            WHERE a.bien_id = ? AND a.id != ?
            ORDER BY a.created_at DESC
            LIMIT 3
        ");
        $stmtRelated->execute([$annonce['bien_id'], $id]);
        $autresAnnonces = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);

        // Annonces similaires (même type de bien, autre ville)
        $stmtSimilaires = $this->pdo->prepare("
            SELECT a.id, a.titre, a.type_transaction, a.prix_demande, a.statut,
                   b.ville, b.surface, b.type as bien_type, b.reference
            FROM annonces a
            JOIN biens b ON a.bien_id = b.id
            WHERE b.type = ? AND a.id != ? AND a.statut = 'active'
            ORDER BY RANDOM()
            LIMIT 3
        ");
        $stmtSimilaires->execute([$annonce['bien_type'], $id]);
        $similaires = $stmtSimilaires->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/annonces/show.php';
    }

    public function index(): void
    {
        $recherche = trim($_GET['q'] ?? '');
        $filtreTx  = $_GET['transaction'] ?? '';
        $filtreStatut = $_GET['statut'] ?? '';

        $where  = ['1=1'];
        $params = [];

        if ($recherche !== '') {
            $where[]       = "(a.titre LIKE :q OR b.ville LIKE :q OR a.contact_nom LIKE :q OR b.reference LIKE :q)";
            $params[':q']  = '%' . $recherche . '%';
        }
        if ($filtreTx !== '') {
            $where[]             = "a.type_transaction = :tx";
            $params[':tx']       = $filtreTx;
        }
        if ($filtreStatut !== '') {
            $where[]             = "a.statut = :statut";
            $params[':statut']   = $filtreStatut;
        }

        $whereStr = implode(' AND ', $where);
        $stmt = $this->pdo->prepare("
            SELECT a.*,
                   b.ville, b.type as bien_type, b.surface, b.prix as prix_bien,
                   b.reference, b.statut as statut_bien, b.details
            FROM annonces a
            JOIN biens b ON a.bien_id = b.id
            WHERE {$whereStr}
            ORDER BY a.created_at DESC
        ");
        $stmt->execute($params);
        $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats globales
        $statsRaw = $this->pdo->query("
            SELECT
                COUNT(*) as total,
                SUM(statut='active') as actives,
                SUM(statut='archivee') as archivees,
                SUM(type_transaction='vente') as ventes,
                SUM(type_transaction='location') as locations
            FROM annonces
        ")->fetch(PDO::FETCH_ASSOC);
        $stats = $statsRaw ?: ['total' => 0, 'actives' => 0, 'archivees' => 0, 'ventes' => 0, 'locations' => 0];

        require __DIR__ . '/../Views/annonces/index.php';
    }

    public function create(): void
    {
        $biens = $this->pdo->query("
            SELECT id, reference, ville, type, prix FROM biens
            ORDER BY ville
        ")->fetchAll(PDO::FETCH_ASSOC);

        $errors = [];
        require __DIR__ . '/../Views/annonces/create.php';
    }

    public function store(): void
    {
        $bienId          = (int)($_POST['bien_id'] ?? 0);
        $titre           = trim($_POST['titre'] ?? '');
        $description     = trim($_POST['description'] ?? '');
        $typeTransaction = $_POST['type_transaction'] ?? 'vente';
        $prixDemande     = !empty($_POST['prix_demande']) ? (float)$_POST['prix_demande'] : null;
        $contactNom      = trim($_POST['contact_nom'] ?? '');
        $contactEmail    = trim($_POST['contact_email'] ?? '');
        $contactTel      = trim($_POST['contact_telephone'] ?? '');
        $statut          = $_POST['statut'] ?? 'active';

        $errors = [];
        if ($bienId <= 0)         $errors[] = "Veuillez sélectionner un bien.";
        if ($titre === '')        $errors[] = "Le titre est obligatoire.";
        if ($contactNom === '')   $errors[] = "Le nom du contact est obligatoire.";
        if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email de contact est invalide.";
        }

        if (!empty($errors)) {
            $biens = $this->pdo->query("SELECT id, reference, ville, type, prix FROM biens ORDER BY ville")->fetchAll(PDO::FETCH_ASSOC);
            require __DIR__ . '/../Views/annonces/create.php';
            return;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO annonces
                (bien_id, titre, description, type_transaction, prix_demande, contact_nom, contact_email, contact_telephone, statut)
            VALUES
                (:bien_id, :titre, :description, :type_transaction, :prix_demande, :contact_nom, :contact_email, :contact_telephone, :statut)
        ");
        $stmt->execute([
            ':bien_id'           => $bienId,
            ':titre'             => $titre,
            ':description'       => $description,
            ':type_transaction'  => $typeTransaction,
            ':prix_demande'      => $prixDemande,
            ':contact_nom'       => $contactNom,
            ':contact_email'     => $contactEmail,
            ':contact_telephone' => $contactTel ?: null,
            ':statut'            => $statut,
        ]);

        header('Location: ?page=annonces');
        exit;
    }

    public function edit(?int $id): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM annonces WHERE id = ?");
        $stmt->execute([$id]);
        $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$annonce) {
            header('Location: ?page=annonces');
            exit;
        }

        $biens  = $this->pdo->query("SELECT id, reference, ville, type, prix FROM biens ORDER BY ville")->fetchAll(PDO::FETCH_ASSOC);
        $errors = [];
        require __DIR__ . '/../Views/annonces/edit.php';
    }

    public function update(?int $id): void
    {
        $bienId          = (int)($_POST['bien_id'] ?? 0);
        $titre           = trim($_POST['titre'] ?? '');
        $description     = trim($_POST['description'] ?? '');
        $typeTransaction = $_POST['type_transaction'] ?? 'vente';
        $prixDemande     = !empty($_POST['prix_demande']) ? (float)$_POST['prix_demande'] : null;
        $contactNom      = trim($_POST['contact_nom'] ?? '');
        $contactEmail    = trim($_POST['contact_email'] ?? '');
        $contactTel      = trim($_POST['contact_telephone'] ?? '');
        $statut          = $_POST['statut'] ?? 'active';

        $errors = [];
        if ($bienId <= 0)         $errors[] = "Veuillez sélectionner un bien.";
        if ($titre === '')        $errors[] = "Le titre est obligatoire.";
        if ($contactNom === '')   $errors[] = "Le nom du contact est obligatoire.";
        if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email de contact est invalide.";
        }

        if (!empty($errors)) {
            $stmt = $this->pdo->prepare("SELECT * FROM annonces WHERE id = ?");
            $stmt->execute([$id]);
            $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
            $biens   = $this->pdo->query("SELECT id, reference, ville, type, prix FROM biens ORDER BY ville")->fetchAll(PDO::FETCH_ASSOC);
            require __DIR__ . '/../Views/annonces/edit.php';
            return;
        }

        $stmt = $this->pdo->prepare("
            UPDATE annonces
            SET bien_id = :bien_id, titre = :titre, description = :description,
                type_transaction = :type_transaction, prix_demande = :prix_demande,
                contact_nom = :contact_nom, contact_email = :contact_email,
                contact_telephone = :contact_telephone, statut = :statut
            WHERE id = :id
        ");
        $stmt->execute([
            ':bien_id'           => $bienId,
            ':titre'             => $titre,
            ':description'       => $description,
            ':type_transaction'  => $typeTransaction,
            ':prix_demande'      => $prixDemande,
            ':contact_nom'       => $contactNom,
            ':contact_email'     => $contactEmail,
            ':contact_telephone' => $contactTel ?: null,
            ':statut'            => $statut,
            ':id'                => $id,
        ]);

        header('Location: ?page=annonces');
        exit;
    }

    public function delete(?int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pdo->prepare("DELETE FROM annonces WHERE id = ?")->execute([$id]);
        }
        header('Location: ?page=annonces');
        exit;
    }
}
