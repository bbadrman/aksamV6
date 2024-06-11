# Aksam-Assurance

#### Aksam-Assurance App With Symfony 5.4, Docker, Nginx, MariaDB.

Online Inventory Management Software will help you to manage your product stock in manageable way.
This system is built on Symfony with proper management of users, groups, brand, stores, product, orders and reports.
You can create as many users as you want and assigned them with required modules. The system features are listed below section.

This system can be also used for small business. It is free web based inventory management software.
This system is based on the store inventory system. The products are controlled by the store.


### System Features
* Manage Users
    * Add new user detail
    * View, Update, and remove user information
* Manage Groups
    * Add new group information
    * View, Update, and remove group information
* Manage Brands
    * Add new brand data
    * View, Update, and remove brand information
* Manage Category
    * Add new category information
    * View, Update, and remove category information
* Manage Stores
    * Add new store information
    * View, Update, and remove stores information
* Manage Attributes
    * Add new attribute information
    * View, Update, and remove attributes information
* Manage Products
    * Add new product information
    * View, Update, and remove products information
* Manage Orders
    * Add new order information
    * View, Update, and remove orders information
* Reports
    * View total amount of sales represented on the graphical chart based on yearly.
* Company
    * Update the company information
    * That includes company name, address, phone, message, vat charge, service charge and more..
* Profile
    * View the logged in user information
* Setting
    * View, and Update logged in user information


# Installation guidelines:

### Installing Symfony 5.4 (app folder) :

`composer create-project symfony/website-skeleton app:5.4`


### Configuring the Database :

The database connection information is stored as an environment variable called DATABASE_URL.
For development, you can find and customize this inside app >> .env:

```
# to use mariadb:
DATABASE_URL="mysql://badr:123456@test_database:3306/test_db?serverVersion=mariadb-10.5.9"
```

### Main Tasks :

#### Creation of the Home Page :

`symfony make:controller`

- Created: src/Controller/HomePageController.php
- Created: templates/home_page/index.html.twig


#### Creation of the User entity & Migrate to the DB :

`symfony make:user`

- Created: src/Entity/User.php
- Created: src/Repository/UserRepository.php
- Updated: config/packages/security.yaml

`symfony make:entity`

- Updated: src/Entity/User.php
- N.B: this command is executed to add **firstname**, **lastname**, **username**, **embuchAt**, **remuneration** and **gender** fields to the User entity.

`symfony make:migration`

- Created: migrations/Version20221214183129.php

`symfony doctrine:migrations:migrate`

- N.B: the user table is created in the Database.

### Encode Password in 5.4

 NB: il faut 'chnage UserPasswordEncoderInterface' par 'UserPasswordHasherInterface'
     et method 'encodePassword' par 'hashPassword'

#### Creation of a registration system :

`symfony make:controller RegisterController`

- Created: src/Controller/RegisterController.php
- Created: templates/frontend/register/index.html.twig


`symfony make:form RegisterType`

- Created: src/Form/RegisterFormType.php


`composer require giggsey/libphonenumber-for-php`

- Created: app/composer.json
- Updated: app/composer.lock


`symfony make:validator Telephone`

- Created: src/Validator/Telephone.php
- Created: src/Validator/TelephoneValidator.php
NB:  
  /inscription/aksam  pour avoire l'inscription
#### Creation of a login system :

`symfony make:auth LoginFormAuthenticator`

- Created: src/Security/LoginFormAuthenticator.php
- Updated: config/packages/security.yaml
- Created: src/Controller/SecurityController.php
- Created: templates/frontend/security/login.html.twig


#### Creation of the Admin Dashboard :

`symfony make:controller`

- Created: src/Controller/DashboardController.php
- Created: templates/backend/dashboard/index.html.twig

