<?php

namespace App\Security;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    private $client;
    private $endpoint;

    public function __construct(HttpClientInterface $client, string $akismetKey)
    {
        $this->client = $client;
        $this->endpoint = 'https://rest.akismet.com/1.1/comment-check';
        $this->api_key = $akismetKey;
    }

    /**
     * @return int Spam score: 0: not spam, 1: maybe spam, 2: blatant spam
     *
     * @throws \RuntimeException if the call did not work
     */
    public function getSpamScore(array $comment, array $context): int
    {
        $request_data = array_merge($context, [
            'api_key' => $this->api_key,
            'blog' => 'https://zinnechoeur.be',
            'comment_type' => 'contact-form',
            'comment_author' => $comment['author'],
            'comment_author_email' => $comment['email'],
            'comment_content' => $comment['content'],
            'blog_lang' => 'fr',
            'blog_charset' => 'UTF-8',
        ]);

        $response = $this->client->request('POST', $this->endpoint, ['body' => $request_data]);
        $headers = $response->getHeaders();

        if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')) {
            return 2;
        }

        $content = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])) {
            throw new \RuntimeException(sprintf('Unable to check for spam: %s (%s).', $content, $headers['x-akismet-debug-help'][0]));
        }

        return 'true' === $content ? 1 : 0;
    }
}