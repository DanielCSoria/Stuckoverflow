# Projet PRWB 1920 - StackOverflow

## Notes de version itération 1 

### Liste des utilisateurs et mots de passes

  * Utilisateur `admin` (Administrator), mot de passe `Password1,`
  * Utilisateur `alain` (Alain Silovy), mot de passe `Password1,`
  * Utilisateur `ben` (Benoît Penelle), mot de passe `Password1,`
  * Utilisateur `bruno` (Bruno Lacroix), mot de passe `Password1,`
  * Utilisateur `boris` (Boris Verhaegen), mot de passe `Password1,`
  * Utilisateur `daniel` (daniel soria), mot de passe `Lol!654321,`
  * Utilisateur `matthieu` (matthieu stockbauer) mot de passe `Lol!123456,`

  

### Liste des bugs connus

  * Lorsqu'on donne un mauvais ID à post edit, ou show (/show/11522222 || /edit/-50) alors on redirige à l'index, plutôt que de throw Exception
  * pas vraiment un bugg mais un choix ;)
  * Rappelez vous des selecteurs js plutot que des selecteurs jquey dans le cas des classes qui sont refresh en js 
  * On avait parlé de la view stats et du fait que l'énoncé ne précisait pas la restriction pour les non membres, je ne l'ai pas implémenté du coup :p
  * Sans js tout fonctionne MAIS dans view show on a un dropdown bootstap(donc powered by jquery clairement) donc la fonctionnalité en elle meme fonctionne mais est innaccessible 
  sans js (a cause du display :-')
  * easyMDE empeche la validation des textarea de view ask/show , si on enleve le new easyMDE en début des pages concernées , on voit que sans easyMDE la validation marche


### Liste des fonctionnalités supplémentaires
  * Signup/login/logout en modal
  * delete comment en modal aussi
  * easyMDE importé pour les answer/questions(en local)
  * Quelques modal d'erreurs, par exemple edit/delete comment with empty body(remarquez aussi futile que ce soit qu'un message d'erreur est passé à la modale d'erreur (Cannot edit with empty body/Must enter a body for your comment)), vote si user pas co


### Divers

* Pour nous faciliter la vie / peut être que ça vous sera utile aussi, tous les services ajax sont en général(je vais essayer de vérifier qu'il n'en reste pas ailleurs) en bas des pages
(que ce soit en controller ou modèle)
* pas de retri de la page après vote (car pas de template pour rebuild les answers : on en a aussi parlé :p)


# Stuckoverflow
# Stuckoverflow
