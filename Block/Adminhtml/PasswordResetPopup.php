<?php

namespace Sandesh\CustomerPasswordReset\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\LoginAsCustomerAdminUi\Ui\Customer\Component\ConfirmationPopup\Options;
use Magento\LoginAsCustomerApi\Api\ConfigInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

class PasswordResetPopup extends Template
{

    /**
     * @var StoreOptions
     */
    private $storeOptions;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param Template\Context $context
     * @param StoreOptions $storeOptions
     * @param ConfigInterface $config
     * @param Json $json
     * @param array $data
     * @param Options|null $options
     */
    public function __construct(
        Template\Context $context,
        StoreOptions $storeOptions,
        ConfigInterface $config,
        Json $json,
        array $data = [],
        ?Options $options = null
    ) {
        parent::__construct($context, $data);
        $this->storeOptions = $storeOptions;
        $this->config = $config;
        $this->json = $json;
        $this->options = $options ?? ObjectManager::getInstance()->get(Options::class);
    }

    /**
     * @inheritdoc
     * @since 100.4.0
     */
    public function getJsLayout()
    {
        $layout = $this->json->unserialize(parent::getJsLayout());

        $layout['components']['rpa-popup']['title'] = __('Reset Password as Admin');
        $layout['components']['rpa-popup']['content'] = "You are about to reset the password of the customer";
        $layout['components']['rpa-popup']['passwordField'] = $this->getHtmlForPasswordField();
        $layout['components']['rpa-popup']['url'] = $this->getUrl('customer/password/reset');
        $layout['components']['rpa-popup']['customerId'] = $this->_request->getParam('id');
        return $this->json->serialize($layout);
    }

    /**
     * @inheritdoc
     * @since 100.4.0
     */
    public function toHtml()
    {
        if (!$this->config->isEnabled()) {
            return '';
        }
        return parent::toHtml();
    }

    private function getHtmlForPasswordField()
    {
        return "
        <form class='password-input-field'>
        <span><label for='password' class='password-label'>Enter Password</label></span>
        <span><input class='password-field' pattern='^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-]).{8,}$' type='password' name='new-password' value='' minlength='6' required/></span>
        </form>
        ";
    }

}
