{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='My account'}{/capture}

<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		{if isset($meta_description) AND $meta_description}
			<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
		{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		<!-- Start WOWSlider.com HEAD section -->
		<!--
        <link rel="stylesheet" type="text/css" href="diaporama/engine1/style.css" />
        <script type="text/javascript" src="diaporama/engine1/jquery.js"></script>
		<script type="text/javascript" src="diaporama/engine1/wowslider.js"></script>
		<script type="text/javascript" src="diaporama/engine1/script.js"></script>
		-->
        <!-- End WOWSlider.com HEAD section -->
		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
				<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
			{/foreach}
		{/if}
		{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
			{$js_def}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
			{/foreach}
		{/if}
		{$HOOK_HEADER}
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
	</head>

<body id="my-account">
<h1 >
<!--{if !isset($email_create)}{l s='Authentication'}{else}{l s='Create an account'}{/if}-->
</h1>
   <table width=100% >
      <tr >
	  <TH></TH
	  <TH width="80%"></TH>
	  <TH></TH>
      <tr >
	  <td rowspan =2>
								<div id="logo">
									<a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="vinicom_accueil2016.jpg" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>
	</td>
	  <TD style="color:white;" width="80%">
	  <B>
	  <P>{l s='Bienvenue sur votre compte , merci de vous identifier'}</P>
	  <P>{l s='Vous trouverez dans votre compte : '}</P>
	  <P>{l s='- votre bon de commande pré-établi regroupe tous les produits que vous commandez habituellement. Ajoutez les articles au panier pour passer votre commande en toute simplicité.'}</P>
	  <P>{l s="- le catalogue  qui vous permet d'accéder à l'ensemble des produits vendus sur le site, des caisses bois aux fontaines à vin."}</P>
	  <P>{l s="- l'historique et détails des commandes vous donne accès aux précédentes commandes passées depuis le site."}</P>
	  <P>{l s='Nous vous accompagnons pour toutes vos questions au 02 99 68 89 12.'}</P>
	  <P>{l s='Bonne navigation !'}</P>
	  </B>
	  </TD>
	  <td rowspan =2>
								<div id="header_logo">
									<a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
										<img class="logo img-responsive" src="vinicom_accueil2016.jpg" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
									</a>
								</div>

      </td>
      </tr>
	  <TR><TD>
			<div class="box">
<div class="row addresses-lists">
	<div class="col-xs-12 col-sm-6 col-lg-4" style="">
		<!-- Bon de commande préétabli -->
		<ul class="myaccount-link-list">
		{foreach from=$shoppingList item=itemList}
			<li class="lnk_wishlist">
				<a href="{$link->getModuleLink('shoppinglist', 'accountshoppinglistproduct', ['id_shopping_list' => $itemList.id_shopping_list])}">
				<i class="icon-heart"></i>
				<span>{l s='Ma commande pré-établie'}</span>
				</a>
			</li>
		{/foreach}

		<!-- Catalogue Vinicom -->
		<li class="lnk_wishlist">
			<a 	href={$link->getCategoryLink(12, true)} title="Le catalogue ">
				<i class="icon-search"></i>
				<span>LE CATALOGUE </span>
			</a>
		</li>
		<!-- Historique des commandes -->
		<LI></LI><!-- je ne sais pas pourquoi-->
        <li class="lnk_wishlist">
			<a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders'}">
			<i class="icon-list-ol"></i>
			<span>{l s='Order history and details'}</span>
			</a>
		</li>
        <li class="lnk_wishlist">
			<a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" title="{l s='Log me out' mod='blockuserinfo'}">
			<i class="icon-minus"></i>
			<span>{l s='Déconnexion' mod='blockuserinfo'}</span>
			</a>
		</li>
        </ul>
		</div>
</DIV></DIV>
</TD></TR>
</TABLE>