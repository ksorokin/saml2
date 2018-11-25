<?php

declare(strict_types=1);

namespace SAML2\XML\mdrpi;

use SAML2\Utils;

/**
 * Class for handling the mdrpi:RegistrationInfo element.
 *
 * @link: http://docs.oasis-open.org/security/saml/Post2.0/saml-metadata-rpi/v1.0/saml-metadata-rpi-v1.0.pdf
 * @package SimpleSAMLphp
 */
final class RegistrationInfo
{
    /**
     * The identifier of the metadata registration authority.
     *
     * @var string
     */
    private $registrationAuthority;

    /**
     * The registration timestamp for the metadata, as a UNIX timestamp.
     *
     * @var int|null
     */
    private $registrationInstant;

    /**
     * Link to registration policy for this metadata.
     *
     * This is an associative array with language=>URL.
     *
     * @var array
     */
    private $RegistrationPolicy = [];

    /**
     * Create/parse a mdrpi:RegistrationInfo element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(\DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('registrationAuthority')) {
            throw new \Exception('Missing required attribute "registrationAuthority" in mdrpi:RegistrationInfo element.');
        }
        $this->registrationAuthority = $xml->getAttribute('registrationAuthority');

        if ($xml->hasAttribute('registrationInstant')) {
            $this->registrationInstant = Utils::xsDateTimeToTimestamp($xml->getAttribute('registrationInstant'));
        }

        $this->RegistrationPolicy = Utils::extractLocalizedStrings($xml, Common::NS_MDRPI, 'RegistrationPolicy');
    }

    /**
     * Collect the value of the RegistrationAuthority-property
     * @return string
     */
    public function getRegistrationAuthority()
    {
        return $this->registrationAuthority;
    }

    /**
     * Set the value of the registrationAuthority-property
     * @param string $registrationAuthority
     */
    public function setRegistrationAuthority(string $registrationAuthority)
    {
        $this->registrationAuthority = $registrationAuthority;
    }

    /**
     * Collect the value of the registrationInstant-property
     * @return int|null
     */
    public function getRegistrationInstant()
    {
        return $this->registrationInstant;
    }

    /**
     * Set the value of the registrationInstant-property
     * @param int|null $registrationInstant
     */
    public function setRegistrationInstant(int $registrationInstant = null)
    {
        $this->registrationInstant = $registrationInstant;
    }

    /**
     * Collect the value of the RegistrationPolicy-property
     * @return array
     */
    public function getRegistrationPolicy()
    {
        return $this->RegistrationPolicy;
    }

    /**
     * Set the value of the RegistrationPolicy-property
     * @param array $registrationPolicy
     */
    public function setRegistrationPolicy(array $registrationPolicy)
    {
        $this->RegistrationPolicy = $registrationPolicy;
    }

    /**
     * Convert this element to XML.
     *
     * @param \DOMElement $parent The element we should append to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_string($this->registrationAuthority));
        assert(is_int($this->registrationInstant) || is_null($this->registrationInstant));
        assert(is_array($this->RegistrationPolicy));

        if (empty($this->registrationAuthority)) {
            throw new \Exception('Missing required registration authority.');
        }

        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(Common::NS_MDRPI, 'mdrpi:RegistrationInfo');
        $parent->appendChild($e);

        $e->setAttribute('registrationAuthority', $this->registrationAuthority);

        if ($this->registrationInstant !== null) {
            $e->setAttribute('registrationInstant', gmdate('Y-m-d\TH:i:s\Z', $this->registrationInstant));
        }

        Utils::addStrings($e, Common::NS_MDRPI, 'mdrpi:RegistrationPolicy', true, $this->RegistrationPolicy);

        return $e;
    }
}
