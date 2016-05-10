<?php

namespace Filter;

class Filter {

	protected $filters;

	protected $operatorMethods = array(
		'>' => 'greaterThan', 
		'>=' => 'greaterThanOrEqualTo', 
		'=' => 'equalTo', 
		'<=' => 'lessThanOrEqualTo', 
		'<' => 'lessThan', 
		'in' => 'in'
	);

	public function __construct(array $filters, array $data)
	{	
		/*	
		* @PARAM array $filters contains keys, operators and values
		* - See $this->validateFilters() for examples
		* @PARAM array $data data that needs to be filtered
		* - For now only the top level will be filtered
		*/
		
		$this->filters = $this->validateFilters($filters);

		$this->data = $data;
	}

	protected function validateFilters(array $filters)
	{
		/*
		* @PARAM array $filters examples
		* array('key' => 'price', 'value' => 100, 'operator' => '>');
		* array('key' => 'price', 'value' => 200, 'operator' => '<=');
		* array('key' => 'location', 'value' => 'Amsterdam', 'operator' => '=');
		* array('key' => 'category', 'value' => ['Music', 'Festival'], 'operator' => 'in');
		*/

		$validator = new FilterValidator($filters);

		return $validator->validate();
	}

	public function filterData()
	{
		// Method can be changed for unlimited depth. For now just the first level is filtered
		if( count($this->data) === 0 || count($this->filters) === 0 ) return $this->data;

		$filtered = array();

		foreach( $this->data as $entry )
		{
			if( $this->applyFilters($entry) ) $filtered[] = $entry;
		}

		return $filtered;
	}

	protected function applyFilters(array $entry)
	{
		foreach( $this->filters as $filter )
		{
			// If entry doesn't have the $filter['key'] attribute at all
			if(! isset($entry[$filter['key']]) ) return false;

			$method = $this->operatorMethods[$filter['operator']];

			if( method_exists($this, $method) )
			{
				if(! $this->{$method}($filter['value'], $entry[$filter['key']]) ) return false;
			}
		}

		return $entry;
	}

	protected function greaterThan($value, $entryValue)
	{
		return $entryValue > $value;
	}

	protected function greaterThanOrEqualTo($value, $entryValue)
	{
		return $entryValue >= $value;
	}

	protected function equalTo($value, $entryValue)
	{
		return $value === $entryValue;
	}

	protected function lessThanOrEqualTo($value, $entryValue)
	{
		return $entryValue <= $value;
	}

	protected function lessThan($value, $entryValue)
	{
		return $entryValue < $value;
	}

	protected function in($values, $entry)
	{
		if(! is_array($values) ) $values = array($values);

		if( in_array($entry, $values) ) return $entry;

		return false;
	}
	
}