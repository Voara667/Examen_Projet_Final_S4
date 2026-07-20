<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Historique des transactions</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Frais</th>
            <th>Solde</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= esc($transaction['created_at']) ?></td>
                <td><?= esc($transaction['type_transaction_id']) ?></td>
                <td><?= esc($transaction['montant']) ?></td>
                <td><?= esc($transaction['frais']) ?></td>
                <td><?= esc($transaction['nouveau_solde']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
