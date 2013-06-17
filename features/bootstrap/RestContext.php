<?php
use Behat\Behat\Context\BehatContext,
    Symfony\Component\Yaml\Yaml,
    Illuminate\Support\Str;

/**
 * Rest context.
 */
class RestContext extends BehatContext
{
    private $restObject        = null;
    private $restObjectType    = null;
    // private $_restObjectMethod  = 'get';
    private $client            = null;
    private $response          = null;
    private $requestUrl        = null;
    private $token           = null;

    private $parameters = array();

    protected $user = null;
    protected static $userTokens = array();
    protected $storage = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct(array $parameters)
    {
      $this->restObject = new stdClass();
      $this->client     = new Guzzle\Service\Client();
      $eventDispatcher = $this->client->getEventDispatcher();
      $eventDispatcher->addListener('request.error', function($event)
        {
          if(!in_array($event['response']->getStatusCode(), array(500)))
          {
            $event->stopPropagation();
          }
        }, 0);
      
      $this->client->setEventDispatcher($eventDispatcher);
      $this->parameters = $parameters;
    }

    public function getParameter($name)
    {
      if (count($this->parameters) === 0)
      {
        throw new \Exception('Parameters not loaded!');
      }
      else
      {
        $parameters = $this->parameters;
        return (isset($parameters[$name])) ? $parameters[$name] : null;
      }
    } 

     /**
     * @Given /^that ([^"]*) is "([^"]*)"$/
     */
    public function thatParameterIs($name, $value)
    {
      $this->restObject->$name = $this->replaceValueIfNeeded($value);
    }

    /**
     * @When /^I make a ([^"]*) request on "([^"]*)"$/
     */
    public function iMakeARequestOn($method, $pageUrl)
    {
      $baseUrl          = $this->getParameter('base_url');
      $this->requestUrl = $baseUrl.$this->transformURL($pageUrl);

      switch (strtoupper($method))
      {
        case 'GET':
          $request = $this->client->get($this->requestUrl.'?'.http_build_str((array)$this->restObject));
          break;

        case 'POST':
          $postFields = (array)$this->restObject;
          $request = $this->client->post($this->requestUrl,null,$postFields);
          break;

        case 'PUT':
          $request = $this->client->put($this->requestUrl,null,null);
          foreach((array)$this->restObject as $key => $value)
          {
            $request->getQuery()->set($key, $value);
          }
          break;

        case 'DELETE':
          $request = $this->client->delete($this->requestUrl.'?'.http_build_str((array)$this->restObject));
            break;
      }

      if($this->token)
      {
        $request->setAuth($this->token, 'x');
      }

      $this->response = $request->send();
    }


    /**
     * @Then /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
      if ($this->response->getContentType() !== 'application/json')
      {
        throw new Exception("Response was not JSON\n" . $this->response);
      }
    }

    /**
     * @Then /^the response status code is (\d+)$/
     */
    public function theResponseStatusCodeIs($httpStatus)
    {
      if ((string)$this->response->getStatusCode() !== $httpStatus)
      {
        throw new \Exception('HTTP code does not match '.$httpStatus.' (actual: '.$this->response->getStatusCode().')');
      }
    }

    /**
     * @Then /^the response contains ([^"]*)$/
     */
    public function theResponseContains($name)
    {
      $data = json_decode((string) $this->response->getBody());

      $names = explode('.', $name);

      if(!$data = $this->searchElement($data, $names))
      {
        throw new \Exception('Response doesn\'t contain ' . $name);
      }

      $this->store($name, $data);
    }

    /**
     * @Then /^the response contains ([^"]*) and is "([^"]*)"$/
     */
    public function theResponseShouldContainsAndValue($name, $value)
    {
      $data = json_decode((string) $this->response->getBody());

      $names = explode('.', $name);

      if(strpos($name, '*') === false)
      {
        if(($res = $this->searchElement($data, $names)) != $value)
        {
          throw new \Exception('Value doesn\'t not match expected value. Received : ' . $res . ' Expected : ' . $value);
        }
      }
      else {
        if(!in_array($value, $this->searchElement($data, $names, true)))
        {
          throw new \Exception('Can\'t find expected value. Expected : ' . $value);
        }
      }
    }

    /**
     * @Then /^the response doesn't contain ([^"]*)$/
     */
    public function theResponseDoesntContain($name)
    {
      $data = json_decode((string) $this->response->getBody());

      $names = explode('.', $name);

      if($this->searchElement($data, $names))
      {
        throw new \Exception('Response contains ' . $name . ' and should not');
      }
    }

    protected function searchElement($data, Array $names = array(), $allMatching = false)
    {
      foreach($names as $name)
      {
        if(is_array($data) && $name === '*')
        {
          unset($names[0]);
          foreach($data as $row)
          {
            $res = $this->searchElement($row, $names);

            if($res && !$allMatching)
            {
              $result = $res;
              break;
            }
            else {
              $result[] = $res;
            }
          }

          if(is_null($result) || (is_array($result) && !sizeof($result)))
          {
           $data = null;
          }

          $data = $result;
          break;
        }
        else
        {
          if(!is_object($data) && !is_array($data))
          {
            $data = null;
          }
          else
          {
            if(is_array($data) && array_key_exists($name, $data))
            {
              $data = $data[$name];
            }
            elseif(is_object($data) && property_exists($data, $name))
            {
              $data = $data->{$name};
            }
            else
            {
              $data = null;
            }
          }
        }
      }
      return $data;
    }


     /**
     * @Given /^that I\'m connected as user ([^"]*)$/
     */
    public function thatIMConnectedAsUser($user)
    {
      $this->user = $user;

      if(array_key_exists($this->user, static::$userTokens))
      {
        $token = static::$userTokens[$this->user];
      }
      else
      {
        $token = $this->getToken($this->user);
        static::$userTokens[$this->user] = $token;
      }

      $this->token = $token->token;
    }

    protected function getToken($user)
    {
        $users = $this->getParameter('users');
        if(!array_key_exists($user, $users))
        {
          throw new \Exception('Unknown user ' . $user . '. Can\'t login. Please insert data in behat.yml');
        }

        $baseUrl = $this->getParameter('base_url');
        $postFields = array(
            'email' => $user,
            'password' => $users[$user],
            );

        $response = $this->client->post($baseUrl.'/auth' ,null,$postFields)->send();

        return json_decode($response->getBody(true));
    } 

    /**
     * @Then /^I store the response for user indentification$/
     */
    public function iStoreTheResponseForUserIndentification()
    {
      static::$userTokens[$this->user] = json_decode($this->response->getBody(true));
    }


    protected function transformURL($value)
    {
      $segments = explode('/', $value);
        
      $res = '';
      $max = sizeof($segments);
      for($i = 0; $i < $max; $i++)
      {

        $res .= $this->replaceValueIfNeeded($segments[$i]);
        
        if($i < ($max - 1))
        {
          $res .= '/';
        }

      }

      return $res;
    }

    protected function replaceValueIfNeeded($value)
    {
      if(Str::is('{*}', $value))
      {
        $key = substr(substr($value, 1), 0, -1);
        return $this->get($key);
      }

      return $value;
    }

    protected function store($key, $value)
    {
      $this->storage[$key] = $value;
    }

    protected function get($key)
    {
      if(!array_key_exists($key, $this->storage))
      {
        throw new \Exception('Trying to get an unexistant value');
      }

      return $this->storage[$key];
    }




}