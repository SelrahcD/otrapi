<?php
use Behat\Behat\Context\BehatContext;
use Symfony\Component\Yaml\Yaml;
use Behat\Gherkin\Node\PyStringNode; 
/**
 * CLI context.
 */
class CLIContext extends BehatContext {

	protected $parameters;
	protected $output;

	public function __construct(array $parameters = array())
	{
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
     * @Given /^that I\'m in the root directory$/
     */
    public function thatIMInTheRootDirectory()
    {
    	chdir($this->getParameter('root_dir'));
    }

    /**
     * @When /^I run artisan\'s task ([^"]*)$/
     */
    public function iRunArtisanSTask($task)
    {
        $command = 'php artisan ' . $task;

        if($env = $this->getParameter('cli_env'))
        {
            $command .= ' --env='. $env;
        }

        exec($command, $output, $returnVar);

        if($returnVar)
        {
            throw new Exception('Error occured while trying to execute task. Code : ' . $returnVar);
        }

        $this->output = trim(implode("\n", $output));
    }

    /** @Then /^I should get:$/ */
    public function iShouldGet(PyStringNode $string)
    {
        if ((string) $string !== $this->output) {
            throw new Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

}