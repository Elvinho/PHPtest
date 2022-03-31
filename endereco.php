<?php 
function DBConnectMy()
{	
	$servername = "localhost";
	$database = "cep_db";
	$username = "root";
	$password = "";
	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $database);
	// Check connection
	if (!$conn)
	{
		die("Connection failed: " . mysqli_connect_error());
	}
	
	return $conn;
}

	$Conexao = DBConnectMy();

function DBClose($Conexao)
{
	mysqli_close($Conexao);
}

	if(isset($_REQUEST['metodo']) == 'BuscarCep')
	{
		// echo '<pre>';var_dump($_POST);die();
		$arRetorno               = array();
		$arrEnd 				 = array();
		$sql ="SELECT * 
				FROM endereco 
				WHERE cep = '".$_POST['cep']."' ";
				// die($sql);
		$query = mysqli_query($Conexao,$sql);
		if(mysqli_num_rows($query) > 0)
		{
			while ($row = mysqli_fetch_array($query)) 
			{ 
				$arrEnd['cep'] 		= $row['cep'];
				$arrEnd['endereco'] = $row['endereco'];
				$arrEnd['bairro'] 	= $row['bairro'];
				$arrEnd['cidade'] 	= $row['cidade'];
				$arrEnd['uf'] 		= $row['uf'];
			}

			$arRetorno["valido"] = "1";
			$arRetorno[1] = "Já Existe";
			$arRetorno[2] = $arrEnd;
			DBClose($Conexao);
			die(json_encode($arRetorno));
			
		}
		else
		{
			$endereco = (get_cep($_POST['cep']));

			$cep = implode("",explode("-",$endereco->cep));

			if($endereco->cep == "" || $endereco->cep == "null")
			{
				$arRetorno["valido"] = "0";
				$arRetorno[1] = "Cep não encontrado!";
				$arRetorno[2] = $cep;
				DBClose($Conexao);
				die(json_encode($arRetorno));
			}
			$SQL ="INSERT INTO endereco(
										cep,
										endereco,
										bairro,
										cidade,
										uf
										)
										VALUES
										(
										 '".$cep."',
										 '".$endereco->logradouro."',
										 '".$endereco->bairro."',
										 '".$endereco->localidade."',
										 '".$endereco->uf."'
										)"; 
			// die($SQL);
			$query = mysqli_query($Conexao,$SQL);
			$id = mysqli_insert_id($Conexao);
			if(!$query)
			{
				$arRetorno["valido"] = "0";
				$arRetorno[1] = "Cep não encontrado!";
				$arRetorno[2] = utf8_encode($sql);
				DBClose($Conexao);
				die(json_encode($arRetorno));
			}

				$arrEnde = array();
				$sql ="SELECT * 
						FROM endereco 
						WHERE id = '".$id."'";
					// die($sql);
			$query = mysqli_query($Conexao,$sql);
			if(mysqli_num_rows($query) > 0)
			{
				while ($row = mysqli_fetch_array($query)) 
				{ 
					$arrEnde['cep'] 		= $row['cep'];
					$arrEnde['endereco'] = $row['endereco'];
					$arrEnde['bairro'] 	= $row['bairro'];
					$arrEnde['cidade'] 	= $row['cidade'];
					$arrEnde['uf'] 		= $row['uf'];
				}

				$arRetorno["valido"] = "1";
				$arRetorno[1] = "Cadastrou!";
				$arRetorno[2] = $arrEnde;
				DBClose($Conexao);
				die(json_encode($arRetorno));
				
			}
		
		}
	}

    function get_cep($cep)
    {
        $cep = preg_replace("/[^0-9]/","",$cep);
        $url = 'http://viacep.com.br/ws/'.$cep.'/xml/';
        $xml = simplexml_load_file($url);

        return $xml;
    }

   /*  echo '<pre>';
   

    // echo $endereco->logradouro; */


?>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>App Pesquisa Endereço</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<body>
		
		<nav class="navbar navbar-light bg-light mb-4">
			<div class="container">
				<div class="navbar-brand mb-0 h1">
					<h3>App Pesquisa Endereço</h3>
				</div>
			</div>
		</nav>

		<div class="container">
            <form action="">
                <div class="row form-group">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" placeholder="CEP" name="cep" id="cep" onfocus="limpa()"/>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" placeholder="Endereço" readonly id="endereco"/>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" placeholder="Bairro" readonly id="bairro"/>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" placeholder="Cidade" readonly id="cidade"/>
                    </div>

                    <div class="col-sm-2">
                        <input type="text" class="form-control" placeholder="UF" readonly id="uf"/>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="button" name='cadastrar' onclick="BuscarCep()">Buscar</button>
                    </div>
                </div>
            </form>
		</div>
		<script>
			function limpa()
			{
				// $("#cep").val("");
				$("#endereco").val("");
				$("#bairro").val("");
				$("#cidade").val("");
				$("#uf").val("");
			}
			/* function getDadosCep()
			{

				console.log(document.querySelector('#cep').value)
               
                    var cep = document.querySelector('#cep').value;
				    
                    let url = 'https://viacep.com.br/ws/'+cep+'/json/unicode/';
                    
                    let xmlHttp = new XMLHttpRequest();
                    xmlHttp.open('GET',url);

                    xmlHttp.onreadystatechange = () => 
                    {
                        if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
                        {
                            let dadosJSONText = xmlHttp.responseText;
                            let dadosJSONObj = JSON.parse(dadosJSONText)

							if(xmlHttp.responseText == "")
							{
								return alert('Cep não encontrado!');
								document.getElementById('endereco').value = "";
								document.getElementById('bairro').value = "";
								document.getElementById('cidade').value = "";
								document.getElementById('uf').value = "";
								return false;
							}
							else
							{
								document.getElementById('endereco').value = dadosJSONObj.logradouro
								document.getElementById('bairro').value = dadosJSONObj.bairro
								document.getElementById('cidade').value = dadosJSONObj.localidade
								document.getElementById('uf').value = dadosJSONObj.uf
							}

                            

							
                        }
						
                    }
                    xmlHttp.send()
            } */

            function BuscarCep()
            {
				if($('#cep').val() == "")
				{
					alert('Informe um numero de CEP!');
					$('#cep').focus();
					return false;
				}
                var parametros = new FormData();
					parametros.append("metodo", "BuscarCep");
					parametros.append("cep", 	$("#cep").val());

					$.ajax({
						type: "POST",
						url: '<?php echo $_SERVER['PHP_SELF']; ?>',
						data: parametros,
						contentType: false,
						processData: false,
						beforeSend: function() {
							
						},
						success: function(retorno) 
						{
							
							console.log(retorno);
							try 
							{
								var arRetorno = JSON.parse(retorno.trim());
								if (arRetorno['valido'] == 0) 
								{
									alert("Cep não encontrado!");
									return false;
								}
								else if (arRetorno['valido'] == 1) 
								{
									$("#endereco").val(arRetorno[2].endereco);
									$("#bairro").val(arRetorno[2].bairro);
									$("#cidade").val(arRetorno[2].cidade);
									$("#uf").val( arRetorno[2].uf);
								}
							} 
							catch(error) 
							{
								alert("CEP não Encontrado!, Tente novamente...");

								return false;
							}
						}
					});
            }
		
		</script>
	</body>
</html>