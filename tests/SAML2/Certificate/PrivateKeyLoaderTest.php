<?php

declare(strict_types=1);

namespace SAML2\Tests\Certificate;

use SAML2\Configuration\PrivateKey as ConfPrivateKey;
use SAML2\Certificate\PrivateKey;
use SAML2\Certificate\PrivateKeyLoader;

class PrivateKeyLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \SAML2\Certificate\PrivateKeyLoader
     */
    private $privateKeyLoader;

    public function setUp()
    {
        $this->privateKeyLoader = new PrivateKeyLoader();
    }

    /**
     * @group        certificate
     * @test
     * @dataProvider privateKeyTestProvider
     *
     * @param \SAML2\Configuration\PrivateKey $configuredKey
     */
    public function loading_a_configured_private_key_returns_a_certificate_private_key(
        ConfPrivateKey $configuredKey
    ) {
        $resultingKey = $this->privateKeyLoader->loadPrivateKey($configuredKey);

        $this->assertInstanceOf(PrivateKey::class, $resultingKey);
        $this->assertEquals($resultingKey->getKeyAsString(), "This would normally contain the private key data.\n");
        $this->assertEquals($resultingKey->getPassphrase(), $configuredKey->getPassPhrase());
    }

    /**
     * Dataprovider for 'loading_a_configured_private_key_returns_a_certificate_private_key'
     *
     * @return array
     */
    public function privateKeyTestProvider()
    {
        return [
            'no passphrase'   => [
                new ConfPrivateKey(
                    dirname(__FILE__) . '/File/a_fake_private_key_file.pem',
                    ConfPrivateKey::NAME_DEFAULT
                )
            ],
            'with passphrase' => [
                new ConfPrivateKey(
                    dirname(__FILE__) . '/File/a_fake_private_key_file.pem',
                    ConfPrivateKey::NAME_DEFAULT,
                    'foo bar baz'
                )
            ],
        ];
    }
}
