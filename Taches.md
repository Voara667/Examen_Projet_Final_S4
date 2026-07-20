# Taches — Livraison v1

## Équipe
- Voara : côté opérateur / back-office
- Samuel : côté client / self-service

## Travaux réalisés à la livraison v1

### Voara
- Configuration du projet CodeIgniter 4 avec SQLite3 embarqué et base SQL importée
- Définition des routes admin/client, filtres d’authentification et layout Bootstrap commun
- Implémentation du back-office opérateur :
  - login/logout administrateur
  - gestion des préfixes (ajout + activation/désactivation)
  - gestion des barèmes de frais
  - vues des gains et des comptes clients
- Protection CSRF et affichage sécurisé des données avec esc()

### Samuel
- Implémentation du parcours client sans inscription classique :
  - login automatique via numéro de téléphone
  - création de compte à la volée si inexistant
  - affichage du solde
  - dépôt, retrait et transfert avec contrôle de solde et calcul de frais
  - historique des transactions
- Interfaces Bootstrap adaptées au parcours client mobile-friendly
- Protection CSRF et affichage sécurisé des données avec esc()

## Point commun
- La méthode BaremeFraisModel::calculerFrais(int $typeTransactionId, int $montant): int a été centralisée pour alimenter les opérations de retrait et de transfert.