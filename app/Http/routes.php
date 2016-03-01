<?php

/**
 * Since this application is primarily a queue worker, we just
 * expose a simple status endpoint for debugging/monitoring.
 */
$app->get('/', function () use ($app) {
    return response()->json([
        'lumen' => $app->version(),
    ]);
});
