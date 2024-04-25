# Pour avoir les données du back en local

1. **Cloner** le repo
2. Installer **composer** et ses dépendances ` composer install `
3. Configurer le **.env** ( copier le fichier en .env.local et remplacer dans DATABASE_URL par ses identifiants BDD)
4. Créer la **BDD**, appliquer les migrations et exécuter les fixtures s'il y en a

```shell

- bin/console doctrine:database:create
- bin/console doctrine:migrations:migrate
- bin/console doctrine:fixtures:load

```

## Routes = endpoints de l'API'

On a les mêmes routes pour chaque entité : Textiles, Embroideries, Customers,Users, Products,Contracts

### Exemple avec Contracts

| URL | HTTP Method | Controller  | Method | Comments |
|--|--|--|--|--|
| `/api/contracts/` | `GET` | `ContractController` | `index` | liste des contrats|
| `/api/contracts/{id}` | `GET` | `ContractController` |`show`| voir un contrat depuis son id id=integer |
| `/api/contracts/delete/{id}` | `GET` | `ContractController` |`delete`| effacer un contrat depuis son id id=integer |
| `/api/contracts/create` | `POST` | `ContractController` |`create`| créer un nouveau contrat|
| `/api/contracts/edit/{id}` | `GET` | `ContractController` |`edit`| voir les infos du contrat à modifier id=integer |
| `/api/contracts/update/{id}` | `PUT` | `ContractController` |`update`| envoyer les infos du contrat à modifier id=integer |
| `/api/contracts/type/{type}` | `GET` | `ContractController` || afficher que les contrats de type quotation/invoice ou order type=quotation/invoice/ order|
| `/api/contracts/customer/{name}` | `GET` | `ContractController` || afficher que les contrats du client {name} où {name}=string|
| `api/contracts/{id}/viewpdf` | `GET` | `ContractController` || afficher la prévisualisation du pdf du contrat n°={id} où {id}=integer|
| `api/contracts/{id}/renderpdf}` | `GET` | `ContractController` || télécharger le pdf |
| `api/contracts/{id}/sendpdf?mail={userMail}` | `GET` | `ContractController` || envoyer le pdf par mail |

### Routes spécifiques à Customer

| URL | HTTP Method | Controller  | Method | Comments |
|--|--|--|--|--|
| `/api/customers/customer/{name}` | `GET` | `CustomerController` || afficher les clients dont le nom contient {name}=string|
| `/api/customers/customer/{email}` | `GET` | `CustomerController` || afficher les clients dont l'email contient la chaîne de caractères {email}=string|
|`/api/customers/customer/{phone_number}` | `GET` | `CustomerController` || afficher les clients dont le numéro de téléphone contient les chiffres{phone_number}=integer|

### Exemple de JSON

- Contract :  

``` json

{
    "id": 1,
    "type": "quotation",
    "ordered_at": "2024-03-20T00:00:00+00:00",
    "invoiced_at": "2024-03-22T00:00:00+00:00",
    "delivery_address": "65 chemin de ruine",
    "status": "deleted",
    "comment": "Je veux un chiot dessiné sur la casquette",
    "user": 1,
    "customer": 1,
    "products": [
        1,2,3
    ]
}

```
