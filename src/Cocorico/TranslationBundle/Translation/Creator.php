<?php

namespace Cocorico\TranslationBundle\Translation;

use JMS\TranslationBundle\Translation\ExtractorManager;
use JMS\TranslationBundle\Translation\FileWriter;
use JMS\TranslationBundle\Translation\LoaderManager;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\Message;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Wrapper around the different components.
 *
 * This class ties the different components together, and is responsible for
 * creating new messages in the message catalogue, and persisting them
 */
class Creator
{
    /**
     * @var LoaderManager
     */
    private $loader;
    /**
     * @var ExtractorManager
     */
    private $extractor;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var FileWriter
     */
    private $writer;

    /**
     * Creator constructor.
     * @param LoaderManager $loader
     * @param ExtractorManager $extractor
     * @param LoggerInterface $logger
     * @param FileWriter $writer
     */
    public function __construct(LoaderManager $loader, ExtractorManager $extractor, LoggerInterface $logger, FileWriter $writer)
    {
        $this->loader = $loader;
        $this->extractor = $extractor;
        $this->logger = $logger;
        $this->writer = $writer;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->extractor->setLogger($logger);
    }

    /**
     * @param string $file
     * @param string $format
     * @param string $domain
     * @param string $locale
     * @param string $id
     */
    public function createTranslation($file, $format, $domain, $locale, $id)
    {
        /* @var $catalogue MessageCatalogue */
        $catalogue = $this->loader->loadFile($file, $format, $locale, $domain);
        $message = new Message($id, $domain);
        $catalogue->add($message);
        $this->writer->write($catalogue, $domain, $file, $format);
    }

}
