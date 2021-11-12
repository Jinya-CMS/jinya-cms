<?php

namespace App\Web\Actions\Blog\Post;

use App\Database\BlogPost;
use App\Logging\Logger;
use Exception;
use League\Uri\Http as HttpUri;

trait PostActionHelper
{
    protected function executeHook(BlogPost $post, string $host): void
    {
        $logger = Logger::getLogger();
        $body = [
            'post' => $post->format(),
            'url' => "https://$host/" . $post->createdAt->format('Y/m/d') . "/$post->slug",
        ];

        try {
            $category = $post->getCategory();
            $url = HttpUri::createFromString($category?->webhookUrl);
            $postBody = json_encode($body, JSON_THROW_ON_ERROR);

            $scheme = $url->getScheme() === 'https' ? 'ssl://' : '';
            $host = $scheme . $url->getHost();
            $port = $url->getPort() ?: $this->getDefaultPort($url->getScheme());

            $path = $url->getPath();
            $request = "POST $path HTTP/1.1\r\n";
            $request .= 'Host: ' . $url->getHost() . "\r\n";
            $request .= "Content-Type: application/json\r\n";
            $request .= "Content-Length: " . strlen($postBody) . "\r\n";
            $request .= "Connection: Close\r\n";
            $request .= "\r\n";
            $request .= $postBody;

            $errno = null;
            $errstr = null;
            $socket = @fsockopen($host, $port, $errno, $errstr, 5);

            if (!$socket) {
                $logger->warning("Failed to open socket: $errno, $errstr");
                return;
            }

            fwrite($socket, $request);
            fclose($socket);
        } catch (Exception $exception) {
            $logger->warning('Failed to send webhook: ' . $exception->getMessage());
        }
    }

    private function getDefaultPort(string $scheme): int
    {
        if ($scheme === 'https') {
            return 443;
        }

        return 80;
    }
}