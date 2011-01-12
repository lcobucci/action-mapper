<?php
/**
 * Prove a classe para gerenciamento da sessão
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage session
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe para gerenciamento da sessão
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage session
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppSession
{
	/**
	 * Identificador onde serão armazenados os identificadores de objetos
	 * 
	 * @var string
	 */
	const SESS_OBJECTS = 'sess_objects_var';
	
	/**
	 * Instância da classe
	 * 
	 * @var AppSession
	 */
	private static $instance;
	
	/**
	 * Controla se a sessão foi inicializada
	 * 
	 * @var bool
	 */
	private $initialized;
	
	/**
	 * Armazenamento temporário dos dados da sessão
	 * 
	 * @var array
	 */
	private $sessionVar;
	
	/**
	 * Singleton usage
	 * 
	 * @return AppSession
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Unset the instance
	 */
	public static function unsetInstance()
	{
		self::$instance = null;
	}
	
	/**
	 * Constructor da classe
	 */
	protected function __construct()
	{
		$this->initialized = false;
	}
	
	/**
	 * Destrutor da classe
	 */
	public function __destruct()
	{
		if ($this->isInitialized()) {
			$this->serializeObjects();
			$_SESSION = $this->sessionVar;
		}
	}
	
	/**
	 * Serializa os objetos, caso existirem 
	 */
	protected function serializeObjects()
	{
		if (count($this->getObjects()) > 0) {
			foreach ($this->getObjects() as $identifier) {
				$this->sessionVar[$identifier] = serialize($this->get($identifier));
			}
			
			$this->sessionVar[self::SESS_OBJECTS] = $this->getObjects();
		} elseif ($this->exists(self::SESS_OBJECTS)) {
			unset($this->sessionVar[self::SESS_OBJECTS]);
		}
	}

	/**
	 * Inicia a sessão (apenas se não estiver iniciada)
	 * 
	 * @param string $sessionName
	 */
	public function start($sessionName = null)
	{
		if (!$this->isInitialized()) {
			if ($sessionName) {
				session_name($sessionName);
			}
			
			session_start();
			$this->initialized = true;
			$this->sessionVar = $_SESSION;
			
			$this->unserializeObjects();
		}
	}
	
	/**
	 * Remove a serialização dos objetos, caso existirem 
	 */
	protected function unserializeObjects()
	{
		if (count($this->getObjects()) > 0) {
			foreach ($this->getObjects() as $identifier) {
				$this->sessionVar[$identifier] = unserialize($this->get($identifier));
			}
		}
	}
	
	/**
	 * Finaliza a sessão limpando todos os dados armazenados
	 * 
	 * @throws AppSessionException
	 */
	public function terminate()
	{
		if (!$this->isInitialized()) {
			throw new AppSessionException('Sessão não pôde ser finalizada, pois ainda não foi iniciada');
		}
		
		$this->initialized = false;
		session_unset();
		session_destroy();
	}
	
	/**
	 * Verifica se a sessao foi inicializada ou nao
	 * 
	 * @return boolean
	 */
	public function isInitialized()
	{
		return $this->initialized;
	}
	
	/**
	 * Verifica se o identificador existe
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	public function exists($identifier)
	{
		if (!$this->isInitialized()) {
			throw new AppSessionException('Sessão ainda não iniciada');
		}
		
		return isset($this->sessionVar[$identifier]);
	}
	
	/**
	 * Verifica se o identificador possui um objeto
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	public function isObject($identifier)
	{
		return in_array($identifier, $this->getObjects());
	}
	
	/**
	 * Retorna o array de identificadores dos objetos armazenados
	 * 
	 * @return array
	 */
	protected function getObjects()
	{
		return isset($this->sessionVar[self::SESS_OBJECTS]) ? $this->sessionVar[self::SESS_OBJECTS] : array();
	}
	
	/**
	 * Remove a marcação de objeto
	 * 
	 * @param string $identifier
	 */
	protected function removeObject($identifier)
	{
		foreach ($this->getObjects() as $i => $name) {
			if ($identifier == $name) {
				unset($this->sessionVar[self::SESS_OBJECTS][$i]);
			}
		}
	}
	
	/**
	 * Adiciona a marcação de objeto a um identificador
	 * 
	 * @param string $identifier
	 */
	protected function addObject($identifier)
	{
		if (!isset($this->sessionVar[self::SESS_OBJECTS])) {
			$this->sessionVar[self::SESS_OBJECTS] = array();
		}
		
		if (!$this->isObject($identifier)) {
			$this->sessionVar[self::SESS_OBJECTS][] = $identifier;
		}
	}
	
	/**
	 * Verifica se o identificador é reservado ao sistema
	 * 
	 * @param string $identifier
	 * @return boolean
	 */
	protected function isReserved($identifier)
	{
		$reserved = array(self::SESS_OBJECTS);
		
		return in_array($identifier, $reserved);
	}
	
	/**
	 * Remove os dados pertencentes ao identificador
	 * 
	 * @param string $identifier
	 * @throws AppSessionException
	 */
	public function remove($identifier)
	{
		if (!$this->isInitialized()) {
			throw new AppSessionException('Dados não podem ser removidos, pois a sessão não foi iniciada');
		}
		
		if (!$this->exists($identifier)) {
			throw new AppSessionException('Identificador não encontrado');
		}
		
		if ($this->isReserved($identifier)) {
			throw new AppSessionException('Identificador reservado ao sistema');
		}
		
		if ($this->exists($identifier)) {
			if ($this->isObject($identifier)) {
				$this->removeObject($identifier);
			}
			
			$this->set($identifier, null);
			unset($this->sessionVar[$identifier]);
		}
	}
	
	/**
	 * Retorna os dados armazenados relacionados ao identificador
	 * 
	 * @param string $identifier
	 * @throws AppSessionException
	 */
	public function get($identifier)
	{
		if (!$this->isInitialized()) {
			throw new AppSessionException('Dados não podem ser buscados, pois a sessão não foi iniciada');
		}
		
		if (!$this->exists($identifier)) {
			throw new AppSessionException('Identificador não encontrado');
		}
		
		if ($this->isReserved($identifier)) {
			throw new AppSessionException('Identificador reservado ao sistema');
		}
		
		return $this->sessionVar[$identifier];
	}
	
	/**
	 * Armazena o valor, relacionando-o ao identificador
	 * 
	 * @param string $identifier
	 * @param mixed $value
	 * @throws AppSessionException
	 */
	public function set($identifier, $value)
	{
		if (!$this->isInitialized()) {
			throw new AppSessionException('Dados não podem ser armazenados, pois a sessão não foi iniciada');
		}
		
		if ($this->isReserved($identifier)) {
			throw new AppSessionException('Identificador reservado ao sistema');
		}
		
		if ($this->exists($identifier)) {
			$this->sessionVar[$identifier] = null;
		}
		
		if (is_object($value)) {
			$this->addObject($identifier);
		}
		
		$this->sessionVar[$identifier] = $value;
	}
	
	/**
	 * Retorna um array de nomes das sessões
	 * 
	 * @return array
	 */
	public function getSessionsByName()
	{
		return array_keys($this->sessionVar);
	}
}