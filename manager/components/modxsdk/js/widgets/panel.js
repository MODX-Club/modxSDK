
modxSDK.Panel = function(config) {
    config = config || {};
    
    Ext.applyIf(config, {
        itemsHeight: Ext.getCmp('modx-content').getHeight() * 0.8 
    });
    
    this.ident = config.ident || 'modxsdk-'+Ext.id();
    Ext.applyIf(config,{
        width: '100%',
        // margins:'35 5 5 0',
        layout:'column',
        autoScroll:false,
        items:[{
            columnWidth:.6
            ,baseCls:'x-plain'
            // ,bodyStyle:'padding:5px 0 5px 5px'
            ,items:[{
                items:[{
                    xtype: 'modxsdk-tree-builderobjectstree'
                    ,height: config.itemsHeight + 40
                    ,autoHeight: false
                    ,border: false
                },]
            }]
        },{
            columnWidth:.4
            ,baseCls:'x-plain'
            // bodyStyle:'padding:5px 0 5px 5px',
            ,border: false
            ,items:[{
                title: 'Objects'
                ,items: [{
                    xtype: 'modxsdk-tree-objectstree'
                    ,source: config.source || 0
                    ,height: config.itemsHeight
                    ,autoHeight: false
                    ,border: false 
                }]
            }]
        }]
    });
    modxSDK.Panel.superclass.constructor.call(this,config)
};

Ext.extend(modxSDK.Panel, MODx.Panel,{
     verifyPerm: function(perm,rs) {
        var valid = true;
        for (var i=0;i<rs.length;i++) {
            if (rs[i].data.cls.indexOf(perm) == -1) {
                valid = false;
            }
        }
        return valid;
    } 
});
Ext.reg('modxsdk-panel', modxSDK.Panel);

