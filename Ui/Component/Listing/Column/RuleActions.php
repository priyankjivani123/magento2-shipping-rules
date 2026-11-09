<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class RuleActions extends Column
{
    private UrlInterface $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['rule_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl('shippingrules/rule/edit', ['rule_id' => $item['rule_id']]),
                            'label' => __('Edit')
                        ],
                        'duplicate' => [
                            'href' => $this->urlBuilder->getUrl('shippingrules/rule/duplicate', ['rule_id' => $item['rule_id']]),
                            'label' => __('Duplicate')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl('shippingrules/rule/delete', ['rule_id' => $item['rule_id']]),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Rule'),
                                'message' => __('Are you sure you want to delete this rule?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
