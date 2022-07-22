<p align="center">
  <img src="https://i.ibb.co/XJxbyHB/Union.png" width="200" height="200" style="text-align: center;">
  </p>


# Sported

Looking to build your own sport-themed website? Sported is made for you! This innovative CMS (Content Managing System) will guide you step by step to shape and condition your next idea.


## Implémentation design patterns - PHP



### Singleton 

Nous avons utilisé le design patern singleton pour la connexion à la base de données.
Logger efficace pour garder une trace de tout nouvel utilisateur , nouveaux commentaires dans le CMS , envoi de mail , connexion.
- Chemins Singleton :
   -- www/Core/Db.class.php
   -- www/Core/Logger.class.php
- On utilise le Singleton dans le fichier:
    -- www/Core/BaseSQL.class.php
    -- www/controller/Comment.class.php
  
### Query Builder

Nous avons utilisé le Query Builder pour faciliter la création de requête sql.  

- Chemin Builder :
  -- www/Core/SqlBuilder.class.php
  -- www/Core/Interfaces/QueryBuilder.class.php
- On utilise le Builder dans le fichier :
  --  www/Core/BaseSQL.class.php

### Observeur 

Nous avons utilisé l’observateur pour l’envoi des mails à chaque update d'un utilisateur . 
- Chemins Observeur :
    - www/Core/Observer/Observer.class.php
    - www/Core/Observer/Subject.class.php
- Chemins fichiers utilisation Observeur :
    - www/controller/User.class.php
    
    
### Decorator

L'objectif du decorator est de pouvoir modifier le comportement de la classe suivant certains cas ou de rajouter des foncionnalité. Dans notre projet on a utilisé de decorator pour modifier la général fonction Greeting lorsque l'installeur est installer.

- Chemins Decorator :
    - www/Core/Observer/GreetingInstall.class.php
    - www/Core/Observer/Errors.class.php
- Chemins fichiers utilisation Decorator :
    - www/Controller/General.classs.php --> Fonction Install()
    
    
## Authors

- [@charlesl76](https://www.github.com/charlesl76)
- [@twiney94](https://www.github.com/twiney94)
- [@linalexis](https://www.github.com/linalexis)
- [@douniaharag](https://www.github.com/douniaharag)


## Support

For support, email admin@sported.site or join our Discord.









