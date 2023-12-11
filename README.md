# Projet PRWB 2223 - Gestion de comptes entre amis

## Notes de version itération 1 

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur
  * thielens.marie@gmail.com, password "Azerty1234,", utilisateur
  * benben@test.com, password "Pierre12309630,",utilisateur 
  * pierre@gmail.com, password "Password1," , utilisateur

### Liste des bugs connus

  * update du profil : afficher un message qui confirme que l'update à été effectué
  * view_tricount : le bouton du footer ne va nulle part. La balise footer se superpose sur le containeur. Positionnement incorrect
  * view_edit_tricount : le bouton add rajoute dans la db mais la redirection n'est pas faites
  * la vue edit_operation n'affiche pas la répartition correctement et la partie du controlleur qui le gère n'est pas fait
  * add_operation : l'input est codé en dur et ne récupère pas depuis la db, from whom pareil
  * add_operation : la méthode existe mais ne fonctionne pas et le bouton add ne redirige pas correctement

### Liste des fonctionnalités supplémentaires
  * Lorsqu'on rajoute un participant il est possible d'en selectionner direcetement plusieurs dans le menu déroulant


### Divers

## Notes de version itération 2

### Liste des bugs connus
  * Dans edit_tricount : Quand le formulaire est mal encodé il n'affiche pas les erreurs
  * view_balance : Css pas totalement conforme à la maquette
  * delete_participant (iteration1) : Le boutton delete enlève le dernier de la liste
  * edit_operation : N'affiche pas les erreurs dans le formulaire
  * gestion dynamique des participants(ajout): Plante quand on appuye sur le bouton sans avoir séléctionner un participant
  * L'ajout de plusieurs participant simultanément n'est plus possbile dans add_participant avec javascrit


### Liste des fonctionnalités supplémentaires
  * Ajout d'un agenda avec jquery-ui
  * Ajout multiple des particpants en Javascript

### Divers
* Un createur ne peut pas se retirer des particpants du tricount qu'il a créer

## Notes de version itération 3 

### Liste des bugs connus
   * view_add_operation : 
     * pour le weight avec validator si le champ est vide il met que c'est correct.
     * Si on met un montant négatif il le met aussi dans le for whom. Problème uniquement visuel.
     * Erreur dans la console par rapport à just_validate. Nous avons rajouté une rule "callback". Elle fonctionne mais génère une erreur.

### Divers
   * Notre base de donnée se trouve dans le dossier database avec le nom `personalDataBase?sql`
   * Mise en place d'un calendrier avec la librairie datePicker : [lien vers la librairie](https://jqueryui.com/datepicker/) 