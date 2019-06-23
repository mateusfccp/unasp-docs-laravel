<?php

namespace unaspbr\Docs;

use unaspbr\Docs\Exceptions\DadosObrigatoriosFaltando;
use unaspbr\Docs\Request;

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

        // Cria nova pessoa com base nos dados enviados
        $pessoa = new Self;
        $pessoa->update($response['data']);

        return $pessoa;
    }

    /**
     * Obtém a pessoa via ID através da API.
     *
     * @param int|mixed[] $query Parâmetro de busca na API. Caso seja int, buscará por um ID correspondente.
     *                         Caso seja array, buscará por documentos correspondentes.
     *
     * @return unaspbr\Docs\Pessoa
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
        $pessoa = new Self;
        $pessoa->update($response['data']);

        return $pessoa;
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
