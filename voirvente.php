<?php
// ── Connexion ────────────────────────────────────────────────────────────────
define("host","localhost");
define("user","root");
define("pass","");
define("nameBDD","essaiebdd");

// Force mysqli à lancer des exceptions sur toute erreur SQL. Permet d'utiliser le bloc try {} catch {} un peu plus bas.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$connect = new mysqli(host, user, pass, nameBDD);
if ($connect->connect_error) {
    die("Connexion échouée : " . $connect->connect_error);
}
$connect->set_charset('utf8mb4'); //On force la communication entre PHP et MySQL en encodage utf8mb4

// ── Variables de message ──────────────────────────────────────────────────────
$messageSucces = '';
$messageErreur = '';

// ── Traitement du formulaire ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données client
    //trim() : Une fonction qui supprime les espaces inutiles au début et à la fin d'une chaîne de caractères
    $nom     = trim($_POST['nom']);
    $prenom  = trim($_POST['prenom']);
    $email   = trim($_POST['email']);
    $ville   = trim($_POST['ville']);
    $age     = (int) $_POST['age'];
    $adresse = trim($_POST['adresse']);

    // Récupération des données commande
    $date_achat = trim($_POST['date_achat']);
    $statut     = trim($_POST['statut']);

    // Récupération des produits
    $produits = $_POST['produits'];

    // Calcul du montant total (prix × quantité pour chaque produit)
    $montant = 0;
    foreach ($produits as $produit) {
        $montant += (float)$produit['prix'] * (int)$produit['qte_comm'];
    }

    // Ouverture de la transaction
    $connect->begin_transaction(); //permet de regrouper plusieurs requetes SQL. Si l'une d'entre elles echoue on pourra tout annuler

    try {

        // ── 1. INSERT dans client ─────────────────────────────────────────────
        $stmtClient = $connect->prepare(
            "INSERT INTO client (nom, prenom, mail, adresse, ville, age)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmtClient->bind_param('sssssi',
            $nom, $prenom, $email, $adresse, $ville, $age
        );
        $stmtClient->execute();

        $idClient = $connect->insert_id; // id du client inséré. Permet de lier précisément le client à sa commande

        // ── 2. INSERT dans commande ───────────────────────────────────────────
        $stmtCommande = $connect->prepare(
            "INSERT INTO commande (id_client, `date`, montant, statut) 
             VALUES (?, ?, ?, ?)"   //date est un mot réservé raison pour laquelle on le met entre backticks
        );
        $stmtCommande->bind_param('isds',
            $idClient, $date_achat, $montant, $statut
        );
        $stmtCommande->execute();

        $idComm = $connect->insert_id; // id de la commande insérée

        // ── 3 & 4. Pour chaque produit : INSERT article puis INSERT contenir ──
        foreach ($produits as $produit) {
            $designation = trim($produit['designation']);
            $categorie   = trim($produit['categorie']);
            $prix        = (float) $produit['prix'];
            $qte_comm    = (int)   $produit['qte_comm'];

            // 3. INSERT dans article
            $stmtArticle = $connect->prepare(
                "INSERT INTO article (designation, prix, `catégorie`)
                 VALUES (?, ?, ?)"
            );
            $stmtArticle->bind_param('sds',
                $designation, $prix, $categorie
            );
            $stmtArticle->execute();

            $idArticle = $connect->insert_id; // id de l'article inséré

            // 4. INSERT dans contenir
            $stmtContenir = $connect->prepare(
                "INSERT INTO contenir (id_comm, id_article, `qté_comm`)
                 VALUES (?, ?, ?)"
            );
            $stmtContenir->bind_param('iii',
                $idComm, $idArticle, $qte_comm
            );
            $stmtContenir->execute();
        }

        // Tout s'est bien passé → on valide
        $connect->commit();
        $messageSucces = "✓ Vente enregistrée avec succès ! Montant total : " . number_format($montant, 2) . " FCFA";

    } catch (Exception $e) {
        // Une erreur est survenue → on annule tout
        $connect->rollback();
        $messageErreur = "Erreur SQL : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="voirvente.css">
    <title>Formulaire des ventes</title>
</head>
<body>
<div class="container">
    <h1>Enregistrement d'une vente</h1>

    <?php if ($messageSucces): ?>
        <div class="msg-success"><?= htmlspecialchars($messageSucces) ?></div>
    <?php endif; ?>

    <?php if ($messageErreur): ?>
        <div class="msg-error"><?= htmlspecialchars($messageErreur) ?></div>
    <?php endif; ?>

    <form id="form-vente" method="POST" action="">

        <!-- ───── TABLE: client ───── -->
        <div class="card">
            <h2>Client</h2>

            <div class="grid-2">
                <div class="field">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required placeholder="Dupont" />
                </div>
                <div class="field">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required placeholder="Jean" />
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label for="age">Âge *</label>
                    <input type="number" id="age" name="age" required placeholder="18" min="1" />
                </div>
                <div class="field">
                    <label for="ville">Ville *</label>
                    <input type="text" id="ville" name="ville" required placeholder="Cotonou" />
                </div>
            </div>

            <div class="grid-2">
                <div class="field">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="jean@exemple.com" />
                </div>
                <div class="field">
                    <label for="adresse">Adresse *</label>
                    <input type="text" id="adresse" name="adresse" required placeholder="Rue, Ville, Pays" />
                </div>
            </div>
        </div>

        <!-- ───── TABLE: commande ───── -->
        <div class="card">
            <h2>Commande</h2>

            <div class="grid-2">
                <div class="field">
                    <label for="date_achat">Date de l'achat *</label>
                    <input type="date" id="date_achat" name="date_achat" required />
                </div>
                <div class="field">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="en_cours">En cours</option>
                        <option value="payee">Payée</option>
                        <option value="annulee">Annulée</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ───── TABLE: article + contenir ───── -->
        <div class="card">
            <h2>Produits</h2>

            <div class="ligne-header">
                <span>Désignation</span>
                <span>Catégorie</span>
                <span>Prix unitaire</span>
                <span>Quantité</span>
                <span></span>
            </div>

            <div id="lignes-produits">
                <!-- Ligne 1 — non supprimable -->
                <div class="ligne-produit">
                    <input type="text"   name="produits[0][designation]" required placeholder="Nom du produit" />
                    <input type="text"   name="produits[0][categorie]"   required placeholder="Catégorie" />
                    <input type="number" name="produits[0][prix]"        required placeholder="0.00" min="0" step="0.01" />
                    <input type="number" name="produits[0][qte_comm]"    required placeholder="1" min="1" value="1" />
                    <span></span>
                </div>
            </div>
            <button type="button" class="btn-add-ligne" onclick="ajouterLigne()">+ Ajouter un produit</button>
        </div>
        <button type="submit" class="btn-submit">Enregistrer la vente</button>
    </form>
</div>
<footer>
      <?php
        echo "<button><a href='accueil.php'>Quitter</a></button>"
      ?>
    </footer>

<script>
    let nbLignes = 1;

    function ajouterLigne() {
        const idx = nbLignes++;
        const div = document.createElement('div');
        div.className = 'ligne-produit';
        div.innerHTML = `
            <input type="text"   name="produits[${idx}][designation]" required placeholder="Nom du produit" />
            <input type="text"   name="produits[${idx}][categorie]"   required placeholder="Catégorie" />
            <input type="number" name="produits[${idx}][prix]"        required placeholder="0.00" min="0" step="0.01" />
            <input type="number" name="produits[${idx}][qte_comm]"    required placeholder="1" min="1" value="1" />
            <button type="button" class="btn-remove" onclick="this.closest('.ligne-produit').remove()" title="Supprimer">×</button>
        `;
        document.getElementById('lignes-produits').appendChild(div);
    }
</script>
</body>
</html>
