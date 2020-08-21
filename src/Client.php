<?php

namespace DoubleOh13\RocketShipIt;

use DoubleOh13\RocketShipIt\Exceptions\RocketShipItBinaryMissingException;
use DoubleOh13\RocketShipIt\Exceptions\RocketShipItBinaryMissingOrNotExecutableException;
use DoubleOh13\RocketShipIt\Exceptions\RocketShipItException;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    protected $binPath;
    protected $apiKey;
    protected $endpoint;

    public function __construct($binPath = '', $apiKey = '', $endpoint = 'https://api.rocketship.it/v1/')
    {
        $this->binPath = $binPath;
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    public function request(array $params)
    {
        // Try the binary first
        try {
            return $this->binRequest($params);
        } catch (\Exception $exception) {
            // do nothing
        }

        //if that fails, try an API Request
        return $this->httpRequest($params);
    }

    public function httpRequest(array $params)
    {
        $client = new GuzzleClient;

        $response = $client->post(
            $this->endpoint,
            [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                ],
                'json' => $params,
            ]
        );

        return json_decode($response->getBody());
    }

    /**
     * @param $params
     *
     * @throws \DoubleOh13\RocketShipIt\Exceptions\RocketShipItBinaryMissingException
     */
    public function binRequest($params)
    {
        if(empty($this->binPath) || !file_exists($this->binPath)) {
            throw new RocketShipItBinaryMissingException($this->binPath);
        }

        $descriptorspec = [
            0 => ['pipe', 'r'], // stdin is a pipe that the child will read from
            1 => ['pipe', 'w'], // stdout is a pipe that the child will write to
            2 => ['pipe', 'w'], // stderr is a file to write to
        ];

        $pipes = [];

        if(!is_executable($this->binPath)) {
            chmod($this->binPath, 0755);
        }

        if(!is_executable($this->binPath)) {
            throw new RocketShipItBinaryMissingOrNotExecutableException($this->binPath);
        }

        $command = pathinfo($this->binPath, PATHINFO_FILENAME);
        $workingDirectory = pathinfo($this->binPath, PATHINFO_DIRNAME);

        if(strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
            $command = './' . $command;
        }

        $process = proc_open($command, $descriptorspec, $pipes, $workingDirectory);

        if(is_resource($process)) {
            // send the data via stdin to RocketShipIt
            fwrite($pipes[0], json_encode($params));
            fclose($pipes[0]);

            // response from RocketShipIt
            $result = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $returnValue = proc_close($process);
        }

        $resp = json_decode($result, true);
        if(!$resp) {
            throw new RocketShipItException('Unable to communicate with RocketShipIt binary or parse JSON, got: '. $result. ' '. $errors);
        }

        return $resp;
    }
}
