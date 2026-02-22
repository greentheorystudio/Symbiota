<?php
header('Content-Type: text/plain');
if (!function_exists('getallheaders')) {
    function getallheaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
echo "--- FULL HTTP REQUEST ---\n\n";
if (isset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'])) {
    echo $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";
}
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
echo "\n";
$body = file_get_contents('php://input');
echo $body;

echo "--- END OF REQUEST ---\n";

