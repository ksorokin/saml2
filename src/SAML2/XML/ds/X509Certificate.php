<?php

declare(strict_types=1);

namespace SAML2\XML\ds;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use SAML2\Utils;

/**
 * Class representing a ds:X509Certificate element.
 *
 * @package SimpleSAMLphp
 */
final class X509Certificate
{
    /**
     * The base64-encoded certificate.
     *
     * @var string
     */
    private $certificate;

    /**
     * Initialize an X509Certificate element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(\DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        $this->certificate = $xml->textContent;
    }


    /**
     * Collect the value of the certificate-property
     * @return string
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * Set the value of the certificate-property
     * @param string $certificate
     */
    public function setCertificate(string $certificate)
    {
        $this->certificate = $certificate;
    }


    /**
     * Convert this X509Certificate element to XML.
     *
     * @param \DOMElement $parent The element we should append this X509Certificate element to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_string($this->certificate));

        return Utils::addString($parent, XMLSecurityDSig::XMLDSIGNS, 'ds:X509Certificate', $this->certificate);
    }
}
