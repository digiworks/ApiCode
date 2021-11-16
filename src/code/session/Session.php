<?php

namespace code\session;

use ArrayAccess;
use ArrayIterator;
use code\service\ServiceInterface;
use Countable;
use IteratorAggregate;
use Traversable;

class Session implements SessionInterface, ServiceInterface, ArrayAccess, Countable, IteratorAggregate {

    private $isSessionActive = false;

    public static function is_session_started() {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    public function get($key, $defualt = null) {
        $result = $default;

        if ($this->isSessionActive) {
            if ($this->exists($key)) {
                $result = $_SESSION[$key];
            }
        }
        return $result;
    }

    public function set($key, $value) {
        if ($this->isSessionActive) {
            $_SESSION[$key] = $value;
        }
        return $this;
    }

    public function init() {
        $this->isSessionActive = static::is_session_started();
    }

    /**
     * 
     * @param type $key
     * @param type $value
     * @return type
     */
    public function merge($key, $value) {
        if (is_array($value) && is_array($old = $this->get($key))) {
            $value = array_merge_recursive($old, $value);
        }

        return $this->set($key, $value);
    }

    /**
     * Delete a session variable.
     *
     * @param string $key
     *
     * @return $this
     */
    public function delete($key) {
        if ($this->isSessionActive) {
            if ($this->exists($key)) {
                unset($_SESSION[$key]);
            }
        }
        return $this;
    }

    /**
     * Clear all session variables.
     *
     * @return $this
     */
    public function clear() {
        $_SESSION = [];

        return $this;
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key) {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public static function id($new = false) {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }

        return session_id() ?: '';
    }

    /**
     * Destroy the session.
     */
    public static function destroy() {
        if (self::id()) {
            session_unset();
            session_destroy();
            session_write_close();

            if (ini_get('session.use_cookies')) {
                Cookie::set(
                        session_name(),
                        '',
                        time() - 4200,
                        session_get_cookie_params()
                );
            }
        }
    }

    /**
     * Magic method for get.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Magic method for set.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Magic method for delete.
     *
     * @param string $key
     */
    public function __unset($key) {
        $this->delete($key);
    }

    /**
     * Magic method for exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key) {
        return $this->exists($key);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count() {
        $count = 0;
        if ($this->isSessionActive) {
            $count = count($_SESSION);
        }
        return $count;
    }

    /**
     * Retrieve an external Iterator.
     *
     * @return Traversable
     */
    public function getIterator() {
        return new ArrayIterator($_SESSION);
    }

    /**
     * Whether an array offset exists.
     *
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset) {
        return $this->exists($offset);
    }

    /**
     * Retrieve value by offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /**
     * Set a value by offset.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    /**
     * Remove a value by offset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        $this->delete($offset);
    }

}
