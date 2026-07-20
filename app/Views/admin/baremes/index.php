<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Gestion des barèmes</h2>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <?= form_open('/admin/baremes/store') ?>
            <?= csrf_field() ?>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type_transaction_id" class="form-select">
                        <?php foreach ($types as $type): ?>
                            <option value="<?= esc($type['id']) ?>"><?= esc($type['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Min</label>
                    <input type="number" name="montant_min" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Max</label>
                    <input type="number" name="montant_max" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Frais</label>
                    <input type="number" name="frais" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary mt-4">Ajouter</button>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Type</th>
            <th>Min</th>
            <th>Max</th>
            <th>Frais</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($baremes as $bareme): ?>
            <tr>
                <td><?= esc($bareme['type_transaction_id']) ?></td>
                <td><?= esc($bareme['montant_min']) ?></td>
                <td><?= esc($bareme['montant_max']) ?></td>
                <td><?= esc($bareme['frais']) ?></td>
                <td>
                    <?= form_open('/admin/baremes/delete/' . $bareme['id'], ['style' => 'display:inline']) ?>
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    <?= form_close() ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
