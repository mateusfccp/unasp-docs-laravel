<?php

namespace unaspbr\Docs;

use Illuminate\Support\Facades\File;
use unaspbr\Docs\Request;
use unaspbr\Docs\Resource;
use unaspbr\Docs\ResourceConflict;

class Documento extends Resource {
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

        if ($response->status_code === 409) {
            throw new ResourceConflict("Já existe um documento desse tipo para essa pessoa!");
        }

        // Cria um novo documento com base na resposta da API
        return new Self($response->json);
    }

    /**
     * Substitui um documento via API e retorna o objeto relacionado.
     *
     * @param int $id O ID do documento que será substituído.
     *
     * @param mixed[] Arquivos que serão enviados. Cada arquivo deve ser uma array associativa com 'extensao', com a extensão do arquivo,
     *                e 'base64', com o arquivo codificado em Base64.
     *
     * @return unaspbr\Docs\Documento
     */
    public static function substituir(int $id, array $files)
    {
        // Envia os dados para a API
        $response = Request::put("documento/{$id}", [
            'files' => $files,
        ]);

        if ($response->status_code === 404) {
            throw new ResourceConflict("Já existe um documento desse tipo para essa pessoa!");
        }
        
        // Cria um novo documento com base na resposta da API
        return new Self($response->json);
    }

    /**
     * Reenvia (substitui) o documento via API e retorna o objeto relacionado.
     *
     * @param mixed[] Arquivos que serão enviados. Cada arquivo deve ser uma array associativa com 'extensao', com a extensão do arquivo,
     *                e 'base64', com o arquivo codificado em Base64.
     *
     * @return unaspbr\Docs\Documento
     */
    public function reenviar(array $files)
    {
        return Self::substituir($this->id, $files);
    }

    /**
     * Obtém o documento via ID através da API.
     *
     * @param int $id Parâmetro de busca na API.
     *
     * @return unaspbr\Docs\Documento|null
     */
    public static function buscar($id)
    {
        // Busca por documento na API
        $response = Request::get("documento/{$id}");

        // Cria nova pessoa com base nos dados obtidos
        if ($response->status_code === 200) {
            return new Self($response->json);
        }

        return null;
    }

    /**
     * Obtém os documentos de uma pessoa via ID da pessoa através da API.
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
        return  Self::toArray($response->json);
    }
}
