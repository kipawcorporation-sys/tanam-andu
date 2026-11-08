<?php
/**
 * TanamPohon.id - Router
 * 
 * Mengelola routing URL ke controller dan action yang sesuai.
 * Mendukung: GET, POST, PUT, DELETE, named routes, route parameters,
 * route groups dengan prefix dan middleware.
 */

class Router
{
    /** @var array Daftar semua route yang terdaftar */
    private array $routes = [];

    /** @var array Daftar named routes untuk URL generation */
    private array $namedRoutes = [];

    /** @var array Stack group untuk nested grouping */
    private array $groupStack = [];

    /** @var string|null Prefix grup aktif saat ini */
    private ?string $currentPrefix = null;

    /** @var array Middleware grup aktif saat ini */
    private array $currentMiddleware = [];

    /**
     * Daftarkan route GET
     *
     * @param string $uri Pattern URI (contoh: /donasi/{id})
     * @param string $action Controller@method (contoh: DonasiController@show)
     * @return self
     */
    public function get(string $uri, string $action): self
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Daftarkan route POST
     */
    public function post(string $uri, string $action): self
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Daftarkan route PUT
     */
    public function put(string $uri, string $action): self
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Daftarkan route DELETE
     */
    public function delete(string $uri, string $action): self
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Tambahkan route ke daftar internal
     *
     * @param string $method HTTP method
     * @param string $uri Pattern URI
     * @param string $action Controller@method
     * @return self
     */
    private function addRoute(string $method, string $uri, string $action): self
    {
        // Gabungkan prefix dari group jika ada
        $fullUri = $this->buildUri($uri);

        // Gabungkan middleware dari group
        $middleware = $this->currentMiddleware;

        $this->routes[] = [
            'method'     => $method,
            'uri'        => $fullUri,
            'action'     => $action,
            'middleware'  => $middleware,
            'name'       => null,
        ];

        return $this;
    }

