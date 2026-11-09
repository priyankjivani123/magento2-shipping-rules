<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use PriyankJivani\ShippingRules\Model\Service\ExportService;

class ExportCsv extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::shipping_areas';

    private FileFactory $fileFactory;
    private ExportService $exportService;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ExportService $exportService
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->exportService = $exportService;
    }

    public function execute()
    {
        $csvContent = $this->exportService->exportAreasToCsv();
        return $this->fileFactory->create(
            'shipping_areas_' . date('Y-m-d_His') . '.csv',
            $csvContent,
            DirectoryList::VAR_DIR,
            'text/csv'
        );
    }
}
