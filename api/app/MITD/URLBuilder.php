<?php

namespace App\MITD;

use Carbon\Carbon;
use InvalidArgumentException;

use Illuminate\Support\InteractsWithTime;

class URLBuilder {
    use InteractsWithTime;

    protected $_scheme = null;
    protected $_host = null;
    protected $_port = null;
    protected $_paths = [];
    protected $_query = [];

    /**
     * Create a new URL Builder instance.
     *
     * @param  string|null  $base
     * @return void
     */
    public function __construct(?string $base = null) {
        if ($base != null) {
            $this->base($base);
        }
    }

    /**
     * Set the base URL.
     *
     * @param string $baseUrl
     * @return URLBuilder
     */
    public function base(string $baseUrl): URLBuilder {
        $breakdown = parse_url($baseUrl);

        $this->_scheme = $breakdown["scheme"] ?? null;
        $this->_host = $breakdown["host"] ?? null;
        $this->_port = $breakdown["port"] ?? null;

        if (isset($breakdown["path"])) {
            $this->paths($breakdown["path"]);
        }

        if (isset($breakdown["query"])) {
            parse_str($breakdown["query"], $this->_query);
        }
        return $this;
    }

    /**
     * Add paths to the URL.
     *
     * @param  array  $paths
     * @return URLBuilder
     */
    public function paths(...$paths): URLBuilder {
        $p = [];
        foreach ($paths as $path) {
            if ($path == "") {
                continue;
            }
            $tmp = explode("/", trim($path, "/"));
            $p = array_merge($p, $tmp);
        }

        $this->_paths = array_merge($this->_paths, $p);
        return $this;
    }

    /**
     * Add a query parameter to the URL.
     *
     * @param  string  $key
     * @param  string  $value
     * @return URLBuilder
     */
    public function query(string $key, string $value): URLBuilder {
        $this->_query[$key] = $value;
        ksort($this->_query);
        return $this;
    }
    /**
     * Create a signed URL
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @return URLBuilder
     */
    public function signUrl($expiration = null): URLBuilder {
        $this->ensureSignedRouteParametersAreNotReserved($this->_query);
        $key = config("app.key");
        if ($expiration) {
            $this->query("expires", $this->availableAt($expiration));
        }
        $signature = hash_hmac("sha256", $this->build(), $key);
        return $this->query("signature", $signature);
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl(): string {
        $url = "";

        if (!!$this->_scheme) {
            $url .= $this->_scheme . "://";
        }

        $url .= $this->_host;

        if (!!$this->_port) {
            $url .= ":" . $this->_port;
        }

        return rtrim($url, "/\\");
    }

    /**
     * Get the path string
     *
     * @return string
     */
    public function getPathString(): string {
        return implode("/", $this->_paths);
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function getQueryString(array $ignoreQuery = []): string {
        $q = collect($this->_query)
            ->reject(function ($value, $key) use ($ignoreQuery) {
                return in_array($key, $ignoreQuery);
            })
            ->toArray();
        return http_build_query($q);
    }

    /**
     * Check if the URL has a valid signature and is not expired
     *
     * @param  array  $ignoreQuery
     * @return bool
     */
    public function hasValidSignature(array $ignoreQuery = []): bool {
        return $this->hasCorrectSignature($ignoreQuery) && !$this->isExpired();
    }

    /**
     * Check if the URL has the correct signature
     *
     * @param  array  $ignoreQuery
     * @return bool
     */
    public function hasCorrectSignature(array $ignoreQuery = []): bool {
        if (isset($this->_query["signature"])) {
            $ignoreQuery[] = "signature";
            $key = config("app.key");
            $built = $this->build($ignoreQuery);
            return hash_equals(hash_hmac("sha256", $built, $key), $this->_query["signature"]);
        }
        return false;
    }

    /**
     * Check if the URL has expired
     *
     * @return bool
     */
    public function isExpired(): bool {
        return isset($this->_query["expires"]) ? $this->_query["expires"] < Carbon::now()->getTimestamp() : true;
    }

    /**
     * Build the URL
     *
     * @param  array  $ignoreQuery
     * @return string
     */
    public function build(array $ignoreQuery = []): string {
        if (!$this->_host) {
            throw new \Exception("No Host Specified");
        }

        $url = $this->getBaseUrl();
        $url = $this->appendPaths($url);
        $url = $this->appendQueries($url, $ignoreQuery);
        return $url;
    }

    /**
     * Append the paths to a string
     *
     * @param  string  $url
     * @return string
     */
    private function appendPaths(string $url): string {
        if (count($this->_paths) > 0) {
            $url .= "/" . $this->getPathString();
        }
        return $url;
    }

    /**
     * Append the queries to a string
     *
     * @param  string  $url
     * @param  array  $ignoreQuery
     * @return string
     */
    private function appendQueries(string $url, $ignoreQuery = []): string {
        if (count($this->_query) > 0) {
            $url .= "?" . $this->getQueryString($ignoreQuery);
        }
        return $url;
    }

    /**
     * Ensure that signed route parameters are not reserved
     *
     * @param  array  $parameters
     * @throws InvalidArgumentException
     */
    private function ensureSignedRouteParametersAreNotReserved(array $parameters): void {
        if (array_key_exists("signature", $parameters)) {
            throw new InvalidArgumentException('"Signature" is a reserved parameter when generating signed routes. Please rename your route parameter.');
        }

        if (array_key_exists("expires", $parameters)) {
            throw new InvalidArgumentException('"Expires" is a reserved parameter when generating signed routes. Please rename your route parameter.');
        }
    }
}
