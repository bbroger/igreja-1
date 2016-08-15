<?php


Class AnoQuery {

	private $db;

    public function __construct($db) 
    {
        $this->db = $db;
    }

    public function add($ano, $valor)
    {
    	if (empty($ano))
    		return "Ano em branco";
    	else if (empty($valor))
    		return "Valor em branco";

    	$sql = "INSERT INTO ano_pagamento(ano, valor) VALUES(?, ?)";

		$stmt = $this->db->prepare($sql);
	    return $stmt->execute(array($ano, $valor));
    }

    public function del($ano)
    {
    	$sql = "DELETE FROM ano_pagamento WHERE ano = :ano";

		$stmt = $this->db->prepare($sql);
	    return $stmt->execute([':ano'=>$ano]);
    }

   	public function getAnos()
	{
		$sql = "SELECT * FROM ano_pagamento WHERE 1";

	    $stmt = $this->db->prepare($sql);
	    $stmt->execute();

	    return $stmt->fetchall();
	}
}




?>