<?php
namespace CodeCloud\Bundle\ShopifyBundle\Security;

class HmacSignature
{
    /**
     * @var string
     */
    private $sharedSecret;

    /**
     * @param string $sharedSecret
     */
    public function __construct(string $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    /**
     * Check if the signature is correct
     * @param string $signature
     * @param array $params
     * @return bool
     */
    public function isValid($signature, array $params): bool
    {
        return $this->generateHmac($params) === $signature;
    }

    /**
     * Generate parameters to be used to authenticate subsequent requests
     * @param string $storeName
     * @param array  $params
     * @return array
     */
    public function generateParams($storeName, array $params = []): array
    {
        $timestamp = time();

        $params['shop'] = $storeName;
        $params['timestamp'] = $timestamp;

        $hmac = $this->generateHmac($params);
        $params['hmac'] = $hmac;

        return $params;
    }

    /**
     * @param array $params
     * @param bool  $rawOutput
     * @return string
     */
    private function generateHmac(array $params, bool $rawOutput = false): string
    {
        $signatureParts = array();

        foreach ($params as $key => $value) {
            if (in_array($key, array('signature', 'hmac'))) {
                continue;
            }

            if (is_array($value)) {
                if (1 == count($value)) {
                    $signatureParts[] = $key.'='.$value[0];
                }
            } else {
                $signatureParts[] = $key.'='.$value;
            }
        }

        natsort($signatureParts);

        return hash_hmac(
            'sha256',
            implode('&', $signatureParts),
            $this->sharedSecret,
            $rawOutput
        );
    }
}
