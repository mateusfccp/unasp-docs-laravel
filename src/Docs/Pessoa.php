<?php

namespace unaspbr\Docs;

use unaspbr\Docs\Exceptions\DadosObrigatoriosFaltando;
use unaspbr\Docs\Request;
use unaspbr\Docs\ResourceConflict;

class Pessoa {
    /**
     * Factory para criar a pessoa através da API.
     *
     * @param mixed[] $dados Dados da pessoa. É obrigatório incluir "cpf" ou "rg".
     *
     * @return unaspbr\Docs\Pessoa
     *
     * @throws unaspbr\Exceptions\DadosObrigatoriosFaltando Quando não forem passados CPF ou RG.
     */
    public static function criar($dados)
    {
        // Verifica pelo CPF ou RG
        if (!array_key_exists('cpf', $dados) && !array_key_exists('rg', $dados)) {
            throw new DadosObrigatoriosFaltando("É necessário passar RG ou CPF!");
        }

        // Envia os dados para a API
        $response = Request::post('pessoa', $dados);

        if ($response->status_code === 422) {
            throw new ResourceConflict("Já existe uma pessoa com o(s) documento(s) passado(s)!");
        }
        
        // Cria nova pessoa com base nos dados enviados
        return new Self($response->json);
    }

    /**
     * Obtém a pessoa via ID através da API.
     *
     * @param int|mixed[] $query Parâmetro de busca na API. Caso seja int, buscará por um ID correspondente.
     *                         Caso seja array, buscará por documentos correspondentes.
     *
     * @return unaspbr\Docs\Pessoa|null
     *
     * @throws \Exception Quando o argumento for do tipo incorreto.
     */
    public static function buscar($query)
    {
        // Busca por pessoa na API
        if (is_array($query)) { // Por dados (documentos, meta)
            $response = Request::get("pessoa/buscar", $query);
        } elseif (is_integer($query)) { // Por ID
            $response = Request::get("pessoa/{$query}");
        } else {
            throw new \Exception("Parâmetro deve ser do tipo int ou array!");
        }

        // Cria nova pessoa com base nos dados obtidos
        if ($response->statu_code === 200) {
            $pessoa = new Self($response->json);
        }

        return null;
    }

    /**
     * Atualiza os dados via API de acordo com dados da classe.
     *
     * @return unaspbr\Docs\Pessoa
     */
    public function salvar()
    {
        // Obtém array a partir dos dados do modelo
        $dados = (array) $this;

        // ID e metadados não são alteráveis
        unset($dados['id'], $dados['metas']);

        // Atualiza pessoa na API
        $response = Request::patch("pessoa/{$this->id}", $dados);

        return $this;
    }

    /**
     * Envia um documento para a API e retorna o objeto relacionado.
     *
     * @param int $tipo_documento O ID do tipo de documento.
     *
     * @param string $extensao A extensão do arquivo do documento.
     *
     * @param string $file_base64 O arquivo codificado em base64. 
     *
     * @return unaspbr\Docs\Pessoa
     */
    public function enviarDocumento(int $tipo_documento, string $extensao, string $file_base64)
    {
        return Documento::enviar($this->id, $tipo_documento, $extensao, $file_base64);
    }

    /**
     * Obtém os documentos da pessoa através da API.
     *
     * @return unaspbr\Docs\Documento[]
     */
    public function documentos()
    {
        // Busca por documento na API
        $response = Request::get("documento/pessoa/{$this->id}");

        // Gera a lista de tipos de documento e retorna-a
        Self::toArray($response->json);
    }

    /**
     * Obtém o documento de uma pessoa via ID do tipo.
     *
     * @param int $tipo_documento_id Parâmetro de busca na API.
     *
     * @return unaspbr\Docs\Documento[]|null
     */
    public static function buscarPorTipo($tipo_documento_id)
    {
        // Busca por documento na API
        $response = Request::get("documento/pessoa/{$id}", ['tipo_documento_id' => $tipo_documento_id]);

        // Retorna documento se encontrou
        if ($response->status_code === 200) {
            return new Self($response->json);
        }

        return null;
    }
}
