<?php
//registra o tempo inicial
$initialTime = time();
//chama classe de requisicoes microsoft
require_once "class-microsoft_vision.php";

//definicao de url
$url = "https://microsoft-computer-vision3.p.rapidapi.com/ocr";
//corpo da reequisicao
$request_body = "{'url': '".$_GET['url']."'}";
//exibe resultado da requisicao
$result = MicrosoftVision::request_rapidapi($url, $request_body);
//converte a resposta em um array dividido por linhas
$array_result_per_lines = MicrosoftVision::format_result_per_line_rapidapi($result);

//armazena dados comparativos
store_comparative_result($initialTime, $_GET['url'], implode(PHP_EOL, $array_result_per_lines));

echo json_encode($array_result_per_lines);

//desc: gerencia o armazenamento do resultado para fins de comparacaao
//parasm: (time) tempo inicial, (string) url, (string) conteudo convertido
//return: nenhum
function store_comparative_result($initialTime, $url, $result_translate){
//caminho para salvar resultados
    $path = "./results";
    //nome do arquivo
    $file_name = "rapid-api-" . str_replace(".", "-", substr($url, -5)) . ".txt";
    //se o caminho do diretorio nao existir
    if(!is_dir($path)){
        //cria o diretorio
        mkdir($path, 0755);
    }

    //junta o tempo gasto com o resultado da conversao
    $array_data = array_merge(
        ['tempo_gasto' => 'tempo gasto: '.(time() - $initialTime).' segundos'],
        ['url' => "url: $url"],
        ['resultado' => "resultado: \n$result_translate"]
    );

    //salva em arquivo
    file_put_contents("$path/$file_name", implode( PHP_EOL, ($array_data))); //armazena resultado

}