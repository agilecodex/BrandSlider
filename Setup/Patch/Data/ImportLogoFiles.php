<?php
declare(strict_types=1);

namespace Acx\BrandSlider\Setup\Patch\Data;

use Acx\BrandSlider\Model\BrandFactory;
use Acx\BrandSlider\Model\BrandRepository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Import logo files supplied in pub/logo-files as enabled global brands.
 * The logo filename is used as the stable idempotency key.
 */
class ImportLogoFiles implements DataPatchInterface
{
    private const SOURCE_DIRECTORY = 'logo-files';
    private const MEDIA_DIRECTORY = 'acx/brand/logo';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly DirectoryList $directoryList,
        private readonly File $fileDriver,
        private readonly BrandFactory $brandFactory,
        private readonly BrandRepository $brandRepository
    ) {
    }

    public function apply(): void
    {
        $sourceDirectory = $this->directoryList->getPath(DirectoryList::PUB)
            . DIRECTORY_SEPARATOR . self::SOURCE_DIRECTORY;

        if (!$this->fileDriver->isExists($sourceDirectory)) {
            return;
        }

        $mediaDirectory = $this->directoryList->getPath(DirectoryList::MEDIA)
            . DIRECTORY_SEPARATOR . self::MEDIA_DIRECTORY;
        $this->fileDriver->createDirectory($mediaDirectory);

        $files = $this->fileDriver->readDirectory($sourceDirectory);
        sort($files, SORT_STRING);
        $sortOrder = 10;

        foreach ($files as $sourceFile) {
            if (!$this->isLogoFile($sourceFile)) {
                continue;
            }

            $filename = basename($sourceFile);
            $targetFile = $mediaDirectory . DIRECTORY_SEPARATOR . $filename;
            if (!$this->fileDriver->isExists($targetFile)) {
                $this->fileDriver->copy($sourceFile, $targetFile);
            }

            $connection = $this->moduleDataSetup->getConnection();
            $table = $this->moduleDataSetup->getTable('acx_brand_slider');
            $select = $connection->select()
                ->from($table, ['brand_id'])
                ->where('logo = ?', $filename)
                ->limit(1);

            if ($connection->fetchOne($select)) {
                $sortOrder += 10;
                continue;
            }

            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = ucwords(str_replace(['-', '_'], ' ', $name));
            $brand = $this->brandFactory->create();
            $brand->setName($name)
                ->setLogo($filename)
                ->setLogoAlt($name)
                ->setStatus(1)
                ->setSortOrder($sortOrder)
                ->setStoreIds([0]);
            $this->brandRepository->save($brand);
            $sortOrder += 10;
        }
    }

    private function isLogoFile(string $file): bool
    {
        return (bool)preg_match('/\.(png|jpe?g|gif|webp|bmp)$/i', $file);
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
