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

    private $parameters = array();

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
            if($event['response']->getStatusCode() !== 500)
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
                $response = $this->_client->get($this->requestUrl.'?'.http_build_str((array)$this->restObject))->send();
                break;

            case 'POST':
                $postFields = (array)$this->restObject;
                $response = $this->client->post($this->requestUrl,null,$postFields)->send();
                break;

            case 'DELETE':
                $response = $this->client->delete($this->requestUrl.'?'.http_build_str((array)$this->restObject))->send();
                  break;
        }

            $this->response = $response;
    }


    /**
     * @Then /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->response->getBody(true));

        if (empty($data))
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
       $data = json_decode($this->response->getBody(true));
       if(!property_exists($data, $name))
       {
          throw new \Exception('Response doesn\'t contain ' . $name);
       }
    }

    /**
     * @Then /^the response should contain ([^"]*) and is "([^"]*)"$/
     */
    public function theResponseShouldContainAndValue($name, $value)
    {
       $data = json_decode($this->response->getBody(true));
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
        $data = json_decode($this->response->getBody(true));
        if(property_exists($data, $name))
        {
          throw new \Exception('Response contains ' . $name . ' and should not');
        }
    }



}