#### All groups listing

  ##### Creation of the Group entity :
  
  `symfony make:entity Group`
  
  - Created: src/Entity/Group.php
  - Created: src/Repository/GroupRepository.php
  
  ##### Migrate to the DB & CRUD :
  
  `symfony make:migration`
  
  - Created: migrations/Version20210611190552.php
  
  `symfony doctrine:migrations:migrate`
  
  - N.B: the Group table is created in the Database.
  
  ##### Make Listing :
  
  `symfony make:crud`
  
  - Created: src/Controller/GroupController.php
  - Created: templates/backend/group/index.html.twig

#### Add New Group Information :

- Created: src/Form/GroupType.php
- Created: templates/backend/group/_form.html.twig
- Created: templates/backend/group/new.html.twig

#### Show a Group Information :

- Created: templates/backend/group/show.html.twig


#### Edit a Group Information :

- Created: templates/backend/group/edit.html.twig

#### Delete a Group :

- Created: templates/backend/group/show.html.twig


#### Creation of Pagination for groups listing :

`composer require knplabs/knp-paginator-bundle`

- Updated: composer.json
- Updated: composer.lock
- Updated: config/bundles.php
- Updated: symfony.lock

#### Creation of Search system for groups listing :

- Create SearchGroup class inside src/Search
- Create SearchGroupType class inside src/Form
- Adding findSearch function inside GroupRepository
- Update GroupController


#### All users listing

`symfony make:controller UserController`

- Created: src/Controller/UserController.php
- Created: templates/backend/user/index.html.twig

`symfony make:entity User`

- Updated: src/Entity/User.php
- Updated: src/Entity/Group.php
- NB: This command to add the ManyToMany relation between the User & Group Entity

`symfony make:migration`

- Created: migrations/Version20210612191600.php

`symfony doctrine:migrations:migrate`

- NB: This command add the user_group table in the DB


#### Add New User Information :

`symfony make:form UserType`

- Created: src/Form/UserType.php
  
- Creating templates/backend/user/_form.html.twig
- Creating templates/backend/user/new.html.twig
- Adding new() function inside the UserController

#### Show a User Information :

- Creating templates/backend/user/show.html.twig
- Adding show() function inside the UserController

#### Edit a User Information :

- Creating templates/backend/user/edit.html.twig
- Adding edit() function inside the UserController

#### Delete a User :

- Creating templates/backend/user/show.html.twig
- Adding delete() function inside the UserController

#### Creation of Pagination for users listing :

- Editing the index() function inside the UserController
- Editing the index.html.twig template

#### Creation of Search system for users listing :

- Create SearchUser class inside src/Search
- Create SearchUserType class inside src/Form
- Adding findSearch function inside UserRepository
- Adding a _search_form.html.twig template
- Update UserController


#### All stores listing

 ##### Creation of the Stores entity :
  
  `symfony make:entity Group`
  
  - Created: src/Entity/Stores.php
  - Created: src/Repository/StoresRepository.php
  
  ##### Migrate to the DB & CRUD :
  
  `symfony make:migration`
  
  - Created: migrations/Version20210613175016.php
  
  `symfony doctrine:migrations:migrate`
  
  - N.B: the Group table is created in the Database.
  
  ##### Make Listing :
  
  `symfony make:crud`
  
  - Created: src/Controller/StoresController.php
  - Created: templates/backend/Stores/index.html.twig

  #### Add New Store Information :
+
+`symfony make:form StoresType`
+
+- Created: src/Form/Storesype.php
+  
+- Creating templates/backend/Stores/_form.html.twig
+- Creating templates/backend/stores/new.html.twig
+- Adding new() function inside the StoresController


#### Show a store Information :

- Creating templates/backend/stores/show.html.twig
- Adding show() function inside the StoresController

#### Edit a store Information :

- Creating templates/backend/stores/edit.html.twig
- Adding edit() function inside the StoresController
- Adding in asside and stores/index.html.twig

#### Delete a store :

- Created: templates/backend/stores/show.html.twig
- Adding delete() function inside the StoresController


#### Creation of Pagination for store listing :

`composer require knplabs/knp-paginator-bundle`

