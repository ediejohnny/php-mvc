<?php 

namespace App\Http;

class Response {
    /**
     * Codigo do Status HTTP
     * @var integer
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do response
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteudo que está sendo retornado
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo da Response
     * @var string
     */
    private $content;

    /**
     * Construtor da classe
     * @param integer $httpCode
     * @param string $content
     * @param string $contentType
     */
    public function __construct($httpCode, $content, $contentType = 'text/html') {
        $this->httpCode     = $httpCode;
        $this->content      = $content;
        $this->setContentType($contentType);
    }
    
    /**
     * Metodo responsavel por alterar o content type do response
     * @param string $contentType
     */
    public function setContentType($contentType) {
      $this->contentType  = $contentType;
      $this->addHeader('Content-type', $contentType);
    }

    /**
     * Adiciona os registros ao cabeçalho da requisição
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
    }

    private function sendHeaders() {
      // Aplica o status code
      http_response_code($this->httpCode);

      // Preenche os headers da requisição
      foreach($this->headers as $key => $value) {
        header($key.': '.$value);
      }
    }

    public function sendResponse() {
      // Envia os headers da requisição
      $this->sendHeaders();

      // Imprime o conteúdo
      switch($this->contentType) {
        case 'text/html':
          echo $this->content;
          exit;
        case 'application/json':
          echo json_encode($this->content, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
          exit;
        case 'text/plain':
          echo $this->content;
          exit;
        default:
          echo 'Content-type not supported.';
          exit;
      }
    }

}