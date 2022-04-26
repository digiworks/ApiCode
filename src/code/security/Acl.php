<?php
namespace code\security;

Class Acl {

    private $db;
    private $user_empty = false;

    //initialize the database object here
    function __construct() {
        $this->db = new db;
    }

    function check($permission, $userid, $group_id) {

        //we check the user permissions first
        If (!$this->user_permissions($permission, $userid)) {
            return false;
        }

        if (!$this->group_permissions($permission, $group_id) & $this->IsUserEmpty()) {
            return false;
        }

        return true;
    }

    function user_permissions($permission, $userid) {
        $this->db->q("SELECT COUNT(*) AS count FROM user_permissions WHERE permission_name='$permission' AND userid='$userid' ");

        $f = $this->db->f();

        If ($f['count'] > 0) {
            $this->db->q("SELECT * FROM user_permissions WHERE permission_name='$permission' AND userid='$userid' ");

            $f = $this->db->f();

            If ($f['permission_type'] == 0) {
                return false;
            }

            return true;
        }
        $this->setUserEmpty('true');

        return true;
    }

    function group_permissions($permission, $group_id) {
        $this->db->q("SELECT COUNT(*) AS count FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

        $f = $this->db->f();

        if ($f['count'] > 0) {
            $this->db->q("SELECT * FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

            $f = $this->db->f();

            If ($f['permission_type'] == 0) {
                return false;
            }

            return true;
        }

        return true;
    }

    function setUserEmpty($val) {
        $this->userEmpty = $val;
    }

    function isUserEmpty() {
        return $this->userEmpty;
    }

}
