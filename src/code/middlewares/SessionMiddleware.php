<?php

namespace code\middlewares;

use code\session\Cookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SessionHandlerInterface;

class SessionMiddleware implements MiddlewareInterface {

    /**
     * @var array
     */
    protected $settings;

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = []) {
        $defaults = [
            'lifetime' => '',
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => 'Lax',
            'name' => 'slim_session',
            'autorefresh' => false,
            'handler' => null,
            'ini_settings' => [],
        ];
        $settings = array_merge($defaults, $settings);

        if (!empty($settings['lifetime']) && is_string($lifetime = $settings['lifetime'])) {
            $settings['lifetime'] = strtotime($lifetime) - time();
        }
        $this->settings = $settings;

        $this->iniSet($settings['ini_settings']);
        // Just override this, to ensure package is working
        if (ini_get('session.gc_maxlifetime') < $settings['lifetime']) {
            $this->iniSet([
                'session.gc_maxlifetime' => $settings['lifetime'] * 2,
            ]);
        }
    }

    //put your code here
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $this->startSession();
        return $handler->handle($request);
    }

    /**
     * Start session
     */
    protected function startSession() {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $settings = $this->settings;
        $name = $settings['name'];

        Cookie::setup($settings);

        // Refresh session cookie when "inactive",
        // else PHP won't know we want this to refresh
        if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
            Cookie::set(
                    $name,
                    $_COOKIE[$name],
                    time() + $settings['lifetime'],
                    $settings
            );
        }

        session_name($name);

        $handler = $settings['handler'];
        if ($handler) {
            if (!($handler instanceof SessionHandlerInterface)) {
                $handler = new $handler();
            }
            session_set_save_handler($handler, true);
        }

        session_cache_limiter('');
        session_start();
    }

    protected function iniSet($settings) {
        foreach ($settings as $key => $val) {
            if (strpos($key, 'session.') === 0) {
                ini_set($key, $val);
            }
        }
    }

}
