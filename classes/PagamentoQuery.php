<?php


Class PagamentoQuery {

	private $db;

    public function __construct($db) 
    {
        $this->db = $db;
    }

    public function add($id_pessoa, $ano, $tipo, $valor = 0)
    {
    	$sql = "INSERT INTO pagamento(valor, tipo, ano, data_pagamento, id_pessoa) VALUES(?, ?, ?, ?, ?)";

    	$data_pagamento  = date('Y-m-d H:i:s');

		$stmt = $this->db->prepare($sql);
	    return $stmt->execute(array($valor, $tipo, $ano, $data_pagamento, $id_pessoa));
    }

    public function del($id_pagamento)
    {
        $sql = "DELETE FROM pagamento WHERE id_pagamento = :id_pagamento";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array('id_pagamento'=>$id_pagamento));
    }

    public function getPagamentos($id_pessoa)
    {
        $sql = "SELECT * FROM pagamento WHERE id_pessoa = :id_pessoa";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id_pessoa'=>$id_pessoa));

        $data = $stmt->fetchall();

        
        foreach ($data as $d => &$value) {
              
            if ($value['valor'] == "0.00") {

                $sql = "SELECT valor FROM ano_pagamento WHERE ano = :ano";

                $stmt = $this->db->prepare($sql);
                $stmt->execute(array('ano'=>$value['ano']));

                $collumn = $stmt->fetchColumn();

                $value['valor'] = $collumn;

                if ($value['tipo'] === "Meio") {
                    $value['valor'] = $value['valor']/2;
                }
            }
        }


        return $data;
    }

    public function getLastIdValor($id_pessoa)
    {
        $sql = "SELECT * FROM pagamento WHERE id_pessoa = :id_pessoa ORDER BY data_pagamento DESC LIMIT 1";

        $stmt = $this->dp->prepare($sql);
        $stmt->execute(array('id_pessoa'=>$id_pessoa));

        return $stmt->fetchColumn();
    }

}

?>