<?php
/**
 * @var Goridge\RelayInterface $relay
 */

use Spiral\Debug\Dumper;
use Spiral\Debug\Renderer\ConsoleRenderer;
use Spiral\Goridge;
use Spiral\RoadRunner;

ini_set('display_errors', 'stderr');
require 'vendor/autoload.php';

$worker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($worker);

$relay = new Spiral\Goridge\SocketRelay("127.0.0.1", 6001);
$rpc = new Spiral\Goridge\RPC($relay);

$dumper = new Dumper();
$dumper->setRenderer(Dumper::ERROR_LOG, new ConsoleRenderer());


while ($req = $psr7->acceptRequest()) {
    try {
        $resp = new \Zend\Diactoros\Response();

        $params = $req->getQueryParams();
        if (!array_key_exists('login', $params)) {
            $resp->getBody()->write("cannot find login\n");
        } else {
            $login = $params['login'];
            $permission = $rpc->call('myRpc.Check', $login);
            $resp->getBody()->write(sprintf("Permission: %s", $permission ));
        }

        $psr7->respond($resp);
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
