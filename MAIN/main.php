<?php
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
set_time_limit(0);
$address = '0.0.0.0';
$port = 8888;
ob_implicit_flush();

$sock = socket_create(AF_INET, SOCK_STREAM, 0);

if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
    echo 'Не могу установить опцию на сокете: ' . socket_strerror(socket_last_error()) . PHP_EOL;
}

socket_bind($sock, $address, $port) or error_log('Could not bind to address');;
socket_listen($sock);
socket_set_nonblock($sock);

$clients = [];

function send_data($client, $string)
{
    socket_set_nonblock($client);

    while ($string != '') {
        $write = [$client];
        $read = $except = null;
        $n = socket_select($read, $write, $except, 1);
        if ($n === false) {
            return;
        } elseif ($n > 0) {
            $length = strlen($string);
            $sent = socket_write($client, $string, $length);
            if ($sent === false || $sent == $length) {
                return;
            } else /* $sent < $length */ {
                $string = substr($string, $sent);
            }
        }
    }
}

while (true) {
    if ($newsock = socket_accept($sock)) {
        if (is_resource($newsock)) {
            socket_write($newsock, "", 1) . chr(0);
            socket_set_nonblock($newsock);
            echo "New client connected\n";
            $clients[] = $newsock;
        }
    }

    if (count($clients)) {

        foreach ($clients as $k => $client_socket) {
            $res_type = get_resource_type($client_socket);
            if ($res_type === "Unknown") {
                unset($clients[$k]);
                unset($client_socket);
                array_values($clients);
            }
            if (isset($client_socket)) {

                $data = '';
                $done = false;

                while (!$done) {

                    socket_clear_error($sock);
                    $bytes = @socket_recv($client_socket, $r_data, 1024, MSG_DONTWAIT);

                    $lastError = socket_last_error($sock);
                    if ($lastError != 11 && $lastError > 0) {
                        $done = true;
                    } else if ($bytes === false) {
                        $done = true;
                    } else if (intval($bytes) > 0) {
                        $data .= $r_data;
                    } else {
                        usleep(2000); // prevent "CPU burn"
                        break;
                    }
                }

                if ($data && $data != '') {
                    echo "FromClient: " . $data . "\n";
                        $strData = "ВСТАВЬ СЮДА СВОИ ЛЮБЫЕ ДАННЫЕ";
                        send_data($client_socket, $strData . '||||END');
                        break;
                }
            }
        }
    }
}
