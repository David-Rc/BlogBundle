Blog
====

A Symfony project created on August 2, 2016, 12:25 pm.
====

# Presentation :

Blog est un bundle créer en Symfony 3, il permet principalement de consulter un article ainsi que de le commenter.

L'application gere trois roles d'utilisateur :

* Le visiteur peut : 
	* Se connecter
	* Créer un compte
	* Voir la liste des articles Publiés
	* Lire le contenu de l'article et les commentaires
* L'utilisateur regulier peut :
	* Faire les mêmes choses que le visiteur
	* Se deconnecter
	* Ajouter un commentaire
	* Supprimer un commentaire
	* Voir la liste des commentaires qu'il a posté
	* Modifier les informations de son compte
	* Supprimer son compte
* L'administrateur peut :
	* Faire les même choses que l'utilisateur régulier
	* Acceder au back-office
	* Voir la liste des articles publiés et non publiés
	* Ajouter un article
	* Modifier un article
	* Supprimer un article
	* Publier ou dépublier un article
	* /!\ Il est impossible de créer un compte Administrateur

# Technologies :

Le blog est responsive et gere l'affichage sur mobile. 

Les technologies utilisées :

* Symfony 3 : Pour l'apprentissage du framework
* Doctrine : Pour la gestion de la base de données et la compatibilité avec Symfony.
* Bootstrap : Pour la chartre graphique ainsi que le reponsive
* Javascript ( es5 ) : Pagination
* HTML 5 / CSS 3
