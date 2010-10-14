<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana 3 port of the Zend Framework Dom Query
 * library. More information to follow...
 * 
 * Original code copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * 
 * Adapted for Kohana 3 by Karol Janyst <lapkom@gmail.com>
 *
 * @package Dom Query
 * @copyright (c) 2010 LapKom Karol Janyst
 * @license ISC License http://www.opensource.org/licenses/isc-license.txt
 */
class Dom_Query_Result implements Iterator,Countable {
	
	protected $_document;
	
	protected $_query;
	
	protected $_xpath_query;
	
	protected $_node_list;
	
	protected $_position = 0;
	
	public function __construct($query, $xpath_query, DOMDocument $dom_document, DOMNodeList $node_list) {
		$this->_query = $query;
		$this->_xpath_query = $xpath_query;
		$this->_document = $dom_document;
		$this->_node_list = $node_list;
	}
	
	public function get_query() {
		return $this->_query;
	}
	
	public function get_xpath_query() {
		return $this->_xpath_query;		
	}
	
	public function get_document() {
		return $this->_document;				
	}
	
	public function rewind() {
		$this->_position = 0;
		return $this->_node_list->item(0);
	}

	public function valid() {
		if (in_array($this->_position, range(0, $this->_node_list->length - 1)) && $this->_node_list->length > 0) {
			return true;
		}
		return false;
	}

	public function current() {
		return $this->_node_list->item($this->_position);
	}

	public function key() {
		return $this->_position;
	}

	public function next() {
		++$this->_position;
		return $this->_node_list->item($this->_position);
	}

    public function count()
    {
        return $this->_node_list->length;
    }
	
}