    /**
     * Beri nama pada route terakhir yang didaftarkan
     * Berguna untuk URL generation: route('donasi.show', ['id' => 1])
     *
     * @param string $name Nama route
     * @return self
     */
    public function name(string $name): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $this->routes[$lastIndex]['name'] = $name;
            $this->namedRoutes[$name] = $lastIndex;
        }
        return $this;
    }

    /**
     * Tambahkan middleware ke route terakhir
     *
     * @param string|array $middleware Nama middleware atau array middleware
     * @return self
     */
    public function middleware(string|array $middleware): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $middlewares = is_array($middleware) ? $middleware : [$middleware];
            $this->routes[$lastIndex]['middleware'] = array_merge(
                $this->routes[$lastIndex]['middleware'],
                $middlewares
            );
        }
        return $this;
    }

    /**
     * Buat grup route dengan prefix dan middleware yang sama
     * Mendukung nested groups.
     *
     * @param array $attributes Atribut grup: prefix, middleware
     * @param callable $callback Fungsi yang berisi definisi route
     */
    public function group(array $attributes, callable $callback): void
    {
        // Simpan state sebelumnya
        $previousPrefix = $this->currentPrefix;
        $previousMiddleware = $this->currentMiddleware;

        // Terapkan prefix baru (gabungkan dengan yang sudah ada)
        if (isset($attributes['prefix'])) {
            $this->currentPrefix = $this->currentPrefix
                ? rtrim($this->currentPrefix, '/') . '/' . ltrim($attributes['prefix'], '/')
                : $attributes['prefix'];
        }

        // Terapkan middleware baru (gabungkan dengan yang sudah ada)
        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware'])
                ? $attributes['middleware']
                : [$attributes['middleware']];
            $this->currentMiddleware = array_merge($this->currentMiddleware, $middlewares);
        }

        // Jalankan callback untuk mendaftarkan route dalam grup
        $callback($this);

        // Kembalikan state sebelumnya
        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    /**
     * Bangun URI lengkap dengan prefix grup
     *
     * @param string $uri URI asli
     * @return string URI dengan prefix
     */
    private function buildUri(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');

        if ($this->currentPrefix) {
            $prefix = '/' . ltrim($this->currentPrefix, '/');
            if ($uri === '/') {
                $uri = $prefix;
            } else {
                $uri = rtrim($prefix, '/') . $uri;
            }
        }

        return $uri === '' ? '/' : $uri;
    }

    /**
     * Generate URL dari named route
     *
     * @param string $name Nama route
     * @param array $params Parameter untuk replace placeholder
     * @return string URL yang dihasilkan
     * @throws Exception Jika nama route tidak ditemukan
     */
    public function route(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route dengan nama '{$name}' tidak ditemukan.");
        }

        $route = $this->routes[$this->namedRoutes[$name]];
        $uri = $route['uri'];

        // Ganti parameter placeholder {param} dengan nilai sebenarnya
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        $baseUrl = rtrim(env('APP_URL', ''), '/');
        return $baseUrl . $uri;
    }

    /**
     * Proses request dan arahkan ke controller yang sesuai
     * Ini adalah method utama yang dipanggil dari index.php
     */
    public function dispatch(): void
    {
        $request = new Request();
        $method = $request->method();
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $uri = $request->uri();

        // Cari route yang cocok
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Buat pattern regex dari URI route
            $pattern = $this->buildPattern($route['uri']);

            if (preg_match($pattern, $uri, $matches)) {
                // Ekstrak parameter dari URL
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Jalankan middleware sebelum controller
                if (!$this->runMiddleware($route['middleware'], $request)) {
                    return;
                }

                // Panggil controller dan method
                $this->callAction($route['action'], $params, $request);
                return;
            }
        }

        // Route tidak ditemukan - tampilkan 404
        $this->handleNotFound();
    }

    /**
     * Bangun pattern regex dari URI route
     * Contoh: /donasi/{id} menjadi regex /^\/donasi\/(?P<id>[^\/]+)$/
     *
     * @param string $uri Pattern URI
     * @return string Pattern regex
     */
    private function buildPattern(string $uri): string
    {
        $cleanUri = rtrim($uri, '/');
        if ($cleanUri === '') {
            return '#^/?$#';
        }

        // Escape karakter khusus regex kecuali /
        $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^\/]+)';
        }, $cleanUri);

        return '#^' . $pattern . '/?$#';
    }

    /**
     * Jalankan semua middleware untuk route
     *
     * @param array $middlewares Daftar nama middleware
     * @param Request $request Objek request
     * @return bool True jika semua middleware lolos
     */
    private function runMiddleware(array $middlewares, Request $request): bool
    {
        // Peta nama middleware ke class middleware
        $middlewareMap = [
            'auth'  => 'AuthMiddleware',
            'admin' => 'AdminMiddleware',
            'guest' => 'GuestMiddleware',
            'csrf'  => 'CsrfMiddleware',
        ];

        foreach ($middlewares as $name) {
            if (!isset($middlewareMap[$name])) {
                continue;
            }

            $className = $middlewareMap[$name];
            $filePath = APP_PATH . '/Middleware/' . $className . '.php';

            if (!file_exists($filePath)) {
                continue;
            }

            require_once $filePath;

            $middlewareInstance = new $className();
            if (!$middlewareInstance->handle($request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Panggil method controller berdasarkan string action
     *
     * @param string $action Format: ControllerName@methodName
     * @param array $params Parameter dari URL
     * @param Request $request Objek request
     */
    private function callAction(string $action, array $params, Request $request): void
    {
        [$controllerName, $methodName] = explode('@', $action);

        $controllerFile = APP_PATH . '/Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            $this->handleNotFound();
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            $this->handleNotFound();
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            $this->handleNotFound();
            return;
        }

        // Panggil method controller dengan parameter
        call_user_func_array([$controller, $methodName], array_values($params));
    }

    /**
     * Tangani halaman 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);

        // Cek apakah ada view 404 custom
        $errorView = VIEW_PATH . '/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8f9fa; color: #333; }
        .container { text-align: center; }
        h1 { font-size: 6rem; margin: 0; color: #198754; }
        p { font-size: 1.2rem; color: #666; }
        a { color: #198754; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>Maaf, halaman yang Anda cari tidak ditemukan.</p>
        <a href="/">← Kembali ke Beranda</a>
    </div>
</body>
</html>';
        }
        exit;
    }

    /**
     * Getter untuk daftar route (berguna untuk debugging)
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Getter untuk named routes
     */
    public function getNamedRoutes(): array
    {
        return $this->namedRoutes;
    }
}
