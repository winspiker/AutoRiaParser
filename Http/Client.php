<?php

declare(strict_types=1);

final class Client
{
    private const REFERER = "https://www.google.com";

    private string $url;

    public function __construct(string $url)
    {
        $this->checkUrl($url);

        $this->url = $url;
    }

    public function request(int $page): string
    {
        $url = sprintf('%s?page=%s', $this->url, $page);

        $ch = \curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, self::REFERER);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        \curl_close($ch);

        if ($data === false) {
            throw new \RuntimeException("Request isn't correct");
        }

        return $data;
    }

    private function checkUrl(string $url)
    {
        $regex = '/^(?:https?:\/\/(?:www\.)?)?[a-z0-9]+(?:[-.][a-z0-9]+)*\.[a-z]{2,}(?::[0-9]{1,5})?(\/.*)?$/';

        if (1 !== \preg_match($url, $regex)) {
            throw new \InvalidArgumentException('Invalid base url: ' . $url);
        }

        try {
            $this->request($url);
        } catch (\RuntimeException) {
            throw new \InvalidArgumentException('Invalid base url: ' . $url);
        }
    }
}