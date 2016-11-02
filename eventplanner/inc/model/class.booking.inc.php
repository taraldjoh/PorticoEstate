<?php
	/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package eventplanner
	 * @subpackage booking
	 * @version $Id: $
	 */

	phpgw::import_class('eventplanner.bobooking');

	include_class('phpgwapi', 'model', 'inc/model/');

	class eventplanner_booking extends phpgwapi_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;
		const acl_location = '.booking';

		protected
			$id,
			$active,
			$completed,
			$cost,
			$from_,
			$to_,
			$application_id,
			$application_name,
			$customer_id,
			$customer_name,
			$created,
			$secret;

		protected $field_of_responsibility_name = '.booking';

		public function __construct( int $id = null )
		{
			parent::__construct((int)$id);
			$this->field_of_responsibility_name = self::acl_location;
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			return new eventplanner_booking();
		}

		public static function get_status_list()
		{
			return array(
				self::STATUS_REGISTERED => lang('registered'),
				self::STATUS_PENDING	=> lang('pending'),
				self::STATUS_REJECTED => lang('rejected'),
				self::STATUS_APPROVED	=> lang('approved')
			);
		}

		public static function get_fields($debug = true)
		{
			 $fields = array(
				'id' => array('action'=> PHPGW_ACL_READ,
					'type' => 'int',
					'label' => 'id',
					'sortable'=> true,
					'formatter' => 'JqueryPortico.formatLink',
					),
				'active' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'history'	=> true
					),
				'completed' => array('action'=>  PHPGW_ACL_EDIT,
					'type' => 'int',
					'history'	=> true
					),
				'cost' => array('action'=>  PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'decimal'
					),
				'from_' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'	=> 'from',
					'history' => true,
					'required' => true,
					),
				'to_' => array('action'=> PHPGW_ACL_READ | PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'date',
					'label'	=> 'to',
					'history' => true,
					'required' => true,
				),
				'application_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'application',
					'sortable' => true,
					'required' => true,
					),
				'application_name' => array('action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'application',
					'join' => array(
						'table' => 'eventplanner_application',
						'fkey' => 'application_id',
						'key' => 'id',
						'column' => 'title'
						)
					),
				'customer_id' => array('action'=> PHPGW_ACL_ADD | PHPGW_ACL_EDIT,
					'type' => 'int',
					'label' => 'customer',
					'sortable' => true,
					'history' => true,
					),
				'customer_name' => array('action'=>  PHPGW_ACL_READ,
					'type' => 'string',
					'query' => true,
					'label' => 'customer',
					'join' => array(
						'table' => 'eventplanner_customer',
						'fkey' => 'customer_id',
						'key' => 'id',
						'column' => 'name'
						)
					),
				'created' => array('action'=> PHPGW_ACL_READ,
					'type' => 'date',
					'label' => 'created',
					'sortable' => true,
					),
				'secret' => array('action'=> PHPGW_ACL_ADD,
					'type' => 'string',
					'label' => 'secret',
					'sortable' => false,
					),
				);

			if($debug)
			{
				foreach ($fields as $field => $field_info)
				{
					if(!property_exists('eventplanner_booking', $field))
					{
					   phpgwapi_cache::message_set('$'."{$field},", 'error');
					}

				}
			}
			return $fields;
		}

		/**
		 * Implement in subclasses to perform actions on entity before validation
		 */
		protected function preValidate( &$entity )
		{
			if (!empty($entity->comment))
			{
				$entity->comment_input = array(
					'time' => time(),
					'author' => $GLOBALS['phpgw_info']['user']['fullname'],
					'comment' => $entity->comment,
					'type' => 'comment'
				);
			}

			$entity->modified = time();
			$entity->active = (int)$entity->active;

			if($entity->get_id())
			{
			}
			else
			{
				$entity->status = eventplanner_booking::STATUS_REGISTERED;
				$entity->secret = self::generate_secret();
			}
		}

		protected function generate_secret( $length = 10 )
		{
			return substr(base64_encode(rand(1000000000, 9999999999)), 0, $length);
		}

		public function serialize()
		{
			return self::toArray();
		}

		public function store()
		{
			return eventplanner_bobooking::get_instance()->store($this);
		}

		public function read_single($id)
		{
			return eventplanner_bobooking::get_instance()->read_single($id, true);
		}
	}