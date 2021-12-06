<?php

namespace code\security\permissions;

use models\UserPermissionsQuery;

class AclPermissionProvider extends PermissionProvider {

    public function check($permission, $userid) {
        //we check the user permissions first
        If (!$this->user_permissions($userid, $permission)) {
            return false;
        }

        if (!$this->group_permissions($userid, $permission) & $this->IsUserEmpty()) {
            return false;
        }

        return true;
    }

    function user_permissions($userid, $permission) {
        $is_permitted = false;
        $query = new UserPermissionsQuery();
        if ($query->countPermission($userid, $permission)) {
            $is_permitted = true;
        }

        return $is_permitted;
    }

    function group_permissions($user_id, $permission) {
        $is_permitted = false;

        return $is_permitted;
    }

}
