<?php

namespace GeorgRinger\BelayoutFileprovider\Provider;


use TYPO3\CMS\Backend\View\BackendLayout\BackendLayout;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayoutCollection;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderContext;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileProvider implements DataProviderInterface {

	const FILE_TYPES_LAYOUT = 'ts,txt';
	const FILE_TYPES_ICON = 'png,gif,jpg';
	const FILE_TYPES_TRANSLATION = 'xlf,xml';

	/**
	 * Adds backend layouts to the given backend layout collection.
	 *
	 * @param DataProviderContext $dataProviderContext
	 * @param BackendLayoutCollection $backendLayoutCollection
	 * @return void
	 */
	public function addBackendLayouts(DataProviderContext $dataProviderContext, BackendLayoutCollection $backendLayoutCollection) {
		$files = $this->getLayoutFiles();
		foreach ($files as $file) {
			$backendLayout = $this->createBackendLayout($file);
			$backendLayoutCollection->add($backendLayout);
		}
	}

	/**
	 * Gets a backend layout by (regular) identifier.
	 *
	 * @param string $identifier
	 * @param integer $pageId
	 * @return NULL|BackendLayout
	 */
	public function getBackendLayout($identifier, $pageId) {
		$files = $this->getLayoutFiles();
		foreach ($files as $file) {
			$fileInformation = pathinfo($file);
			if ($identifier === $fileInformation['filename']) {
				return $this->createBackendLayout($file);
			}
		}
	}


	/**
	 * Creates a new backend layout using the given record data.
	 *
	 * @param string $file
	 * @return BackendLayout
	 */
	protected function createBackendLayout($file) {
		$fileInformation = pathinfo($file);
		$backendLayout = BackendLayout::create(
			$fileInformation['filename'],
			$this->getTitle($fileInformation),
			GeneralUtility::getUrl($file)
		);

		$this->addIcon($fileInformation, $backendLayout);
		return $backendLayout;
	}


	/**
	 * Get all files
	 *
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	protected function getLayoutFiles() {
		$fileCollection = array();

		if (!isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']) || !is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider'])) {
			return $fileCollection;
		}

		// Handle extensions
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['ext'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['ext'] as $extensionKey) {
				$path = GeneralUtility::getFileAbsFileName('EXT:' . $extensionKey . '/Resources/Private/BackendLayouts/');

				$filesOfDirectory = GeneralUtility::getFilesInDir($path, self::FILE_TYPES_LAYOUT, TRUE, 1);
				foreach ($filesOfDirectory as $file) {
					$this->addFileToCollection($file, $fileCollection);
				}
			}
		}

		// Handle directories
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['dir'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['dir'] as $extensionKey) {
				$extensionKey = GeneralUtility::getFileAbsFileName($extensionKey);

				$filesOfDirectory = GeneralUtility::getFilesInDir($extensionKey, self::FILE_TYPES_LAYOUT, TRUE, 1);
				foreach ($filesOfDirectory as $file) {
					$this->addFileToCollection($file, $fileCollection);
				}
			}
		}

		// Handle single files
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['file'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['file'] as $file) {
				$file = GeneralUtility::getFileAbsFileName($file);
				$this->addFileToCollection($file, $fileCollection);
			}
		}

		return $fileCollection;
	}


	/**
	 * Add an optional icon to the BackendLayout
	 *
	 * @param array $fileInformation pathinfo() of the given file
	 * @param BackendLayout $backendLayout
	 * @return void
	 */
	protected function addIcon($fileInformation, BackendLayout $backendLayout) {
		$imageExtensions = explode(',', self::FILE_TYPES_ICON);
		$filePath = $fileInformation['dirname'] . '/' . $fileInformation['filename'];

		foreach ($imageExtensions as $extension) {
			$icon = $filePath . '.' . $extension;
			if (is_file($icon)) {
				$icon = '../' . str_replace(PATH_site, '', $icon);
				$backendLayout->setIconPath($icon);
				break;
			}
		}
	}

	/**
	 * @param array $fileInformation pathinfo() information of the given file
	 * @return string
	 */
	protected function getTitle(array $fileInformation) {
		$title = $fileInformation['filename'];

		$translationFileEndings = explode(',', self::FILE_TYPES_TRANSLATION);
		$filePath = $fileInformation['dirname'] . '/locallang.';

		foreach ($translationFileEndings as $extension) {
			$file = $filePath . $extension;
			if (is_file($file)) {
				$file = str_replace(PATH_site, '', $file);
				$translatedTitle = $GLOBALS['LANG']->sL('LLL:' . $file . ':' . $fileInformation['filename']);
				if ($translatedTitle) {
					$title = $translatedTitle;
					break;
				}
			}
		}

		return $title;
	}

	/**
	 * @param $file
	 * @param $fileCollection
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	protected function addFileToCollection($file, array &$fileCollection) {
		$key = sha1($file);
		if (isset($fileCollection[$key])) {
			throw new \UnexpectedValueException(sprintf('The file "%s" exists already, see "%s"', $file, $fileCollection[$key]));
		}
		$fileCollection[$key] = $file;
	}

}