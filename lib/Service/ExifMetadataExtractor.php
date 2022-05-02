<?php

namespace OCA\Photos\Service;

use OC;
use OCP\Files\FileInfo;
use OCP\IConfig;

class ExifMetadataExtractor
{
	private const EXIF_SECTION = 'EXIF';
	private const EXIF_DATE_TIME_ORIGINAL = 'DateTimeOriginal';

	private string $dataDirectory;

	public function __construct(IConfig $config)
	{
		$this->dataDirectory = $config->getSystemValue('datadirectory', OC::$SERVERROOT . '/data');
	}

	public function getOriginalCreationDate(FileInfo $node): ?int
	{
		$filePath = $this->dataDirectory . $node->getPath();

		if (!\exif_imagetype($filePath)) {
			return null;
		}

		$metadata = \exif_read_data($filePath, self::EXIF_SECTION);

		if ($metadata === false
			|| !isset($metadata[self::EXIF_DATE_TIME_ORIGINAL])
			|| ($dateTimeOriginal = \strtotime($metadata[self::EXIF_DATE_TIME_ORIGINAL])) === false
		) {
			return null;
		}

		return $dateTimeOriginal;
	}
}
