<?php
namespace Netsilik\Tests;

require(__DIR__.'/../src/d.php');

class TestObject
{
	public $communal = 'nature';
	
	protected static $governmental = 'park';
	
	private $individual = 'garden';
	
	public function __construct()
	{
		$this->changeling = $this;
	}
}
