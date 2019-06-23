namespace unaspbr\Docs;

use GuzzleHttp\Client;
use Exceptions\DadosObrigatoriosFaltando;

class Pessoa {
    /**
     * Factory para criar a pessoa através da API.
     *
     * @param array $dados Dados da pessoa. É obrigatório incluir "cpf" ou "rg".
     *
     * @return unaspbr\Docs\Pessoa
     */
    public function criar($dados)
    {
        if (!in_array('cpf', $dados) || !in_array("rg", $dados)) {
            throw new DadosObrigatoriosFaltando("É necessário passar RF ou CPF!");
        }

        $client = new Client([
            'base_uri' => 'localhost:8001/api',   
        ]);

        $client->post('/pessoa', ['body' => $dados]);

        var_dump($client); die();
    }
}
