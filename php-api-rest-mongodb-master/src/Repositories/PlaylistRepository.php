<?php

namespace App\Repositories;

class PlaylistRepository extends AbstractRepository
{
    protected $documentName = 'playlists';
    protected $allowedFields = ['title', 'videos'];
}
