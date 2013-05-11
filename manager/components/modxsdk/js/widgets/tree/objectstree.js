/*
 * Objects tree
 **/

modxSDK.tree.ObjectsTree = function(config){ 
    config = config || {}; 
      
    
    Ext.applyIf(config,{
        source: MODx.config['modxsdk.default_source'] || 1
    });
    
    Ext.applyIf(config,{
        url: modxSDK.config.connector_url + 'model/objects.php'
        ,baseParams: {
            hideFiles: config.hideFiles || false
            ,wctx: MODx.ctx || 'web'
            ,currentAction: MODx.request.a || 0
            ,currentFile: MODx.request.file || ''
            ,source: config.source
        }
        ,border: true
        ,autoHeight: false
        ,loaderConfig:{}
        /*,dragConfig:{
            ddGroup: 'modx-treedrop-dd'
            ,allowDrop: true
            ,appendOnly: true
            ,notifyDrop: function(ddSource, e, data) {
                 console.log('sdfsdfdddd');
                 return false;
            }
            ,beforeDragDrop: function( target, e, id ){
                console.log('sdfsdfddddddddd');
                 return true;
            }
            ,afterDragDrop: function( target, e, id ){
                console.log('khdfgfdsdd');
                 return false;
            }
        }*/
    });
    
    config.loaderConfig.dataUrl = config.url;
    config.loaderConfig.baseParams = config.baseParams;
    
    Ext.applyIf(config.loaderConfig,{
        preloadChildren: true
        ,clearOnLoad: true
    });
     
    
    var tl;
    
    tl = new Ext.tree.TreeLoader(config.loaderConfig);
    tl.on('beforeload',function(l,node) {
        tl.dataUrl = this.config.url+'?action='+this.config.action+'&id='+node.attributes.id;
        if (node.attributes.type) {
            tl.dataUrl += '&type='+node.attributes.type;
        }
        if (node.attributes.name) {
            tl.dataUrl += '&name='+node.attributes.name;
        }
    },this);
    tl.on('load', this.onLoad,this); 
    
    Ext.applyIf(config,{
        loader:  tl 
    });
    
    modxSDK.tree.ObjectsTree.superclass.constructor.call(this,config);
};

Ext.extend(modxSDK.tree.ObjectsTree, MODx.tree.Directory,{
    config:{}
});

Ext.reg('modxsdk-tree-objectstree',modxSDK.tree.ObjectsTree);