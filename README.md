# vinicom.wine
site internet Vinicom.wine
#V20200303150000
0001179: Modification des cols du backoffice Client

#V20180820
#865: Annomalie en Saisie de Commande
#866: Traitement des produits désactivés en précommande

#V20180702
#850: Le bouton "Accès au catalogue" d'un nouveau client le fonctionne pas

#V20180628
#839: Ajout des informations de contacts dans la fiche client. 
#837: Erreur en création de clients sur les sites internet Hobivin et Vinicom 

#V20180305
#832 : Validation de précommande : certains produit sont manquants => MAJ de la table ShoppingListProduct 
#831 : Erreurs en import de tarifs : (Pblm cohérence ce fichier)

#V20171106
#794 : Impossible de référencer/Dereferencer un produit après une recherche 
#795 : Les qte sont doublées si on visualise 2 fois la commande

#V20170912
#764 : 	Ajout des boutons "Reférencer", "Déréférencer" sur la iste des produits
#785 : Lien vers le catalogue depuis la précommande 
#786 : Suivi de commande : Affichage du détail  

#V20170825
#783 : Les pictogrammes n'appraissent plus sur le site
#782 : L'accès à l'espace 'Visiteur' demande une authentification
#784 : suppression de la partie suivi de commande dans l'historique des commandes

#V20170718
#769 : Modif du mail new_oder : mettre la société dans livraison company
#0000767: Déconnexion intempestive du back-office
#0000755: Toutes les lignes ne sont pas prise en compte lors de la validation d'une précommande  
#0000781: Affichage du panier sur la page d'accueil du catalogue (Paramétrage du module)
#0000765: bouton "Continuer mes achats" 
#0000766: bouton "Préparer la commande" 

#V20170707
#760 : Modification des quantités en préparation de commandes 

#V20170706
#768 : Produit en double ou triple dans le catalogue
#776 : 	Logo Hobivin (HBV)
#777 : mettre à jour le module mailalert avec l'ancienne version 


#V20170627,V20170627-2
#756 BO : La Description produit ne se sauvegarde pas
#757 : Bock superUser dans Gestion des clients
#758 : 	retour à mon compte si pas de produit référencé
#759 : 	Suppression du mot vinincom sur la page d'accueil

#V20170616
#753 : Le panier ne se recalcule Pas

#V20170612
#752 : Affichage du nombre de lignes 

#V20170612
#750 : Le Code postal ne s'affiche pas dans le template adress.tpl
#751 : Gestion des clients


#V20170609
#749 : le bouton "Commander le produit" ne s'affiche plus

#V20170522
#747 : Visualisation des adresses d'un client si pas d'adresse 

#V20170515
#746 : BO Gestion des produits extrement lent


Version 20170505
================
#745 : Connection en tant que ... demande l'authentification

Version 20170317
================
#743 : 	Suppression des références inactives

Version 20170314
================
#740 : Modififcation des Qte dans le panier
#739 : Suppression du dernier champ sur la visualisation des produits sur le site
#741 : 	Ordre de Visualisation sur les produits + Simplication de l'import

Version 20170202
================
#734 : Certains prixs sont à 0 sur la liste des produits

Version 20170124
=====================
Revision: 96
Author: marccollin
Date: vendredi 20 janvier 2017 17:34:37
Message:
#722: Module D'import-export
----
Modified : /www/vinicom/override/controllers/admin/AdminProductsController.php

Revision: 95
Author: marccollin
Date: vendredi 20 janvier 2017 15:59:24
Message:
#724 : Mémorisation des Qtes en commandes
----
Modified : /www/vinicom/modules/cheque/controllers/front/validation.php
Modified : /www/vinicom/modules/shoppinglist/classes/ShoppingListObject.php
Modified : /www/vinicom/modules/shoppinglist/views/js/shoppinglist.js
Modified : /www/vinicom/modules/shoppinglist/views/templates/front/accountshoppinglistproductindex.tpl

Revision: 94
Author: marccollin
Date: vendredi 20 janvier 2017 13:47:48
Message:
#727 : Pied de page
----
Modified : /www/vinicom/themes/default-bootstrap/modules/blockmyaccountfooter/blockmyaccountfooter.tpl

