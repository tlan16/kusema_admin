<?php

Abstract class AccessControl
{
	private static $_cache;
	public static function canAccessDevelopingPage(Role $role)
	{
		switch($role->getId())
		{
			case Role::ID_SYSTEM_DEVELOPER:
				{
					return true;
				}
		}
		return false;
	}
	public static function canAccessUserPage(Role $role)
	{
		switch($role->getId())
		{
			case Role::ID_SYSTEM_DEVELOPER:
				{
					return true;
				}
		}
		return false;
	}
}