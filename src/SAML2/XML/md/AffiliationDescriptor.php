<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use SAML2\Constants;
use SAML2\SignedElementHelper;
use SAML2\Utils;

/**
 * Class representing SAML 2 AffiliationDescriptor element.
 *
 * @package SimpleSAMLphp
 */
final class AffiliationDescriptor extends SignedElementHelper
{
    /**
     * The affiliationOwnerID.
     *
     * @var string
     */
    private $affiliationOwnerID;

    /**
     * The ID of this element.
     *
     * @var string|null
     */
    private $ID;

    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var \SAML2\XML\Chunk[]
     */
    private $Extensions = [];

    /**
     * The AffiliateMember(s).
     *
     * Array of entity ID strings.
     *
     * @var array
     */
    private $AffiliateMember = [];

    /**
     * KeyDescriptor elements.
     *
     * Array of \SAML2\XML\md\KeyDescriptor elements.
     *
     * @var \SAML2\XML\md\KeyDescriptor[]
     */
    private $KeyDescriptor = [];

    /**
     * Initialize a AffiliationDescriptor.
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

        if (!$xml->hasAttribute('affiliationOwnerID')) {
            throw new \Exception('Missing affiliationOwnerID on AffiliationDescriptor.');
        }
        $this->affiliationOwnerID = $xml->getAttribute('affiliationOwnerID');

        if ($xml->hasAttribute('ID')) {
            $this->ID = $xml->getAttribute('ID');
        }

        if ($xml->hasAttribute('validUntil')) {
            $this->setValidUntil(Utils::xsDateTimeToTimestamp($xml->getAttribute('validUntil')));
        }

        if ($xml->hasAttribute('cacheDuration')) {
            $this->setCacheDuration($xml->getAttribute('cacheDuration'));
        }

        $this->Extensions = Extensions::getList($xml);

        $this->AffiliateMember = Utils::extractStrings($xml, Constants::NS_MD, 'AffiliateMember');
        if (empty($this->AffiliateMember)) {
            throw new \Exception('Missing AffiliateMember in AffiliationDescriptor.');
        }

        foreach (Utils::xpQuery($xml, './saml_metadata:KeyDescriptor') as $kd) {
            $this->KeyDescriptor[] = new KeyDescriptor($kd);
        }
    }

    /**
     * Collect the value of the affiliationOwnerId-property
     * @return string
     */
    public function getAffiliationOwnerID()
    {
        return $this->affiliationOwnerID;
    }

    /**
     * Set the value of the affiliationOwnerId-property
     * @param string $affiliationOwnerId
     */
    public function setAffiliationOwnerID(string $affiliationOwnerId)
    {
        $this->affiliationOwnerID = $affiliationOwnerId;
    }

    /**
     * Collect the value of the ID-property
     * @return string|null
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * Set the value of the ID-property
     * @param string|null $Id
     */
    public function setID(string $Id = null)
    {
        $this->ID = $Id;
    }

    /**
     * Collect the value of the Extensions-property
     * @return \SAML2\XML\Chunk[]
     */
    public function getExtensions()
    {
        return $this->Extensions;
    }

    /**
     * Set the value of the Extensions-property
     * @param array $extensions
     */
    public function setExtensions(array $extensions)
    {
        $this->Extensions = $extensions;
    }

    /**
     * Collect the value of the AffiliateMember-property
     * @return array
     */
    public function getAffiliateMember()
    {
        return $this->AffiliateMember;
    }

    /**
     * Set the value of the AffiliateMember-property
     * @param array $affiliateMember
     */
    public function setAffiliateMember(array $affiliateMember)
    {
        $this->AffiliateMember = $affiliateMember;
    }

    /**
     * Collect the value of the KeyDescriptor-property
     * @return KeyDescriptor[]
     */
    public function getKeyDescriptor()
    {
        return $this->KeyDescriptor;
    }

    /**
     * Set the value of the KeyDescriptor-property
     * @param array $keyDescriptor
     */
    public function setKeyDescriptor(array $keyDescriptor)
    {
        $this->KeyDescriptor = $keyDescriptor;
    }

    /**
     * Add this AffiliationDescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this endpoint to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_string($this->affiliationOwnerID));
        assert(is_null($this->ID) || is_string($this->ID));
        assert(is_null($this->getValidUntil()) || is_int($this->getValidUntil()));
        assert(is_null($this->getCacheDuration()) || is_string($this->getCacheDuration()));
        assert(is_array($this->Extensions));
        assert(is_array($this->AffiliateMember));
        assert(!empty($this->AffiliateMember));
        assert(is_array($this->KeyDescriptor));

        $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:AffiliationDescriptor');
        $parent->appendChild($e);

        $e->setAttribute('affiliationOwnerID', $this->affiliationOwnerID);

        if (isset($this->ID)) {
            $e->setAttribute('ID', $this->ID);
        }

        if ($this->getValidUntil() !== null) {
            $e->setAttribute('validUntil', gmdate('Y-m-d\TH:i:s\Z', $this->getValidUntil()));
        }

        if ($this->getCacheDuration() !== null) {
            $e->setAttribute('cacheDuration', $this->getCacheDuration());
        }

        Extensions::addList($e, $this->Extensions);

        Utils::addStrings($e, Constants::NS_MD, 'md:AffiliateMember', false, $this->AffiliateMember);

        foreach ($this->KeyDescriptor as $kd) {
            $kd->toXML($e);
        }

        $this->signElement($e, $e->firstChild);

        return $e;
    }
}
