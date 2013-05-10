var modxSDK = function(config){
    config = config || {};
    modxSDK.superclass.constructor.call(this,config);
};
Ext.extend(modxSDK,Ext.Component,{
    config: {}, tree: {}
});
Ext.reg('modxsdk',modxSDK);

var modxSDK = new modxSDK();