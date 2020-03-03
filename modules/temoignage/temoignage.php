<?php

if(!defined( '_PS_VERSION_'))
 exit;

class Temoignage extends Module{

	public function __construct(){
		$this->name = 'temoignage';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Moi';
		$this->need_instance = 0;
		parent::__construct();
		$this->displayName = $this->l('Témoignages');
		$this->description = $this->l('Affiche un bloc avec des témoignages clients');
		$this->confirmUninstall = $this->l('Êtes-vous certain de vouloir supprimer les informations de ce module ?');
	}
	
	public function install(){
		if(	!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('leftColumn')
		)
			return false;
		
		if( !Configuration::updateValue('TEMOIGNAGE_1', '')
			|| !Configuration::updateValue('TEMOIGNAGE_2', '')
			|| !Configuration::updateValue('TEMOIGNAGE_3', '')
		)
			return false; 
		return true;
	}

	public function uninstall(){
		if (!parent::uninstall()
			|| !Configuration::deleteByName('TEMOIGNAGE_1')
			|| !Configuration::deleteByName('TEMOIGNAGE_2')
			|| !Configuration::deleteByName('TEMOIGNAGE_3')
		)
			return false;					
		return true;
	}
	
	public function getContent(){
		$this->_preProcess();
		$this->_html.='
			<h2>'.$this->displayName.'</h2>
			<form id="temoignage_settings" class="width3" method="post" action="'.$_SERVER['REQUEST_URI'].'">
				<fieldset>					
					<legend><img src="../img/admin/cog.gif" />'.$this->l('Paramètres').'</legend>
					<div class="clear"></div>
					<label>'.$this->l('Témoignage 1MCII').'</label>
					<div class="margin-form">
						<textarea name="temoignage_1">';
							if(Configuration::get('TEMOIGNAGE_1') != '')
								$this->_html .= Configuration::get('TEMOIGNAGE_1');
							else $this->_html .= '';
						$this->_html .= '</textarea>
					</div>
					<div class="clear"></div>
					<label>'.$this->l('Témoignage 2').'</label>
					<div class="margin-form">
						<textarea name="temoignage_2">';
							if(Configuration::get('TEMOIGNAGE_2') != '')
								$this->_html .= Configuration::get('TEMOIGNAGE_2');
							else $this->_html .= '';
						$this->_html .= '</textarea>
					</div>
					<div class="clear"></div>
					<label>'.$this->l('Témoignage 3').'</label>
					<div class="margin-form">
						<textarea name="temoignage_3">';
							if(Configuration::get('TEMOIGNAGE_3') != '')
								$this->_html .= Configuration::get('TEMOIGNAGE_3');
							else $this->_html .= '';
						$this->_html .= '</textarea>
					</div>
					<div class="clear"></div>
					<center><input type="submit" name="save" value="'.$this->l('Enregistrer').'" class="button" /></center>
				</fieldset>
			</form>
		';
		return $this->_html;
	}
	
	private function _preProcess(){		
		if(Tools::isSubmit('save')){				
			if(isset($_POST['temoignage_1']))
				Configuration::updateValue('TEMOIGNAGE_1', addslashes($_POST['temoignage_1']));
			if(isset($_POST['temoignage_2']))
				Configuration::updateValue('TEMOIGNAGE_2', addslashes($_POST['temoignage_2']));
			if(isset($_POST['temoignage_3']))
				Configuration::updateValue('TEMOIGNAGE_3', addslashes($_POST['temoignage_3']));
			$this->_html .= '<div class="conf confirm">
					<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />';
			$this->_html .= $this->l('Témoignages enregistrés');
			$this->_html .= '</div>';
		}
	}
	
	public function hookHeader($params){
		Tools::addCSS(($this->_path).'css/temoignage.css', 'all');
		Tools::addJS(($this->_path).'js/temoignage.js', 'all');
	}
	
	public function hookLeftColumn($params){
		global $smarty;
		
		$smarty->assign('temoignages', array(stripslashes(Configuration::get('TEMOIGNAGE_1')),stripslashes(Configuration::get('TEMOIGNAGE_2')),stripslashes(Configuration::get('TEMOIGNAGE_3'))));
		
		return $this->display(__FILE__,'temoignage.tpl');
	}

}