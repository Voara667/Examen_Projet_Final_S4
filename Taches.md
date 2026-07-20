Répartition des tâches:
Voara:
Authentification	        AuthFilter + AdminController::login/logout, protège tout le reste du côté opérateur
Gestion préfixes	        CRUD prefixes (ajouter/désactiver un préfixe)
Gestion barèmes	            CRUD baremes_frais par type de transaction (modifier les tranches/montants)
Situation gains	            Vue basée sur vue_situation_gains (total frais par type)S
Situation comptes clients	Vue basée sur vue_situation_comptes_clients
Vues Bootstrap	            Dashboard admin (tableaux, formulaires)



Samuel:
Login auto	    ClientController::login — saisie numéro → vérifie préfixe valide → crée le compte si inexistant → session
Solde	        Affichage solde courant
Dépôt	        Formulaire montant → crédite directement (frais = 0)
Retrait	        Formulaire montant → vérifie solde suffisant → calcule frais via barème → débite
Transfert	    Formulaire numéro destinataire + montant → vérifie solde → calcule frais (émetteur seul) → débite émetteur, crédite destinataire
Historique	    Liste des transactions du client connecté
Vues Bootstrap	Interface client (mobile-friendly, simple)

Point de vigilance commun : la logique de calcul de frais (trouver le bon barème selon montant + type) sera utilisée par Personne B en écriture (transactions) — je vous suggère de mettre cette méthode dans BaremeFraisModel (ex: calculerFrais($typeCode, $montant)) pour que ce soit centralisé et testable indépendamment.