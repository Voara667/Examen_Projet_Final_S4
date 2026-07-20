<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Transfert multiple</h2>
                <?= form_open('/client/transfert-multiple/valider') ?>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Montant total</label>
                        <input type="number" name="montant_total" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Numéros destinataires</label>
                        <div id="recipients-container">
                            <div class="input-group mb-2 recipient-row">
                                <input type="text" name="numeros[]" class="form-control" placeholder="Numéro 1" required>
                                <button type="button" class="btn btn-outline-danger remove-number">Retirer</button>
                            </div>
                            <div class="input-group mb-2 recipient-row">
                                <input type="text" name="numeros[]" class="form-control" placeholder="Numéro 2" required>
                                <button type="button" class="btn btn-outline-danger remove-number">Retirer</button>
                            </div>
                        </div>
                        <button type="button" id="add-number" class="btn btn-outline-secondary btn-sm">+ Ajouter un numéro</button>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" value="1" id="inclureFraisRetraitMultiple">
                        <label class="form-check-label" for="inclureFraisRetraitMultiple">
                            Inclure les frais de retrait au destinataire
                        </label>
                    </div>
                    <button class="btn btn-info text-white">Valider</button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('recipients-container');
        const addButton = document.getElementById('add-number');

        addButton.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'input-group mb-2 recipient-row';
            row.innerHTML = `
                <input type="text" name="numeros[]" class="form-control" placeholder="Numéro ${container.children.length + 1}" required>
                <button type="button" class="btn btn-outline-danger remove-number">Retirer</button>
            `;
            container.appendChild(row);
        });

        container.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-number')) {
                const rows = container.querySelectorAll('.recipient-row');
                if (rows.length > 2) {
                    event.target.closest('.recipient-row').remove();
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
