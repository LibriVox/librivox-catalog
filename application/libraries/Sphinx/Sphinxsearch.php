<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/*

Initially released as a CodeIgniter Spark by @sirfilip: https://github.com/sirfilip/sphinx
Array/object swapping from Jamie Rumbelow's MY_Model: http://github.com/jamierumbelow/codeigniter-base-model

@Author: Jeff Madsen (@codebyjeff) https://github.com/jrmadsen67

*/


class_exists('SphinxClient') or require_once('sphinxapi.php');

class Sphinxsearch {

    private   $client;

    protected $return_type = 'array';

    protected $_temporary_return_type = NULL;  

    protected $driver = 'style1';
    
    function __construct($config = array())
    {

      $this->config = $config;
      $this->client = new SphinxClient();

      // You'll want this most of the time, but can override via config
      $this->set_match_mode(SPH_MATCH_EXTENDED2);

      $this->initialize();

      $this->_temporary_return_type = $this->return_type;
    }
    
    function initialize($config = array())
    {
      $this->config = array_merge($this->config, $config);
      foreach ($this->config as $setting => $value) 
      {
        $setter = "set_{$setting}";
        if ( ! is_array($value)) $value = array($value);
        call_user_func_array(array($this, $setter), $value);
      }
    }
  
    //delegates all method calls to sphinx client providing ci method naming convention
    function __call($method, $args)
    {
      $sphinx_method = ucfirst($this->_camelize($method));
      if (method_exists($this->client, $sphinx_method))
      {
        return call_user_func_array(array($this->client, $sphinx_method), $args);
      }

      return $this->client;
    }


    // 
    /**
    * Override the SphinxClient Query function
    * Makes the return object the same as a typical codeigniter result
    * This is where most of the work needs done to be framework agnostic
    *
    * @access private
    * @param string
    * @param string
    * @return object or array
    */

    function query($term, $index)
    {
        $return = $this->client->query($term, $index);

        $results = array();

        if (empty($return['matches'])) return $results;

        $results = $this->{'_driver_' . $this->driver }($return, $index);


        return ($this->_temporary_return_type == 'array') ? $results : $this->_array_to_object($results);

    }


    // "Drivers"  

    //codeigniter, zend
    function _driver_style1(array $return, $index)
    {
        foreach ($return['matches'] as $key => $items) {
            $results[$key]  = $items['attrs'];     
        }
        return $results;
    }

    // cakephp
    function _driver_style2(array $return, $index)
    {
        foreach ($return['matches'] as $key => $items) {
            $results[$key][$index]  = $items['attrs'];     
        }
        return $results;
    }

    /**
    * Camelizes string. Borrowed from Codeigniter. Will return when finished.
    *
    * @access private
    * @param string
    * @return string
    */
    function _camelize($str)
    {
      $str = 'x'.strtolower(trim($str));
      $str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
      return substr(str_replace(' ', '', $str), 1);
    }



    /**
    * Creates object from array
    *
    * @access private
    * @param array
    * @return object
    */
    function _array_to_object($array)
    {
      return (is_array($array)) ? json_decode(json_encode($array), FALSE) : $array;
    }


    /**
     * Return the next call as an array rather than an object
     */
    public function as_array()
    {
        $this->_temporary_return_type = 'array';
        return $this;
    }

    /**
     * Return the next call as an object rather than an array
     */
    public function as_object()
    {
        $this->_temporary_return_type = 'object';
        return $this;
    }

}
