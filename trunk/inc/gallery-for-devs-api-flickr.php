<?php

class FlickrAPI {

    private $key;
    private $secret;
    private $username;
    private $request;
    private $web;
    private $validUser;

    public function __construct() {
        $this->setKey();
        $this->setSecret();
        $this->setUsername();

        $this->setRequestURL();
        $this->setWebURL();

        // Validate
        $this->validUser = $this->testUserKey();
    }

    /**
     * Set API key
     */
    private function setKey() {
        $key = get_option('gfdevs_options_flickr_key');
        $this->key = $key != '' ? $key : NULL;
    }

    /**
     * Set API secret
     */
    private function setSecret() {
        $secret = get_option('gfdevs_options_flickr_secret');
        $this->secret = $secret != '' ? $secret : NULL;
    }

    /**
     * Set Flickr username
     */
    private function setUsername() {
        $username = get_option('gfdevs_options_flickr_username');
        $this->username = $username != '' ? $username : NULL;
    }

    /**
     * Set Flickr API Request URL
     */
    private function setRequestURL() {
        $this->request = 'https://api.flickr.com/services/rest/?&api_key='.$this->key.'&user_id='.$this->username.'&format=json&nojsoncallback=1&method=';
    }

    /**
     * Set Flickr Web URL
     */
    private function setWebURL() {
        $this->web = 'https://www.flickr.com/photos/'.$this->username;
    }

    /**
     * API Key validation request
     * Method: flickr.test.echo
     */
    private function testUserKey() {
        if ($this->key === NULL || $this->username === NULL) {
            return false;
        }

        $method = 'flickr.test.echo';
        $request = $this->request.$method;

        return json_decode(file_get_contents($request))->stat == 'ok';
    }

    /**
     * Get Flickr Album URL
     */
    private function getAlbumURL($albumId) {
        if (isset($albumId) === false) {
            return false;
        }
        return $this->web.'/sets/'.$albumId;
    }

    /**
     * Pull all Flickr Albums
     * Method: flickr.photosets.getList
     */
    private function getAlbumsList() {

        if (!$this->validUser) {
            return false;
        }

        $method = 'flickr.photosets.getList&primary_photo_extras=url_q,url_n,url_c,url_k,url_o';

        $request = $this->request.$method;
        $result = json_decode(file_get_contents($request));

        if ($result->stat !== 'ok') {
            return false;
        }

        $photosets = $result->photosets->photoset;

        if (count($photosets) == 0) {
            return array();
        }

        $photos = 0;
        $albums = array();

        foreach($photosets as $photoset) {

            // fallback to lower image size
            if (!isset($photoset->primary_photo_extras->url_m)) {
                $photoset->primary_photo_extras->url_m = $photoset->primary_photo_extras->url_q;
                $photoset->primary_photo_extras->width_m = $photoset->primary_photo_extras->width_q;
                $photoset->primary_photo_extras->height_m = $photoset->primary_photo_extras->height_q;
            }

            // fallback to lower image size
            if (!isset($photoset->primary_photo_extras->url_n)) {
                $photoset->primary_photo_extras->url_n = $photoset->primary_photo_extras->url_m;
                $photoset->primary_photo_extras->width_n = $photoset->primary_photo_extras->width_m;
                $photoset->primary_photo_extras->height_n = $photoset->primary_photo_extras->height_m;
            }

            // fallback to lower image size
            if (!isset($photoset->primary_photo_extras->url_c)) {
                $photoset->primary_photo_extras->url_c = $photoset->primary_photo_extras->url_n;
                $photoset->primary_photo_extras->width_c = $photoset->primary_photo_extras->width_n;
                $photoset->primary_photo_extras->height_c = $photoset->primary_photo_extras->height_n;
            }

            // fallback to lower image size
            if (!isset($photoset->primary_photo_extras->url_k)) {
                $photoset->primary_photo_extras->url_k = $photoset->primary_photo_extras->url_c;
                $photoset->primary_photo_extras->width_k = $photoset->primary_photo_extras->width_c;
                $photoset->primary_photo_extras->height_k = $photoset->primary_photo_extras->height_c;
            }

            $albums['photosets'][] = array(
                'ID' => $photoset->id,
                'title' => $photoset->title->_content,
                'description' => $photoset->description->_content,
                'url' => $this->getAlbumURL($photoset->id),
                'thumbnail' => $photoset->primary_photo_extras->url_q,
                'small' => $photoset->primary_photo_extras->url_n,
                'medium' => $photoset->primary_photo_extras->url_c,
                'large' => $photoset->primary_photo_extras->url_k,
                'original' => $photoset->primary_photo_extras->url_o,
                'photos_count' => $photoset->photos,
                'date_create' => $photoset->date_create
            );

            // Add album data to additional option
            if (get_option('gfdevs_flickr_photoset_' . $photoset->id) == false) {
                add_option('gfdevs_flickr_photoset_' . $photoset->id, $this->getAlbumPhotos($photoset->id));
            }

            $photos += $photoset->photos;
        }

        $albums['count'] = $result->photosets->total;
        $albums['photos_count'] = $photos;
        $albums['sync_time'] = time() + (get_option('gmt_offset')*60*60);

        return $albums;
    }

