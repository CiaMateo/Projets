# WeSports, notre projet de S3 !
## Le MVC Kesako ?
Afin de palier à certains problème, nos page sont basé sur le modèle MVC : Model Vue Controlleur.  
Pour faire simple voici les tâches des 3 parties :
- **Model** : Cela est juste la représentation des données utilisé, notamment sur la base de donnée. Ainsi on peut récupérer les éléments et les utiliser simplement. C'est aussi lui qui s'occupe de modifier les éléments en question dans la base de donnée.
- **Controlleur** : C'est ce qui s'occupe du fonctionnement en fond. Il traite les données afin de générer une **Vue** pour l'utilisateur.
- **Vue** : C'est ce qui permet de générer un affiche pour l'utilisateur.

## Le routeur
Un routeur est utilisé pour ce modèle.  

### Késaco ?
Et bien, le routeur permet de transformer cette adresse :  

`http://monsite.xyz/user/in/Resnox`

en une adresse lisible en la séparant de cette manière :
1. **user** : C'est le controlleur invoqué
2. **in** : C'est la méthode invoqué
3. **Resnox** : C'est un des arguments, si d'autre arguments sont suivi (séparé par des '/') ceux-ci sont envoyé également.

Je ne parlerais pas ici du moyen technique utilisé.

## Utilisation du framework
L'utilisation du framework maison est relativement simple même si elle peut poser problème. (je tacherais de mettre à jour le framework si besoin)  
Les parties du MVC, *càd.* les **Model**, **Vue** et **Controlleur** sont dans leur dossiers nommé respectivement '*model*', '*view*' et '*controller*'.  
Les classes de ***Model*** et de ***Controller*** doivent hérité de la classe mère du même nom.
Notez également que le nom du **Controlleur** influe sur l'url (grâce au routeur, *cf* #Le routeur) et les vues possèdent plusieurs script sur l'interieur de la page change selon les arguements dans l'url.

***

Pour appeler une vue depuis un controlleur il suffit d'invoquer la méthode suivante :  
```php
void render(string $template, array $arguments, string $layout = 'default'));
```

Ici plusieurs choses sont à remarquer :
1. **$template** correspond au template pour créer la vue, ceux-ci sont présent dans le dossier 'view/**NomDuController**/**$template**.php'
2. **$arguments** correspond aux arguements que vous souhaitez donner. C'est un tableau associatif, les éléments dedans sont ensuite éclaté.
3. **$layout** le layout utilisé, ceux-ci sont présent dans le dossier 'layout/**$layout**.php'.  
L'éclatement du tableau ce fait comme-ci, exemple :
```php
['pageTitle' => 'Accueil', 'nom' => 'MonSite']
```
va s'éclater en 
```php
$pageTitle = 'Accueil';
$nom = 'MonSite';
```
***Attention*** : `$content` est une variable réservé. Si vous l'utilisez son contenu se fait remplacer lors la création de la page !

***

Pour construire une vue, il suffit simplement d'y écrire contenu HTML et d'ajouter des balises ouvrantes/fermantes pour spécifier le code PHP. Exemple :
```php
<h1>Bienvenue à toi, <?php $name ?></h1>
```
Ici l'arguement nommé `$name` va donc être affiché après 'Bienvenue à toi, '.