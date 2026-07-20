<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Connexion client</h2>
                <p class="text-muted">Entrez votre numéro de téléphone pour accéder à votre espace.</p>
                <?= form_open('/client/login') ?>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Numéro de téléphone</label>
                        <input type="text" name="numero_telephone" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Se connecter</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
