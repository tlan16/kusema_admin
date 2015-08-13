<?php

Abstract class AccessControl
{
	private static $_cache;

	public static function canAccessDevelopingPage(Role $role)
	{
		switch($role->getId())
		{
			case Role::ID_SYSTEM_ADMIN:
				{
					return true;
				}
		}
		return false;
	}
}