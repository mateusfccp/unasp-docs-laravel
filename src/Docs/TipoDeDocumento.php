<?php

namespace unaspbr\Docs;

use unaspbr\Docs\Request;

class TipoDeDocumento {
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
        return array_map(function ($item) {
            $tipo_documento = new Self;
            $tipo_documento->update($item);
            return $tipo_documento;
        }, $response['data']);
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
        $tipo_de_documento = new Self;
        $tipo_de_documento->update($response['data']);

        return $tipo_de_documento;
    }

    /**
     * Atualiza os dados da classe conforme os dados da response.
     *
     * @param mixed[] $dados Dados para atualizar.
     */
    private function update(array $dados)
    {
        foreach ($dados as $k => $v) {
            $this->$k = $v;
        }
    }
}
