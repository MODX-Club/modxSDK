<?php

/*
	For MODX-2.3+ compatibility
*/

// die(__FILE__);

require_once dirname(__FILE__) . '/mgr/index.class.php';

class ModxsdkControllersManagerController extends ModxsdkIndexManagerController{
	
    public static function getInstance(modX &$modx, $className, array $config = array()) {
        $className = __CLASS__;
        $controller = new $className($modx,$config); 
        return $controller;
    }
	
    public static function getInstanceDeprecated(modX &$modx, $className, array $config = array()) {
        $className = __CLASS__;
        $controller = new $className($modx,$config); 
        return $controller;
    }
    
	public function registerBaseScripts() {
		parent::registerBaseScripts();
		# $siteId = $this->modx->user->getUserToken('mgr');
		$o = $this->modx->smarty->get_template_vars('maincssjs');
		$o .= '<script type="text/javascript">Ext.onReady(function() { 
                var Config = ace.require("ace/config");
                var acePath = MODx.config["manager_url"] + "components/modxsdk/libs/ace/src-min/";
                Config.set("basePath", acePath);
                Config.set("modePath", acePath);
                Config.set("themePath", acePath);
                Config.set("workerPath", acePath);    
                MODx.add("modxsdk-layouttabs");
                
                window.onbeforeunload = function(){
                    return false;
                }
            });</script>';
		$this->modx->smarty->assign('maincssjs',$o);
	}
	
    
    public function loadCustomCssJs() {
        parent::loadCustomCssJs();
        
        $assets_url = $this->getOption('assets_url'); 
		
        $this->addJavascript($assets_url.'libs/ace/src-min/ace.js');
        $this->addJavascript($assets_url.'js/modxsdk-layouttabs.js');
        $this->addJavascript($assets_url.'js/widgets/panel/file.js');
        $this->addJavascript($assets_url.'js/widgets/tree/objectstree.js');
        $this->addJavascript($assets_url.'js/widgets/tree/builderobjectstree.js');
        
        return;
    } 
}
  

class ControllersManagerController extends ModxsdkControllersManagerController{
} 

