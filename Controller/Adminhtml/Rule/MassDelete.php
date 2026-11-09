<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;

class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private RuleRepositoryInterface $ruleRepository;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;

        foreach ($collection as $rule) {
            $this->ruleRepository->delete($rule);
            $count++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 shipping rule(s) have been deleted.', $count)
        );

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
