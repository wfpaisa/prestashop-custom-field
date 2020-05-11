<?php
/**
 * After install clean prestashop cache
 */

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ps_customercedula extends Module
{
    // const CLASS_NAME = 'ps_customercedula';

    public function __construct()
    {
        $this->name = 'ps_customercedula';
        $this->version = '1.0.0';
        $this->author = 'wfpaisa';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans(
            'Cedula',
            [],
            'Modules.ps_customercedula.Admin'
        );

        $this->description =
            $this->getTranslator()->trans(
                'Customer cedula',
                [],
                'Modules.ps_customercedula.Admin'
            );

        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];
    }
    /**
     * This function is required in order to make module compatible with new translation system.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Install module and register hooks to allow grid modification.
     *
     * @see https://devdocs.prestashop.com/1.7/modules/concepts/hooks/use-hooks-on-modern-pages/
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('additionalCustomerFormFields') &&
            $this->registerHook('actionCustomerFormBuilderModifier') &&
            $this->registerHook('actionAfterCreateCustomerFormHandler') &&
            $this->registerHook('actionAfterUpdateCustomerFormHandler') &&
            $this->alterCustomerTable()
        ;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallAlterCustomerTable();
    }

    /**
     * Alter customer table, add module fields
     *
     * @return bool true if success or already done.
     */
    protected function alterCustomerTable()
    {
        $sql = 'ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` ADD `cedula` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL';
        return Db::getInstance()->execute($sql);
    }

    /**
     * Uninstalls sample tables required for demonstration.
     *
     * @return bool
     */
    private function uninstallAlterCustomerTable()
    {
        $sql = 'ALTER TABLE `' . pSQL(_DB_PREFIX_) . 'customer` DROP `cedula`';
        return Db::getInstance()->execute($sql);
    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     * FRONT_END
     * @param array $params
     */
    public function hookAdditionalCustomerFormFields($params)
    {
        return [
            (new FormField)
                ->setName('cedula')
                ->setType('text')
                ->setRequired(true) // is Required
                ->setLabel($this->l('Cédula'))
        ];
    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     * BACK_END
     * @param array $params
     */
    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $formBuilder->add('cedula', TextType::class, [
            'label' => $this->getTranslator()->trans('Cedula', [], 'Modules.ps_customercedula.Admin'),
            'required' => false,
        ]);
        
        $customer = new Customer($params['id']);
        $params['data']['cedula'] = $customer->cedula;
        
        $formBuilder->setData($params['data']);

    }


    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws CustomerException
     */
    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        $this->updateCustomerCedula($params);
    }

    /**
     * Hook allows to modify Customers form and add additional form fields as well as modify or add new data to the forms.
     *
     * @param array $params
     *
     * @throws CustomerException
     */
    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        $this->updateCustomerCedula($params);
    }

    /**
     * Update / Create 
     * 
     * @param array $params
     *
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     */
    private function updateCustomerCedula(array $params)
    {
        $customerId = (int)$params['id'];
        /** @var array $customerFormData */
        $customerFormData = $params['form_data'];
        $cedula = $customerFormData['cedula'];
        
        try {

            $customer = new Customer($customerId);
            $customer->cedula= $cedula;
            $customer->update();

        } catch (ReviewerException $exception) {
            throw new \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException($exception);
        }
    }

}