- Updated: composer.json
- Updated: composer.lock
- Updated: config/bundles.php
- Updated: symfony.lock


#### Creation of Search system for store listing :


- Create SearchGroup class inside src/Search
- Create SearchGroupType class inside src/Form
- Adding findSearch function inside GroupRepository
- Update GroupController


#### All brand listing

 ##### Creation of the brand entity :
  
  `symfony make:entity Brand`
  
  - Created: src/Entity/Brand.php
  - Created: src/Repository/BrandRepository.php
  - Adding index() function inside the BrandController
  - Adding in asside and Brand/index.html.twig
  

  #### Show a brand Information :

- Creating templates/backend/brand/show.html.twig
- Adding show() function inside the brandController

	modified:   src/Controller/BrandController.php
	modified:   templates/backend/brand/index.html.twig
	create : src/Form/BrandType.php
	create : templates/backend/brand/_form.html.twig
	
#### Add New brand Information :

`symfony make:form StoresType`

- Created: src/Form/Storesype.php
- Creating templates/backend/Stores/_form.html.twig
- Creating templates/backend/stores/new.html.twig
- Adding new() function inside the StoresController

#### edite a brand Information :

    modified:   src/Controller/BrandController.php
	new file:   templates/backend/brand/edit.html.twig
	modified:   templates/backend/brand/index.html.twig
	modified:   templates/backend/brand/show.html.twig


#### Delete a brand :

    modified:   src/Controller/BrandController.php
	modified:   templates/backend/brand/index.html.twig
	Creating:   templates/backend/brand/_delete_form.html.twig

####  Creation of Pagination for brand listing :
    
    modified:   src/Controller/BrandController.php
	modified:   templates/backend/brand/_delete_form.html.twig
	modified:   templates/backend/brand/index.html.twig
	modified:   templates/backend/brand/show.html.twig


#### All Category listing

 ##### Creation of the Category entity :
  
  `symfony make:entity Category`

    modified:   templates/backend/aside.html.twig
	migrations/Version20210621174646.php
	src/Controller/CategoryController.php
	src/Entity/Category.php
	src/Repository/CategoryRepository.php
	created: templates/category/index.html.twig


  #### Show a Category Information :

- Creating templates/backend/Category/show.html.twig
- Adding show() function inside the CategoryController
	modified:   templates/backend/Category/index.html.twig
	create : src/Form/CategoryType.php
	create : templates/backend/Category/_form.html.twig
    
 ### UserChecker (pour desactive and active users)
  -Creating script in Security nomie UserChecker.php 
  -Adding this in security.yml 
     main:
            pattern: ^/
            user_checker: App\Security\UserChecker

 ### Select Diynamic:
    - il faut dabord met une relation entre les deux entity (team -> product)
    - metre EntityType in add team and generer une function look script UserTpe
    - metre un script js pour automatise select en temps real
 
 ### Select2 (muliple choices)
    - On ajouter le code cdn du select2  apartir  du site cdnjs.com
    - On ajouter cet script js :
       # select par id prisise
	
	 $('#user_fonctions').select2();
	 $('#user_roles').select2();

	# pour select multiple en seul class (en ajoute selectchoice sur attr de chaque champ qu' on a besoin)
	$('.selectchoice').select2();

### Envoie les données via jsonResponse:
   On Ajouter une autre function add ou new avec un route deferente 
   On crée un poop pour ajouter le données
   On ajouter un scripte js

