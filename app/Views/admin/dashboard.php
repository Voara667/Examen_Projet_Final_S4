<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Tableau de bord opérateur</h2>
    <a href="/admin/logout" class="btn btn-outline-danger">Déconnexion</a>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Préfixes</h5>
                <p class="card-text">Gérez les préfixes téléphoniques autorisés.</p>
                <a href="/admin/prefixes" class="btn btn-primary">Voir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Barèmes</h5>
                <p class="card-text">Configurez les frais par type de transaction.</p>
                <a href="/admin/baremes" class="btn btn-primary">Voir</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Rapports</h5>
                <p class="card-text">Consultez les gains et les comptes clients.</p>
                <a href="/admin/gains" class="btn btn-primary">Voir</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
