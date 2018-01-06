<?php

/**********************************************************
 * Array|Hash Functions
 *********************************************************/

/**
 * Hash returns if the php array is an assoc array or hash.
 *
 * @param Array $o
 * @return boolean
 */
static function ArrayIsHash( $o )
{
	if( !is_array( $o ) )
		return false;

	if( !count( $o ) )
		return true;

	foreach( $o as $k => $v )
	{
		if( !is_numeric( $k ) )
			return true;
	}

	return false;
}

/**
 * Takes an XML string and returns an array
 *
 * @param string
 * @return array
 */
function XMLtoArray( $xmlstring )
{
	$parser = xml_parser_create();

	xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
	xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
	xml_parse_into_struct( $parser, $xmlstring, $val, $idx );
	xml_parser_free( $parser );

	$xml = XMLtoArray_BuildArray( $val );

	return $xml;
}

function XMLtoArray_BuildArray( &$nodelist )
{
	$done = false;
	$rc = [];

	while( !$done && ( $node = next( $nodelist ) ) !== false )
	{
		switch( $node['type'] )
		{
			case 'open':
				$value = XMLtoArray_BuildArray( $nodelist );

				if( isset( $rc[$node['tag']] ) )
				{
					if( !is_array( $rc[$node['tag']] ) )
						$rc[$node['tag']] = [ $rc[$node['tag']] ];

					$rc[$node['tag']][] = $value;
				}
				else
				{
					$rc[$node['tag']] = $value;
				}
				break;

			case 'close':
				$done = true;
				break;

			case 'complete':
				$rc[$node['tag']] = $node['value'];
			// trim( htmlspecialchars_decode( urldecode( (string)$item ) ) )
				break;
		}
	}

	return $rc;
}

/**
 * Takes an array and makes XML
 *
 * @param array
 * @return string
 */
function ArrayToXML( &$arr )
{
	$xml =
		"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<phparray>" .
		ArrayToXML_BuildXML( $arr ) .
		"\n</phparray>";

	return $xml;
}

/**
 * Private function to ArrayToXML
 */
function ArrayToXML_BuildXML( &$arr )
{
	$crlf = "\n";
	$xml = '';

	if( is_array( $arr ) )
	{
		foreach( $arr as $key => $val )
		{
			if( is_array( $val ) )
			{
				if( ArrayIsHash( $val ) )
				{
					$xml .= "{$crlf}<{$key}>" . ArrayToXML_BuildXML( $val ) . "{$crlf}</{$key}>";
				}
				else
				{
					foreach( $val as $key2 => $val2 )
					{
						$xml .= "{$crlf}<{$key}>" . ArrayToXML_BuildXML( $val2 ) . "</{$key}>";
					}
				}
			}
			else
			{
				$xml .= "{$crlf}<{$key}>" . htmlspecialchars( $val, ENT_SUBSTITUTE | ENT_XML1 ) . "</{$key}>";
			}
		}
	}
	else
		return htmlspecialchars( $arr, ENT_SUBSTITUTE | ENT_XML1 );

	return $xml;
}

/**
 * Takes an array and outputs a JSON object
 *
 * @param array $data
 * @return string
 */
function ArrayToObject( &$data )
{
	$fields = "{";

	if( is_array( $data ) )
	{
		foreach( $data as $k => $v )
		{
			if( is_array( $v ) )
				$v = ArrayToObject( $v );
			else# if( !is_numeric( $v ) ) // string? add slashes and outer quotes
				$v = "\"" . addslashes( $v ) . "\""; // rawurlencode, what about CRLF?

			// not the first one, add seperator
			if( $fields != "{" )
				$fields .= ",";

			$fields .= "{$k}:{$v}";
		}
	}

	$fields .= "}";

	return $fields;
}

/**
 * Converts an array into a nvp string
 *
 * @return string
 */
function ArrayToNVP( &$arr, $sep = '&' )
{
	// format is KEY=VAL or KEY[LEN]=VAL
	$nvp = "";

	foreach( $arr as $key => $val )
	{
		$len = strlen( $val );

		if( strlen( $nvp ) )
			$nvp .= $sep;

		if( strpbrk( $val, "{$sep}=" ) !== false )
			$key = "{$key}[" . strlen( $val ) . "]";

		$nvp .= "{$key}={$val}";
	}

	return $nvp;
}

/**
 * Converts a nvp string into an array
 *
 * @return array
 */
function NVPToArray( &$nvp, $sep = '&' )
{
	// normally this would be simple to split on & then on =, but since the value may
	// contain one of these characters we should be more careful
	// format is KEY=VAL or KEY[LEN]=VAL

	$arr = [];

	$beg = $end = 0;

	$len = strlen( $nvp );

	while( $beg < $len )
	{
		$end = strpos( $nvp, '=', $beg );
		if( $end === false || $end < $beg )
			break;
		$key = substr( $nvp, $beg, $end - $beg );
		$beg = $end + 1;
		// check for key[length], if no match found .. bad!
		if( !preg_match( '/^([^\[\]]+)(\[(\d+)\])?$/', $key, $matches ) )
			break;
		if( isset( $matches[3] ) )
		{
			$end = $beg + $matches[3];
			$key = $matches[1];
		}
		else
		{
			$end = strpos( $nvp, $sep, $beg );
			if( $end === false )
				$end = $len;
		}
		$val = substr( $nvp, $beg, $end - $beg );
		$beg = $end + 1;
		$arr[$key] = $val;
	}

	return $arr;
}

function ArrayMerge( $array1, $array2 )
{
	foreach( $array2 as $key => $val )
	{
		if( !array_key_exists( $key, $array1 ) || !is_array( $val ) )
		{
			$array1[$key] = $val;
		}
		else
		{
			$array1[$key] = ArrayMerge( $array1[$key], $val );
		}
	}

	return $array1;
}

function ArrayKeyCase( &$arr, $nCase = CASE_LOWER, $bRecurrsive = true )
{
	$arr = array_change_key_case( $arr, $nCase );

	if( $bRecurrsive )
	{
		foreach( $arr as $k => $v )
		{
			// Do not alter "values/keys" in a data set
			if( $k == 'data' )
				continue;

			if( is_array( $v ) )
			{
				ArrayKeyCase( $arr[$k], $nCase, $bRecurrsive );
			}
		}
	}
}