### Redirecto apres identifie :
 sur LoginFomrAuth:
 in function onAuthentication
                        /** @var User $user */
                      $user = $token->getUser();

                   if(in_array('ROLE_ADMIN', $user->getRoles(), true) ){
                     return new RedirectResponse($this->urlGenerator->generate('user_index'));
                     
                                    if(in_array('ROLE_COMER', $user->getRoles(), true) ){
                                        return new RedirectResponse($this->urlGenerator->generate('app_team_index'));}
                                                              
                    return new RedirectResponse($this->urlGenerator->generate('dashboard'));
### Configurer expiration session:
  
in framwork.yaml:
    session:
        handler_id: session.handler.native_file
        save_path: '%kernel.project_dir%/var/sessions/%kernel.environment%'
        cookie_lifetime: 14400

nb: 14400 second  = 4 heurs

## in branch i includ entity prospect with champ provisoire

## relation entre entitie user et prospect
 
 on a affect deux propretie autor et consl
 'autor'  relation ManyToMany ce que fait que autor(user) peut cree plaisir prospect 
 'consl'  relation ManyToMany ce que fait que consielle (user) peut recoit plaisir prospect

## Récupérer les donnes d’un user
 1er method:
     twig:  paramétrer les que tu veux afficher au admin à l' aide du isGranted
     et les autres champs à l'aide du app.user (pour afficher seulement les information du user connecter)
2eme méthode:  
     il faut paramétrer une fonction sur repository , et demander les inf après fait appeler sur controller 

## Panier :
dans cet tache en get  les nombre du prospects de chaque utilisateur grace a "session->get" 
le probleme c que on a affecter cet methoud sur controller du dashbord afin d'afficher au depart du connection
 mais en tembe dans un probleme  du l'affichage du pannier avant d'enthentifie opps c pas utiles :
 alors en resouler cet probleme avec methoud "app.user"  : le panier il affiche que sur l'enthtification

 N.B: au deployement on azure j'ai reconntrai un error de sessionInterface qui Deprecated et pour cela  j'ai changer sessionInterface par RequestStack
voila l'error qui j'ai: 
 [2023-09-05T17:19:21.345437+00:00] deprecation.INFO: User Deprecated: Since symfony/framework-bundle 5.3: The "Symfony\Component\HttpFoundation\Session\SessionInterface" and "SessionInterface" aliases are deprecated, use "$requestStack->getSession()" instead. It is being referenced by the "App\Cart\Panier" service. {"exception":"[object] (ErrorException(code: 0): User Deprecated: Since symfony/framework-bundle 5.3: The \"Symfony\\Component\\HttpFoundation\\Session\\SessionInterface\" and \"SessionInterface\" aliases are deprecated, use \"$requestStack->getSession()\" instead. It is being referenced by the \"App\\Cart\\Panier\" service. at /home/site/wwwroot/vendor/symfony/dependency-injection/Compiler/ResolveReferencesToAliasesPass.php:68)"} []

## Search Interval date :
 
 I create two parametre in form  add (d) and add (add)
 I  modif in repository in the query  
 In the view i f create two div 

 ## Deployment :

 il metre cet script afin maitre ajour la base données sur 'compser.json' :
    "scripts": {
        "auto-scripts": {
            "cache:clear": "symfony-cmd",
            "assets:install %PUBLIC_DIR%": "symfony-cmd"
        },
        "database-setup": [
          "php bin/console doctrine:schema:update --force --no-interaction"
        ],
        "post-install-cmd": [
            "@auto-scripts",
            "@database-setup"
        ],
        "post-update-cmd": [
            "@auto-scripts"
        ]
    },

  ## Error en prod :
      -First Error: A 500 Internal Server Error is a generic error message that indicates something went wrong on the server. It doesn't provide much information about the specific problem, but there are a few things you can do to try to fix it.
      - Fix : i fixet the error message par migration instante ete modifi en entitie (champ null)

     -second Error : javascript dont working  on click on button 
     -Fix :  i fixeted par included comont.min.js

     les error et les fixed sur doc: "https://docs.google.com/document/d/1oI_IR68yiIR_NNyTjHtYpYbYUQPFqaIBzV8FvjudjnA/edit"
   ## Api :

   le but c'est de lie des sites pub avec php native avec notre application symfony, c'est pour cela on a choisir le web service, pricisament l'api .
   N.B: afin de securiser notre resources on blouquer les autre verbe http, on rester seulement POST pour poster seulement les prosepes apartir des sites 
      access_control:
        - { path: ^/api, roles: ROLE_ADMIN, methods: [GET, PUT, DELETE], }
        - { path: ^/api/prospects, roles: ROLE_ADMIN, methods: [GET, PUT, DELETE], }
        - { path: ^/api/users, roles: ROLE_ADMIN  }
        - { path: ^/api/products, roles: ROLE_ADMIN }
        - { path: ^/api/teams, roles: ROLE_ADMIN }
        - { path: ^/api/clients, roles: ROLE_ADMIN }
        - { path: ^/api/fonctions, roles: ROLE_ADMIN }
   ## Liaison via api de php native :
                </head>
                    <?php
                       // Votre code PHP pour traiter la requête cURL

                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        // Récupérer les données du formulaire

                        $nom = $_POST['name'];    
                        $prenom = $_POST['lastname'];
                        $telephone = $_POST['phone'];
                        $email = $_POST['email'];
                        $raisonSociale = $_POST['raisonSociale'];
                        $assure = $_POST['assure'];
                        $lastAssure = $_POST['lastAssure'];
                        
                        // Données à envoyer à l'API
                        $data = array(

                            'name' => $nom,
                            'lastname' => $prenom,
                            'phone' => $telephone,
                            'email' => $email,
                            'raisonSociale' => $raisonSociale,
                            'assure' => $assure,
                            'lastAssure' => $lastAssure,
                            
                            
                        );

                        // Convertir les données en JSON
                        $jsonData = json_encode($data);

                        // URL du point d'extrémité de l'API
                        $apiUrl = 'http://aksama-assurance.azurewebsites.net/api/prospects';

                        // Initialisation de cURL
                        $curl = curl_init();

                        // Définition des options de cURL
                        curl_setopt($curl, CURLOPT_URL, $apiUrl);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($jsonData)
                        ));

                        // Exécution de la requête cURL
                        $response = curl_exec($curl);

                        // Vérification des erreurs
                        if ($response === false) {
                            $error = curl_error($curl);
                            // Gérer l'erreur cURL
                            echo "Erreur cURL : " . $error;
                        } else {
                            // Rediriger vers la page de confirmation
                            header("Location: https://www.assurance-des-vtc.fr/confirmation-VTC.php");
                            exit; // Assurez-vous de terminer l'exécution du script ici
                        }

                        // Fermeture de la session cURL
                        curl_close($curl);
                    }
                    ?>
                 <body>
 ## modifie numero telephone au format europe
  # au form de l'application
 private function convertPhoneNumberToInternational($phoneNumber)
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Vérifier la longueur du numéro pour s'assurer qu'il est valide
        if (strlen($phoneNumber) === 10) {
            // Vérifier s'il commence par '0' (format local)
            if (substr($phoneNumber, 0, 1) === '0') {
                // Remplacer le '0' par '33' pour obtenir le format international
                return '+33' . substr($phoneNumber, 1);
            }
        }
    }
    
 # au form du site pub
  $telephone = $_POST['phone'];
