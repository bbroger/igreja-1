<?php


Class PessoaQuery {
	
	private $db;

    public function __construct($db) 
    {
        $this->db = $db;
    }

	public function add($nome, $sobrenome, $status, $data_cadastro)
	{

		if (empty($nome))
			return "Nome em branco";
		else if(empty($sobrenome))
			return "Sobrenome em branco";

	    $sql = 'INSERT INTO pessoa(nome,sobrenome,status,data_cadastro) VALUES(?, ?, ?, ?)';

	    $stmt = $this->db->prepare($sql);
	    return $stmt->execute(array($nome, $sobrenome, $status, $data_cadastro));
	}

	public function del($id)
	{
        $sql = 'DELETE FROM pessoa WHERE id_pessoa = :id';

       	$stmt = $this->db->prepare($sql);
	    return $stmt->execute([':id'=>$id]);
	}

	public function get($id)
	{
	    $sql = "SELECT * FROM pessoa WHERE id_pessoa  = :id";

	    $stmt = $this->db->prepare($sql);
	    $stmt->execute([':id'=>$id]);

	    return $stmt->fetch();
	}

	public function getPessoa($id)
	{
		return new Pessoa($this->get($id));
	}

	public function getPessoas()
	{
		$sql = "SELECT * FROM pessoa WHERE 1";

	    $stmt = $this->db->prepare($sql);
	    $stmt->execute();

	    return $stmt->fetchall();
	}

	public function getLike($l) {

		$l = "%".$l."%";

		$sql = "SELECT * FROM pessoa WHERE id_pessoa LIKE :l OR sobrenome LIKE :l OR nome LIKE :l ORDER BY nome";

		$stmt = $this->db->prepare($sql);
		$stmt->execute([':l'=>$l]);

		return $stmt->fetchall();
	}

	public function alt($id, $nome, $sobrenome, $status)
	{
		$sql = "UPDATE pessoa SET nome = :nome, sobrenome = :sobrenome, status = :status WHERE id_pessoa = :id";

		$stmt = $this->db->prepare($sql);
	    return $stmt->execute([':id'=>$id, ':nome'=>$nome, ':sobrenome'=>$sobrenome, ':status' => $status]);
	}

	public function numMembros() 
	{
		$sql = "SELECT COUNT(*) FROM pessoa";

		$stmt = $this->db->prepare($sql);
		$stmt->execute();

		return $stmt->fetchColumn();
	}

	public function getId($nome, $sobrenome)
	{
		$sql = "SELECT id_pessoa FROM pessoa WHERE nome = :nome AND sobrenome = :sobrenome"; 

		$stmt = $this->db->prepare($sql);
		$stmt->execute(['nome'=>$nome, 'sobrenome'=>$sobrenome]);

		return $stmt->fetchColumn();
	}

}

?>