# Projet 7 Formation Développeur d'application - PHP/Symfony Openclassrooms

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5bd9920ad6f549c88ea16a5781d17f0f)](https://app.codacy.com/gh/aurore-dw/projet-7/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Contexte :

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.

## Besoin client :

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

 Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

- Consulter la liste des produits BileMo ;
- Consulter les détails d’un produit BileMo ;
- Consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
- Consulter le détail d’un utilisateur inscrit lié à un client ;
- Cjouter un nouvel utilisateur lié à un client ;
- Supprimer un utilisateur ajouté par un client.
Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via OAuth ou JWT.

Vous avez le choix entre mettre en place un serveur OAuth et y faire appel (en utilisant le FOSOAuthServerBundle), et utiliser Facebook, Google ou LinkedIn. Si vous décidez d’utiliser JWT, il vous faudra vérifier la validité du token ; l’usage d’une librairie est autorisé.

## Guide d'installation du projet :

1. Clonez ou télécharger le repository GitHub

- `git clone https://github.com/aurore-dw/projet-6.git`

2. Configurez vos variables d'environnement tel que la connexion à la base de données dans le fichier .env

3. Dans le projet, téléchargez et installez composer
   
- `composer install`

4. Mettre en place la base de donnée
   
- `php bin/console doctrine:database:create`
- `php bin/console doctrine:migrations:migrate`
- `php bin/console doctrine:schema:update --force`

5. Implémenter les fixtures
   
- `php bin/console doctrine:fixtures:load`

6. Démarrer le serveur web local de Symfony
   
- `symfony server:start`

7. Accéder à l'application, généralement `http://localhost:8000`.
