<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Situation des comptes clients</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Numéro</th>
            <th>Solde</th>
            <th>Créé le</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comptes as $compte): ?>
            <tr>
                <td><?= esc($compte['id']) ?></td>
                <td><?= esc($compte['numero_telephone']) ?></td>
                <td><?= esc($compte['solde']) ?></td>
                <td><?= esc($compte['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
