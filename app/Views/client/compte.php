<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="mb-3">Bonjour <?= esc($client['numero_telephone']) ?></h2>
                <div class="alert alert-success">
                    <strong>Solde actuel : </strong><?= esc($client['solde']) ?> Ar
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/client/depot" class="btn btn-primary">Dépôt</a>
                    <a href="/client/retrait" class="btn btn-warning">Retrait</a>
                    <a href="/client/transfert" class="btn btn-info text-white">Transfert</a>
                    <a href="/client/transfert-multiple" class="btn btn-outline-primary">Transfert multiple</a>
                    <a href="/client/historique" class="btn btn-outline-secondary">Historique</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
