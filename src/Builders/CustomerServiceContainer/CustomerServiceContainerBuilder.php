<?php

namespace AndrewSvirin\Ebics\Builders\CustomerServiceContainer;

use AndrewSvirin\Ebics\Contracts\OrderDataInterface;
use AndrewSvirin\Ebics\Handlers\Traits\XPathTrait;
use AndrewSvirin\Ebics\Models\CustomerServiceContainer;
use AndrewSvirin\Ebics\Models\OrderData;
use AndrewSvirin\Ebics\Services\RandomService;
use DateTime;

/**
 * Class CustomerServiceContainerBuilder builder for schema "urn:conxml:xsd:container.nnn.001.02 container.nnn.001.02_GBIC_3.xsd"
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Serhii Raspopov
 */
class CustomerServiceContainerBuilder {

    use XPathTrait;

    private RandomService $randomService;
    private ?CustomerServiceContainer $instance;

    public function __construct() {
        $this->randomService = new RandomService();
    }

    /**
     * @param string $senderId
     * @param string $idType
     * @param DateTime $dateTime   Createion datetime
     * @return $this
     */
    public function createInstance(
        string $senderId,
        string $idType,
        DateTime $dateTime = null
    ): CustomerServiceContainerBuilder {

        $this->instance = new CustomerServiceContainer();

        if ($dateTime == null)
            $dateTime = new DateTime();

        $xmDocument = $this->instance->createElementNS(
            'urn:conxml:xsd:container.nnn.001.02',
            'conxml'
        );
        $xmDocument->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $xmDocument->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'urn:conxml:xsd:container.nnn.001.02 container.nnn.001.02.xsd'
        );
        $this->instance->appendChild($xmDocument);

        $xmlContainerId = $this->instance->createElement('ContainerId');
        $xmDocument->appendChild($xmlContainerId);

        $xmlSenderId = $this->instance->createElement('SenderId');
        $xmlSenderId->nodeValue = $senderId;
        $xmlContainerId->appendChild($xmlSenderId);

        $xmlIdType = $this->instance->createElement('IdType');
        $xmlIdType->nodeValue = $idType;
        $xmlContainerId->appendChild($xmlIdType);

        $xmlTimeStamp = $this->instance->createElement('TimeStamp');
        $xmlTimeStamp->nodeValue = $dateTime->format('Hisv');
        $xmlContainerId->appendChild($xmlTimeStamp);

        $xmlCreDtTm = $this->instance->createElement('CreDtTm');
        $xmlCreDtTm->nodeValue = $dateTime->format('Y-m-d\TH:i:s\.vP');
        $xmDocument->appendChild($xmlCreDtTm);

        return $this;
    }

    /**
     * @param OrderData $orderData
     * @param int       $painNumber     Number N of pain message tag <MsgPain00N>
     */
    public function addMessage(
        OrderData $orderData
    ): CustomerServiceContainerBuilder {

        $tag = null;
        $ns = $orderData->documentElement->lookupNamespaceURI(null);
        if (preg_match('/\:xsd\:pain\.([0-9]{3})\./', $ns, $tag)) {
            $tag = $tag[1];
        }
        if (!is_numeric($tag)) {
            throw new InvalidArgumentException("Invalid schema of OrderData");
        }

        $xmlMsgPain = $this->instance->createElement("MsgPain{$tag}");

        $xmlMsgDoc = $this->instance->createElement('Document');
        $xmlMsgDoc->setAttribute('xmlns', $ns);
        foreach ($orderData->documentElement->childNodes as $child) {
            $xmlMsgDoc->appendChild($this->instance->importNode($child, true));
        }

        $method = 'sha256';

        $xmlMsgPain->appendChild($xmlHash = $this->instance->createElement('HashValue', ''));
        $xmlMsgPain->appendChild($this->instance->createElement('HashAlgorithm', strtoupper($method)));
        $xmlMsgPain->appendChild($xmlMsgDoc);

        $this->instance->documentElement->appendChild($xmlMsgPain);

        $xmlHash->nodeValue = strtoupper(hash($method, $xmlMsgDoc->C14N()));

        return $this;
    }

    public function popInstance(): CustomerServiceContainer {
        $instance = $this->instance;
        $this->instance = null;

        return $instance;
    }

}
