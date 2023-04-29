<?php

namespace Sandesh\CustomerPasswordReset\Ui\Customer\Component\Button;

use Magento\Framework\Escaper;

class DataProvider
{

    private Escaper $escaper;
    private $data;

    /**
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Escaper $escaper,
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->data = $data;
    }

    /**
     * Get data for Login as Customer button.
     *
     * @param int $customerId
     * @return array
     */
    public function getData(int $customerId): array
    {
        $buttonData = [
            'label' => __('Reset Password as Admin'),
            'on_click' => 'window.rpaPopup("")',
        ];

        return array_merge_recursive($buttonData, $this->data);
    }
}
