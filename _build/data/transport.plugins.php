<?php
$plugins = array();
$list = array(
    'modxSDK' => array(
        'OnManagerPageInit'
    ) ,
);
foreach ($list as $v => $e) 
{
    $plugin_name = $v;
    $path = $sources['plugins'] . $plugin_name . '.plugin.php';
    $content = getSnippetContent($path);
    if (!empty($content)) 
    {
        /*
            New plugin
        */
        $plugin = $modx->newObject('modPlugin');
        $plugin->fromArray(array(
            'name' => $plugin_name,
            'description' => $plugin_name . '_desc',
            'plugincode' => $content,
            'source' => 1,
            'static' => true,
            'static_file' => str_replace($sources['root'], '', $path)
        ));
        /* add plugin events */
        $events = array();
        if (is_array($e)) 
        {
            foreach ($e as $event) 
            {
                $events[$event] = $modx->newObject('modPluginEvent');
                $events[$event]->fromArray(array(
                    'event' => $event,
                    'priority' => 0,
                    'propertyset' => 0,
                ) , '', true, true);
            }
        }
        $plugin->addMany($events, 'PluginEvents');
        $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in ' . count($events) . ' Plugin Events.');
        flush();
        $plugins[] = $plugin;
    }
}
unset($plugin, $events, $plugin_name, $content);
return $plugins;
