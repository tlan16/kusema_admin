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
	public static function canAccessQuestionListingPage(Role $role)
	{
		switch($role->getId())
		{
			default:
				{
					return true;
				}
		}
		return false;
	}
	public static function canAccessQuestionDetailsPage(Role $role)
	{
		switch($role->getId())
		{
			default:
				{
					return true;
				}
		}
		return false;
	}
	public static function canEditQuestionDetailsPage(Role $role)
	{
		switch($role->getId())
		{
			case Role::ID_SYSTEM_DEVELOPER:
			case Role::ID_ADMIN_USER:
			case Role::ID_SYSTEM_ADMIN:
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
			case Role::ID_ADMIN_USER:
			case Role::ID_SYSTEM_ADMIN:
				{
					return true;
				}
		}
		return false;
	}
}