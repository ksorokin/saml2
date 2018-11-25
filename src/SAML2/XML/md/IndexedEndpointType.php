<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use SAML2\Utils;

/**
 * Class representing SAML 2 IndexedEndpointType.
 *
 * @package SimpleSAMLphp
 */
final class IndexedEndpointType extends EndpointType
{
    /**
     * The index for this endpoint.
     *
     * @var int
     */
    private $index;

    /**
     * Whether this endpoint is the default.
     *
     * @var bool|null
     */
    private $isDefault = null;

    /**
     * Initialize an IndexedEndpointType.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(\DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('index')) {
            throw new \Exception('Missing index on '.$xml->tagName);
        }
        $this->index = intval($xml->getAttribute('index'));

        $this->isDefault = Utils::parseBoolean($xml, 'isDefault', null);
    }

    /**
     * Collect the value of the index-property
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the value of the index-property
     * @param int $index
     */
    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    /**
     * Collect the value of the isDefault-property
     * @return bool|null
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set the value of the isDefault-property
     * @param bool|null $flag
     */
    public function setIsDefault(bool $flag = null)
    {
        $this->isDefault = $flag;
    }


    /**
     * Add this endpoint to an XML element.
     *
     * @param \DOMElement $parent The element we should append this endpoint to.
     * @param string     $name   The name of the element we should create.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent, string $name)
    {
        assert(is_int($this->index));
        assert(is_null($this->isDefault) || is_bool($this->isDefault));

        $e = parent::toXML($parent, $name);
        $e->setAttribute('index', strval($this->index));

        if ($this->isDefault === true) {
            $e->setAttribute('isDefault', 'true');
        } elseif ($this->isDefault === false) {
            $e->setAttribute('isDefault', 'false');
        }

        return $e;
    }
}
