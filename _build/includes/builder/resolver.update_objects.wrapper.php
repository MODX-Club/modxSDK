<?php
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolver.update_objects.php',
));
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in update objects.');
flush();
