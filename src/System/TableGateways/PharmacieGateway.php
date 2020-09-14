<?php
namespace Src\TableGateways;

class PharmacieGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, name, description, contact, latitude, longitude, localites_id
            FROM
                pharmacie;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, name, description, contact, latitude, longitude, localites_id
            FROM
                pharmacie
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO pharmacie 
                (name, description, contact, latitude, longitude, localites_id)
            VALUES
                (:name, :description, :contact, :latitude, :longitude, :localites_id);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'description'  => $input['description'],
                'contact' => $input['contact'],
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                'localites_id' => $input['localites_id'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE pharmacie
            SET 
                name = :name,
                description  = :description,
                contact = :contact,
                latitude = :latitude,
                longitude = :longitude,
                localites_id = :localites_id,
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'name' => $input['name'],
                'description'  => $input['description'],
                'contact' => $input['contact'],
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
                'localites_id' => $input['localites_id'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM pharmacie
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}