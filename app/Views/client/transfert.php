<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Transfert d'argent</h2>
                <?= form_open('/client/transfert/valider') ?>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Numéro du destinataire</label>
                        <input type="text" name="numero_destinataire" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant</label>
                        <input type="number" name="montant" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" value="1" id="inclureFraisRetrait">
                        <label class="form-check-label" for="inclureFraisRetrait">
                            Inclure les frais de retrait au destinataire
                        </label>
                        <div class="form-text">Le destinataire pourra retirer ce montant plus tard sans frais supplémentaires, dans la limite du montant envoyé.</div>
                    </div>
                    <button class="btn btn-info text-white">Valider</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
