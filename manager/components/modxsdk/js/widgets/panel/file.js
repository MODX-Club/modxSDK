modxSDK.panel.FileEdit = function(config){
    config = config || {};
    
    Ext.applyIf(config,{
        id: 'modxsdk-panel-fileedit',
        title: config.filename || 'file'
        ,url: modxSDK.config.connector_url + 'file/index.php'
        ,height: 500
        ,autoHeight: false
        ,border: true
        ,bbar:[{
            text: 'Save'
            ,handler: this.save
            ,scope: this
        }]
        ,aceTheme: MODx.config['modxsdk.ace_theme'] || "ace/theme/monokai"
    });
    config.autoHeight = false;
    
    
    modxSDK.panel.FileEdit.superclass.constructor.call(this,config);
    
    
    this.config = config;
     
    this.on('afterrender', this.initEditor);
    this.on('afterrender', this.loadSource);
    
    
};
Ext.extend(modxSDK.panel.FileEdit,Ext.Panel,{
    
    
    initEditor: function(){
        this.EditorContainer = new Ext.Panel({
            bodyStyle: {
                top: 0
                ,left: 0
                ,right: 0
                ,bottom: 0
                ,position: 'absolute'
            }
            ,bodyCfg:{
                cls: 'editor'
            }
        });
        this.add(this.EditorContainer);
    }
    
    ,setUpdater: function(){
        this.getUpdater().on('beforeupdate', this.onBeforeUpdate)
    }
     
    
    ,loadSource: function(){
        
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'get'
                ,'source': this.config.source
                ,'file': this.config.file
            }
            ,listeners: {
                'success': {fn:function(r) {
                    if(r.success != true){
                        var msg = r.message || 'Error request';
                        MODx.msg.alert('Error', msg);
                        return;
                    }
                    
                    this.editor = ace.edit(this.EditorContainer.body.dom);
                    this.editor.setTheme( this.config.aceTheme);
                    
                    var basename = r.object.basename;
                    var ext_arr = basename.split('.');
                    var ext = ext_arr[ext_arr.length-1];
                   
                    var mode = "ace/mode/php";
                    switch(ext){
                        case 'js':
                            mode = 'ace/mode/javascript';
                            break;
                        default:;
                    }
                    
                    this.editor.getSession().setMode(mode);
                    this.editor.setValue(r.object.content);
                    // console.log(this.editor);
                    
                },scope:this}
	    }
        });
    }
    
    ,save: function(){
        var mask = new Ext.LoadMask(this.editor.container);
        mask.show();
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'update'
                ,'source': this.config.source
                ,'file': this.config.file
                ,'content': this.editor.getValue()
            }
            ,listeners: {
                'success': {
                    fn:function(r) {
                        mask.hide();
                    } 
                },
                'failure': {
                    fn:function(r) {
                        mask.hide();
                    } 
                }
	    }
        });
    }
});

Ext.reg('modxsdk-panel-fileedit',modxSDK.panel.FileEdit); 