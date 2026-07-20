<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Gestion des préfixes</h2>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <?= form_open('/admin/prefixes/store') ?>
            <?= csrf_field() ?>
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Nouveau préfixe</label>
                    <input type="text" name="prefixe" class="form-control" maxlength="3" required>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        <?= form_close() ?>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Préfixe</th>
            <th>État</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($prefixes as $prefixe): ?>
            <tr>
                <td><?= esc($prefixe['prefixe']) ?></td>
                <td><?= $prefixe['actif'] ? 'Actif' : 'Inactif' ?></td>
                <td>
                    <?= form_open('/admin/prefixes/toggle/' . $prefixe['id'], ['style' => 'display:inline']) ?>
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-outline-secondary">Basculer</button>
                    <?= form_close() ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
