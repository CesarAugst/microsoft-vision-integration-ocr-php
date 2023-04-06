<?php

class MicrosoftVision{
    //desc: responsavel por requisitar reconhecimento de texto na imagem para api da microsoft usando rapidapi como interface
    //params: (string) url da trquisicao, (string) corpo da requisicao
    //return: (obj) resposta da requisicao em json
    static public function request_rapidapi($url, $request_body){
        //inicia a requisicao
        $ch = curl_init();
        //define url da requisicao
        curl_setopt($ch, CURLOPT_URL, $url);
        //resposta da requisicao se torna retorno para variavel
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //requisicao POST
        curl_setopt($ch, CURLOPT_POST, 1);
        //corpo da requisicao
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        //define array de cabecalhos
        $headers = array();
        //declara que aceita resposta tipo json
        $headers[] = 'Accept: application/json';
        //declara que conteudo da requisicao tipo json
        $headers[] = 'Content-Type: application/json';
        //declara chave de api da interface RapidAPI
        $headers[] = 'X-RapidAPI-Key: 25d3bfd11fmsh2ac54e25fb3ffc6p17afd4jsn41ed56d77b5a';
        //identifica servico de api da interface RapidAPI
        $headers[] = 'X-RapidAPI-Host: microsoft-computer-vision3.p.rapidapi.com';
        //insere os cabecalhos a requisicao
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //realiza a requisicao e coloca resposta na variavel
        $result = curl_exec($ch);
        //se houve erro na requisicao
        if (curl_errno($ch)) {
            //exibe o erro
            echo 'Error:' . curl_error($ch);
        }
        //finaliza requisicao
        curl_close($ch);
        //retorna resposta da requisicao
        return $result;
    }

    //desc: faz requisicao para azure converter imageme m texto
    //params: (string) url da trquisicao, (string) corpo da requisicao
    //return: (string) url apra requisitar o resultado
    static public function request_azure($url, $request_body){
        //chave de autenticacao portal azure
        $azure_key = "private_key_azure";
        //define vazio a url de consulta de resultado
        $url_result_consult = "";
        //inicia a requisicao
        $ch = curl_init();
        //define url da requisicao
        curl_setopt($ch, CURLOPT_URL, $url);
        //resposta da requisicao se torna retorno para variavel
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //requisicao POST
        curl_setopt($ch, CURLOPT_POST, 1);
        //corpo da requisicao
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        //pega os headers com uma funcao
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$url_result_consult) {
            $matches = array();

            if ( preg_match('/^([^:]+)\s*:\s*([^\x0D\x0A]*)\x0D?\x0A?$/', $header, $matches) )
            {
                //armazena a url de consulta
                if($matches[1] == "operation-location")$url_result_consult = $matches[2];
            }

            return strlen($header);
        });
        //define array de cabecalhos
        $headers = array();
        //declara que aceita resposta tipo json
        $headers[] = 'Accept: application/json';
        //declara que conteudo da requisicao tipo json
        $headers[] = 'Content-Type: application/json';
        //declara chave de api da interface RapidAPI
        $headers[] = "Ocp-Apim-Subscription-Key: $azure_key";
        //insere os cabecalhos a requisicao
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //realiza a requisicao e coloca resposta na variavel
        $result = curl_exec($ch);
        //se houve erro na requisicao
        if (curl_errno($ch)) {
            //exibe o erro
            echo 'Error:' . curl_error($ch);
        }
        //finaliza requisicao
        curl_close($ch);
        //se nao foi possivel consultar a url de resultado
        if($url_result_consult == ""){
            //retorna nulo
            return null;
        }
        //retorna a url de consulta de resultado
        return $url_result_consult;
    }

    //desc: com a url de requisicao consulta o resultado
    //params: (string) url da requisicao
    //return: (obj) dados da conversao parseados em json
    static public function consult_result_translate($url_result_consult){
        //chave de autenticacao portal azure
        $azure_key = "private_key_azure";
        //inicia a requisicao
        $ch = curl_init();
        //define url da requisicao
        curl_setopt($ch, CURLOPT_URL, $url_result_consult);
        //resposta da requisicao se torna retorno para variavel
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //define array de cabecalhos
        $headers = array();
        //declara que aceita resposta tipo json
        $headers[] = 'Accept: application/json';
        //declara que conteudo da requisicao tipo json
        $headers[] = 'Content-Type: application/json';
        //declara chave de api da interface RapidAPI
        $headers[] = "Ocp-Apim-Subscription-Key: $azure_key";
        //insere os cabecalhos a requisicao
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //realiza a requisicao e coloca resposta na variavel
        $result = curl_exec($ch);
        //se houve erro na requisicao
        if (curl_errno($ch)) {
            //exibe o erro
            echo 'Error:' . curl_error($ch);
        }
        //finaliza requisicao
        curl_close($ch);
        //retorna resposta da requisicao
        return json_decode($result);
    }

    //desc: mantem loop de consulta enquanto nao finalizar a conversa
    //params: (obj) resposta da requisicao parseada em json, (string) url de consulta de resultado
    //return: (obj) resposta da requisicao parseada em json
    public static function loop_until_successful($result_translate, $url_result_consult)
    {
        //marca tempo inicial da execucao
        $initial_time = time();
        //enquanto nao fincalizar
        while($result_translate->status != "succeeded"){
            //se passar de 20 segundos retorna vazio
            if(time() - $initial_time > 20){
                //retorna objeto vazio
                $result_translate = json_encode("{}");
                //sai do loop
                break;
            }
            //espera por 2 segundos
            sleep(2);
            //busca reesultado da consulta com a url da requisicao de conversao novamente
            $result_translate = MicrosoftVision::consult_result_translate($url_result_consult);
        }
        //retorna a conversao quando houver sucesso
        return $result_translate;
    }

    //desc: pega a resposta da conversao e formata em um array dividido por linhas
    //params: (obj) resposta da requisicao
    //return: (array) conversao em array da resposta
    public static function format_result_per_line($result_translate)
    {
        //define vazio o array de linhas
        $array_only_lines = [];
        //pega o array onde estao as paginas
        $array_pages = $result_translate->analyzeResult->readResults;
        //para cada pagina
        foreach ($array_pages as $page){
            //pega o array de linhas
            $array_data_lines = $page->lines;
            //para cada linha
            foreach ($array_data_lines as $line){
                //armazena a linha no array
                $array_only_lines[] = $line->text;
            }
        }
        //retorna o array de linhas
        return $array_only_lines;
    }
}