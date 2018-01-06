<?php

/**
 * Provides object to assemble a string
 *
 * Will insert single space between each string added.
 */
class StrObj
{
	/** @var array */
	private $m_SubStrs = [];

	/**
	 * constructor
	 *
	 * @param mixed $s (optional)
	 * @return self
	 */
	public function __construct( $s = null )
	{
		if( !is_null( $s ) )
		{
			if( is_array( $s ) )
			{
				$this->m_SubStrs = $s;
			}
			else
			{
				$this->m_SubStrs = explode( ' ', $s );
			}
		}
	}

	/**
	 * Magic function to return string value
	 *
	 * @return string
	 */
	public function __toString()
	{
		if( count( $this->m_SubStrs ) )
		{
			return $this->GetString();
		}

		return '';
	}

	/**
	 * Appends string to object
	 *
	 * @param array|string
	 * @return self
	 */
	public function Append( $s )
	{
		if( is_array( $s ) )
		{
			foreach( $s as $sub )
				$this->Append( $sub );

			return $this;
		}

		$this->m_SubStrs[] = trim( $s );

		return $this;
	}

	/**
	 * Appends string to object and appends new line terminator
	 *
	 * @param string
	 * @return self
	 */
	 public function AppendLine( string $s )
	{
		$this->m_SubStrs[] = trim( $s ) . "\n";

		return $this;
	}

	/**
	 * Clears string object
	 */
	public function Clear()
	{
		$this->m_SubStrs = [];

		return $this;
	}

	/**
	 * Inserts a string at an indexed position
	 *
	 * @param string
	 * @param integer
	 * @return self
	 */
	public function Insert( string $s, int $i = 0 )
	{
		$str = $this->GetString();

		$this->Clear();

		$str = trim( substr( $str, 0, $i ) ) . " {$s} " . trim( substr( $str, $i ) );

		$this->m_SubStrs = explode( ' ', $str );

		return $this;
	}

	/**
	 * Removes a block of characters within the string
	 *
	 * @param integer
	 * @param integer
	 * @return self
	 */
	public function Remove( int $i, int $l )
	{
		$str = $this->GetString();

		$str = substr( $str, 0, $i ) . substr( $str, $i + $l );

		$this->m_SubStrs = explode( ' ', $str );

		return $this;
	}

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @param mixed
	 * @param mixed
	 * @return self
	 */
	public function Replace( $a, $b )
	{
		foreach( $this->m_SubStrs as &$str )
			$str = str_replace( $a, $b, $str );

		return $this;
	}

	/**
	 * @return integer ( Returns < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal )
	 */
	public function CompareTo( StrObj $b )
	{
		return strcmp( "{$this}", "{$b}" );
	}

	/**
	 * Quotes string and escapes [ ', ", `, NUL ] characters. Does not modify string
	 *
	 * @return string
	 */
	public function Escape()
	{
		return addslashes( $this->GetString() );
	}

	/**
	 * Unquotes string and unescapes [ ', ", `, NUL ] characters. Does not modify string
	 *
	 * @return string
	 */
	public function Unescape()
	{
		return stripslashes( $this->GetString() );
	}

	/**
	 * Returns string length
	 */
	public function GetLength()
	{
		return mb_strlen( $this->GetString() );
	}

	/**
	 * Returns the string
	 */
	public function GetString()
	{
		return implode( ' ', $this->m_SubStrs );
	}

	/**
	 * Returns word count of string
	 *
	 * @return integer
	 */
	public function GetWordCount()
	{
		return str_word_count( $this->GetString(), 0 );
	}

	/**
	 * Returns string less any tags removed. Does NOT modify original string
	 *
	 * @param string
	 * @return string
	 */
	public function StripTags( string $tags = null )
	{
		return strip_tags( $this->GetString(), $tags );
	}

	/**
	 * Returns string in lowercase. Does NOT modify original string
	 *
	 * @return string
	 */
	public function ToLower()
	{
		return strtolower( $this->GetString() );
	}

	/**
	 * Returns string in uppercase. Does NOT modify original string
	 *
	 * @return string
	 */
	public function ToUpper()
	{
		return strtoupper( $this->GetString() );
	}
}
