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
        $uri = str_replace ("//", "/", $_SERVER['REQUEST_URI']);
        if ( strpos($uri, '/api/') === 0 )
            return true;
        return false;
    }
}
