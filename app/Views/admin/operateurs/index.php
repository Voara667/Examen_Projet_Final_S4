<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Opérateurs externes</h2>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <?= form_open('/admin/operateurs/store') ?>
            <?= csrf_field() ?>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Commission (%)</label>
                    <input type="number" step="0.01" min="0" name="commission_pourcentage" class="form-control" value="0">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Ajouter</button>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Commission (%)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($operateurs as $operateur): ?>
            <tr>
                <td>
                    <?= form_open('/admin/operateurs/update/' . $operateur['id']) ?>
                        <?= csrf_field() ?>
                        <input type="text" name="nom" class="form-control" value="<?= esc($operateur['nom']) ?>" required>
                </td>
                <td>
                        <input type="number" step="0.01" min="0" name="commission_pourcentage" class="form-control" value="<?= esc($operateur['commission_pourcentage']) ?>">
                </td>
                <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">Mettre à jour</button>
                    <?= form_close() ?>
                    <?= form_open('/admin/operateurs/delete/' . $operateur['id'], ['style' => 'display:inline']) ?>
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    <?= form_close() ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
