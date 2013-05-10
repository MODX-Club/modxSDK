<?php

class modxSDKManagerController extends modExtraManagerController{
    
    function __construct(modX &$modx, $config = array()) {
        parent::__construct($modx, $config);
        $this->config['namespace_assets_path'] = $modx->call('modNamespace','translatePath',array(&$modx, $this->config['namespace_assets_path']));
        #manager url still called "assets_url" for safe install
        $this->config['manager_url'] = $modx->getOption('modxsdk.manager_url', null, $modx->getOption('manager_url').'components/modxsdk/');
        $this->config['connector_url'] = $this->config['manager_url'].'connectors/mgr/';
    }

    public function getLanguageTopics() {
        return array('modxsdk:default');
    }
    
    public function checkPermissions() { return true;}

    function initialize(){
        $source = $this->modx->getOption('modxsdk.default_source', null, 0);
        
        $this->addJavascript($this->config['manager_url'].'js/modxsdk.js');
        $this->addJavascript($this->config['manager_url'].'js/widgets/tree/objectstree.js');
        $this->addJavascript($this->config['manager_url'].'js/widgets/tree/builderobjectstree.js');
        $this->addJavascript($this->config['manager_url'].'js/widgets/panel.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            modxSDK.config = '. $this->modx->toJSON($this->config).';
                
            new modxSDK.Panel({
                renderTo: Ext.get("modxsdk-container")
                ,source: "'.$source.'"
            });
        });
        </script>');
    }
    
    function getTemplate($tpl) {
        return $this->config['namespace_path']."templates/default/{$tpl}.tpl";
    }
    
    function getTemplateFile(){
        return $this->getTemplate('index');
    }
}
?>
