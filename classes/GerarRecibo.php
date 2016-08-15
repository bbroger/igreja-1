<?php  

require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

Class GerarRecibo {

	private $id_pagamento;
	private $nome;
	private $dinheiro;
	private $dia;
	private $mes;
	private $ano;

	public function __construct($id_pagamento, $valor, $nome) {
		$this->id_pagamento		= $numero;
		$this->valor 			= $valor;
		$this->nome  			= $nome;
		$this->dia 				= date('d'); 
		$this->mes 				= date('m');
		$this->ano 				= date('Y');
	}

	private function gerar() {

		$recibo = '
		<div>
			<table style="width:100%">
			  <tr>
			    <th></th>
			    <th></th>		
			    <th></th>
			  </tr>
			  <tr>
			    <td><h1>Paróquia Envangélica de Quilombo<h1></td>
			    <td>Nº $NUMERO</td>		
			    <td></td>
			  </tr>
			  <tr>
			    <td>IGREJA ENVANGÉLICA DE CONFISSÃO LUTERANA NO BRASIL</td>		
			    <td></td>
			    <td></td>
			  </tr>
			  <tr>
			    <td>QUILOMBO - CANDELÁRIA - RS</td>
			    <td></td>
			    <td></td>
			  </tr>
			</table>

			<h1 align="right" >Recibo R$: $VALOR</h1>

			<p>
			Recebemos do Sr. $NOME a importancia de R$ $VALOR em pagamento da anuidade. 
			</p>

			<p align="right">
			Candelária, $DIA de $MES de $ANO  
			</p>

			<p align="right">
			..........................................................................................................................
			<p>
		</div>';

		$begin = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	        <html>
	        <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	        <title>Recibo e segunda via</title>
			<style>
			div{
    		border: 1px solid black;
    		text-align: left;
			}
			</style>
			</head>
	        <body>';

		$end = '
		</body>
		</html>';



	    $output = strtr($recibo, array('$NUMERO'	=> $this->numero)); 
	    $output = strtr($output, array('$VALOR'		=> $this->valor)); 
	    $output = strtr($output, array('$NOME'		=> $this->nome)); 
	    $output = strtr($output, array('$DINHEIRO' 	=> $this->dinheiro)); 
	    $output = strtr($output, array('$DIA' 		=> $this->dia)); 
	    $output = strtr($output, array('$MES' 		=> $this->mes)); 
	    $output = strtr($output, array('$ANO' 		=> $this->ano)); 

    	return $begin.$output.$output.$end;
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