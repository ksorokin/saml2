<?php

declare(strict_types=1);

namespace SAML2\XML\mdrpi;

use SAML2\Utils;

/**
 * Class for handling the mdrpi:PublicationInfo element.
 *
 * @link: http://docs.oasis-open.org/security/saml/Post2.0/saml-metadata-rpi/v1.0/saml-metadata-rpi-v1.0.pdf
 * @package SimpleSAMLphp
 */
final class PublicationInfo
{
    /**
     * The identifier of the metadata publisher.
     *
     * @var string
     */
    private $publisher;

    /**
     * The creation timestamp for the metadata, as a UNIX timestamp.
     *
     * @var int|null
     */
    private $creationInstant;

    /**
     * Identifier for this metadata publication.
     *
     * @var string|null
     */
    private $publicationId;

    /**
     * Link to usage policy for this metadata.
     *
     * This is an associative array with language=>URL.
     *
     * @var array
     */
    private $UsagePolicy = [];

    /**
     * Create/parse a mdrpi:PublicationInfo element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(\DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('publisher')) {
            throw new \Exception('Missing required attribute "publisher" in mdrpi:PublicationInfo element.');
        }
        $this->publisher = $xml->getAttribute('publisher');

        if ($xml->hasAttribute('creationInstant')) {
            $this->creationInstant = Utils::xsDateTimeToTimestamp($xml->getAttribute('creationInstant'));
        }

        if ($xml->hasAttribute('publicationId')) {
            $this->publicationId = $xml->getAttribute('publicationId');
        }

        $this->UsagePolicy = Utils::extractLocalizedStrings($xml, Common::NS_MDRPI, 'UsagePolicy');
    }

    /**
     * Collect the value of the publisher-property
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Collect the value of the creationInstant-property
     * @return int|null
     */
    public function getCreationInstant()
    {
        return $this->creationInstant;
    }

    /**
     * Collect the value of the publicationId-property
     * @return string|null
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * Collect the value of the UsagePolicy-property
     * @return array
     */
    public function getUsagePolicy()
    {
        return $this->UsagePolicy;
    }

    /**
     * Set the value of the publisher-property
     * @param string $publisher
     */
    public function setPublisher(string $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * Set the value of the creationInstant-property
     * @param int|null $creationInstant
     */
    public function setCreationInstant(int $creationInstant = null)
    {
        $this->creationInstant = $creationInstant;
    }

    /**
     * Set the value of the publicationId-property
     * @param string|null $publicationId
     */
    public function setPublicationId(string $publicationId = null)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * Set the value of the UsagePolicy-property
     * @param array $usagePolicy
     */
    public function setUsagePolicy(array $usagePolicy)
    {
        $this->UsagePolicy = $usagePolicy;
    }

    /**
     * Convert this element to XML.
     *
     * @param \DOMElement $parent The element we should append to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_string($this->publisher));
        assert(is_int($this->creationInstant) || is_null($this->creationInstant));
        assert(is_string($this->publicationId) || is_null($this->publicationId));
        assert(is_array($this->UsagePolicy));

        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(Common::NS_MDRPI, 'mdrpi:PublicationInfo');
        $parent->appendChild($e);

        $e->setAttribute('publisher', $this->publisher);

        if ($this->creationInstant !== null) {
            $e->setAttribute('creationInstant', gmdate('Y-m-d\TH:i:s\Z', $this->creationInstant));
        }

        if ($this->publicationId !== null) {
            $e->setAttribute('publicationId', $this->publicationId);
        }

        Utils::addStrings($e, Common::NS_MDRPI, 'mdrpi:UsagePolicy', true, $this->UsagePolicy);

        return $e;
    }
}
