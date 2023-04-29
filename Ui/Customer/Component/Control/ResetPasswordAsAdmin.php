<?php

namespace Sandesh\CustomerPasswordReset\Ui\Customer\Component\Control;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Sandesh\CustomerPasswordReset\Ui\Customer\Component\Button\DataProvider;

class ResetPasswordAsAdmin extends GenericButton implements ButtonProviderInterface
{
    private DataProvider $dataProvider;
    private AuthorizationInterface $authorization;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param DataProvider $dataProvider
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        $this->authorization = $context->getAuthorization();
        parent::__construct($context, $registry);
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $customerId = (int)$this->getCustomerId();
        $isAllowed = $this->authorization->isAllowed('Sandesh_CustomerPasswordReset::reset_password_as_admin');
        if($isAllowed) {
            return $this->dataProvider->getData($customerId);
        }
        return [];
    }
}
