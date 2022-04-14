<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Phuong LE <phuong.le@menincode.com>
 * @copyright Copyright (c) 2022 Men In Code Ltd (https://www.menincode.com)
 */

declare(strict_types=1);

namespace Easygento\CustomPagination\Block;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Items
 */
class Items extends Template
{
    /**
     * Description PARAM constant
     *
     * @var string PARAM
     */
    public const PARAM = 'item_from';
    /**
     * Description PAGE_SIZE constant
     *
     * @var int PAGE_SIZE
     */
    public const PAGE_SIZE = 5;
    /**
     * Description $segment
     *
     * @var int $segment
     */
    protected int $segment;
    /**
     * Description $data
     *
     * @var mixed[] $results
     */
    protected array $results;
    /**
     * Description $items
     *
     * @var mixed[] $items
     */
    protected array $items = [];
    /**
     * Description $request
     *
     * @var RequestInterface $request
     */
    protected RequestInterface $request;
    /**
     * Description $urlBuilder
     *
     * @var UrlInterface $urlBuilder
     */
    protected UrlInterface $urlBuilder;

    /**
     * Items constructor
     *
     * @param Context          $context
     * @param RequestInterface $request
     * @param UrlInterface     $urlBuilder
     * @param array            $data
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request    = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return mixed[]
     */
    public function getItems(): array
    {
        $this->results = // get Items based on your needs
        $from = $this->request->getParam(self::PARAM) ?? '0';
        $this->segment = (int)$from;
        $this->items = array_slice($this->results, (int)$from, self::PAGE_SIZE);

        return $this->items;
    }

    /**
     * @return string
     */
    protected function getCurrentUrl(): string
    {
        /** @var string[] $params */
        $params = $this->request->getParams();
        /** prevent duplicate get param */
        unset($params[self::PARAM]);

        return $this->urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * @return string|null
     */
    public function getPreviousUrl(): ?string
    {
        if ($this->segment < self::PAGE_SIZE) {
            return null;
        }
        /** @var string $url */
        $url = $this->getCurrentUrl();
        /** @var int $segment */
        $segment = $this->segment - self::PAGE_SIZE;

        return $url . '?' . self::PARAM . '=' . $segment;
    }

    /**
     * @return string|null
     */
    public function getNextUrl(): ?string
    {
        /** @var int $segment */
        $segment = $this->segment + self::PAGE_SIZE;
        if ($segment > count($this->results)) {
            return null;
        }
        /** @var string $url */
        $url = $this->getCurrentUrl();

        return $url . '?' . self::PARAM . '=' . $segment;
    }

    /**
     * @return Phrase
     */
    public function getEmptyItemMessage(): Phrase
    {
        return __('You have no items.');
    }
}
