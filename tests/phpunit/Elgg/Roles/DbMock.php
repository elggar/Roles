<?php

namespace Elgg\Roles;

use ElggRole;

class DbMock implements DbInterface {

	private $conf = array(
		'default' => array(
			'title' => 'roles:role:DEFAULT_ROLE',
			'extends' => array(),
			'permissions' => array(
				'actions' => array(
					'bar/foo' => 'allow',
					'baz' => array('rule' => 'deny', 'redirect' => 'bar/foo'),
				),
			),
		),
		'tester1' => array(
			'title' => 'tester1',
			'extends' => array('default'),
			'permissions' => array(
				'actions' => array(
					'foo/bar' => 'allow',
					'foo/bar/baz' => 'deny',
					'bar/foo' => ['rule' => 'deny']
				),
			),
		),
		'tester2' => array(
			'title' => 'tester2',
			'extends' => array('default'),
			'permissions' => array(
				'actions' => array(
					'foo/foo/bar' => 'allow',
					'bar/baz' => 'deny',
					'bar/foo' => 'deny',
				),
			),
		),
		'deny' => array(
			'title' => 'deny',
			'extends' => array(),
			'permissions' => array(
				'views' => array(
					'foo/bar' => 'deny',
				)
			)
		),
		'allow' => array(
			'title' => 'deny',
			'extends' => array('deny'),
			'permissions' => array(
				'views' => array(
					'foo/bar' => 'allow',
				)
			)
		),
		'extend' => array(
			'title' => 'extend',
			'extends' => array('deny'),
			'permissions' => array(
				'views' => array(
					'foo/bar' => array(
						'rule' => 'extend',
						'view_extension' => array(
							'view' => 'foo/baz',
							'priority' => 400,
						),
					),
				)
			)
		),
		'replace' => array(
			'title' => 'replace',
			'extends' => array('deny'),
			'permissions' => array(
				'views' => array(
					'foo/baz' => array(
						'rule' => 'replace',
						'view_replacement' => array(
							'location' => '/mod/roles/tests/phpunit/test_files/views2/',
						),
					),
				)
			)
		),
	);

	public function getAllRoles() {
		$roles = array();
		foreach (array_keys($this->conf) as $name) {
			$roles[] = $this->getRoleByName($name);
		}
		return $roles;
	}

	public function getRoleByName($role_name = '') {

		if (!isset($this->conf[$role_name])) {
			return false;
		}
		$conf = $this->conf[$role_name];
		$role = new ElggRole();
		$role->name = $role_name;
		$role->title = $conf['title'];
		$role->setExtends($conf['extends']);
		$role->setPermissions($conf['permissions']);

		return $role;
	}

	public function getUserRole(\ElggUser $user) {
		return $user->getVolatileData('role');
	}

	public function setUserRole(\ElggUser $user, ElggRole $role) {
		$user->setVolatileData('role', $role);
		return true;
	}

	public function unsetUserRole(\ElggUser $user) {
		$user->setVolatileData('role', null);
		return true;
	}

}
