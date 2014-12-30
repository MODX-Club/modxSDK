/*
 * Редактор компаний
 **/

modxSDK.tree.BuilderObjectsTree = function(config){
    config = config || {}; 
    
    var root = {
            nodeType: 'async'
            ,text: config.root_name || config.rootName || 'New project'
            ,draggable: false
            ,allowDrop: false
            ,id: config.root_id || config.rootId || 'project'
            ,type: 'project'
            ,cls: 'modxsdk-package-icon'
        };
    
    Ext.applyIf(config,{
        url: modxSDK.config.connector_url + 'builder/objects.php'
        ,id: 'BuilderObjectsTree'
        ,baseParams: {
            hideFiles: config.hideFiles || false
            ,wctx: MODx.ctx || 'web'
            ,currentAction: MODx.request.a || 0
            ,currentFile: MODx.request.file || ''
            ,projectid: null
        }
        ,autoHeight: true
        ,title: 'Project: New Project'
        ,header:  false
        ,autoScroll: true
        ,loaderConfig:{}
        ,rootVisible: false
        ,root: root
        ,action: 'getList'
        ,ddGroup: 'modx-treedrop-dd'
        ,allowDrop: true
        ,listeners: {
            'beforenodedrop': this._handleBeforeNodeDrop
        }
        ,useDefaultToolbar: true
        ,tbar: [{
            text: _('modxsdk_create_project')
            ,tooltip: {text: _('modxsdk_create_new_project')}
            ,handler: this.createProject
            ,scope: this
        },'-',{
            text: _('modxsdk_select_project')
            ,tooltip: {text: _('modxsdk_select_existing_project')}
            ,handler: this.selectProject
            ,scope: this
        },'-',{
            text: _('modxsdk_create_package')
            ,tooltip: {text: _('modxsdk_create_new_package')}
            ,handler: this.createProjectPackage
            ,scope: this
        }]
    });
     
    config.loaderConfig.dataUrl = config.url;
    config.loaderConfig.baseParams = config.baseParams;
    
    Ext.applyIf(config.loaderConfig,{
        preloadChildren: true
        ,clearOnLoad: true
    });
      
    
    modxSDK.tree.BuilderObjectsTree.superclass.constructor.call(this,config);
};

