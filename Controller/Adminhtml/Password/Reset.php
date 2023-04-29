<?php

namespace Sandesh\CustomerPasswordReset\Controller\Adminhtml\Password;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Reset extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = '';
    private CustomerRepositoryInterface $customerRepository;
    private CustomerRegistry $customerRegistry;
    private EncryptorInterface $encryptor;
    private JsonFactory $jsonFactory;

    /**
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry $customerRegistry
     * @param EncryptorInterface $encryptor
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context                     $context,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry            $customerRegistry,
        EncryptorInterface          $encryptor,
        JsonFactory                 $jsonFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Execute action based on request and return result
     *
     * @return Json
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $params = $this->_request->getParams();
            $customer = $this->customerRepository->getById($params['customerId']);
            $customerSecure = $this->customerRegistry->retrieveSecureData($params['customerId']);
            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($this->encryptor->getHash($params['newPassword'], true));
            $this->customerRepository->save($customer);
            $result->setData(
                [
                    'message' => "Password Reset Successful",
                ]
            );
        } catch (\Exception $exception) {
            $result->setData(
                [
                    'message' => $exception->getMessage(),
                ]
            );
        }
        return $result;
    }
}
