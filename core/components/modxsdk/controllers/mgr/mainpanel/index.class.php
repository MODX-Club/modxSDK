<?php
 

require_once dirname(dirname(__FILE__)).'/index.class.php';

class ControllersMgrMainpanelManagerController extends modxSDKManagerController{
    /** @var bool Set to false to prevent loading of the header HTML. */
    public $loadHeader = true;
    /** @var bool Set to false to prevent loading of the footer HTML. */
    public $loadFooter = true;
    /** @var bool Set to false to prevent loading of the base MODExt JS classes. */
    // public $loadBaseJavascript = false;
    
    public static function getInstance(modX &$modx, $className, array $config = array()) {
        $className = __CLASS__;
        return new $className($modx, $config);
    }
    
    public function getTemplateFile() {
        return 'mainpanel/index.tpl';
    }    
    
    public function initialize(){
        $this->modx->getVersionData();
        $modxVersion = $this->modx->version['full_version'];
    
        if (!version_compare($modxVersion, '2.2.6', '>=')) {
            return $this->failure("MODX 2.2.6 or highter required");
        }
        return parent::initialize();
    }
    
    public function registerBaseScripts() {
        $managerUrl = $this->modx->getOption('manager_url');
        $externals = array();

        if ($this->loadBaseJavascript) {
            $externals[] = $managerUrl.'assets/modext/core/modx.localization.js';
            $externals[] = $managerUrl.'assets/modext/util/utilities.js';

            $externals[] = $managerUrl.'assets/modext/core/modx.component.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.panel.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.tabs.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.window.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.tree.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.combo.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.grid.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.console.js';
            $externals[] = $managerUrl.'assets/modext/widgets/core/modx.portal.js';
            $externals[] = $managerUrl.'assets/modext/widgets/modx.treedrop.js';
            $externals[] = $managerUrl.'assets/modext/widgets/windows.js';

            $externals[] = $managerUrl.'assets/modext/widgets/resource/modx.tree.resource.js';
            $externals[] = $managerUrl.'assets/modext/widgets/element/modx.tree.element.js';
            $externals[] = $managerUrl.'assets/modext/widgets/system/modx.tree.directory.js';
            $externals[] = $managerUrl.'assets/modext/core/modx.view.js';
            
            $siteId = $this->modx->user->getUserToken('mgr');

            $externals[] = $managerUrl.'assets/modext/core/modx.layout.js';

            $o = '';
            $compressJs = (boolean)$this->modx->getOption('compress_js',null,true);
            $compressJsInGroups = (boolean)$this->modx->getOption('compress_js_groups',null,false);
            $this->modx->setOption('compress_js',$compressJs);
            $this->modx->setOption('compress_js_groups',$compressJsInGroups);
            
            if (!empty($compressJs) && empty($compressJsInGroups)) {
                if (!empty($externals)) {
                    $minDir = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL).'min/';

                    /* combine into max script sources */
                    $maxFilesPerMin = $this->modx->getOption('compress_js_max_files',null,10);
                    $sources = array();
                    $i = 0;
                    $idx = 0;
                    foreach ($externals as $script) {
                        if (empty($sources[$idx])) $sources[$idx] = array();
                        $sources[$idx][] = $script;
                        if ($i >= $maxFilesPerMin) { $idx++; $i = 0; }
                        $i++;
                    }
                    foreach ($sources as $scripts) {
                        $o .= '<script type="text/javascript" src="'.$minDir.'index.php?f='.implode(',',$scripts).'"></script>';
                    }
                }
            } else if (empty($compressJs)) {
                foreach ($externals as $js) {
                    $o .= '<script type="text/javascript" src="'.$js.'"></script>'."\n";
                }
            }
            if ($this->modx->getOption('compress_css',null,true)) {
                $this->modx->setOption('compress_css',true);
            }

            $state = $this->getDefaultState();
            if (!empty($state)) {
                $state = 'MODx.defaultState = '.$this->modx->toJSON($state).';';
            } else { $state = ''; }
            $o .= '<script type="text/javascript">Ext.onReady(function() {
                '.$state.'
                var Config = ace.require("ace/config");
                var acePath = MODx.config["manager_url"] + "components/modxsdk/libs/ace/src-min/";
                Config.set("modePath", acePath);
                Config.set("themePath", acePath);
                Config.set("workerPath", acePath);                
                MODx.load({xtype: "modxsdk-layout",accordionPanels: MODx.accordionPanels || [],auth: "'.$siteId.'"});
});</script>';
            $this->modx->smarty->assign('maincssjs',$o);
        }
    }    
    
    public function loadCustomCssJs() {
        parent::loadCustomCssJs();
        
        $assets_url = $this->getOption('assets_url');
        $this->addJavascript($assets_url.'libs/ace/src-min/ace.js');
        $this->addJavascript($assets_url.'js/modxsdk-layout.js');
        $this->addJavascript($assets_url.'js/modxsdk-layouttabs.js');
        $this->addJavascript($assets_url.'js/widgets/panel/file.js');
        $this->addJavascript($assets_url.'js/widgets/tree/objectstree.js');
        $this->addJavascript($assets_url.'js/widgets/tree/builderobjectstree.js');
        return;
    }
    
    public function getHeader() {
        $this->loadController('header.php',true);
        return $this->fetchTemplate('mainpanel/header.tpl');
    }
}