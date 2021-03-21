<?php

namespace Citsk\Controllers;

use Citsk\Exceptions\DataBaseException;
use Citsk\Library\Shared;
use Exception;

class ControllerBase
{

    /**
     * @return object|null
     */
    public function callRequestedMethod(): ?array
    {

        global $ROUTE;

        $action = Shared::toCamelCase($ROUTE['action']);

        //controller methods
        if (method_exists($this, $action)) {

            try {
                return call_user_func([$this, $action]);
            } catch (DataBaseException | Exception $e) {
                $this->errorResponse($e->getCode(), $e->getMessage());

            }
        }

        //model methods
        if (method_exists($this->model, $action)) {

            try {
                $data = call_user_func([$this->model, $action]);
                $this->dataResponse($data);
            } catch (DataBaseException | Exception $e) {
                $this->errorResponse($e->getCode(), $e->getMessage());

            }
        }

        exit(http_response_code(404));
    }

    /**
     * @param mixed $data
     * @param bool $isSpliceStructure
     *
     * @return void
     */
    public function dataResponse($data, $isSpliceStructure = true): void
    {
        $result = [];

        if (is_array($data)) {
            $result = $data;
        }

        if (is_object($data)) {

            if ($data->structure) {
                if ($isSpliceStructure && count($data->structure) == 1) {
                    $result = $data->structure[0];
                } else {
                    $result = $data->structure;
                }
            }
        }

        exit(json_encode($result));
    }

    /**
     * @param array|null $data
     * @param int $status
     *
     * @return void
     */
    public function successResponse(?array $data = null, int $status = 1): void
    {
        $response = [
            "status" => $status,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        exit(json_encode($response));
    }

    /**
     * @param int $status
     * @param array|null $data
     *
     * @return void
     */
    public function errorResponse(int $status = 0, ?string $errorMessage = null): void
    {
        $response = [
            "status" => $status,
        ];

        if ($errorMessage) {
            $response['error'] = $errorMessage;
        }

        exit(json_encode($response));
    }

    /**
     * @param string $HTTPMethod
     *
     * @return void
     */
    protected function setHTTPMethod($HTTPMethod = "POST"): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($HTTPMethod)) {
            exit(http_response_code(405));
        }
    }
}
