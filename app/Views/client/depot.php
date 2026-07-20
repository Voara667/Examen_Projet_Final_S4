<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Dépôt d'argent</h2>
                <?= form_open('/client/depot/valider') ?>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="number" name="montant" class="form-control" required>
                    </div>
                    <button class="btn btn-primary">Valider</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
