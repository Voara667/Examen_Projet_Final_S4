<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h2 class="mb-4">Historique des transactions</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Destinataire</th>
            <th>Commission</th>
            <th>Montant</th>
            <th>Frais</th>
            <th>Solde</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?= esc($transaction['created_at']) ?></td>
                <td><?= esc($transaction['type_transaction_libelle']) ?></td>
                <td>
                    <?php if (! empty($transaction['numero_destinataire_externe'])): ?>
                        <?= esc($transaction['numero_destinataire_externe']) ?>
                        <?php if (! empty($transaction['operateur_externe_nom'])): ?>
                            (<?= esc($transaction['operateur_externe_nom']) ?>)
                        <?php endif; ?>
                    <?php elseif (! empty($transaction['client_destinataire_numero'])): ?>
                        <?= esc($transaction['client_destinataire_numero']) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?= esc($transaction['commission']) ?></td>
                <td><?= esc($transaction['montant']) ?></td>
                <td><?= esc($transaction['frais']) ?></td>
                <td><?= esc($transaction['nouveau_solde']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
