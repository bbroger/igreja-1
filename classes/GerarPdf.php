<?php  

require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

Class GerarPdf {

	private $pessoas;
	private $mensagem;
	private $colunas;
	private $nome;

	public function __construct($pessoas, $mensagem, $colunas, $nome) {
		$this->pessoas	= $pessoas;
		$this->mensagem	= $mensagem;
		$this->colunas 	= $colunas;
		$this->nome 	= $nome;
	}

	private function gerar() {
	
		$mensagem = nl2br($this->mensagem);

	    $begin = '
	        <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	        <html>
	        <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	        <title>Gerador de bilhetes</title>
	        </head>
	        <body>
	        <table style="table-layout: fixed; width: 1035px;" border="1">
	        <tbody>';

	    $end = '
	        </tr>
	        </tbody>
	        </table>
	        </body>
	        </html>';

	    $mod = '
	        </tr>
	        <tr>';



    	$html = '';

    	$cont = 0;

    	if ($this->colunas < 2) {
    		$this->colunas = 2;
    	}

    	foreach($this->pessoas as $row => $link):

        	$cont = $cont + 1;

        	if ( ($cont % $this->colunas) == 1 ) {
         	   $html = $html.$mod;
        	}

	        $output = strtr($mensagem, array('$nome' => $link['nome'])); 
	        $output = strtr($output, array('$sobrenome' => $link['sobrenome']));

        	$html = $html.'<td>'.$output.'</td>';
    	endforeach;

    	return $begin.$html.$end;
	}

	public function abrirPdf() {

		$html = $this->gerar();

	    $dompdf = new Dompdf();
	    $dompdf->set_option('isHtml5ParserEnabled', true);
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'landscape');
	    $dompdf->render();
	    $dompdf->stream($this->nome, array('Attachment'=>0));
	}

	public function downloadPdf() {

		$html = $this->gerar();

	    $dompdf = new Dompdf();
	    $dompdf->set_option('isHtml5ParserEnabled', true);
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'landscape');
	    $dompdf->render();
	    $dompdf->stream($this->nome);
	}

}


?>