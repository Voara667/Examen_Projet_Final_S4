<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Situation des gains</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Type</th>
            <th>Libellé</th>
            <th>Portée</th>
            <th>Opérateur externe</th>
            <th>Nombre</th>
            <th>Total frais</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($gains as $gain): ?>
            <tr>
                <td><?= esc($gain['type_transaction']) ?></td>
                <td><?= esc($gain['libelle_type']) ?></td>
                <td><?= esc($gain['portee']) ?></td>
                <td><?= $gain['operateur_externe_nom'] ? esc($gain['operateur_externe_nom']) : '-' ?></td>
                <td><?= esc($gain['nombre_transactions']) ?></td>
                <td><?= esc($gain['total_frais']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
