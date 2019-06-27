<?php

namespace unaspbr\Docs;

use unaspbr\Docs\Request;

class TipoDeDocumento extends Resource {
    /**
     * Obtém todos os tipos de documento da API.
     *
     * @return unaspbr\Docs\TipoDeDocumento[]
     */
    public static function listar()
    {
        // Busca pelo tipo de documento na API
        $response = Request::get("tipos_documento");

        // Gera a lista de tipos de documento e retorna-a
        return Self::toArray($response->json);
    }
    
    /**
     * Obtém o tipo de documento via ID através da API.
     *
     * @param int $id ID do tipo de documento no docs
     *
     * @return unaspbr\Docs\TipoDeDocumento
     */
    public static function buscar($id)
    {
        // Busca pelo tipo de documento na API
        $response = Request::get("tipos_documento/{$id}");

        // Cria novo tipo de documento com base nos dados obtidos
        if ($response->status_code === 200) {
            return new Self($response->json);
        }
        
        return null;
    }
}
