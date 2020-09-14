<?php
namespace Src\Controller;

use Src\TableGateways\PharmacieGateway;

class PharmacieController {

    private $db;
    private $requestMethod;
    private $pharmaId;

    private $pharmacieGateway;

    public function __construct($db, $requestMethod, $pharmaId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->pharmaId = $pharmaId;

        $this->pharmacieGateway = new PharmacieGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->pharmaId) {
                    $response = $this->getPharma($this->pharmaId);
                } else {
                    $response = $this->getAllPharma();
                };
                break;
            case 'POST':
                $response = $this->createPharmaFromRequest();
                break;
            case 'PUT':
                $response = $this->updatePharmaFromRequest($this->pharmaId);
                break;
            case 'DELETE':
                $response = $this->deletePharma($this->pharmaId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllPharma()
    {
        $result = $this->pharmacieGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getPharma($id)
    {
        $result = $this->pharmacieGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createPharmaFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePharma($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->pharmacieGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updatePharmaFromRequest($id)
    {
        $result = $this->pharmacieGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePharma($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->pharmacieGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deletePharma($id)
    {
        $result = $this->pharmacieGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->pharmacieGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validatePharma($input)
    {
        if (! isset($input['name'])) {
            return false;
        }
        if (! isset($input['description'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}