<?php
/*
 * Turiknox_SampleImageUploader

 * @category   Turiknox
 * @package    Turiknox_SampleImageUploader
 * @copyright  Copyright (c) 2017 Turiknox
 * @license    https://github.com/turiknox/magento2-sample-imageuploader/blob/master/LICENSE.md
 * @version    1.0.0
 */
namespace Turiknox\SampleImageUploader\Controller\Adminhtml\Image;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Turiknox\SampleImageUploader\Api\ImageRepositoryInterface;
use Turiknox\SampleImageUploader\Controller\Adminhtml\Image;
use Turiknox\SampleImageUploader\Model\ResourceModel\Image\CollectionFactory;

class MassDelete extends Image
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var string
     */
    protected $_successMessage;

    /**
     * @var string
     */
    protected $_errorMessage;

    /**
     * MassAction constructor.
     *
     * @param Registry $registry
     * @param ImageRepositoryInterface $imageRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param $successMessage
     * @param $errorMessage
     */
    public function __construct(
        Registry $registry,
        ImageRepositoryInterface $imageRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        parent::__construct($registry, $imageRepository, $resultPageFactory, $dateFilter, $context);
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_successMessage    = $successMessage;
        $this->_errorMessage      = $errorMessage;
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $image) {
                $this->_imageRepository->delete($image);
            }
            $this->messageManager->addSuccessMessage(__($this->_successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->_errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('sampleimageuploader/image');
        return $redirectResult;
    }
}