// Formatage du numéro de téléphone en ajoutant le préfixe "+33"
    $telephone = '+33' . substr($telephone, 1); // Supposant que le numéro commence par "0"
   j'ai depose cet script au form du index.php du site

 ## Traitement Prospects :
   dans cette tache je veux ajouter une champ,je vais l'appelais RelanceAt pour afficher seleument les prosect qui sont relance a une date pricis 
  1- cree      RelanceAt dans entitie prospect
  2- ajouter un table contient seulement les prospect de cet relance
  3- crée une function en repository contient condition RelenceAt = dateNew
  4- envoie cet function par controller ver la table

# ajouter heur Europe/Paris

#[ORM\PrePersist]
public function prePersist()
{
    if (empty($this->creatAt)) {
        $timezone = new \DateTimeZone('Europe/Paris'); // Remplacez par le fuseau horaire approprié
        $this->creatAt = new \Datetime('now', $timezone);
    }
}

## pour que soit la date naissance peut envoie null ; il faut ajouter ?
 public function setBrithAt(?\DateTimeInterface $brithAt): self
{}
## humanize on twig pour render en majisqule:
https://symfony.com/doc/current/reference/twig_reference.html

## probleme de pagination:
 si je passe a l autre page et la page sera vide en fonctionalite de cache la table , 
 alors il faut cache cette fonction sur searchEntityType.php
  
      public function getBlockPrefix(): string
      {
         return '';
      }
