<?php

	/**
	* phpGroupWare - logistic: a part of a Facilities Management System.
	*
	* @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	* @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/
	* @package property
	* @subpackage logistic
 	* @version $Id$
	*/

	phpgw::import_class('logistic.socommon');

	include_class('logistic', 'requirement_resource_allocation', 'inc/model/');

	class logistic_sorequirement_resource_allocation extends logistic_socommon
	{
		protected static $so;
		private $local_db;

		public function __construct()
		{
			parent::__construct();
			$this->local_db = clone $this->db;
		}

		protected function add(&$resource_alloc)
		{
			$this->db->transaction_begin();

			$value_set = array
			(
				'location_id'		=> $resource_alloc->get_location_id(),
				'item_id'			=> $this->marshal($resource_alloc->get_resource_id(), 'int'),
				'allocation_id'		=> 0,//not known yet
				'create_user'		=> $resource_alloc->get_create_user(),
				'create_date'		=> time(),
				'start_date'		=> $resource_alloc->get_start_date(),
				'end_date'			=> $resource_alloc->get_end_date(),
			);

			$sql = 'INSERT INTO lg_calendar (' . implode(',', array_keys($value_set)) . ') VALUES (' . implode(',', array_values($value_set)) . ')';
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				$calendar_id = $this->db->get_last_insert_id('lg_calendar', 'id');
			}
			else
			{
				return 0;
			}

			$value_set = array
			(
				'requirement_id'	=> $this->marshal($resource_alloc->get_requirement_id(), 'int'),
				'resource_id'		=> $this->marshal($resource_alloc->get_resource_id(), 'int'),
				'location_id'		=> $this->marshal($resource_alloc->get_location_id(), 'int'),
	//			'start_date'		=> $this->marshal($resource_alloc->get_start_date(), 'int'),
	//			'end_date'			=> $this->marshal($resource_alloc->get_end_date(), 'int'),
				'create_user'		=> $this->marshal($resource_alloc->get_create_user(), 'int'),
				'create_date'		=> time(),
				'calendar_id'		=> $calendar_id
			);

			$sql = 'INSERT INTO lg_requirement_resource_allocation (' . implode(',', array_keys($value_set)) . ') VALUES (' . implode(',', array_values($value_set)) . ')';
			$result = $this->db->query($sql, __LINE__,__FILE__);

			if($result)
			{
				$allocation_id =  $this->db->get_last_insert_id('lg_requirement_resource_allocation', 'id');
				$this->db->query("UPDATE lg_calendar SET allocation_id = {$allocation_id} WHERE id = {$calendar_id}", __LINE__,__FILE__);
			}
			else
			{
				$allocation_id = 0;
			}

			$this->db->transaction_commit();

			return $allocation_id;
		}

		protected function update($resource_alloc)
		{
			$id = intval($resource_alloc->get_id());
		
			$values = array(
				'requirement_id=' . $this->marshal($resource_alloc->get_requirement_id(), 'string'),
				'resource_id=' . $this->marshal($resource_alloc->get_resource_id(), 'string'),
				'location_id=' . $this->marshal($resource_alloc->get_location_id(), 'int')
			);

			$result = $this->db->query('UPDATE lg_requirement_resource_allocation SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

			if($result)
			{
				return $id;
			}
			else
			{
				return 0;
			}
		}

		protected function get_id_field_name()
		{
			if(!$extended_info)
			{
				$ret = 'id';
			}
			else
			{
				$ret = array
				(
					'table'			=> 'lg_requirement_resource_allocation', // alias
					'field'			=> 'id',
					'translated'	=> 'id'
				);
			}

			return $ret;
		}

		protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
		{
			$clauses = array('1=1');

			$filter_clauses = array();
			if(isset($filters[$this->get_id_field_name()]))
			{
				$filter_clauses[] = "allocation.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
			}
			if(isset($filters['location_id']))
			{
				$filter_clauses[] = "allocation.location_id = {$this->marshal($filters['location_id'],'int')}";
			}
			if(isset($filters['requirement_id']))
			{
				$filter_clauses[] = "allocation.requirement_id = {$this->marshal($filters['requirement_id'],'int')}";
			}

			if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}

			$condition =  join(' AND ', $clauses);


			$tables = "lg_requirement_resource_allocation allocation";
			$joins .= "	{$this->left_join} phpgw_locations ON (phpgw_locations.location_id = allocation.location_id)";
			$joins .= "	{$this->left_join} fm_bim_item ON (fm_bim_item.location_id = allocation.location_id and fm_bim_item.id = allocation.resource_id )";
			 
			if($return_count)
			{
				$cols = 'COUNT(DISTINCT(allocation.id)) AS count';
			}
			else
			{
				$cols .= "allocation.*, ";
				$cols .= "phpgw_locations.descr AS resource_type_descr, ";
				$cols .= "fm_bim_item.location_code AS location_code, (xpath('//address/node()', fm_bim_item.xml_representation))[1]::text AS fm_bim_item_address, (xpath('//navn[1]/text()', fm_bim_item.xml_representation))[1]::text AS fm_bim_item_name ";
			}
						
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
			
			return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
		}

		protected function populate(int $allocation_id, &$allocation)
		{
			if($allocation == null)
			{
				$allocation = new logistic_requirement_resource_allocation((int) $allocation_id);

				$allocation->set_location_id($this->unmarshal($this->db->f('location_id'), 'int'));
				$allocation->set_requirement_id($this->unmarshal($this->db->f('requirement_id'), 'string'));
				$allocation->set_resource_id($this->unmarshal($this->db->f('resource_id'), 'int'));
				$allocation->set_resource_type_descr($this->unmarshal($this->db->f('resource_type_descr'), 'string'));
				$allocation->set_location_code($this->unmarshal($this->db->f('location_code'), 'string'));
				$allocation->set_fm_bim_item_address($this->unmarshal($this->db->f('fm_bim_item_address'), 'string'));
				
				$fm_bim_name = $this->unmarshal( $this->db->f('fm_bim_item_name'), 'string');
				
				$allocation->set_fm_bim_item_name($fm_bim_name);
			}

			return $allocation;
		}
		

		/**
		* Finds allocated items within timespan
		*
		*/
		public function check_calendar($location_id = 0, $ids = array(), $start_date, $end_date)
		{
			$location_id = (int)$location_id;
			$values = array();
			$items = array();
			
			if (!$ids)
			{
				return $values;
			}
			$sql = "SELECT lg_calendar.item_id, lg_requirement.activity_id, lg_calendar.allocation_id, lg_calendar.start_date, lg_calendar.end_date"
			. " FROM lg_calendar"
			. " {$this->join} lg_requirement_resource_allocation ON lg_requirement_resource_allocation.id = lg_calendar.allocation_id"
			. " {$this->join} lg_requirement ON lg_requirement_resource_allocation.requirement_id = lg_requirement.id"
			. " WHERE lg_calendar.location_id = {$location_id}"
			. " AND lg_calendar.item_id IN (" . implode(',', $ids) . ')'
			. " AND lg_calendar.end_date >= {$start_date} AND lg_calendar.start_date <= {$end_date}";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$item_id = $this->db->f('item_id');
				$items[$item_id] = true;
				$values[$item_id][] = array
				(
					'start_date'	=> $this->db->f('start_date'),
					'end_date'		=> $this->db->f('end_date'),
					'activity_id'	=> $this->db->f('activity_id'),
					'allocation_id' => $this->db->f('allocation_id'),
					'item_id'		=> $item_id
				);
			}
			return array('calendar' => $values, 'items' => $items);
		}

		function delete($resource_allocation_id)
		{
			$this->db->transaction_begin();

			$resource_allocation_id = (int) $resource_allocation_id;
			$this->db->query("DELETE FROM lg_requirement_resource_allocation WHERE id = $resource_allocation_id");
			$this->db->query("DELETE FROM lg_calendar WHERE allocation_id = $resource_allocation_id");

			return !!$this->db->transaction_commit();
		}
		
		public function delete_resources($requirement_id)
		{
			
			echo "i delete_resources: " . $requirement_id;
			
			$this->db->transaction_begin();

			$requirement_id = (int) $requirement_id;
			$this->db->query("SELECT id FROM lg_requirement_resource_allocation WHERE requirement_id = $requirement_id",__LINE__,__FILE__);
			$ids = array();
			while ($this->db->next_record())
			{
				$ids[] = $this->db->f('id');
			}
			
			if($ids)
			{
				$this->db->query("DELETE FROM lg_calendar WHERE allocation_id = IN ( " . explode(',', $id) . ')',__LINE__,__FILE__);
			}
			$this->db->query("DELETE FROM lg_requirement_resource_allocation WHERE requirement_id = $requirement_id",__LINE__,__FILE__);

			return !!$this->db->transaction_commit();
		}

		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('logistic.sorequirement_resource_allocation');
			}
			return self::$so;
		}
	}
