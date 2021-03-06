<?php

declare(strict_types=1);

/*
 * @copyright   2019 Mautic Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://www.mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\IntegrationsBundle\Tests\Unit\Sync\Notification\Helper;

use MauticPlugin\IntegrationsBundle\Event\InternalObjectOwnerEvent;
use MauticPlugin\IntegrationsBundle\IntegrationEvents;
use MauticPlugin\IntegrationsBundle\Sync\Exception\ObjectNotFoundException;
use MauticPlugin\IntegrationsBundle\Sync\Exception\ObjectNotSupportedException;
use MauticPlugin\IntegrationsBundle\Sync\Notification\Helper\OwnerProvider;
use MauticPlugin\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\Contact;
use MauticPlugin\IntegrationsBundle\Sync\SyncDataExchange\Internal\ObjectProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OwnerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectProvider;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

    /**
     * @var OwnerProvider
     */
    private $ownerProvider;

    protected function setUp(): void
    {
        $this->objectProvider = $this->createMock(ObjectProvider::class);
        $this->dispatcher     = $this->createMock(EventDispatcherInterface::class);
        $this->ownerProvider  = new OwnerProvider($this->dispatcher, $this->objectProvider);
    }

    public function testGetOwnersForObjectIdsWithoutIds(): void
    {
        $this->objectProvider->expects($this->never())
            ->method('getObjectByName');

        $this->assertSame([], $this->ownerProvider->getOwnersForObjectIds(Contact::NAME, []));
    }

    public function testGetOwnersForObjectIdsWithUnknownObject(): void
    {
        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with('Unicorn')
            ->willThrowException(new ObjectNotFoundException('Unicorn'));

        $this->expectException(ObjectNotSupportedException::class);

        $this->ownerProvider->getOwnersForObjectIds('Unicorn', [123]);
    }

    public function testGetOwnersForObjectIdsWithKnownObject(): void
    {
        $internalObject = new Contact();
        $this->objectProvider->expects($this->once())
            ->method('getObjectByName')
            ->with(Contact::NAME)
            ->willReturn($internalObject);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                IntegrationEvents::INTEGRATION_FIND_OWNER_IDS,
                $this->callback(function (InternalObjectOwnerEvent $event) use ($internalObject) {
                    $this->assertSame($internalObject, $event->getObject());
                    $this->assertSame([123], $event->getObjectIds());

                    // Simulate a subscriber. Format: [object_id => owner_id].
                    $event->setOwners([$event->getObjectIds()[0] => 456]);

                    return true;
                })
            );

        $this->assertSame([123 => 456], $this->ownerProvider->getOwnersForObjectIds(Contact::NAME, [123]));
    }
}
