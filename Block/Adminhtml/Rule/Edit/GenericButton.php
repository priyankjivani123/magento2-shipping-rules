<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Block\Adminhtml\Rule\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

class GenericButton
{
    protected Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    public function getRuleId(): ?int
    {
        $id = $this->context->getRequest()->getParam('rule_id');
        return $id ? (int)$id : null;
    }
}