    /**
     * Get photos from album
     *
     * @param $albumId
     * @return array
     */
    private function getAlbumPhotos($albumId) {

        if (!$this->validUser) {
            return false;
        }

        $method = 'flickr.photosets.getPhotos&photoset_id='.$albumId.'&extras=url_q,url_m,url_n,url_c,url_k,url_o';

        $request = $this->request.$method;
        $result = json_decode(file_get_contents($request));

        if ($result->stat !== 'ok') {
            return false;
        }

        $photos = array();
        $photoset_photos = $result->photoset->photo;

        foreach($photoset_photos as $photo) {

            // fallback to lower image size
            if (!isset($photo->url_m)) {
                $photo->url_m = $photo->url_q;
                $photo->width_m = $photo->width_q;
                $photo->height_m = $photo->height_q;
            }

            // fallback to lower image size
            if (!isset($photo->url_n)) {
                $photo->url_n = $photo->url_m;
                $photo->width_n = $photo->width_m;
                $photo->height_n = $photo->height_m;
            }

            // fallback to lower image size
            if (!isset($photo->url_c)) {
                $photo->url_c = $photo->url_n;
                $photo->width_c = $photo->width_n;
                $photo->height_c = $photo->height_n;
            }

            // fallback to lower image size
            if (!isset($photo->url_k)) {
                $photo->url_k = $photo->url_c;
                $photo->width_k = $photo->width_c;
                $photo->height_k = $photo->height_c;
            }

            $photos[] = array(
                'ID' => $photo->id,
                'alt' => '',
                'title' => $photo->title,
                'caption' => '',
                'description' => '',
                'url' => $photo->url_o,
                'width' => $photo->width_o,
                'height' => $photo->height_o,
                'mime_type' => '',
                'type' => 'image',
                'sizes' => array(
                    'thumbnail' => $photo->url_q,
                    'thumbnail-width' => $photo->width_q,
                    'thumbnail-height' => $photo->height_q,
                    'medium' => $photo->url_n,
                    'medium-width' => $photo->width_n,
                    'medium-height' => $photo->height_n,
                    'medium_large' => $photo->url_c,
                    'medium_large-width' => $photo->width_c,
                    'medium_large-height' => $photo->height_c,
                    'large' => $photo->url_k,
                    'large-width' => $photo->width_k,
                    'large-height' => $photo->height_k,
                )
            );
        }

        return $photos;
    }

    /**
     * Test if username and api key is valid
     */
    public function isValid() {
        return $this->validUser;
    }

    /**
     * Sync albums
     */
    public function syncAlbums() {

        if (!$this->isValid()) {
            return false;
        }

        if (!$_POST) {
            return false;
        }

        if (isset($_POST['provider']) && $_POST['provider'] !== 'flickr') {
            return false;
        }

        if (isset($_POST['action']) && $_POST['action'] !== 'sync') {
            return false;
        }

        $albums = $this->getAlbumsList();

        if (!is_array($albums)) {
            return false;
        }

        // Save data to WP options
        delete_option('gfdevs_flickr_photosets');
        add_option('gfdevs_flickr_photosets', $albums);

        return true;
    }

    /**
     * Reset cache
     */
    public function resetAlbums() {

        if (!$_POST) {
            return false;
        }

        if (isset($_POST['provider']) && $_POST['provider'] !== 'flickr') {
            return false;
        }

        if (isset($_POST['action']) && $_POST['action'] !== 'reset') {
            return false;
        }

        // Save data to WP options
        $photosets = get_option('gfdevs_flickr_photosets');
        $albums = $photosets['photosets'];

        if (!is_array($albums)) {
            return false;
        }

        foreach($albums as $album) {
            delete_option('gfdevs_flickr_photoset_'.$album['ID']);
        }

        delete_option('gfdevs_flickr_photosets');

        return true;
    }    
    
}
