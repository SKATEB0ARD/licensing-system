<?php

use Licensing\Controllers\LicenseController;
use Licensing\Rest;
use Slim\App;

require "vendor/autoload.php";
require_once "config.php";

$app = new App;
$rest = new Rest($config["sql_host"], $config["database_name"], $config["database_username"], $config["database_password"]);

$app->group("/api", function() use($app) {
    $app->group("/v1", function() use ($app) {
        $app->group("/licenses", function() use ($app) {
            $app->post("",              [new LicenseController(), "createLicense"]);
            $app->get("/{code}",        [new LicenseController(), "getLicense"]);
            $app->put("/{code}",        [new LicenseController(), "updateLicense"]);
            $app->delete("/{code}",     [new LicenseController(), "deleteLicense"]);

            $app->put("/verify/{code}/{discord_id}",        [new LicenseController(), "veirfyCode"]);

        
            $app->get("/check/discord/{discord_id}",        [new LicenseController(), "checkDiscord"]);
            $app->get("/check/hwid/{hwid}",                 [new LicenseController(), "checkHWID"]);
        });
    });
});

$app->run();
