<?php

/**
 * Configuração da YouTube Data API v3.
 *
 * A api_key pode ser definida via variável de ambiente YOUTUBE_API_KEY
 * ou diretamente neste arquivo (fallback).
 */

return [
    // Chave da API — defina via variável de ambiente ou cole aqui
    'api_key'    => getenv('YOUTUBE_API_KEY') ?: '',

    // Channel ID do Yanns no YouTube
    'channel_id' => 'UCpbRjfY2hvgKJBJkOsvzuMw',

    // Tempo de vida do cache em segundos (padrão: 6 horas)
    'cache_ttl'  => 21600,

    // Quantidade padrão de vídeos a buscar
    'max_results' => 8,
];
