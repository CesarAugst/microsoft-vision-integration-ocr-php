<?php
//chama classe de requisicoes microsoft
require_once "class-microsoft_vision.php";

//definicao de url
$url = "https://microsoft-computer-vision3.p.rapidapi.com/ocr";
//corpo da reequisicao
$request_body = "{'url': 'https://s.dicio.com.br/texto.jpg'}";
//exibe resultado da requisicao
echo MicrosoftVision::request_rapidapi($url, $request_body);


