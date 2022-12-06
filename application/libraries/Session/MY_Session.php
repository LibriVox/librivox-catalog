<?php

// save as application/libraries/MY_Session
class MY_Session extends CI_Session {

    public function __construct(array $params = array())
    {
        if ( $this->ignore_sessions() )
            return;
        parent::__construct();
    }

    function ignore_sessions()
    {
        if (array_key_exists('REQUEST_URI', $_SERVER) and $_SERVER['REQUEST_URI'])
        {
            $uri = str_replace ("//", "/", $_SERVER['REQUEST_URI']);
            if ( strpos($uri, '/api/') === 0 )
                return true;
            return false;
        } else {
            return false;
        }
    }
}
