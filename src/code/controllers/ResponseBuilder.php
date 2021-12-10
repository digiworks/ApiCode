<?php

namespace code\controllers;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder {

    /**
     * 
     * @var ResponseInterface $response
     */
    private $response = null;

    /**
     * @var int Security level to be set for X-Frame-Options.
     * 3 = DENY
     * 2 = SAMEORIGIN
     * 1 = ALLOW-FROM $frameAllowFrom
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-Frame-Options
     */
    public $frameLevel = 3;

    /**
     * @var string URI for X-Frame-Options ALLOW-FROM.
     */
    public $frameAllowFrom = '';

    /**
     * @var int Security level to be set for X-XSS-Protection.
     * 3 = filter enabled with report=$xssReport
     * 2 = filter enabled with mode=block
     * 1 = filter enabled
     * 0 = filter disabled
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-XSS-Protection
     */
    public $xssLevel = 2;

    /**
     * @var string URI for X-XSS-Protection report.
     */
    public $xssReport = '';

    /**
     * @var int Number of seconds browser should remember that this site is only to be accessed using HTTPS.
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#HTTP_Strict_Transport_Security_.28HSTS.29
     */
    public $hstsMaxAge = 31536000;

    /**
     * @var bool Whether to apply Strict-Transport-Security rule to all of the site's subdomains as well.
     */
    public $hstsIncludeSubdomains = true;

    /**
     * @var int Whether to prevent Internet Explorer and Chrome from MIME-sniffing a response away from the declared content-type.
     * 1 = on
     * 0 = off
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#X-Content-Type-Options
     */
    public $contentTypeLevel = 1;

    /**
     * @var array Content Security Policy directives to be applied.
     * Array items structure is following:
     * 'directive' => "value"
     * Remember that special keywords require single quotes i.e. 'none', 'self', 'unsafe-inline', 'unsafe-eval'
     * Set to empty array, false or null to switch off.
     * https://wiki.mozilla.org/Security/Guidelines/Web_Security#Content_Security_Policy
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#Content-Security-Policy
     */
    public $cspDirectives = [
        'default-src' => "'none'",
        'connect-src' => "'self'",
        'img-src' => "'self'",
        'script-src' => "'self'",
        'style-src' => "'self'"
    ];

    /**
     * @var array HPKP pins.
     * Every item should be Base64 encoded Subject Public Key Information (SPKI) fingerprint.
     * Set to empty array, false or null to switch off.
     * https://www.owasp.org/index.php/OWASP_Secure_Headers_Project#Public_Key_Pinning_Extension_for_HTTP_.28HPKP.29
     */
    public $hpkpPins = [];

    /**
     * @var int Number of seconds browser should remember that this site is only to be accessed using one of the pinned keys.
     * Works only with set $hpkpPins.
     */
    public $hpkpMaxAge = 10000;

    /**
     * @var bool Whether to apply HPKP rule to all of the site's subdomains as well.
     */
    public $hpkpIncludeSubdomains = true;

    /**
     * @var string URL where validation failures are reported to.
     * Set empty to ommit.
     */
    public $hpkpReportUri = '';

    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }

    public function buildViewResponse(): ResponseInterface {
        $this->addSafetyHeaders();
        return $this->response;
    }

    /**
     * Adds safety headers.
     */
    protected function addSafetyHeaders() {
        $this->addContentSecurityPolicy();
        $this->addStrictTransportSecurity();
        $this->addContentTypeOptions();
        $this->addXssProtection();
        $this->addFrameOptions();
        $this->addPublicKeyPins();
    }

    /**
     * Sets CSP header.
     * @param HeaderCollection $headers
     */
    protected function addContentSecurityPolicy() {
        if ($this->cspDirectives && is_array($this->cspDirectives)) {
            $values = [];
            foreach ($this->cspDirectives as $directive => $content) {
                $values[] = $directive . " " . $content;
            }
            if (!$this->response->hasHeader('Content-Security-Policy')) {
                $this->response->withHeader('Content-Security-Policy', implode("; ", $values));
            }
        }
    }

    /**
     * Sets X-Frame-Options header.
     * @param HeaderCollection $headers
     */
    protected function addFrameOptions() {
        if (!$this->response->hasHeader('X-Frame-Options')) {
            $value = "DENY";
            $config = ApiAppFactory::getApp()->getService(ServiceTypes::CONFIGURATIONS);
            $debug = $config->get("env.debug", false);
            if ($this->frameLevel == 3 && $debug) {
                // Lower frameLevel for debug module frames
                $this->frameLevel = 2;
            }
            switch ($this->frameLevel) {
                case 3:
                    $value = "DENY";
                    break;
                case 2:
                    $value = "SAMEORIGIN";
                    break;
                case 1:
                    $value = "ALLOW-FROM {$this->frameAllowFrom}";
                    break;
            }
            $this->response->withHeader('X-Frame-Options', $value);
        }
    }

    /**
     * Sets X-XSS-Protection header.
     * @param HeaderCollection $headers
     */
    protected function addXssProtection() {
        if (!$this->response->hasHeader('X-XSS-Protection')) {
            $value = "1; report={$this->xssReport}";

            switch ($this->xssLevel) {
                case 3:
                    $value = "1; report={$this->xssReport}";
                    break;
                case 2:
                    $value = "1; mode=block";
                    break;
                case 1:
                    $value = "1";
                    break;
            }
            $this->response->withHeader('X-XSS-Protection', $value);
        }
    }

    /**
     * Sets Strict-Transport-Security header.
     * @param HeaderCollection $headers
     */
    protected function addStrictTransportSecurity() {
        if (!$this->response->hasHeader('Strict-Transport-Security')) {
            if ($this->hstsMaxAge) {
                $value = "max-age={$this->hstsMaxAge}";
                if ($this->hstsIncludeSubdomains) {
                    $value .= "; includeSubDomains";
                }
                $this->response->withHeader('Strict-Transport-Security', $value);
            }
        }
    }

    /**
     * Sets X-Content-Type-Options header.
     * @param HeaderCollection $headers
     */
    protected function addContentTypeOptions() {
        if (!$this->response->hasHeader('X-Content-Type-Options')) {
            if ($this->contentTypeLevel) {
                $this->response->withHeader('X-Content-Type-Options', "nosniff");
            }
        }
    }

    /**
     * Sets HPKP header.
     * @param HeaderCollection $headers
     */
    protected function addPublicKeyPins() {
        if (!$this->response->hasHeader('Public-Key-Pins')) {
            if ($this->hpkpPins && is_array($this->hpkpPins)) {
                $values = [];
                foreach ($this->hpkpPins as $pin) {
                    $values[] = "pin-sha256=\"" . $pin . "\"";
                }
                if (!empty($this->hpkpReportUri)) {
                    $values[] = "report-uri=\"" . $this->hpkpReportUri . "\"";
                }
                $values[] = "max-age=" . $this->hpkpMaxAge;
                if ($this->hpkpIncludeSubdomains) {
                    $values[] = "includeSubDomains";
                }
                $this->response->withHeader('Public-Key-Pins', implode("; ", $values));
            }
        }
    }

}
