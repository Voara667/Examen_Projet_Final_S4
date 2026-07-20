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

## Travaux V2 réalisés côté opérateur(voara)
- Mise à jour du schéma (`base.sql`) : nouvelle table `operateurs_externes` (nom + commission en %), colonne `operateur_externe_id` sur `prefixes`, colonnes `operateur_externe_id`, `numero_destinataire_externe` et `commission` sur `transactions`, nouvelles vues `vue_situation_gains` (séparée interne/externe) et `vue_montants_a_envoyer_operateurs`.
- Ajout du modèle `OperateurExterneModel` et de la méthode `PrefixeModel::trouverPourNumero()` (détection interne/externe réutilisée côté client).
- Ajout du CRUD complet des opérateurs externes (ajout, modification de la commission, suppression) et rattachement de préfixes à un opérateur externe.
- Mise à jour de la page « Situation des gains » pour séparer les gains internes des gains liés aux transferts vers les opérateurs externes.
- Ajout de la page « Montants à envoyer » listant, par opérateur externe, le montant total transféré, la commission due et le total à leur reverser.
- Mise à jour de la navbar et du tableau de bord opérateur avec l'accès aux nouvelles pages (Opérateurs externes, Montants à envoyer).
- Protection CSRF et affichage sécurisé des données avec esc() sur toutes les nouvelles vues.

## Travaux V2 réalisés côté client(samuel)
- Ajout du transfert vers opérateur externe avec calcul de commission et débit du solde du client.
- Ajout de l’option « inclure les frais de retrait » pour les transferts internes.
- Ajout du transfert multiple avec répartition équitable du montant, vérification du solde global et transaction atomique.
- Mise à jour de l’historique pour afficher les destinataires externes et la commission associée.
- Mise à jour de la navigation client et de la page de compte avec l’accès au transfert multiple et l’information sur les crédits de frais de retrait.