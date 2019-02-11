<?php declare(strict_types=1);

namespace Shopware\Storefront\Page\Listing;

use Shopware\Core\Checkout\CheckoutContext;
use Shopware\Core\Framework\Routing\InternalRequest;
use Shopware\Storefront\Framework\Page\PageLoaderInterface;
use Shopware\Storefront\Framework\Page\PageWithHeaderLoader;
use Shopware\Storefront\Pagelet\Listing\ListingPageletLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ListingPageLoader implements PageLoaderInterface
{
    /**
     * @var PageWithHeaderLoader|PageLoaderInterface
     */
    private $genericLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ListingPageletLoader|PageLoaderInterface
     */
    private $listingPageletLoader;

    public function __construct(
        PageLoaderInterface $genericLoader,
        EventDispatcherInterface $eventDispatcher,
        PageLoaderInterface $listingPageletLoader
    ) {
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->listingPageletLoader = $listingPageletLoader;
    }

    public function load(InternalRequest $request, CheckoutContext $context)
    {
        $page = $this->genericLoader->load($request, $context);

        $page = ListingPage::createFrom($page);

        $page->setListing($this->listingPageletLoader->load($request, $context));

        $this->eventDispatcher->dispatch(
            ListingPageLoadedEvent::NAME,
            new ListingPageLoadedEvent($page, $context, $request)
        );

        return $page;
    }
}