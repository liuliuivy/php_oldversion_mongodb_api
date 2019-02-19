# php-api-rest-mongodb
A from scratch PHP7 REST API made as a test for a job interview


## What does it do ?

Very basic from scratch micro framework with a router handling simple REST routes. 
As a use case, this API handles playlist of videos and returns something like:
```bash
{
  "data": {
    "title": "Best playlist",
    "videos": {
      "0": {
        "video_id": "21254",
        "position": 1
      },
      "1": {
        "video_id": "897875",
        "position": 2
      }
    },
    "id": "5832d3258af87c10183f4d7d"
  }
}
```


## Quick start

First of all, you'll need to install a couple of things:
- PHP 7
- [Composer](https://getcomposer.org/download/)
- [MongoDB](https://docs.mongodb.com/getting-started/shell/installation/)


```bash
# clone the repo
git clone git@github.com:cbaudras/php-api-rest-mongodb.git

# change directory 
cd php-api-rest-mongodb

# install the dependencies with composer
composer install

# launch MongoDB server
mongod

# launch local  PHP server
php -S localhost:8080

```

The API should be up and running on [http://localhost:8080/](http://localhost:8080/)

MongoDB connection can be edited in `config.php`.


## Routes

```bash
Create a playlist
/playlist    POST
Posting data looking like that:

{
  "title": "Best playlist",
  "videos": {
    "0": {
      "video_id": "21254",
      "position": 1
    },
    "1": {
      "video_id": "897875",
      "position": 2
    }
  }
}

Update a playlist
/playlist/1234    PUT
Posting data looking like that:

{
  "title": "Best playlist EVER",
}

Get all playlists
/playlist    GET

Get one playlist
/playlist/1234   GET

Remove a playlist
/playlist/1234   DELETE

Remove videos from playlist. 
Careful though, if you don't post videoIds, it will remove the whole playlist ! ;)
/playlist/1234   DELETE

Posting data looking like that:
{
  "videoIds": [897875],
}


```

