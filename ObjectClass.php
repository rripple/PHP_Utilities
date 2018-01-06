<?php

/**
 * Provide default object storage functionality utilising PHP magic methods
 */
class ObjectClass
{
	/**
	 * Stores properties in hash key => val
	 *
	 * Keys are in lowercase
	 *
	 * @var array
	 */
	protected $m_Data = [];

	/**
	 * Boolean if to convert keys to lowercase.
	 *
	 * False does NOT alter key case
	 *
	 * @var boolean Default true
	 */
	protected $m_bAlterKeyCase = true;

	function __construct( $params = null, $bAlterKeyCase = true )
	{
		$this->m_bAlterKeyCase = $bAlterKeyCase;

		if( $params && is_array( $params ) )
			$this->SetData( $params );
	}

	/**
	 * Magic function to get property from class
	 *
	 * @param string
	 * @return mixed
	 */
	function __get( $k )
	{
		if( $this->m_bAlterKeyCase )
			$k = strtolower( $k );

		if( isset( $this->m_Data[$k] ) )
			return $this->m_Data[$k];
	}

	/**
	 * Magic function to set property on object
	 *
	 * @param string
	 * @param mixed
	 * @return self
	 */
	function __set( $k, $v )
	{
		if( $this->m_bAlterKeyCase )
			$k = strtolower( $k );

		$this->m_Data[$k] = $v;

		return $this;
	}

	/**
	 * Magic function to see if property isset on object
	 *
	 * Returns false if key exists but is set to null
	 *
	 * @param string
	 * @return boolean
	 */
	function __isset( $k )
	{
		if( $this->m_bAlterKeyCase )
			$k = strtolower( $k );

		return isset( $this->m_Data[$k] );
	}

	/**
	 * Removes property from object
	 *
	 * @param string
	 * @return void
	 */
	function __unset( $k )
	{
		if( $this->m_bAlterKeyCase )
			$k = strtolower( $k );

		unset( $this->m_Data[$k] );
	}

	/**
	 * Returns boolean if key is defined in hash
	 *
	 * Difference - isset returns false if key exists but is null. This returns true
	 *
	 * @param mixed
	 * @return boolean
	 */
	function Has( $key )
	{
		return array_key_exists( $key, $this->m_Data );
	}

	/**
	 * Returns has of parameters
	 *
	 * @return array
	 */
	function GetData()
	{
		return $this->m_Data;
	}

	/**
	 * Returns parameter keys
	 *
	 * @return array
	 */
	function GetKeys()
	{
		return array_keys( $this->m_Data );
	}

	/**
	 * Returns value if key is set on object even if null
	 *
	 * @param string
	 * @param mixed
	 * @return mixed
	 */
	function GetKey( $key, $default = null )
	{
		if( $this->m_bAlterKeyCase )
			$key = strtolower( $key );

		return array_key_exists( $key, $this->m_Data ) ? $this->m_Data[$key] : $default;
	}

	/**
	 * Sets hash on object
	 *
	 * @param array
	 * @return void
	 */
	function SetData( Array $data = [] )
	{
		if( $this->m_bAlterKeyCase )
			ArrayKeyCase( $data );

		$this->m_Data = $data;
	}

	/**
	 * Allows to add an array of data to object overwriting any already exists
	 *
	 * @param array
	 * @return void
	 */
	function AddData( Array $data = [] )
	{
		if( $this->m_bAlterKeyCase )
			ArrayKeyCase( $data );

		$this->m_Data = array_replace( $this->m_Data, $data );
	}
}
