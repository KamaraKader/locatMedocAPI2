<?php
namespace Src\Controller;

use Src\TableGateways\LocaliteGateway;

class LocaliteController {

    private $db;
    private $requestMethod;
    private $localId;

    private $localiteGateway;

    public function __construct($db, $requestMethod, $localId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->localId = $localId;

        $this->localiteGateway = new LocaliteGateway($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->localId) {
                    $response = $this->getLocal($this->localId);
                } else {
                    $response = $this->getAllLocal();
                };
                break;
            case 'POST':
                $response = $this->createLocalFromRequest();
                break;
            case 'PUT':
                $response = $this->updateLocalFromRequest($this->localId);
                break;
            case 'DELETE':
                $response = $this->deleteLocal($this->localId);
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

    private function getAllLocal()
    {
        $result = $this->localiteGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getLocal($id)
    {
        $result = $this->localiteGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createLocalFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateLocal($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->localiteGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateLocalFromRequest($id)
    {
        $result = $this->localiteGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateLocal($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->localiteGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteLocal($id)
    {
        $result = $this->localiteGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->localiteGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateLocal($input)
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