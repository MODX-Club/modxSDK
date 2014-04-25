modxSDK.panel.FileEdit = function(config){
    config = config || {};
    
    Ext.applyIf(config,{
        id: 'modxsdk-panel-fileedit',
        title: config.filename || 'file'
        ,url: modxSDK.config.connector_url + 'file/index.php'
        ,autoHeight: true
        ,border: true
        ,tbar:[{
            text: _('save')
            ,handler: this.save
            ,scope: this
        }]
        ,bbar:[{
            text: _('save')
            ,handler: this.save
            ,scope: this
        }]
        ,aceTheme: MODx.config['modxsdk.ace_theme'] || "ace/theme/monokai"
    });
    
    modxSDK.panel.FileEdit.superclass.constructor.call(this,config);
    
    this.config = config;
    this.on('afterrender', this.initEditor);
    this.on('afterrender', this.loadSource);
};



Ext.extend(modxSDK.panel.FileEdit,Ext.Panel,{
    
    initEditor: function(){
        this.EditorContainer = new Ext.form.TextArea({
            value : '',
            enableKeyEvents: true
            ,listeners: {
                keydown: function(editor, e){
                    // On Ctrl+S
                    if (e && e.ctrlKey && e.keyCode == 83) {
                        e.stopEvent();
                        this.save();
                    }
                }
                ,scope: this
            }
            
            ,onRender : function(ct, position){
                if(!this.el){
                    this.defaultAutoCreate = {
                        tag: "div",
                        cls: "x-form-textarea",
                        style:"width: 100%;height:300px;position:relative"
                    };
                }
                Ext.form.TextField.superclass.onRender.call(this, ct, position);
                var component = Ext.getCmp('modx-content');
                if(component){
                    this.setHeight(component.getHeight()-200);
                }
            },
        });
        this.add(this.EditorContainer);
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
                    if(!r.success){
                        var msg = r.message || 'Error request';
                        MODx.msg.alert('Error', msg);
                        return;
                    }
                    
                    this.editor = ace.edit(this.EditorContainer.el.dom);
                    this.editor.setTheme( this.config.aceTheme);
                    
                    var basename = r.object.basename;
                    var ext_arr = basename.split('.');
                    var ext = ext_arr[ext_arr.length-1];
                   
                    var mode = "ace/mode/text";
                    switch(ext){
                        case 'js':
                            mode = 'ace/mode/javascript';
                            break;
                        case 'css':
                            mode = 'ace/mode/css';
                            break;
                        case 'php':
                            mode = 'ace/mode/php';
                            break;
                        case 'sql':
                            mode = 'ace/mode/sql';
                            break;
                        case 'htm':
                        case 'html':
                        case 'tpl':
                            mode = 'ace/mode/html';
                            break;
                        case 'json':
                            mode = 'ace/mode/json';
                            break;
                        case 'xml':
                            mode = 'ace/mode/xml';
                            break;
                    }
                    
                    this.editor.getSession().setMode(mode);
                    this.editor.setValue(r.object.content);
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