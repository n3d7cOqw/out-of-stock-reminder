<?php

namespace OutOfStockReminder\Form;

use OutOfStockReminder\Entity\Rule;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShopObjectNotFoundException;

final class RuleDataProvider implements FormDataProviderInterface
{
    /**
     * Get form data for given object with given id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getData($id)
    {
        $ruleObjectModel = new Rule($id);

        // check that the element exists in db
        if (empty($ruleObjectModel->getId())) {
            throw new PrestaShopObjectNotFoundException('Object not found');
        }

        return [
            'id' => $ruleObjectModel->getId(),
            'title' => $ruleObjectModel->getTitle(),
            'category_id' => $ruleObjectModel->getCategoryId(),
            'product_id' => $ruleObjectModel->getProductId(),
            'email' => $ruleObjectModel->getEmail(),
            'status' => $ruleObjectModel->getStatus(),
        ];
    }

    /**
     * Get default form data.
     *
     * @return mixed
     */
    public function getDefaultData()
    {
        return [
            'id' => null,
            'title' => null,
            'category_id' => null,
            'product_id' => null,
            'email' => null,
            'status' => null
        ];
    }
}
