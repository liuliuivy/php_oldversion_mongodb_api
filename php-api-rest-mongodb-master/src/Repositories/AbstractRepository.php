<?php

namespace App\Repositories;

abstract class AbstractRepository
{
    protected $dbClient;
    protected $dbName;
    protected $documentName;
    protected $allowedFields = [];

    public function insertOne($playlist){
        $playlist = $this->cleanFields($playlist);

        $result = $this->getDb()->{$this->documentName}->insertOne($playlist);
        $playlist['id'] = $result->getInsertedId()->__toString();
        return $playlist;
    }

    public function update($id, $playlist){
        $playlist = $this->cleanFields($playlist);
        $updateResult = $this->getDb()->{$this->documentName}->updateOne(['_id'=> new \MongoDB\BSON\ObjectID($id)], [ '$set' => $playlist]);
        return $updateResult->getModifiedCount() ? $playlist : null;
    }

    public function deleteOne($id){
        $deleteResult = $this->getDb()->{$this->documentName}->deleteOne(['_id'=> new \MongoDB\BSON\ObjectID($id)]);
        return (bool)$deleteResult->getDeletedCount();
    }

    public function findById($id){
        try{
            $results = $this->getDb()->{$this->documentName}->find(['_id' => new \MongoDB\BSON\ObjectID($id)]);
            return $results->toArray()[0];
        }catch(\Exception $e){
            return null;
        }
    }

    public function find(){
        try{
            $results = $this->getDb()->{$this->documentName}->find();
            return $results->toArray();
        }catch(\Exception $e){
            return [];
        }
    }

    private function getDb(){
        return $this->dbClient->{$this->dbName};
    }

    private function cleanFields($data){
        if(empty($this->allowedFields)){
            return $data;
        }

        foreach($data as $k => $value){
            if(!in_array($k, $this->allowedFields)){
                unset($data[$k]);
            }
        }

        return $data;
    }

    public function setDbName($dbName){
        $this->dbName = $dbName;
    }

    public function setDbClient($dbClient){
        $this->dbClient = $dbClient;
    }
}