Revision: 93
Author: marccollin
Date: vendredi 20 janvier 2017 13:35:58
Message:
#721 : Référencement / Déreferencement d'un produit
----
Modified : /www/vinicom/config/xml/default_country_modules_list.xml
Modified : /www/vinicom/config/xml/modules_native_addons.xml
Modified : /www/vinicom/config/xml/must_have_modules_list.xml
Modified : /www/vinicom/config/xml/trusted_modules_list.xml
Modified : /www/vinicom/controllers/front/ProductController.php
Modified : /www/vinicom/controllers/front/SearchController.php
Modified : /www/vinicom/modules/shoppinglist/classes/ShoppingListObject.php
Modified : /www/vinicom/modules/shoppinglist/controllers/front/ajaxproductshoppinglist.php
Modified : /www/vinicom/modules/shoppinglist/views/css/shoppinglist.css
Modified : /www/vinicom/modules/shoppinglist/views/js/shoppinglist.js
Modified : /www/vinicom/themes/default-bootstrap/my-account.tpl
Modified : /www/vinicom/themes/default-bootstrap/product.tpl
Added : /www/vinicom/modules/shoppinglist/controllers/front/ajaxRemoveproductshoppinglist.php

Revision: 92
Author: marccollin
Date: jeudi 19 janvier 2017 17:49:18
Message:
#725 : Retour à Mon Compte après Validation de commande
----
Modified : /www/vinicom/controllers/front/OrderConfirmationController.php
Modified : /www/vinicom/modules/cheque/views/templates/hook/payment_return.tpl
Modified : /www/vinicom/modules/shoppinglist/controllers/front/accountshoppinglistproduct.php
Modified : /www/vinicom/themes/default-bootstrap/modules/cheque/views/templates/hook/payment_return.tpl
Modified : /www/vinicom/themes/default-bootstrap/order-confirmation.tpl

Revision: 91
Author: marccollin
Date: jeudi 19 janvier 2017 15:25:45
Message:
Harmonisation des Page De login et MyAccount
----
Modified : /www/vinicom/themes/default-bootstrap/authentication.tpl
Modified : /www/vinicom/themes/default-bootstrap/my-account.tpl

Revision: 90
Author: marccollin
Date: jeudi 19 janvier 2017 15:18:39
Message:
#726 :  	Après Connexion Affiche de 'Mon Compte' au lieu de revenir à la page d'accueil 
----
Modified : /www/vinicom/controllers/front/AuthController.php
Modified : /www/vinicom/themes/default-bootstrap/my-account.tpl

Revision: 89
Author: marccollin
Date: jeudi 19 janvier 2017 14:54:58
Message:
#717 : Procédure de connexion
#718 : Processus de commande
#719 : Historique des commandes
#720 : Fiche Produit
----
Modified : /www/vinicom
Modified : /www/vinicom/controllers/front/AuthController.php
Modified : /www/vinicom/controllers/front/CategoryController.php
Modified : /www/vinicom/controllers/front/HistoryController.php
Modified : /www/vinicom/controllers/front/MyAccountController.php
Modified : /www/vinicom/controllers/front/OrderOpcController.php
Modified : /www/vinicom/controllers/front/ProductController.php
Deleted : /www/vinicom/log
Modified : /www/vinicom/modules/blocktopmenu/blocktopmenu.tpl
Modified : /www/vinicom/modules/shoppinglist/controllers/front/accountfulllistproduct.php
Modified : /www/vinicom/modules/shoppinglist/controllers/front/accountshoppinglistproduct.php
Modified : /www/vinicom/modules/shoppinglist/views/css/shoppinglist.css
Modified : /www/vinicom/modules/shoppinglist/views/templates/front/accountshoppinglistindex.tpl
Modified : /www/vinicom/modules/shoppinglist/views/templates/front/accountshoppinglistproductindex.tpl
Modified : /www/vinicom/themes/default-bootstrap/authentication.tpl
Modified : /www/vinicom/themes/default-bootstrap/cms.tpl
Modified : /www/vinicom/themes/default-bootstrap/css/authentication.css
Modified : /www/vinicom/themes/default-bootstrap/css/my-account.css
Modified : /www/vinicom/themes/default-bootstrap/header.tpl
Modified : /www/vinicom/themes/default-bootstrap/history.tpl
Modified : /www/vinicom/themes/default-bootstrap/modules/blocktopmenu/blocktopmenu.tpl
Modified : /www/vinicom/themes/default-bootstrap/modules/cheque/views/templates/hook/payment.tpl
Modified : /www/vinicom/themes/default-bootstrap/my-account.tpl
Modified : /www/vinicom/themes/default-bootstrap/order-payment-classic.tpl
Modified : /www/vinicom/themes/default-bootstrap/product.tpl
Added : /www/vinicom/modules/shoppinglist/views/css/shoppinglist2.css
Deleted : /www/vinicom/override/controllers/front/AuthController.php

Revision: 88
Author: marccollin
Date: mardi 17 janvier 2017 08:48:34
Message:
Recopie du fichier d'origine
----
Added : /www/vinicom/override/controllers/front/AuthController.php

