<?php

namespace App\Services;

use App\DTOs\AuthResponseDTO;
use App\DTOs\BannerDTO;
use App\DTOs\CurrencyDTO;
use App\DTOs\MarketplaceHomeDTO;
use App\DTOs\ProductDTO;
use App\DTOs\ProvinceDTO;
use App\DTOs\SettingsDTO;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Servicio para interactuar con la API de Compay Market.
 * Maneja autenticación dinámica y peticiones a endpoints públicos y privados.
 */
class CompayMarketService
{
    /**
     * Duración por defecto del caché en segundos (10 minutos).
     */
    protected const DEFAULT_CACHE_TTL = 600;

    /**
     * El token de API para peticiones autenticadas.
     */
    protected ?string $token = null;

    /**
     * Establece el token de API para las peticiones autenticadas.
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Obtiene una instancia configurada del cliente HTTP.
     * Inyecta automáticamente el token Bearer si ha sido establecido.
     */
    protected function http(): PendingRequest
    {
        $request = Http::baseUrl(config('services.compay_market.api_base_url'))
            ->timeout(30)
            ->acceptJson();

        if ($this->token) {
            $request->withToken($this->token);
        }

        return $request;
    }

    /**
     * Ejecuta una petición con soporte de caché opcional.
     *
     * @param  string  $cacheKey  Clave única para identificar el caché.
     * @param  Closure  $callback  Función que realiza la petición HTTP.
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos (null usa el valor por defecto).
     */
    protected function cached(string $cacheKey, Closure $callback, bool $cache = false, ?int $cacheTtl = null): mixed
    {
        if (! $cache) {
            return $callback();
        }

        $ttl = $cacheTtl ?? self::DEFAULT_CACHE_TTL;

        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Genera una clave de caché única basada en el endpoint y parámetros.
     *
     * @param  string  $endpoint  El endpoint de la API.
     * @param  array  $params  Parámetros de la petición.
     */
    protected function buildCacheKey(string $endpoint, array $params = []): string
    {
        ksort($params);
        $paramsHash = ! empty($params) ? '_'.md5(serialize($params)) : '';

        return 'compay_market_'.str_replace('/', '_', trim($endpoint, '/')).$paramsHash;
    }

    /**
     * Inicia sesión para obtener un token de acceso (Endpoint Público).
     * Este método NO debe ser cacheado por seguridad.
     */
    public function login(string $email, string $password): AuthResponseDTO
    {
        $response = $this->http()->post('/login', [
            'email' => $email,
            'password' => $password,
        ])->json();

        return AuthResponseDTO::fromArray($response);
    }

    /**
     * Obtiene la configuración del sitio (Endpoint Público).
     *
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     *
     * @throws ConnectionException
     */
    public function getSettings(bool $cache = false, ?int $cacheTtl = null): SettingsDTO
    {
        $cacheKey = $this->buildCacheKey('/settings');

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/settings')->json(),
            $cache,
            $cacheTtl
        );

        return SettingsDTO::fromArray($response['settings'] ?? []);
    }

    /**
     * Obtiene la lista de provincias (Endpoint Público).
     *
     * @param  string|null  $status  Estado de la entrega ('active', 'inactive' o null).
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     * @return ProvinceDTO[]
     */
    public function getProvinces(?string $status = null, bool $cache = false, ?int $cacheTtl = null): array
    {
        $params = [];
        if ($status) {
            $params['status'] = $status;
        }

        $cacheKey = $this->buildCacheKey('/provinces', $params);

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/provinces', $params)->json(),
            $cache,
            $cacheTtl
        );

