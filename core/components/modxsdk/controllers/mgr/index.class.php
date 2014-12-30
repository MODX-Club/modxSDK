<?php

# die(__FILE__);

class ModxsdkIndexManagerController extends modExtraManagerController{
    
    function __construct(modX &$modx, $config = array()) {
        parent::__construct($modx, $config);
        $this->config['namespace_assets_path'] = $modx->call('modNamespace','translatePath',array(&$modx, $this->config['namespace_assets_path']));
        #manager url still called "assets_url" for safe install
        $this->config['manager_url'] = $modx->getOption('modxsdk.manager_url', null, $modx->getOption('manager_url').'components/modxsdk/');
        $this->config['connector_url'] = $this->config['manager_url'].'connectors/mgr/';
    }
    
    # public static function getInstance(modX &$modx, $className, array $config = array()) {
    #     # die(__CLASS__);
    #     return parent::getInstance($modx, __CLASS__ , $config);
    # }
    
    public function getOption($key, $options = null, $default = null, $skipEmpty = false){
        $options = array_merge($this->config, (array)$options);
        return $this->modx->getOption($key, $options, $default, $skipEmpty);
    }

    public function getLanguageTopics() {
        return array('modxsdk:default');
    }
    
    public function initialize() {
        $assets_url = $this->modx->getOption('manager_url').'components/modxsdk/';
        $this->config = array_merge($this->config, array(
            'assets_url'  => $assets_url,
        ));
        return parent::initialize();
    }
    

    function loadCustomCssJs(){
        parent::loadCustomCssJs();
        $this->addJavascript($this->getOption('assets_url').'js/modxsdk.js'); 
        
        $this->addHtml('<script type="text/javascript">
            modxSDK.config = '. $this->modx->toJSON($this->config).';
        </script>');
        
        return;
    }
    
     
    # public function getTemplateFile() {
    #     return '';
    # } 
}
?>
