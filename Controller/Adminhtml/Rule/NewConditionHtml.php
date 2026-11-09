<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\ConditionInterface;
use PriyankJivani\ShippingRules\Model\RuleFactory;

class NewConditionHtml extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private RuleFactory $ruleFactory;

    public function __construct(Context $context, RuleFactory $ruleFactory)
    {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
    }

    public function execute()
    {
        $formName = $this->getRequest()->getParam('form_namespace', 'shippingrules_rule_form');
        $id = $this->getRequest()->getParam('id');
        $typeParam = (string)$this->getRequest()->getParam('type', '');
        $typeArray = explode('|', str_replace('-', '/', $typeParam));
        $type = $typeArray[0];

        if ($type && class_exists($type) && !in_array(ConditionInterface::class, class_implements($type))) {
            $this->getResponse()->setBody('');
            return;
        }

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('conditions');

        if (!empty($typeArray[1])) {
            $model->setAttribute($typeArray[1]);
        }

        if ($model instanceof AbstractCondition) {
            $jsFormName = $this->getRequest()->getParam('form');
            $model->setJsFormObject($jsFormName);
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
