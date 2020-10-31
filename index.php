<?php

use Licensing\Controllers\LicenseController;
use Licensing\Rest;
use Slim\App;

require "vendor/autoload.php";

$app = new App;
$rest = new Rest();


$app->group("/api", function() use($app) {
    $app->group("/v1", function() use ($app) {
        $app->group("/licenses", function() use ($app) {
            $app->post("",              [new LicenseController(), "createLicense"]);
            $app->get("/{code}",        [new LicenseController(), "getLicense"]);
            $app->put("/{code}",        [new LicenseController(), "updateLicense"]);
            $app->delete("/{code}",     [new LicenseController(), "deleteLicense"]);

            $app->get("/verify/{discord_id}", [new LicenseController(), "checkDiscord"]);
            $app->put("/verify/{code}/{discord_id}", [new LicenseController(), "veirfyCode"]);
        });
    });
});

$app->run();
