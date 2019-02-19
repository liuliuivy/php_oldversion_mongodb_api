<?php

namespace App\Services;

use App\Repositories\PlaylistRepository;

class PlaylistService
{
    /**
     * @var PlaylistRepository
     */
    private $repository ;

    public function create($playlist){

        if(empty($playlist['title']))return null;

        return $this->repository->insertOne($playlist);
    }

    public function update($id, $playlist){

        $updatedPlaylist = $this->repository->update($id, $playlist);

        if($updatedPlaylist)
            $updatedPlaylist['id'] = $id;

        return $updatedPlaylist;
    }

    public function remove($id, $videoIds = []){

        if(empty($videoIds)){
            /** Removes the whole playlist **/
            return $this->repository->deleteOne($id);
        }else{
            /** Removes one or multiple videos from the playlist **/
            $playlist = $this->findOne($id);
            $positionModifier = 0;
            $videosToBeRemoved = [];

            // First we find the index of the videos to be removed and we update the position of videos which remains
            foreach($playlist['videos'] as $k => &$video){
                if(in_array($video['video_id'], $videoIds)){
                    $videosToBeRemoved[] = $k;
                    $positionModifier++;
                }else{
                    $video['position'] = $video['position']-$positionModifier;
                }
            }

            // Then we remove the videos from the playlist
            foreach($videosToBeRemoved as $k){
                unset($playlist['videos'][$k]);
            }

            return $this->repository->update($id, $playlist);
        }
    }

    public function findOne($id){
        $playlist = $this->repository->findById($id);

        if(!$playlist) return null;

        $playlist['id'] = (string)$playlist['_id'];
        unset($playlist['_id']);

        return $playlist;
    }

    public function findAll(){
        $playlists = $this->repository->find();

        foreach($playlists as &$playlist){
            $playlist['id'] = (string)$playlist['_id'];
            unset($playlist['_id']);
        }

        return $playlists;
    }

    public function setRepository(PlaylistRepository $playlistRepository){
        $this->repository = $playlistRepository;
    }
}