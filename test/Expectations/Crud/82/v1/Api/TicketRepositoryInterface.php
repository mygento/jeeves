<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TicketRepositoryInterface
{
    /**
     * Save Ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\TicketInterface $entity): Data\TicketInterface;

    /**
     * Retrieve Ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\TicketInterface;

    /**
     * Retrieve Ticket entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\TicketSearchResultsInterface;

    /**
     * Delete Ticket
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\TicketInterface $entity): bool;

    /**
     * Delete Ticket
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
