<?php

namespace Olssonm\Swish;

use Olssonm\Swish\Util\Uuid;

/**
 * @property string $id
 * @property string $originalPaymentReference
 * @property string $payerPaymentReference
 * @property string $callbackUrl
 * @property string $payerAlias
 * @property string $payeeAlias
 * @property string $amount
 * @property string $currency
 * @property string $message
 * @property string $status
 * @property string $dateCreated
 * @property string $datePaid
 * @property string $errorCode
 * @property string $errorMessage
 * @property string $additionalInformation
 */
class Refund extends Resource
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->id = $this->id ?? Uuid::make();
    }
}
