/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


modxSDK.tabs.Layouttabs = function(config){
    config = config || {};
      
    Ext.applyIf(config,{
        id: 'modxsdk-layouttabs'
        ,autoHeight: true
        ,items: [
			{
				xtype: "modxsdk-tree-builderobjectstree"
				,border: false
			} 
		]
    });
    
    modxSDK.tabs.Layouttabs.superclass.constructor.call(this,config);
    
    
    // console.log(this.getHeight());
    // console.log(Ext.getCmp('modx-content').getHeight());
};
Ext.extend(modxSDK.tabs.Layouttabs, MODx.Tabs,{
    
    openFile: function(filedata){
        
        var tab = this.add({
            xtype       : 'modxsdk-panel-fileedit'
            ,id         : 'file-'+filedata.source+'-'+filedata.file
            ,closable   : true
            ,'filename' : filedata.filename
            ,'file'     : filedata.file
            ,'source'   : filedata.source
        });
        this.setActiveTab(tab);
    }
});
Ext.reg('modxsdk-layouttabs',modxSDK.tabs.Layouttabs);