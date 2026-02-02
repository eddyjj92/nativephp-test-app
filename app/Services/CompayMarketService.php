<?php

namespace App\Services;

use App\DTOs\ProvinceDTO;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Servicio para interactuar con la API de Compay Market.
 * Maneja autenticación dinámica y peticiones a endpoints públicos y privados.
 */
class CompayMarketService
{
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
     * Inicia sesión para obtener un token de acceso (Endpoint Público).
     */
    public function login(string $email, string $password): array
    {
        return $this->http()->post('/login', [
            'email' => $email,
            'password' => $password,
        ])->json();
    }

    /**
     * Obtiene la lista de provincias (Endpoint Público).
     *
     * @param  string|null  $status  Estado de la entrega ('active', 'inactive' o null).
     * @return ProvinceDTO[]
     */
    public function getProvinces(?string $status = null): array
    {
        $params = [];
        if ($status) {
            $params['status'] = $status;
        }

        $response = $this->http()->get('/provinces', $params)->json();


        return array_map(
            fn ($province) => ProvinceDTO::fromArray($province),
            $response['provinces'] ?? []
        );
    }

    /**
     * Obtiene la lista de productos.
     *
     * @param  array  $params  Parámetros de filtrado o paginación.
     *
     * @throws ConnectionException
     */
    public function getProducts(array $params = []): array
    {
        return $this->http()->get('/products', $params)->json();
    }

    /**
     * Obtiene los detalles de un producto específico.
     *
     * @param  string  $id  Identificador del producto.
     *
     * @throws ConnectionException
     */
    public function getProduct(string $id): array
    {
        return $this->http()->get("/products/{$id}")->json();
    }

    /**
     * Crea una nueva orden de compra (Endpoint Autenticado).
     * Requiere haber llamado a setToken() previamente.
     *
     * @param  array  $data  Datos de la orden y productos.
     *
     * @throws ConnectionException
     */
    public function createOrder(array $data): array
    {
        return $this->http()->post('/orders', $data)->json();
    }
}