Ext.extend(modxSDK.tree.BuilderObjectsTree, MODx.tree.Tree,{
    config:{
        
    }
    
    /*
     * Получаем главную Tab-панель
     **/
    ,getTabLayout: function(){
        return this.ownerCt;
    }
    
    ,getBaseParam: function(param){
        return this.baseParams[param];
    }
    
    ,setBaseParam: function(param, value){
        this.baseParams[param] = value;
    }
    
    /*
     *  Create project
     **/
    ,createProject: function(){
        var win = new MODx.Window({
            tree: this
            ,title: _('modxsdk_new_project')
            ,modal: true
            ,fields: [{
                xtype: 'textfield'
                ,fieldLabel: _('modxsdk_project_name')
                ,name: 'name'
                ,width: 420
                ,allowBlank: false
            }]
            ,url: modxSDK.config.connector_url + 'builder/project.php'
            ,action: 'create'
            ,success: function(frm, r){
                try{
                    var object = r.result.object;
                    this.setTitle(object.name);
                    this.tree.setBaseParam('projectid', object.id);
                    this.tree.refresh();
                }
                catch(e){
                    Ext.Msg.alert('Error', e);
                }
            }
        });
        win.on('hide', function(){
            win.close();
        });
        win.show();
    }
    
    
    /*
     *  Select project
     **/
    ,selectProject: function(){
        
        var ProjectsCombo = new MODx.combo.ComboBox({
            fieldLabel: _('modxsdk_projects')
            ,xtype: 'modx-combo'
            ,width: 300
            ,allowBlank: false
            ,url: modxSDK.config.connector_url + 'builder/project.php'
            ,action: 'getList'
        });
        
        var win = new MODx.Window({
            title: _('modxsdk_selecting_project')
            ,width: 330
            ,fields:[ProjectsCombo] 
            ,buttons: [{
                text: _('cancel')
                ,scope: this
                ,handler: function() { win.close(); }
            },{
                text: _('save')
                ,scope: this
                ,handler: function(){
                    if(!ProjectsCombo.getRawValue()){return false;}
                    this.setTitle("Project: " + ProjectsCombo.getRawValue());
                    this.setBaseParam('projectid', ProjectsCombo.getValue());
                    this.refresh();
                    win.close();
                }
            }]
        });
        win.show();
    }
    
    /*
     *  create Package
     **/
    ,createProjectPackage: function(){
        var win = new MODx.Window({
            tree: this
            ,title: _('modxsdk_new_package')
            ,modal: true
            ,fields: [{
                xtype: 'textfield'
                ,fieldLabel: _('modxsdk_package_name')
                ,name: 'name'
                ,width: 420
                ,allowBlank: false
                ,allowDecimals: false
            },{
                xtype: 'numberfield'
                ,fieldLabel: _('modxsdk_version_major')
                ,name: 'version_major'
                ,width: 420
                ,allowBlank: false
                ,allowNegative: false
                ,allowDecimals: false
            },{
                xtype: 'numberfield'
                ,fieldLabel: _('modxsdk_version_minor')
                ,name: 'version_minor'
                ,width: 420
                ,allowBlank: false
                ,allowNegative: false
                ,allowDecimals: false
            },{
                xtype: 'numberfield'
                ,fieldLabel: _('modxsdk_version_patch')
                ,name: 'version_patch'
                ,width: 420
                ,allowBlank: false
                ,allowNegative: false
            },{
                xtype: 'textfield'
                ,fieldLabel: _('modxsdk_version_type')
                ,name: 'version_type'
                ,width: 420
                ,allowBlank: false
            },{
                xtype: 'hidden'
                ,name: 'projectid'
                ,value: this.getBaseParam('projectid')
            }]
            ,url: modxSDK.config.connector_url + 'builder/project.php'
            ,action: 'package/create'
            ,success: function(frm, r){
                this.tree.refresh();
            }
        });
        win.on('hide', function(){
            win.close();
        });
        win.show();
    }
    
    
    /*
     *  Context menues
     */
    
    ,_showContextMenu: function(node, e){
        node.select();
        this.cm.activeNode = node;        
        this.cm.removeAll();
        var m;
        var handled = false;
        
        if (!Ext.isEmpty(node.attributes.treeHandler) || (node.isRoot && !Ext.isEmpty(node.childNodes[0].attributes.treeHandler))) {
            var h = Ext.getCmp(node.isRoot ? node.childNodes[0].attributes.treeHandler : node.attributes.treeHandler);
            if (h) {
                if (node.isRoot) { node.attributes.type = 'root'; }
                m = h.getMenu(this,node,e);
                handled = true;
            }
        }
        if (!handled) {
            if (this.getMenu) {
                m = this.getMenu(node,e);
            } else if (node.attributes.menu && node.attributes.menu.items) {
                m = node.attributes.menu.items;
            }
        }
        if (m && m.length > 0) {
            this.addContextMenuItem(m);
            this.cm.showAt(e.xy);
        }
        e.preventDefault();
        e.stopEvent();
    }
    
    ,getMenu: function(node, e){
        // console.log(node);
        if(node.attributes.menu){
            return node.attributes.menu.items;
        } 
        
        switch(node.attributes.type){
            case 'package':
                return this.getPackageMenu();
                break;
            case 'vehicles':
                return this.getVehiclesMenu();
                break;
            case 'sources':
                return this.getSourcesMenu();
                break;
            case 'packagesource':
                return this.getPackageSourceMenu(node);
                break;
            default: return;
        }
    }
    
    /*
     *  PackageMenu
     **/
    ,getPackageMenu: function(node){
        return []
    }
    
    /*
     *  VehiclesMenu
     **/
    ,getVehiclesMenu: function(){
        return [{
            text: _('modxsdk_create_new_vehicle')
            ,handler: this.createVehicle
        }]
    }
    
    /*
     *  SourcesMenu
     **/
    ,getSourcesMenu: function(node){
        return [{
            text: _('modxsdk_add_media_source')
            ,handler: this.addMediaSource
        }]
    }
    
    /*
     *  PackageSourceMenu
     **/
    ,getPackageSourceMenu: function(node){
        /*return [{
            text: _('file_folder_create_here')
            ,handler: this.createDirectory
        }]*/
    }
    
    
    /*
     *
     **/
    
    /*
     *  Vehicles
     **/
    ,createVehicle: function(item, e){
        var node = this.cm && this.cm.activeNode ? this.cm.activeNode : false;
        
        var packageid = node.attributes.id.split('_')[2];
        
        var win = new MODx.Window({
            tree: this
            ,title: _('modxsdk_new_vehicle')
            ,modal: true
            ,fields: [{
                xtype: 'textfield'
                ,fieldLabel: _('modxsdk_vehicle_name')
                ,name: 'name'
                ,width: 420
                ,allowBlank: false
            }]
            ,url: modxSDK.config.connector_url + 'builder/package.php'
            ,baseParams:{
                packageid: packageid
                ,action: 'vehicle/create'
            }
            ,success: function(){
                this.tree.refresh();
            }
        });
        win.on('hide', function(){
            win.close();
        });
        win.show();
    }
    
    /*
     *  MediaSource
     **/
    ,addMediaSource: function(item, e){
        var node = this.cm && this.cm.activeNode ? this.cm.activeNode : false;
        
        var packageid = node.attributes.id.split('_')[2];
        
        var win = new MODx.Window({
            tree: this
            ,title: _('modxsdk_adding_media_source')
            ,modal: true
            ,fields: [{
                xtype: 'modx-combo-source'
                ,fieldLabel: _('modxsdk_media_source')
                ,width: 420
                ,allowBlank: false
                ,hiddenName: 'sourceid'
            }]
            ,url: modxSDK.config.connector_url + 'builder/packagesource.php'
            ,baseParams:{
                packageid: packageid
                ,action: 'create'
            }
            ,success: function(){
                this.tree.refresh();
            }
        });
        win.on('hide', function(){
            win.close();
        });
        win.show();
    }
    
    /*
     *  beforenodedrop event
     *  nodedragover event
     **/
    ,_handleDrop: function(dropEvent) {
        /*if(n.getOwnerTree() === this){
            return false;
        }*/
        
        switch(dropEvent.target.attributes.type){
            case 'vehicles':
                return this._handleDropVehicles(dropEvent);
                break;
            default:;
        }
        
        var n = dropEvent.dropNode; // the node that was dropped
        
        // Check allowed types
        if(dropEvent.target.attributes.allowed_types && !dropEvent.target.attributes.allowed_types.in_array(n.attributes.type)){
            return false;
        }
        
        return true;
    }
    
    ,_handleDrag: function(dropEvent) {
        console.log(dropEvent.dropNode);
        return true;
        
        function simplifyNodes(node) {
            var resultNode = {};
            var kids = node.childNodes;
            var len = kids.length;
            for (var i = 0; i < len; i++) {
                resultNode[kids[i].id] = simplifyNodes(kids[i]);
            }
            return resultNode;
        }
        
        
        var encNodes = Ext.encode(simplifyNodes(dropEvent.tree.root));
        this.fireEvent('beforeSort',encNodes);
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                data: encodeURIComponent(encNodes)
                ,action: this.config.sortAction || 'sort'
            }
            ,listeners: {
                'success': {fn:function(r) {
                    var el = dropEvent.dropNode.getUI().getTextEl();
                    if (el) {Ext.get(el).frame();}
                    this.fireEvent('afterSort',{event:dropEvent,result:r});
                },scope:this}
                ,'failure': {fn:function(r) {
                    MODx.form.Handler.errorJSON(r);
                    this.refresh();
                    return false;
                },scope:this}
            }
        });
    }
    
    ,_handleBeforeNodeDrop: function(e){
        var n = e.dropNode; // the node that was dropped
        // console.log(n);
        // console.log(this); 
        
        var copy = new Ext.tree.TreeNode( // copy it
        Ext.apply({}, n.attributes));
        
        e.dropNode = copy; // assign the copy as the new dropNode
        
        // var fields = [];
        
        //foreach()
        
        /*var win = new MODx.Window({
            fields: fields
        });
        win.show();*/
        
        
        /*e.cancel = true;
        e.dropStatus = true;*/
    }
    
    ,_handleDropVehicles: function(dropEvent){
        /*if(!dropEvent.dropNode.getUI().hasClass('xPDOObject')){
            console.log(dropEvent.dropNode.getUI());
            return false;}*/
        
        if(!dropEvent.dropNode.attributes.cls.split(' ').in_array('xPDOObject')){
            return false;
        }
        
        return true;
    }
    
    ,getPackageId: function(){
        var node = this.cm && this.cm.activeNode ? this.cm.activeNode : false;
        var packageid = node.attributes.id.split('_')[2];
        if(!packageid){
            MODx.msg.alert('Error', 'Could not get Package id');
            return false;
        }
        return packageid;
    }
    
    ,removePackage: function(item,e) {
        var packageid = this.getPackageId();
        if(!packageid){
            return;
        }
        
        MODx.msg.confirm({
            text: _('modxsdk_remove_this_package')
            ,url: modxSDK.config.connector_url + 'builder/package.php'
            ,params: {
                action: 'remove'
                ,id: packageid
            }
            ,listeners: {
                'success': {fn:function() {
                    this.refresh();
                }, scope: this}
            }
        })
    }
    
    ,makePackage: function(item,e) {
        var packageid = this.getPackageId();
        if(!packageid){
            return;
        }
        var mask = new Ext.LoadMask(Ext.getBody());
        mask.show();
        MODx.Ajax.request({
            url: modxSDK.config.connector_url + 'vapor/index.php'
            ,params: {
                action: 'package/createPackage'
                ,package_id: packageid
            }
            ,listeners: {
                'success': {fn:function(r) {
                        mask.hide();
                        MODx.msg.alert('Info', r.message);
                    }, 
                    scope: this
                }
                ,'failure': {fn:function(r) {
                        mask.hide();
                        MODx.msg.alert('Error', 'Request failed');
                    }, 
                    scope: this
                }
            }
        })
    }
    
    ,makeAndDownloadPackage: function(item,e) {
        var packageid = this.getPackageId();
        if(!packageid){
            return;
        }
        var mask = new Ext.LoadMask(Ext.getBody());
        mask.show();
        MODx.Ajax.request({
            url: modxSDK.config.connector_url + 'vapor/index.php'
            ,params: {
                action: 'package/createPackage'
                ,package_id: packageid
            }
            ,listeners: {
                'success': {fn:function(r) {
                        mask.hide();
                        if(r.object && r.object.signature){
                            location.href = modxSDK.config.connector_url+'builder/package.php?action=download&download=1&signature='+r.object.signature+'&HTTP_MODAUTH='+MODx.siteId+'&wctx='+MODx.ctx;
                        }
                        else{
                            MODx.msg.alert('Error', 'Could not get package sugnature');
                        }
                    }, 
                    scope: this,
                }
                ,'failure': {fn:function(r) {
                        mask.hide();
                        MODx.msg.alert('Error', 'Request failed');
                    }, 
                    scope: this
                }
            }
        })
    }
    
    
    
    ,createDirectory: function(item,e) {
        var node = this.cm && this.cm.activeNode ? this.cm.activeNode : false;
        var id = node.attributes.id;
        var separator = id.indexOf('/');
        var path = id.substr(separator);
        var sourceid = id.substr(0, separator).split('_')[3];
        
        var r = {
            'parent': node && node.attributes.type == 'dir' ? node.attributes.pathRelative : '/'
            ,source: sourceid
        };
        var w = MODx.load({
            xtype: 'modx-window-directory-create'
            ,record: r
            ,listeners: {
                'success':{fn:this.refreshActiveNode,scope:this}
                ,'hide':{fn:function() {this.destroy();}}
            }
        });
        w.show(e ? e.target : Ext.getBody());
    }
    
    
    ,quickCreateFile: function(item,e) {
        var node = this.cm.activeNode;
        var id = node.attributes.id;
        var separator = id.indexOf('/');
        var path = id.substr(separator);
        var sourceid = id.substr(0, separator).split('_')[3];
        
        var r = {
            directory: path
            ,source: sourceid
        };
        var w = MODx.load({
            xtype: 'modx-window-file-quick-create'
            ,record: r
            ,listeners: {
                'success':{fn:this.refreshActiveNode,scope:this}
                ,'hide':{fn:function() {this.destroy();}}
            }
        });
        w.show(e.target);
    }
    
    /*
     * Событие на клик
     **/
    ,_handleClick: function (n,e) {
        e.stopEvent();
        e.preventDefault();
        if (this.disableHref) {return true;}
        if (e.ctrlKey) {return true;}
        
        // console.log(n);
        
        if(n.attributes.type && n.attributes.type == 'file'){
            this.openFile(n.attributes);
            return;
        }
        
        if (n.attributes.page && n.attributes.page !== '') {
            MODx.loadPage(n.attributes.page);
        } else {
            n.toggle();
        }
        return true;
    }
    
    ,openFile: function(filedata){
        this.getTabLayout().openFile({
            filename: filedata.text
            ,file: filedata.pathRelative
            ,source: filedata.source
        });
    }
});

Ext.reg('modxsdk-tree-builderobjectstree',modxSDK.tree.BuilderObjectsTree);