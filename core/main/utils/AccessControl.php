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
			case Role::ID_SYSTEM_DEVELOPER:
			case Role::ID_FORUM_USER:
			case Role::ID_ADMIN_USER:
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