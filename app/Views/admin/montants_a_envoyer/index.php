<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Montants à envoyer aux opérateurs externes</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Opérateur</th>
            <th>Nombre de transferts</th>
            <th>Total montant</th>
            <th>Total commission</th>
            <th>Total à envoyer</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($montants)): ?>
            <tr>
                <td colspan="5" class="text-center">Aucun transfert externe enregistré.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($montants as $montant): ?>
                <tr>
                    <td><?= esc($montant['operateur_externe_nom']) ?></td>
                    <td><?= esc($montant['nombre_transferts']) ?></td>
                    <td><?= esc($montant['total_montant']) ?></td>
                    <td><?= esc($montant['total_commission']) ?></td>
                    <td><?= esc($montant['total_a_envoyer']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
