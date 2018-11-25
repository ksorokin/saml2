<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\saml\Attribute;

/**
 * Class representing SAML 2 IDPSSODescriptor.
 *
 * @package SimpleSAMLphp
 */
final class IDPSSODescriptor extends SSODescriptorType
{
    /**
     * Whether AuthnRequests sent to this IdP should be signed.
     *
     * @var bool|null
     */
    private $WantAuthnRequestsSigned = null;

    /**
     * List of SingleSignOnService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $SingleSignOnService = [];

    /**
     * List of NameIDMappingService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $NameIDMappingService = [];

    /**
     * List of AssertionIDRequestService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $AssertionIDRequestService = [];

    /**
     * List of supported attribute profiles.
     *
     * Array with strings.
     *
     * @var array
     */
    private $AttributeProfile = [];

    /**
     * List of supported attributes.
     *
     * Array with \SAML2\XML\saml\Attribute objects.
     *
     * @var \SAML2\XML\saml\Attribute[]
     */
    private $Attribute = [];

    /**
     * Initialize an IDPSSODescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(\DOMElement $xml = null)
    {
        parent::__construct('md:IDPSSODescriptor', $xml);

        if ($xml === null) {
            return;
        }

        $this->WantAuthnRequestsSigned = Utils::parseBoolean($xml, 'WantAuthnRequestsSigned', null);

        foreach (Utils::xpQuery($xml, './saml_metadata:SingleSignOnService') as $ep) {
            $this->SingleSignOnService[] = new EndpointType($ep);
        }

        foreach (Utils::xpQuery($xml, './saml_metadata:NameIDMappingService') as $ep) {
            $this->NameIDMappingService[] = new EndpointType($ep);
        }

        foreach (Utils::xpQuery($xml, './saml_metadata:AssertionIDRequestService') as $ep) {
            $this->AssertionIDRequestService[] = new EndpointType($ep);
        }

        $this->AttributeProfile = Utils::extractStrings($xml, Constants::NS_MD, 'AttributeProfile');

        foreach (Utils::xpQuery($xml, './saml_assertion:Attribute') as $a) {
            $this->Attribute[] = new Attribute($a);
        }
    }

    /**
     * Collect the value of the WantAuthnRequestsSigned-property
     * @return bool|null
     */
    public function wantAuthnRequestsSigned()
    {
        return $this->WantAuthnRequestsSigned;
    }

    /**
     * Set the value of the WantAuthnRequestsSigned-property
     * @param bool|null $flag
     */
    public function setWantAuthnRequestsSigned(bool $flag = null)
    {
        $this->WantAuthnRequestsSigned = $flag;
    }

    /**
     * Collect the value of the SingleSignOnService-property
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getSingleSignOnService()
    {
        return $this->SingleSignOnService;
    }

    /**
     * Set the value of the SingleSignOnService-property
     * @param array $singleSignOnService
     */
    public function setSingleSignOnService(array $singleSignOnService)
    {
        $this->SingleSignOnService = $singleSignOnService;
    }

    /**
     * Collect the value of the NameIDMappingService-property
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getNameIDMappingService()
    {
        return $this->NameIDMappingService;
    }

    /**
     * Set the value of the NameIDMappingService-property
     * @param array $nameIDMappingService
     */
    public function setNameIDMappingService(array $nameIDMappingService)
    {
        $this->NameIDMappingService = $nameIDMappingService;
    }

    /**
     * Collect the value of the AssertionIDRequestService-property
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getAssertionIDRequestService()
    {
        return $this->AssertionIDRequestService;
    }

    /**
     * Set the value of the AssertionIDRequestService-property
     * @param array $assertionIDRequestService
     */
    public function setAssertionIDRequestService(array $assertionIDRequestService)
    {
        $this->AssertionIDRequestService = $assertionIDRequestService;
    }

    /**
     * Collect the value of the AttributeProfile-property
     * @return array
     */
    public function getAttributeProfile()
    {
        return $this->AttributeProfile;
    }

    /**
     * Set the value of the AttributeProfile-property
     * @param array $attributeProfile
     */
    public function setAttributeProfile(array $attributeProfile)
    {
        $this->AttributeProfile = $attributeProfile;
    }

    /**
     * Collect the value of the Attribute-property
     * @return \SAML2\XML\md\Attribute[]
     */
    public function getAttribute()
    {
        return $this->Attribute;
    }

    /**
     * Set the value of the Attribute-property
     * @param array $attribute
     */
    public function setAttribute(array $attribute)
    {
        $this->Attribute = $attribute;
    }

    /**
     * Add this IDPSSODescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this IDPSSODescriptor to.
     * @return \DOMElement
     */
    public function toXML(\DOMElement $parent)
    {
        assert(is_null($this->WantAuthnRequestsSigned) || is_bool($this->WantAuthnRequestsSigned));
        assert(is_array($this->SingleSignOnService));
        assert(is_array($this->NameIDMappingService));
        assert(is_array($this->AssertionIDRequestService));
        assert(is_array($this->AttributeProfile));
        assert(is_array($this->Attribute));

        $e = parent::toXML($parent);

        if ($this->WantAuthnRequestsSigned === true) {
            $e->setAttribute('WantAuthnRequestsSigned', 'true');
        } elseif ($this->WantAuthnRequestsSigned === false) {
            $e->setAttribute('WantAuthnRequestsSigned', 'false');
        }

        foreach ($this->SingleSignOnService as $ep) {
            $ep->toXML($e, 'md:SingleSignOnService');
        }

        foreach ($this->NameIDMappingService as $ep) {
            $ep->toXML($e, 'md:NameIDMappingService');
        }

        foreach ($this->AssertionIDRequestService as $ep) {
            $ep->toXML($e, 'md:AssertionIDRequestService');
        }

        Utils::addStrings($e, Constants::NS_MD, 'md:AttributeProfile', false, $this->AttributeProfile);

        foreach ($this->Attribute as $a) {
            $a->toXML($e);
        }

        return $e;
    }
}
