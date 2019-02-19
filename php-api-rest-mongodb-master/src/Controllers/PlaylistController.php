<?php

namespace App\Controllers;

use App\Services\PlaylistService;

class PlaylistController extends RestControllerAbstract
{
    public function __construct(PlaylistService $servicePlaylist ){
        $this->servicePlaylist = $servicePlaylist;
    }

    public function create(){

        $result = $this->servicePlaylist->create($this->getParams());

        if(!$result){
            $this->returnJson(['data' => null], self::RESOURCE_NOT_MODIFIED);
        }

        $this->returnJson(['data' => $result]);
    }

    public function index(){
        $this->returnJson(['data' => $this->servicePlaylist->findAll()]);
    }

    public function get($id){

        $playlist = $this->servicePlaylist->findOne($id);

        if(!$playlist){
            $this->returnJson(['data' => null], self::RESOURCE_NOT_FOUND);
        }

        $this->returnJson(['data' => $playlist]);
    }

    public function update($id){
        $playlist = $this->servicePlaylist->update($id, $this->getParams());

        if(!$playlist){
            $this->returnJson(['data' => null], self::RESOURCE_NOT_MODIFIED);
        }

        $this->returnJson(['data' => $playlist]);
    }

    public function delete($id){
        $params = $this->getParams();
        $videoIds = isset($params['videoIds']) ? $params['videoIds'] : null;

        $playlist = $this->servicePlaylist->remove($id, $videoIds);

        if(!$playlist){
            $this->returnJson(['data' => null], self::RESOURCE_NOT_MODIFIED);
        }

        $this->returnJson(['data' => $playlist]);
    }

}