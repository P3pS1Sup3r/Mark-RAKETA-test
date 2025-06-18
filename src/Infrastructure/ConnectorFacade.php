<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;

class ConnectorFacade
{
    private string $host;
    private int $port = 6379;
    private ?string $password = null;
    private ?int $dbindex = null;

    public $connector;

    public function __construct($host, $port, $password, $dbindex, private LoggerInterface $logger)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbindex = $dbindex;
    }

    protected function build(): void
    {
        $redis = new Redis();

        try {
            $isConnected = $redis->isConnected();
            if (! $isConnected && $redis->ping('Pong')) {
                $isConnected = $redis->connect(
                    $this->host,
                    $this->port,
                );
            }
        } catch (RedisException $e) {
            $this->logger->error('Не могу подключиться к редиске', [
                'message' => $e->getMessage(),
                'host' => $this->host,
                'port' => $this->port,
            ]);
        }

        if ($isConnected) {
            $redis->auth($this->password);
            $redis->select($this->dbindex);
            $this->connector = new Connector($redis);
        }
    }
}
