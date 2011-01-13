<?php
class ActionMapperAutoLoader
{
	/**
	 * @var array
	 */
	private $dir;
	
	/**
	 * @param string $class
	 */
	public function load($class)
	{
		if (is_null($this->dir)) {
			$this->dir = glob(realpath(dirname(__FILE__) . '/../') . '/*');
		}

		foreach ($this->dir as $path) {
			if ($this->includeClass($path, $class)) {
				break;
			}
		}
	}
	
	/**
	 * @param string $path
	 * @param string $class
	 */
	protected function includeClass($path, $class)
	{
		$file = $path . '/' . $class . '.php';
		
		if (is_readable($file)) {
			include $file;
			
			return true;
		}
		
		return false;
	}
	
	public static function register()
	{
		spl_autoload_register(array(new self(), 'load'));
	}
}