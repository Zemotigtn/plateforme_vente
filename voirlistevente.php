<?php
// ── Connexion ─────────────────────────────────────────────────────────────────

include "connexion.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$connect->set_charset('utf8mb4');

// ── Récupérer tous les clients qui ont au moins une commande ──────────────────
$sqlClients = "
    SELECT DISTINCT c.id_client, c.nom, c.prenom, c.mail, c.ville, c.age, c.adresse
    FROM client c
    INNER JOIN commande cmd ON c.id_client = cmd.id_client
    ORDER BY c.nom ASC, c.prenom ASC
";
$resultClients = $connect->query($sqlClients);

// ── Pour chaque client, récupérer ses commandes et les produits ───────────────
$clients = [];
while ($client = $resultClients->fetch_assoc()) {
    $idClient = $client['id_client'];

    // Récupérer les commandes du client
    $sqlCommandes = "
        SELECT id_comm, `date`, montant, statut
        FROM commande
        WHERE id_client = ?
        ORDER BY `date` DESC
    ";
    $stmtCmd = $connect->prepare($sqlCommandes);
    $stmtCmd->bind_param('i', $idClient);
    $stmtCmd->execute();
    $resultCommandes = $stmtCmd->get_result();

    $commandes = [];
    while ($commande = $resultCommandes->fetch_assoc()) {
        $idComm = $commande['id_comm'];

        // Récupérer les produits de cette commande
        $sqlProduits = "
            SELECT a.designation, a.prix, a.`catégorie`, co.`qté_comm`,
                   (a.prix * co.`qté_comm`) AS sous_total
            FROM contenir co
            INNER JOIN article a ON co.id_article = a.id_article
            WHERE co.id_comm = ?
        ";
        $stmtProd = $connect->prepare($sqlProduits);
        $stmtProd->bind_param('i', $idComm);
        $stmtProd->execute();
        $resultProduits = $stmtProd->get_result();

        $produits = [];
        while ($produit = $resultProduits->fetch_assoc()) {
            $produits[] = $produit;
        }

        $commande['produits'] = $produits; // on attache les produits à la commande;
        $commandes[] = $commande; // on ajoute cette commande au client
    }

    $client['commandes'] = $commandes; // on attche les commandes au client
    $clients[] = $client; // on ajoute ce client au tableau final
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="voirlistevente.css">
    <title>Liste des ventes par client</title>
</head>

<body>
    <div class="container">

        <div class="page-header">
            <h1>Ventes par client</h1>
            <a href="voirvente.php" class="btn-retour">Nouveau enregistrement</a>
        </div>

        <?php if (empty($clients)): ?>
            <div class="vide">Aucune vente enregistrée pour le moment.</div>

        <?php else: ?>
            <?php foreach ($clients as $i => $client): ?>
                <?php
                $initiales = strtoupper(substr($client['nom'], 0, 1) . substr($client['prenom'], 0, 1)); //strtoupper transforme en majuscules
                $nbCommandes = count($client['commandes']);
                ?>
                <div class="client-card">

                    <!-- En-tête client cliquable -->
                    <div class="client-header" onclick="toggleClient(<?= $i ?>)">
                        <div class="client-infos">
                            <div class="avatar"><?= htmlspecialchars($initiales) ?></div>
                            <div>
                                <div class="client-nom">
                                    <?= htmlspecialchars($client['nom']) ?>
                                    <?= htmlspecialchars($client['prenom']) ?>
                                </div>
                                <div class="client-details">
                                    <?= htmlspecialchars($client['ville']) ?> •
                                    <?= htmlspecialchars($client['mail']) ?>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="badge-nb"><?= $nbCommandes ?> commande<?= $nbCommandes > 1 ? 's' : '' ?></span>
                            <span class="chevron" id="chevron-<?= $i ?>"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAA3klEQVR4AeySMQrCQBBFg5ewEBHxHOJtLOxsvIONR7HwCiJeQkTEwmP4XsAUurvZFCkCG+ZnJzN//l+GjKqen2LQuuCyorKiascOxiAW9raxpvXUX7SBsAcXMAG/Yc3egcYaBCNlcGTiBhZAIQVJ6zC3Zk/Oqa4GXimDN/wleIAZUFBhYW7Nnhy5UP4jZSDbQQXufCh45rwCc2v25FAKR5uBUy9eK+Bt55xTYG7NHp/xyDFwWiFv66n4N7eXRK6BIoornHVzB0QXA/ne/mmSi64GuboNrxg0q4glw1/RBwAA//8ha2D3AAAABklEQVQDABNPIjF5ZB/4AAAAAElFTkSuQmCC" /></span>
                        </div>
                    </div>

                    <!-- Corps client -->
                    <div class="client-body" id="body-<?= $i ?>">

                        <!-- Infos client -->
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Âge</label>
                                <span><?= htmlspecialchars($client['age']) ?> ans</span>
                            </div>
                            <div class="info-item">
                                <label>Ville</label>
                                <span><?= htmlspecialchars($client['ville']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Adresse</label>
                                <span><?= htmlspecialchars($client['adresse']) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Email</label>
                                <span><?= htmlspecialchars($client['mail']) ?></span>
                            </div>
                        </div>

                        <!-- Commandes -->
                        <?php foreach ($client['commandes'] as $commande): ?>
                            <div class="commande-bloc">

                                <div class="commande-header">
                                    <span class="commande-date">
                                        <?= date('d/m/Y', strtotime($commande['date'])) ?>
                                    </span>
                                    <div class="commande-meta">
                                        <span class="montant">
                                            <?= number_format($commande['montant'], 2) ?> FCFA
                                        </span>
                                        <span class="statut statut-<?= $commande['statut'] ?>">
                                            <?= match ($commande['statut']) {
                                                'en_cours' => 'En cours',
                                                'payee'    => 'Payée',
                                                'annulee'  => 'Annulée',
                                                default    => $commande['statut']
                                            } ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Tableau des produits -->
                                <table class="produits-table">
                                    <thead>
                                        <tr>
                                            <th>Désignation</th>
                                            <th>Catégorie</th>
                                            <th>Prix unitaire</th>
                                            <th>Quantité</th>
                                            <th>Sous-total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($commande['produits'] as $produit): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($produit['designation']) ?></td>
                                                <td><?= htmlspecialchars($produit['catégorie']) ?></td>
                                                <td><?= number_format($produit['prix'], 2) ?> FCFA</td>
                                                <td><?= $produit['qté_comm'] ?></td>
                                                <td><?= number_format($produit['sous_total'], 2) ?> FCFA</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <footer>
        <?php
        echo "<button><a href='accueil.php'>Quitter</a></button>"
        ?>
    </footer>
    <script>
        function toggleClient(i) {
            const body = document.getElementById('body-' + i);
            const chevron = document.getElementById('chevron-' + i);
            body.classList.toggle('ouvert');
            chevron.classList.toggle('ouvert');
        }
    </script>
</body>

</html>