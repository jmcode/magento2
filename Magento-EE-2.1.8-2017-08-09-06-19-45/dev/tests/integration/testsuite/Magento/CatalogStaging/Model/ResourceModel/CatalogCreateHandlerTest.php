<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogStaging\Model\ResourceModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\Store;

class CatalogCreateHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CategoryFactory */
    private $categoryFactory;

    /** @var  \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->categoryFactory = $this->objectManager->get(CategoryFactory::class);
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/CatalogStaging/_files/apply_version.php
     */
    public function testExecute()
    {
        $category = $this->getStagingCategory();
        $category->save();

        $categoryOnAllStores = $this->categoryFactory->create();
        $categoryOnAllStores->setStoreId(Store::DEFAULT_STORE_ID);
        $categoryOnAllStores->load($category->getId());

        $this->assertEquals($category->getName(), $categoryOnAllStores->getName());
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/CatalogStaging/_files/apply_version.php
     */
    public function testExecuteUsingRepository()
    {
        $category = $this->getStagingCategory();
        $this->objectManager->get(CategoryRepositoryInterface::class)->save($category);

        $categoryOnAllStores = $this->objectManager->create(CategoryRepositoryInterface::class)
            ->get($category->getId(), Store::DEFAULT_STORE_ID);
        $this->assertEquals($category->getName(), $categoryOnAllStores->getName());
    }

    /**
     * Retrieve category for test.
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getStagingCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryFactory->create();
        $category->setPath('1');
        $category->setParentId(1);
        $category->setName('Staging Category');
        $category->setIsActive(false);
        $category->setIncludeInMenu(false);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());

        return $category;
    }
}
