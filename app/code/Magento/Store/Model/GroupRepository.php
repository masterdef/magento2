<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data;

class GroupRepository implements \Magento\Store\Api\GroupRepositoryInterface
{
    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var Data\GroupInterface[]
     */
    protected $entities = [];

    /**
     * @var bool
     */
    protected $allLoaded = false;

    /**
     * @var \Magento\Store\Model\Resource\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @param GroupFactory $groupFactory
     * @param \Magento\Store\Model\Resource\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        GroupFactory $groupFactory,
        \Magento\Store\Model\Resource\Group\CollectionFactory $groupCollectionFactory
    ) {
        $this->groupFactory = $groupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->entities[$id])) {
            return $this->entities[$id];
        }
        $group = $this->groupFactory->create();
        $group->load($id);
        if (!$group->getId()) {
            throw new NoSuchEntityException();
        }
        $this->entities[$id] = $group;
        return $group;
    }

    /**
     * @return Data\StoreInterface[]
     */
    public function getList()
    {
        if (!$this->allLoaded) {
            /** @var \Magento\Store\Model\Resource\Group\Collection $groupCollection */
            $groupCollection = $this->groupCollectionFactory->create();
            $groupCollection->setLoadDefault(true);
            foreach ($groupCollection as $item) {
                $this->entities[$item->getId()] = $item;
            }
            $this->allLoaded = true;
        }
        return $this->entities;
    }

    /**
     * Clear cached data
     */
    public function clean()
    {
        $this->entities = [];
        $this->allLoaded = false;
    }
}
