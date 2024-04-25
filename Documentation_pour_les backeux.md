
## Conventions de nommage pour les méthodes des controller cf doc de Fayis : 

| Nom de la méthode  | Action | Méthode HTTP |
| ----------- | ----------- |----------- |
| index |Show all listings| GET |
| show |Show one listing | GET |
| create | Show form to create new listing | GET |
| add | Store one listing | POST |
| edit | Show form to create new listing | GET |
| update | Update listing | POST |
| delete | Delete listing | POST |

Choix de l'équipe
| index |Show all listings| GET |
| show |Show one listing | GET |
| create | Show form to create new listing | GET |
| edit | Show datas to  update listing | GET |
| update | Update datas from existing item | PUT |
| delete | Delete listing | POST |

Nom des routes commence toutes par app_api_nom_de_l_entite

A soumettre aux backeux :
Perso je suis pas à l'aise avec ces mots

je crois qu'on a des méthodes de controller pour lesquelles on va avoir POST et GET en même temps, du coup on utilise le même nom de méthode de controller mais avec la méthode HTTP différente
les termes add/delete me parlent mieux que create/destroy
mais bon on choisira en créant le premier controller, il sera encore temps de modifier au début.

### Améliorer les validations de contraintes sur les entités notamment :

- pourquoi on n'a pas mis de contraintes sur les TEXT 
- faire des contraintes sur les nombres décimaux
- quel format de date utiliser pour que ça fonctionne lors d'un create (migration) ??

### Créer un mainController qui renvoie un JSON de bienvenue et une route d'exemple

### Pagination par 20 items