## gerer la session  :
j ai crer une event  qui ecoute a chaque entrer au session 

AuthenticationSeccessSubscriber.php

et en service.yaml:
App\EventSubscriber\AuthenticationSuccessSubscriber:
        tags:
            - { name: kernel.event_subscriber }
## bug en cas de supprimer un fichier et rest au mémoire de la application  cash  il mettre a jour autoload a l aide du cdm 👍
composer dump-autoload

## show online and offline user and force logout by Admin
1-create service  UserDisconnecter.php script  to push    function disconnectUser
 pour que soit quand click sur button deconnecte en temps real 
2- create eventSubscriber  LogoutSubscriber.php $user->setIsConnect(false)   function onLogout
pour envoye isconnect = 0 u base donnee afin de savoire status du user onligne or offligne
2-create  UserLogoutSubscriber.php  for Déconnectez l'utilisateur si son statut n'est pas enligne  
   if (!$user->isIsConnect()) {
             

            // Rediriger l'utilisateur vers la page de connexion
            $response = new RedirectResponse('/deconnexion');
            $event->setResponse($response);
        }
3- in service.yaml :
           - { name: 'kernel.event_subscriber' }
    App\Security\UserDisconnecter:
        arguments: ['@doctrine.orm.entity_manager', '@security.helper']
4- view show page on controller   public function userList name="user_acces"  

## Branchement site laravel avec l'application
il suffit de met cet script sur le controller qui gere la form dans notre cas:
public function store(Request $request)

App/Http/Controller/homeController.php
    {
        // Enregistrer les données localement
        $fiche = new Fiche;
        $fiche->nom = $request->input('nom');
        $fiche->prenom = $request->input('prenom');
        $fiche->activite1 = $request->input('demarrage');
        $fiche->raison_sociale = $request->input('raison_sociale');
        $fiche->assure = $request->input('assure');
        $fiche->ancienne = $request->input('ancienne');
        $fiche->motif = $request->input('motif');
        $fiche->telephone = $request->input('tele');
        $fiche->email = $request->input('email');
        $fiche->date_fiche = now();
        $fiche->id_produit = 10;
        $fiche->id_traitement = 1;
        $fiche->dublique = 0;
        $fiche->id_source = 1;
        $fiche->id_motifcloture = 1;
        $fiche->save();
        $request->session()->flash('status', 'formulaire');

        // Envoyer les données à l'API
        $data = [
            'name' => $request->input('nom'),
            'lastname' => $request->input('prenom'),
            'phone' => $request->input('tele'),
            'email' => $request->input('email'),
            'raisonSociale' => $request->input('raison_sociale'),
            'assure' => $request->input('assure'),
            'lastAssure' => $request->input('ancienne'),
            'typeProspect' => "2",
            'activites' => "4",
            'url' => "6",
            'product_id' => 5
        ];

        // Convertir les données en JSON
        $jsonData = json_encode($data);

        // Initialisation de cURL
        $curl = curl_init();

        // Options de cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://aksama-assurance.azurewebsites.net/api/prospects',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
        ]);

        // Exécution de la requête cURL
        $response = curl_exec($curl);

        // Fermer la session cURL
        curl_close($curl);

        // Redirection après traitement
        return redirect('/assurance/professionel');
    }
## Notification:

- il faut ajouter the worker.js et fichie main.js 
- il faut crée une fonction pour avoir les prospect au format json 
- include le fichier worker au page que tu veux affichier notification