namespace unaspbr\Docs;

class Resource {
    public function __construct($data) {
        $this->update($data);
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

    /**
     * Mapeia uma lista de recursos conforme retorno da API.
     *
     * @return unaspbr\Docs\Resource[]
     */
    public static function toArray($data = [])
    {
        return array_map(function ($item) {
            $resource = new Self;
            $resource->update($item);
            
            return $resource;
        }, $data);
    }
}

class ResourceNotFound extends Exception {}

class ResourceConflict extends Exception {}
