<?php
use Behat\Behat\Context\BehatContext;
use Symfony\Component\Yaml\Yaml;

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

    protected static $userTokens = array();

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
        $this->restObject->$name = $value;
    }

    /**
     * @When /^I make a ([^"]*) request on "([^"]*)"$/
     */
    public function iMakeARequestOn($method, $pageUrl)
    {
        $baseUrl          = $this->getParameter('base_url');
        $this->requestUrl = $baseUrl.$pageUrl;

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
     * @Then /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
        if ((string)$this->response->getStatusCode() !== $httpStatus)
        {
           throw new \Exception('HTTP code does not match '.$httpStatus.' (actual: '.$this->response->getStatusCode().')');
        }
    }

    /**
     * @Then /^the response should contain ([^"]*)$/
     */
    public function theResponseShouldContain($name)
    {
       $data = json_decode((string) $this->response->getBody());

       $names = explode('.', $name);

       foreach($names as $name)
       {
          if(!property_exists($data, $name))
          {
            throw new \Exception('Response doesn\'t contain ' . $name);
          }
          else
          {
            $data = $data->{$name};
          }
       }
    }

    /**
     * @Then /^the response should contain ([^"]*) and is "([^"]*)"$/
     */
    public function theResponseShouldContainAndValue($name, $value)
    {
       $data = json_decode((string) $this->response->getBody());

       if($data->{$name} != $value)
       {
          throw new \Exception('Value doesn\'t not match expected value. Received : ' . $data->{$name} . ' Expected : ' . $value);
       }
    }

    /**
     * @Then /^the response should not contain ([^"]*)$/
     */
    public function theResponseShouldNotContain($name)
    {
        $data = json_decode((string) $this->response->getBody());

        if(property_exists($data, $name))
        {
          throw new \Exception('Response contains ' . $name . ' and should not');
        }
    }

     /**
     * @Given /^that I\'m connected as user ([^"]*)$/
     */
    public function thatIMConnectedAsUser($user)
    {
        if(array_key_exists($user, static::$userTokens))
        {
            $token = static::$userTokens[$user];
        }
        else
        {
            $token = $this->getToken($user);
            static::$userTokens[$user] = $token;
        }

        $this->token = $token;

    }

    protected function getToken($user)
    {
        $users = $this->getParameter('users');
        if(!array_key_exists($user, $users))
        {
            throw \Exception('Unknown user ' . $user . '. Can\'t login. Please insert data in behat.yml');
        }

        $baseUrl = $this->getParameter('base_url');
        $postFields = array(
            'email' => $user,
            'password' => $users[$user],
            );

        $response = $this->client->post($baseUrl.'/auth' ,null,$postFields)->send();

        $data = json_decode($response->getBody(true));

        if(!property_exists($data, 'token'))
        {
          throw new \Exception('No token provided in response. Reponse :' . $response->getBody(true));
        }

        return $data->token;
    }




}