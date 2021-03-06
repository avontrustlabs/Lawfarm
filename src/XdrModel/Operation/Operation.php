<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

/**
 * Known operation types:
 *
    xdr.enum("OperationType", {
        createAccount: 0,
        payment: 1,
        pathPayment: 2,
        manageOffer: 3,
        createPassiveOffer: 4,
        setOption: 5,
        changeTrust: 6,
        allowTrust: 7,
        accountMerge: 8,
        inflation: 9,
        manageDatum: 10,
    });
 *
 * See: https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x
 *
 */
abstract class Operation implements XdrEncodableInterface
{
    const TYPE_CREATE_ACCOUNT       = 0;
    const TYPE_PAYMENT              = 1;
    const TYPE_PATH_PAYMENT         = 2;
    const TYPE_MANAGE_OFFER         = 3;
    const TYPE_CREATE_PASSIVE_OFFER = 4;
    const TYPE_SET_OPTIONS          = 5;
    const TYPE_CHANGE_TRUST         = 6;
    const TYPE_ALLOW_TRUST          = 7;
    const TYPE_ACCOUNT_MERGE        = 8;
    const TYPE_INFLATION            = 9;
    const TYPE_MANAGE_DATA          = 10;

    /**
     * @var AccountId
     */
    protected $sourceAccount;

    /**
     * Type constants are defined by each subclass
     *
     * A full list can be found at: https://www.stellar.org/developers/guides/concepts/list-of-operations.html
     *
     * @var int
     */
    protected $type;

    /**
     * Operation constructor.
     *
     * @param $type int operation type constant
     * @param $sourceAccountId AccountId if null this will default to the source for the transaction
     * @return Operation
     */
    public function __construct($type, $sourceAccountId = null)
    {
        if ($sourceAccountId instanceof Keypair) {
            $sourceAccountId = new AccountId($sourceAccountId->getPublicKey());
        }
        if (is_string($sourceAccountId)) {
            $sourceAccountId = new AccountId($sourceAccountId);
        }

        $this->sourceAccount = $sourceAccountId;
        $this->type = $type;

        return $this;
    }

    /**
     * Child classes MUST call this method to get the header for the operation
     * and then append their body
     *
     * @return string
     */
    public function toXdr()
    {
        $bytes = '';

        // Source Account
        $bytes .= XdrEncoder::optional($this->sourceAccount);

        // Type
        $bytes .= XdrEncoder::unsignedInteger($this->type);

        return $bytes;
    }
}