<?php

declare(strict_types=1);

namespace SAML2\XML\ds;

use RobRichards\XMLSecLibs\XMLSecurityDSig;
use SAML2\XML\Chunk;

/**
 * Class representing a ds:KeyInfo element.
 *
 * @package SimpleSAMLphp
 */
final class KeyInfo
{
    /**
     * The Id attribute on this element.
     *
     * @var string|null
     */
    private $Id = null;

    /**
     * The various key information elements.
     *
     * Array with various elements describing this key.
     * Unknown elements will be represented by \SAML2\XML\Chunk.
     *
     * @var (\SAML2\XML\Chunk|\SAML2\XML\ds\KeyName|\SAML2\XML\ds\X509Data)[]
     */
    private $info = [];

    /**
     * Initialize a KeyInfo element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(\DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if ($xml->hasAttribute('Id')) {
            $this->Id = $xml->getAttribute('Id');
        }

        for ($n = $xml->firstChild; $n !== null; $n = $n->nextSibling) {
            if (!($n instanceof \DOMElement)) {
                continue;
            }

            if ($n->namespaceURI !== XMLSecurityDSig::XMLDSIGNS) {
                $this->info[] = new Chunk($n);
                continue;
            }
            switch ($n->localName) {
                case 'KeyName':
                    $this->info[] = new KeyName($n);
                    break;
                case 'X509Data':
                    $this->info[] = new X509Data($n);
                    break;
                default:
                    $this->info[] = new Chunk($n);
                    break;
            }
        }
    }

    /**
     * Collect the value of the Id-property
     * @return string|null
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Set the value of the Id-property
     * @param string|null $id
     */
    public function setId(string $id = null)
    {
        $this->Id = $id;
    }


    /**
     * Collect the value of the info-property
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set the value of the info-property
     * @param array $info
     */
    public function setInfo(array $info)
    {
        $this->info = $info;
    }

    /**
     * Add the value to the info-property
     * @param \SAML2\XML\Chunk|\SAML2\XML\ds\KeyName|\SAML2\XML\ds\X509Data $info
     */
    public function addInfo($info)
    {
        assert($info instanceof Chunk || $info instanceof KeyName || $info instanceof X509Data);
        $this->info[] = $info;
    }

    /**
     * Convert this KeyInfo to XML.
     *
     * @param \DOMElement $parent The element we should append this KeyInfo to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_null($this->Id) || is_string($this->Id));
        assert(is_array($this->info));

        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(XMLSecurityDSig::XMLDSIGNS, 'ds:KeyInfo');
        $parent->appendChild($e);

        if (isset($this->Id)) {
            $e->setAttribute('Id', $this->Id);
        }

        /** @var \SAML2\XML\Chunk|\SAML2\XML\ds\KeyName|\SAML2\XML\ds\X509Data $n */
        foreach ($this->info as $n) {
            $n->toXML($e);
        }

        return $e;
    }
}
