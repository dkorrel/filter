<?php

namespace Filter;

class FilterValidator {

	protected $operators = array(
		'>', '>=', '=', '<=', '<', 'in'
	);

	protected $validators = array(
		'required', 
		'operator'
	);

	public function __construct(array $filters)
	{
		$this->filters = $filters;
	}

	public function validate()
	{
		$validated = array();

		foreach( $this->filters as $filter )
		{
			$invalid = false;

			foreach( $this->validators as $validator )
			{
				$method = 'validate' . ucfirst($validator);
			
				if( method_exists($this, $method) ) 
				{
					$result = $this->{$method}($filter);

					if(! $result )
					{
						$invalid = true;

						break;
					}
				}
			}

			if( $invalid === false ) $validated[] = $filter;
		}

		return $validated;
	}

	protected function validateRequired(array $filter)
	{
		$required = array('key', 'value', 'operator');

		foreach( $required as $require )
		{
			if(! isset($filter[$require]) || ! $filter[$require] ) return false;
		}

		return $filter;
	}

	protected function validateOperator(array $filter)
	{
		if(! isset($filter['operator']) || ! $filter['operator'] ) return false;

		if(! in_array($filter['operator'], $this->operators) ) return false;

		return $filter;
	}

}