        return array_map(
            fn ($province) => ProvinceDTO::fromArray($province),
            $response['provinces'] ?? []
        );
    }

    /**
     * Obtiene la lista de productos paginados.
     *
     * @param  array  $params  Parámetros de filtrado o paginación (province_id es obligatorio).
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     * @return array{data: ProductDTO[], current_page: int, last_page: int, per_page: int, total: int, next_page_url: string|null, prev_page_url: string|null}
     *
     * @throws ConnectionException
     */
    public function getProducts(array $params = [], bool $cache = false, ?int $cacheTtl = null): array
    {
        $cacheKey = $this->buildCacheKey('/products', $params);

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/products', $params)->json(),
            $cache,
            $cacheTtl
        );

        $products = $response['products'] ?? [];

        return [
            'data' => array_map(
                fn ($product) => ProductDTO::fromArray($product),
                $products['data'] ?? []
            ),
            'current_page' => $products['current_page'] ?? 1,
            'last_page' => $products['last_page'] ?? 1,
            'per_page' => $products['per_page'] ?? 15,
            'total' => $products['total'] ?? 0,
            'next_page_url' => $products['next_page_url'] ?? null,
            'prev_page_url' => $products['prev_page_url'] ?? null,
        ];
    }

    /**
     * Obtiene los detalles de un producto específico.
     *
     * @param  string  $id  Identificador del producto.
     * @param  string|null  $currency  Código ISO de la moneda (ej: USD, EUR).
     * @param  string|null  $provinceSlug  Slug de la provincia.
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     *
     * @throws ConnectionException
     */
    public function getProduct(string $id, ?string $currency = null, ?string $provinceSlug = null, bool $cache = false, ?int $cacheTtl = null): ?ProductDTO
    {
        $params = [];
        if ($currency) {
            $params['currency'] = $currency;
        }

        if ($provinceSlug) {
            $params['province_slug'] = $provinceSlug;
        }

        $cacheKey = $this->buildCacheKey("/products/{$id}", $params);

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get("/products/{$id}", $params)->json(),
            $cache,
            $cacheTtl
        );

        if (empty($response['product'])) {
            return null;
        }

        return ProductDTO::fromArray($response['product']);
    }

    /**
     * Obtiene la lista de banners activos.
     *
     * @param  string|null  $status  Estado del banner ('active', 'inactive' o null).
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     * @return BannerDTO[]
     *
     * @throws ConnectionException
     */
    public function getBanners(?string $status = null, bool $cache = false, ?int $cacheTtl = null): array
    {
        $params = [];
        if ($status) {
            $params['status'] = $status;
        }

        $cacheKey = $this->buildCacheKey('/banners', $params);

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/banners', $params)->json(),
            $cache,
            $cacheTtl
        );

        return array_map(
            fn ($banner) => BannerDTO::fromArray($banner),
            $response['banners'] ?? []
        );
    }

    /**
     * Obtiene la lista de monedas disponibles.
     *
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     * @return CurrencyDTO[]
     *
     * @throws ConnectionException
     */
    public function getCurrencies(bool $cache = false, ?int $cacheTtl = null): array
    {
        $cacheKey = $this->buildCacheKey('/currencies');

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/currencies')->json(),
            $cache,
            $cacheTtl
        );

        return array_map(
            fn ($currency) => CurrencyDTO::fromArray($currency),
            $response['currencies'] ?? []
        );
    }

    /**
     * Obtiene los datos del marketplace home (productos recomendados y otras secciones).
     *
     * @param  string  $provinceSlug  Slug de la provincia.
     * @param  string  $currency  Código ISO de la moneda (ej: USD, EUR).
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     *
     * @throws ConnectionException
     */
    public function getMarketplaceHome(string $provinceSlug, string $currency, bool $cache = false, ?int $cacheTtl = null): MarketplaceHomeDTO
    {
        $params = [
            'province_slug' => $provinceSlug,
            'currency' => $currency,
        ];

        $cacheKey = $this->buildCacheKey('/products/marketplace_home', $params);

        $response = $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/products/marketplace_home', $params)->json(),
            $cache,
            $cacheTtl
        );

        return MarketplaceHomeDTO::fromArray($response ?? []);
    }

    /**
     * Obtiene la lista de categorías.
     *
     * @param  array  $params  Parámetros de filtrado o paginación.
     * @param  bool  $cache  Si se debe cachear la respuesta.
     * @param  int|null  $cacheTtl  Tiempo de vida del caché en segundos.
     *
     * @throws ConnectionException
     */
    public function getCategories(array $params = [], bool $cache = false, ?int $cacheTtl = null): array
    {
        $cacheKey = $this->buildCacheKey('/categories', $params);

        return $this->cached(
            $cacheKey,
            fn () => $this->http()->get('/categories', $params)->json(),
            $cache,
            $cacheTtl
        );
    }

    /**
     * Crea una nueva orden de compra (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     * Este método NO debe ser cacheado.
     *
     * @param  array  $data  Datos de la orden y productos.
     *
     * @throws ConnectionException
     */
    public function createOrder(array $data): array
    {
        return $this->http()->post('/orders', $data)->json();
    }

    /**
     * Actualiza los datos del usuario (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  int|string  $userId  ID del usuario.
     * @param  array  $data  Datos a actualizar.
     */
    public function updateUser(int|string $userId, array $data): array
    {
        $request = $this->http()->asMultipart();

        if (! empty($data['avatar'])) {
            $avatar = $data['avatar'];

            if ($avatar instanceof \Illuminate\Http\UploadedFile) {
                $request->attach('avatar', $avatar->get(), $avatar->getClientOriginalName());
            } elseif (is_string($avatar)) {
                // Caso 1: String Base64 (enviado desde el canvas de Profile.vue)
                if (str_starts_with($avatar, 'data:image')) {
                    $parts = explode(',', $avatar);
                    if (count($parts) > 1) {
                        $content = base64_decode($parts[1]);
                        $request->attach('avatar', $content, 'avatar.jpg');
                    }
                } 
                // Caso 2: Ruta absoluta del sistema (NativePHP sin procesar en JS)
                elseif (file_exists($avatar)) {
                    $request->attach('avatar', file_get_contents($avatar), basename($avatar));
                }
            }

            unset($data['avatar']);
        }

        return $request->post("/users/{$userId}", $data)->json();
    }

    /**
     * Obtiene las órdenes de un cliente (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  array  $params  Parámetros de filtrado (beneficiary_id, delivery_type_id, order_by, page, per_page, province_id, status).
     * @return array
     */
    public function getOrders(array $params = []): array
    {
        return $this->http()->get('/orders', $params)->json();
    }

    /**
     * Obtiene los beneficiarios de un cliente (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  array  $params  Parámetros de filtrado (order_by, page, per_page, province_id).
     * @return array
     */
    public function getBeneficiaries(array $params = []): array
    {
        return $this->http()->get('/beneficiaries', $params)->json();
    }

    /**
     * Crea un nuevo beneficiario (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  array  $data  Datos del beneficiario (name, identity_number, email, phone, address, municipality_id).
     * @return array
     */
    public function createBeneficiary(array $data): array
    {
        return $this->http()->post('/beneficiaries', $data)->json();
    }

    /**
     * Actualiza un beneficiario existente (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  int  $id  ID del beneficiario.
     * @param  array  $data  Datos del beneficiario (name, identity_number, email, phone, address, municipality_id).
     * @return array
     */
    public function updateBeneficiary(int $id, array $data): array
    {
        return $this->http()->put("/beneficiaries/{$id}", $data)->json();
    }

    /**
     * Obtiene un beneficiario específico (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  int  $id  ID del beneficiario.
     * @return array
     */
    public function getBeneficiary(int $id): array
    {
        return $this->http()->get("/beneficiaries/{$id}")->json();
    }

    /**
     * Elimina un beneficiario (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  int  $id  ID del beneficiario.
     * @return array
     */
    public function deleteBeneficiary(int $id): array
    {
        return $this->http()->delete("/beneficiaries/{$id}")->json();
    }

    /**
     * Limpia el caché de un endpoint específico.
     *
     * @param  string  $endpoint  El endpoint de la API.
     * @param  array  $params  Parámetros de la petición.
     */
    public function clearCache(string $endpoint, array $params = []): bool
    {
        $cacheKey = $this->buildCacheKey($endpoint, $params);

        return Cache::forget($cacheKey);
    }
}
