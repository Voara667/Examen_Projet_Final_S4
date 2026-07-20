<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mobile Money Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="/">Mobile Money</a>
        <div class="navbar-nav ms-auto flex-row flex-wrap gap-2">
            <?php if (session()->has('admin')): ?>
                <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                <a class="nav-link" href="/admin/prefixes">Préfixes</a>
                <a class="nav-link" href="/admin/baremes">Barèmes</a>
                <a class="nav-link" href="/admin/operateurs">Opérateurs</a>
                <a class="nav-link" href="/admin/montants-a-envoyer">Montants à envoyer</a>
                <a class="nav-link" href="/admin/gains">Gains</a>
                <a class="nav-link" href="/admin/comptes">Comptes</a>
                <a class="nav-link" href="/admin/logout">Déconnexion</a>
            <?php elseif (session()->has('client')): ?>
                <a class="nav-link" href="/client/accueil">Accueil</a>
                <a class="nav-link" href="/client/depot">Dépôt</a>
                <a class="nav-link" href="/client/retrait">Retrait</a>
                <a class="nav-link" href="/client/transfert">Transfert</a>
                <a class="nav-link" href="/client/transfert-multiple">Transfert multiple</a>
                <a class="nav-link" href="/client/historique">Historique</a>
                <a class="nav-link" href="/client/logout">Déconnexion</a>
            <?php else: ?>
                <a class="nav-link" href="/admin/login">Opérateur</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
