<?php
namespace Licensing\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LicenseController {

    public function createLicense(Request $request, Response $response, $args = []) {
        global $rest;
        //$rest->doCheck($request->getHeaderLine("Authorization"), NULL);
        $code = $rest->uuid();

        if(!$rest->createLicense($code)) {
            $rest->error(400, "Could not create license.");
        }

        $rest->respond(200, 
        [
            "code"=>    $code,
            "hwid"=>    NULL,
            "stauts"=>  0,
            "discord_id" => NULL,
            "notes"=> []
        ]);
    }

    public function getLicense(Request $request, Response $response, $args = []) {
        global $rest;
        //$rest->doCheck($request->getHeaderLine("Authorization"), NULL);
        $code = $args["code"];

        if(!$rest->doesLicenseExist($code)) {
            $rest->error(400, "License does not exist.");
        }
        $license = $rest->getLicense($code);
        $notes = $rest->getNotes($code);

        $rest->respond(200, 
        [
            "code"=>    $license["code"], 
            "hwid"=>    $license["hwid"], 
            "status"=>  $license["status"],
            "discord_id"=>$license["discord_id"],
            "notes"=>   $notes
        ]);
    }

    public function updateLicense(Request $request, Response $response, $args = []) {
        global $rest;
        $body = $request->getBody();
        $code = $args["code"];

        $license = json_decode($body);
        if(!$rest->doesLicenseExist($code)) {
            $rest->error(400, "License does not exist.");
        }

        if(!$rest->updateLicense($license)) {
            $rest->error(400, "Could not update license.");
        }

        foreach($license->notes as $note) {
            if(!$rest->doesNoteAlreadyExist($code, $note)) 
                $rest->createNote($code, $note);
        }

        $stored_license = $rest->getLicense($code);
        $notes = $rest->getNotes($code);

        $rest->respond(200, 
        [
            "code"=>    $stored_license["code"], 
            "hwid"=>    $stored_license["hwid"], 
            "status"=>  $stored_license["status"],
            "discord_id"=>$stored_license["discord_id"],
            "notes"=>   $notes
        ]);
    }

    public function deleteLicense(Request $request, Response $response, $args = []) {
        global $rest;
        $code = $args["code"];

        if(!$rest->doesLicenseExist($code)) {
            $rest->error(400, "License does not exist.");
        }
        $license = $rest->getLicense($code);
        $notes =   $rest->getNotes($code);

        if(!$rest->deleteLicense($code)) {
            $rest->error(400, "Could not delete license.");
        }


        $rest->respond(200, 
        [
            "code"=>    $license["code"], 
            "hwid"=>    $license["hwid"], 
            "status"=>  $license["status"],
            "discord_id"=>$license["discord_id"],
            "notes"=>   $notes
        ]);
    }

    public function checkDiscord(Request $request, Response $response, $args = []) {
        global $rest;
        $discord_id = $args["discord_id"];

        if(!$rest->isDiscordAlreadyUsed($discord_id)) {
            $rest->respond(200, ["message"=>"User is not found"]);
        }
        $license = $rest->getLicenseByDiscord($discord_id);
        $notes =   $rest->getNotes($license["code"]);


        $rest->respond(200, 
        [
            "code"=>    $license["code"], 
            "hwid"=>    $license["hwid"], 
            "status"=>  $license["status"],
            "discord_id"=>$license["discord_id"],
            "notes"=>   $notes
        ]);
    }

    public function veirfyCode(Request $request, Response $response, $args = []) {
        global $rest;
        $code = $args["code"];
        $discord_id = $args["discord_id"];

        if($rest->isCodeAlreadyUsed($code)) {
            $rest->error(400, "Code is already used.");
        }

        if(!$rest->setDiscordID($code, $discord_id)) {
            $rest->error(400, "Could not set discord ID.");
        }

        $license = $rest->getLicense($code);
        $notes =   $rest->getNotes($license["code"]);

        $rest->respond(200, 
        [
            "code"=>    $license["code"], 
            "hwid"=>    $license["hwid"], 
            "status"=>  $license["status"],
            "discord_id"=>$license["discord_id"],
            "notes"=>   $notes
        ]);
    }

    public function checkHWID(Request $request, Response $response, $args = []) {
        global $rest;
        $hwid = $args["hwid"];

        if(!$rest->isHWID($hwid)) {
            $rest->respond(200, ["message"=>"User is not found"]);
        }
        $license = $rest->getLicenseByHWID($hwid);
        $notes =   $rest->getNotes($license["code"]);

        $rest->respond(200, 
        [
            "code"=>    $license["code"], 
            "hwid"=>    $license["hwid"], 
            "status"=>  $license["status"],
            "discord_id"=>$license["discord_id"],
            "notes"=>   $notes
        ]);
    }

}