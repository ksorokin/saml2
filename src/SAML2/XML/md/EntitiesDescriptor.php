<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\SignedElementHelper;
use SAML2\Utils;

/**
 * Class representing SAML 2 EntitiesDescriptor element.
 *
 * @package SimpleSAMLphp
 */
final class EntitiesDescriptor extends SignedElementHelper
{
    /**
     * The ID of this element.
     *
     * @var string|null
     */
    public $ID;

    /**
     * The name of this entity collection.
     *
     * @var string|null
     */
    public $Name;

    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var array
     */
    public $Extensions = [];

    /**
     * Child EntityDescriptor and EntitiesDescriptor elements.
     *
     * @var (\SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor)[]
     */
    public $children = [];

    /**
     * Initialize an EntitiesDescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(\DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if ($xml->hasAttribute('ID')) {
            $this->ID = $xml->getAttribute('ID');
        }
        if ($xml->hasAttribute('validUntil')) {
            $this->setValidUntil(Utils::xsDateTimeToTimestamp($xml->getAttribute('validUntil')));
        }
        if ($xml->hasAttribute('cacheDuration')) {
            $this->setCacheDuration($xml->getAttribute('cacheDuration'));
        }
        if ($xml->hasAttribute('Name')) {
            $this->Name = $xml->getAttribute('Name');
        }

        $this->Extensions = Extensions::getList($xml);

        foreach (Utils::xpQuery($xml, './saml_metadata:EntityDescriptor|./saml_metadata:EntitiesDescriptor') as $node) {
            if ($node->localName === 'EntityDescriptor') {
                $this->children[] = new EntityDescriptor($node);
            } else {
                $this->children[] = new EntitiesDescriptor($node);
            }
        }
    }

    /**
     * Convert this EntitiesDescriptor to XML.
     *
     * @param \DOMElement|null $parent The EntitiesDescriptor we should append this EntitiesDescriptor to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent = null)
    {
        assert(is_null($this->ID) || is_string($this->ID));
        assert(is_null($this->getValidUntil()) || is_int($this->getValidUntil()));
        assert(is_null($this->getCacheDuration()) || is_string($this->getCacheDuration()));
        assert(is_null($this->Name) || is_string($this->Name));
        assert(is_array($this->Extensions));
        assert(is_array($this->children));

        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_MD, 'md:EntitiesDescriptor');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:EntitiesDescriptor');
            $parent->appendChild($e);
        }

        if (isset($this->ID)) {
            $e->setAttribute('ID', $this->ID);
        }

        if ($this->getValidUntil() !== null) {
            $e->setAttribute('validUntil', gmdate('Y-m-d\TH:i:s\Z', $this->getValidUntil()));
        }

        if ($this->getCacheDuration() !== null) {
            $e->setAttribute('cacheDuration', $this->getCacheDuration());
        }

        if (isset($this->Name)) {
            $e->setAttribute('Name', $this->Name);
        }

        Extensions::addList($e, $this->Extensions);

        /** @var \SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor $node */
        foreach ($this->children as $node) {
            $node->toXML($e);
        }

        $this->signElement($e, $e->firstChild);

        return $e;
    }
}
