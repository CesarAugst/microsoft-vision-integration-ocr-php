<?php
//chama classe de requisicoes microsoft
require_once "class-microsoft_vision.php";

//definicao de url
$url = "https://southcentralus.api.cognitive.microsoft.com/vision/v3.2/read/analyze";
//corpo da reequisicao
$request_body = "{'url':'https://s.dicio.com.br/texto.jpg'}";
//solicita conversao de imagem em texto
$url_result_consult = MicrosoftVision::request_azure($url, $request_body);
//se nao foi possivel converter
if(!$url_result_consult){
    die("nao foi possivel converter");
}
//busca reesultado da consulta com a url da requisicao de conversao
$result_translate = MicrosoftVision::consult_result_translate($url_result_consult);
//busca reesultado da consulta com a url da requisicao de conversao
$result_translate = MicrosoftVision::loop_until_successful($result_translate, $url_result_consult);
//converte a resposta em um array dividido por linhas
$array_result_per_lines = MicrosoftVision::format_result_per_line($result_translate);


echo json_encode($array_result_per_lines);