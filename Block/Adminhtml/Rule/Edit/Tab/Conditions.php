<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Rule\Block\Conditions as RuleConditionsBlock;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;

class Conditions extends Generic implements TabInterface
{
    protected Fieldset $rendererFieldset;
    protected RuleConditionsBlock $conditions;
    protected RuleFactory $ruleFactory;
    protected RuleRepositoryInterface $ruleRepository;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleConditionsBlock $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabClass(): ?string
    {
        return null;
    }

    public function getTabUrl(): ?string
    {
        return null;
    }

    public function isAjaxLoaded(): bool
    {
        return false;
    }

    public function getTabLabel()
    {
        return __('Conditions');
    }

    public function getTabTitle()
    {
        return __('Conditions');
    }

    public function canShowTab(): bool
    {
        return true;
    }

    public function isHidden(): bool
    {
        return false;
    }

    protected function _prepareForm()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule_id');
        if ($ruleId) {
            try {
                $model = $this->ruleRepository->getById($ruleId);
            } catch (\Exception $e) {
                $model = $this->ruleFactory->create();
            }
        } else {
            $model = $this->ruleFactory->create();
        }

        $formName = 'shippingrules_rule_form';
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'shippingrules/rule/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __('Apply the rule only if the following conditions are met (leave blank for all orders):')
            ]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )->setRule($model)->setRenderer($this->conditions);

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    private function setConditionFormName($conditions, string $formName): void
    {
        if (is_object($conditions)) {
            $conditions->setFormName($formName);
            if (method_exists($conditions, 'getConditions') && is_array($conditions->getConditions())) {
                foreach ($conditions->getConditions() as $condition) {
                    $this->setConditionFormName($condition, $formName);
                }
            }
        }
    }
}
