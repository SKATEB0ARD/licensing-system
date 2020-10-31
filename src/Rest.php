<?php
namespace Licensing;

use Licensing\Data\Database;

class Rest {
    private $conn;

    function __construct()
    {
        $db = new Database("localhost", "licensing", "root", "");
        $this->conn = $db->getConnection();
    }

    function uuid() : string {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function doCheck($key, $body) {
        if(!$this->checkAccessToken($key)) 
            $this->error(401, "Access Token was not found on our database.");

        if($body !== NULL) {
            if(!$this->isJson($body)) 
                $this->error(400, "Body is inncorrectly formatted.");
        }
    }

    public function createLicense($code) : bool {
        $query = $this->conn->prepare("INSERT INTO licenses(code) VALUES(:code)");
        $query->bindParam(":code", $code);
        
        return $query->execute();
    }

    public function getLicense($code) : array {
        $query = $this->conn->prepare("SELECT * FROM licenses WHERE code = :code");
        $query->bindParam(":code", $code);
        $query->execute();

        return $query->fetch();
    }

    public function doesLicenseExist($code) : bool {
        $query = $this->conn->prepare("SELECT id FROM licenses WHERE code = :code");
        $query->bindParam(":code", $code);
        $query->execute();

        return $query->rowCount() == 1;
    }

    public function deleteLicense($code) : bool {
        $query = $this->conn->prepare("DELETE FROM licenses WHERE code = :code");
        $query->bindParam(":code", $code);

        return $query->execute();
    }

    public function isDiscordAlreadyUsed($discord_id) : bool {
        $query = $this->conn->prepare("SELECT id FROM licenses WHERE discord_id = :id");
        $query->bindParam(":id", $discord_id);
        $query->execute();

        return $query->execute();
    }

    public function setDiscordID($code, $discord_id) : bool {
        $query = $this->conn->prepare("UPDATE licenses SET discord_id = :id WHERE code = :code");
        $query->bindParam(":id", $discord_id);
        $query->bindParam(":code", $code);
        
        return $query->execute();
    }

    public function isCodeAlreadyUsed($code) : bool {
        $query = $this->conn->prepare("SELECT discord_id FROM licenses WHERE code = :code");
        $query->bindParam(":code", $code);
        $query->execute();
        $discord_id = $query->fetch()["discord_id"];
        
        return $discord_id != NULL;
    }

    public function getLicenseByDiscord($discord_id) : array {
        $query = $this->conn->prepare("SELECT * FROM licenses WHERE discord_id = :id");
        $query->bindParam(":id", $discord_id);
        $query->execute();

        return $query->fetch();
    }

    public function doesNoteAlreadyExist($code, $note) : bool {
        $query = $this->conn->prepare("SELECT id FROM license_notes WHERE code = :code AND note = :note");
        $query->bindParam(":code", $code);
        $query->bindParam(":note", $note);
        $query->execute();

        return $query->rowCount() == 1;
    }

    public function createNote($code, $note) : bool {
        $query = $this->conn->prepare("INSERT INTO license_notes(license_code, note) VALUES(:code, :note)");
        $query->bindParam(":code", $code);
        $query->bindParam(":note", $note);

        return $query->execute();
    }

    public function getNotes($code) : array {
        $query = $this->conn->prepare("SELECT * FROM license_notes WHERE license_code = :code");
        $query->bindParam(":code", $code);
        $query->execute();
        $rows = $query->fetchAll();
        $notes = array();

        foreach($rows as $row) {
            array_push($notes, $row["note"]);
        }
        
        return $notes;
    }

    public function updateLicense($license) : bool {
        $query = $this->conn->prepare("UPDATE licenses SET hwid = :id, username = :name WHERE code = :code");
        $query->bindParam(":id", $license->hwid);
        $query->bindParam(":name", $license->username);
        $query->bindParam(":code", $license->code);

        return $query->execute();
    }

    public function checkAccessToken($token) : bool {
        $query = $this->conn->prepare("SELECT id FROM access_tokens WHERE token = :tok");
        $query->bindParam(":tok", $token);
        $query->execute();

        return $query->rowCount() == 1;
    }

    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    function respond($code, $body) {
        http_response_code($code);
        header('Content-Type: application/json');
        die (json_encode($body));
    }

    function error($code, $message) {
        $this->respond($code, ["status" => "error", "message"=> $message]);
    }

}
