/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


modxSDK.Layout = function(config){
    config = config || {};
     
    
    Ext.BLANK_IMAGE_URL = MODx.config.manager_url+'assets/ext3/resources/images/default/s.gif';
    Ext.Ajax.defaultHeaders = {
        'modAuth': config.auth
    };
    Ext.Ajax.extraParams = {
        'HTTP_MODAUTH': config.auth
    };
    MODx.siteId = config.auth;
    MODx.expandHelp = !Ext.isEmpty(MODx.config.inline_help);

    var sp = new MODx.HttpProvider();
    Ext.state.Manager.setProvider(sp);
    sp.initState(MODx.defaultState);

    var tabs = [];
    var showTree = false;
    if (MODx.perm.resource_tree) {
       tabs.push({
            title: _('resources')
            ,xtype: 'modx-tree-resource'
            ,id: 'modx-resource-tree'
        });
        showTree = true;
    }
    if (MODx.perm.element_tree) {
        tabs.push({
            title: _('elements')
            ,xtype: 'modx-tree-element'
            ,id: 'modx-tree-element'
        });
        showTree = true;
    }
    if (MODx.perm.file_tree) {
        tabs.push({
            title: _('files')
            ,xtype: 'modx-tree-directory'
            ,id: 'modx-file-tree'
        });
        showTree = true;
    }
    var activeTab = 0;

    Ext.applyIf(config,{
         layout: 'border'
        ,id: 'modx-layout'
        ,saveState: true
        ,items: [{
            xtype: 'box'
            ,region: 'north'
            ,applyTo: 'modx-header'
            ,height: 92
        },{
             region: 'west'
            ,applyTo: 'modx-leftbar'
            ,id: 'modx-leftbar-tabs'
            ,split: true
            ,width: 310
            ,minSize: 150
            ,maxSize: 800
            ,autoScroll: true
            ,unstyled: true
            ,collapseMode: 'mini'
            ,useSplitTips: true
            ,monitorResize: true
            ,layout: 'anchor'
            ,items: [{
                 xtype: 'modx-tabs'
                ,plain: true
                ,defaults: {
                     autoScroll: true
                    ,fitToFrame: true
                }
                ,id: 'modx-leftbar-tabpanel'
                ,border: false
                ,anchor: '100%'
                ,activeTab: activeTab
                ,stateful: true
                ,stateId: 'modx-leftbar-tabs'
                ,stateEvents: ['tabchange']
                ,getState:function() {
                    return {
                        activeTab:this.items.indexOf(this.getActiveTab())
                    };
                }
                ,items: tabs
            }]
            ,listeners:{
                statesave: this.onStatesave
                ,scope: this
            }
        },{
             region: 'east'
            ,applyTo: 'modxsdk-rightbar'
            ,id: 'modxsdk-rightbar-tabs'
            ,split: true
            ,width: 310
            ,minSize: 150
            ,maxSize: 800
            ,autoScroll: true
            ,unstyled: true
            ,collapseMode: 'mini'
            ,useSplitTips: true
            ,monitorResize: true
            ,layout: 'anchor'
            ,items: [{
                    xtype: 'modxsdk-tree-objectstree'
                }]
        },{
            region: 'center'
            ,applyTo: 'modx-content'
            ,id: 'modx-content'
            ,border: false
            ,autoScroll: true
            ,padding: '0 1px 0 0'
            ,bodyStyle: 'background-color:transparent;'
            ,items: [{
                xtype: 'modxsdk-layouttabs'
                ,items: [
                    {
                        xtype: 'modxsdk-tree-builderobjectstree'
                        ,border: false
                    }
                    
                   /* {
                        xtype: 'modxsdk-panel-fileedit'
                        ,source: 5
                        ,'file':	'test/retertert/sdfsdf.php'
                        ,autowidth: true
                    }*/
                ]
            }]
        }]
    });
    
    modxSDK.Layout.superclass.constructor.call(this,config); 
    this.config = config;

    this.addEvents({
        'afterLayout': true
        ,'loadKeyMap': true
        ,'loadTabs': true
    });
    this.loadKeys();
    if (!showTree) {
        Ext.getCmp('modx-leftbar-tabs').collapse(false);
        Ext.get('modx-leftbar').hide();
        Ext.get('modx-leftbar-tabs-xcollapsed').setStyle('display','none');
    }
    this.fireEvent('afterLayout');

    window.onbeforeunload = function(){
        return false; 
    }

};
Ext.extend(modxSDK.Layout, MODx.Layout,{});
Ext.reg('modxsdk-layout',modxSDK.Layout);