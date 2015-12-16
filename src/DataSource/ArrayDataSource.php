<?php

/**
 * @copyright   Copyright (c) 2015 ublaboo <ublaboo@paveljanda.com>
 * @author      Pavel Janda <me@paveljanda.com>
 * @package     Ublaboo
 */

namespace Ublaboo\DataGrid\DataSource;

use DibiFluent,
	Ublaboo\DataGrid\Filter\Filter,
	Nette\Utils\Callback,
	Nette\Utils\Strings;

class ArrayDataSource
{

	/**
	 * @var array
	 */
	protected $data = [];


	public function __construct($data_source)
	{
		$this->data = $data_source;
	}


	/********************************************************************************
	 *                          IDataSource implementation                          *
	 ********************************************************************************/


	/**
	 * @return int
	 */
	public function getCount()
	{
		return sizeof($this->data);
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}


	/**
	 * @param array $filters
	 */
	public function filter(array $filters)
	{
		foreach ($filters as $filter) {
			if ($filter->isValueSet()) {
				$or = [];

				if ($filter->hasConditionCallback()) {
					$this->data = Callback::invokeArgs(
						$filter->getConditionCallback(),
						[$this->data, $filter->getValue()]
					);
				} else {
					$this->data = array_filter($this->data, function($row) use ($filter) {
						return $this->applyFilter($row, $filter);
					});
				}
			}
		}
		
		return $this;
	}


	/**
	 * @param array $filter
	 * @return void
	 */
	public function filterOne(array $filter)
	{
		$this->data = array_filter($this->data, function($row) use ($filter) {
			return $this->applyFilter($row, $filter);
		});

		$this->data = $this->data ? reset($this->data) : [];

		return $this;
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->data = array_slice($this->data, $offset, $limit);

		return $this;
	}

	/**
	 * @param array $sorting
	 */
	public function sort(array $sorting)
	{
		/**
		 * Taken from Grido
		 * @todo Not tested yet
		 */
		if (sizeof($sorting) > 1) {
			throw new Ublaboo\DataGrid\DataGridException('Multi-column sorting is not implemented yet.');
		}

		foreach ($sorting as $column => $sort) {
			$data = array();
			foreach ($this->data as $item) {
				$sorter = (string) $item[$column];
				$data[$sorter][] = $item;
			}

			if ($sort === 'ASC') {
				ksort($data);
			} else {
				krsort($data);
			}

			$this->data = array();
			foreach($data as $i) {
				foreach($i as $item) {
					$this->data[] = $item;
				}
			}
		}

		return $this;
	}


	/**
	 * Apply fitler and tell whether row passes conditions or not
	 * @param  mixed  $row
	 * @param  Filter $filter
	 * @return mixed
	 */
	protected function applyFilter($row, Filter $filter)
	{
		if (is_array($row) || $row instanceof \Traversable) {
			foreach ($row as $key => $value) {
				if (FALSE !== strpos(Strings::toAscii($value), Strings::toAscii($filter->getValue()))) {
					return $row;
				}
			}
		}

		return FALSE;
	}

}
