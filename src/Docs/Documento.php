<?php

namespace unaspbr\Docs;

use unaspbr\Docs\Request;
use Illuminate\Support\Facades\File;

class Documento {
    /**
     * Envia um documento para a API e retorna o objeto relacionado.
     *
     * @param int $pessoa O ID da pessoa associada ao documento.
     *
     * @param int $tipo_documento O ID do tipo de documento.
     *
     * @param string $extensao A extensão do arquivo do documento.
     *
     * @param string $file_base64 O arquivo codificado em base64. 
     *
     * @return unaspbr\Docs\Pessoa
     */
    public static function enviar(int $pessoa, int $tipo_documento, string $extensao, string $file_base64)
    {
        // Envia os dados para a API
        $response = Request::post("documento/pessoa/{$pessoa}", [
            'tipo_documento_id' => $tipo_documento,
            'file' => [
                'extensao' => $extensao,
                'base64' => $file_base64,
            ],
        ]);

        // Cria um novo documento com base na resposta da API
        $documento = new Self;
        $documento->update($response['data']);

        return $documento;
    }

    /**
     * Obtém o documento via ID através da API.
     *
     * @param int $id Parâmetro de busca na API.
     *
     * @return unaspbr\Docs\Documento
     */
    public static function buscar($id)
    {
        // Busca por documento na API
        $response = Request::get("documento/{$id}");

        // Cria nova pessoa com base nos dados obtidos
        $documento = new Self;
        $documento->update($response['data']);

        return $documento;
    }

    /**
     * Obtém os docuemntos de uma pessoa pessoa via ID da pessoa através da API.
     *
     * @param int $id Parâmetro de busca na API.
     *
     * @return unaspbr\Docs\Documento[]
     */
    public static function buscarPorPessoa($id)
    {
        // Busca por documento na API
        $response = Request::get("documento/pessoa/{$id}");

        // Gera a lista de tipos de documento e retorna-a
        return array_map(function ($item) {
            $documento = new Self;
            $documento->update($item);
            return $documento;
        }, $response['data']);
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
