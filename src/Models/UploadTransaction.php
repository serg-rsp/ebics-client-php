<?php

namespace AndrewSvirin\Ebics\Models;

use AndrewSvirin\Ebics\Contracts\UploadTransactionInterface;

/**
 * Upload Transaction represent collection of segments.
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrew Svirin
 */
final class UploadTransaction extends Transaction implements UploadTransactionInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $numSegments;

    /**
     * @var TransferSegment[]
     */
    private $segments;

    /**
     * @var string
     */
    private $orderData;

    /**
     * @var UploadSegment
     */
    private $initialization;

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setNumSegments(int $numSegments): void
    {
        $this->numSegments = $numSegments;
    }

    public function getNumSegments(): int
    {
        return $this->numSegments;
    }

    public function addSegment(TransferSegment $segment): void
    {
        $this->segments[] = $segment;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getLastSegment(): ?TransferSegment
    {
        if (0 === count($this->segments)) {
            return null;
        }

        return end($this->segments);
    }

    public function setOrderData(string $orderData): void
    {
        $this->orderData = $orderData;
    }

    public function getOrderData(): string
    {
        return $this->orderData;
    }

    public function setInitialization(UploadSegment $uploadSegment): void
    {
        $this->initialization = $uploadSegment;
    }

    public function getInitialization(): UploadSegment
    {
        return $this->initialization;
    }
}
